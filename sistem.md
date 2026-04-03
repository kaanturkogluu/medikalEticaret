# Çok Kanallı Pazaryeri Ürün Yönetim Sistemi (Varyant & Özellik Mimarisi)

Bu belge, sistemin mimarisini, veri akışını ve ürün/varyant yönetim mantığını detaylandırmaktadır.

## 1. Genel Mimari
Sistem, merkezi bir ürün kataloğu (Global Catalog) ve bu kataloğu farklı satış kanallarıyla (Trendyol, Hepsiburada vb.) ilişkilendiren bir "Bridge" (Köprü) yapısı üzerine kuruludur.

### Temel Prensipler:
- **Merkezi Yönetim**: Ürün bilgileri global tablolarda tutulur.
- **Kanal Ayrımı**: Kanala özel fiyat, stok ve eşleşme bilgileri `channel_products` tablosunda tutulur.
- **Varyant Hiyerarşisi**: Ürünler Parent-Child (Ana Ürün-Varyant) ilişkisiyle gruplanır.
- **Idempotency (Tekrarlanabilirlik)**: Aynı verilerle tekrar çalışan sync işlemleri veritabanında mükerrer kayıt oluşturmaz, mevcut kayıtları günceller.

---

## 2. Veritabanı Yapısı

### Ürün Tabloları
- **`products`**: Ana ürün verilerini tutar. 
  - `parent_id`: Ürünün bağlı olduğu ana ürünü işaret eder. `NULL` ise ürün bir "Parent" (Ana Ürün) konumundadır.
  - `sku`: İşletmenin benzersiz ürün kodu.
- **`product_attributes`**: Ürüne ait teknik özellikleri tutar.
  - `is_variant`: Bu özelliğin varyant oluşturucu olup olmadığını belirtir (Örn: Beden=Evet, Materyal=Hayır).
  - `_variant_key`: Varyant kombinasyonunun hash değerini tutan gizli bir özelliktir.
- **`category_attributes`**: Kategori bazlı kuralları belirler.
  - Hangi kategoride hangi özelliğin varyant oluşturacağını (örneğin "Ayakkabı" kategorisinde "Numara"nın varyant olması) tanımlar.

### Kanal (Mapping) Tabloları
- **`channels`**: Satış kanalları (Trendyol vb.).
- **`channel_products`**: Global ürünün kanaldaki yansıması (Fiyat, Stok, Dış ID).
- **`channel_categories`**: Pazaryeri kategori ID'si ile sistem kategori ID'sini eşleştirir.
- **`channel_brands`**: Pazaryeri marka ID'si ile sistem marka ID'sini eşleştirir.

---

## 3. Servis Katmanları

### `AttributeService`
- **Normalizasyon**: Attribute isimlerini lowercase yapar, baş/son boşlukları temizler. Değerlerin büyük/küçük harf yapısını korur.
- **Filtreleme**: Kategorinin `category_attributes` tanımlarına bakarak hangi özelliklerin varyant oluşturacağını tespit eder.
- **Performans**: Kategori kurallarını istek süresince (in-memory) cache'leyerek N+1 sorgularını önler.

### `VariantService` & `VariantKeyGenerator`
- **Grup Çözümleme**: `productMainId` (Trendyol) üzerinden otomatik olarak Parent ürün oluşturur veya mevcut olanı bulur.
- **Key Üretimi**: Varyant özelliklerini (Renk, Beden vb.) alfabetik sıralayıp MD5 hash üreterek (`VariantKeyGenerator`), her varyant kombinasyonu için tekil ve değişmez bir kimlik oluşturur.
- **Veri Birleştirme**: Parent ürünün ismini ve açıklamasını varyantlardan gelen verilerle (eğer ana ürün verisi boşsa) otomatik doldurur.

---

## 4. Sync Hattı (Pipeline) Akışı

`php artisan sync:trendyol-products` komutu tetiklendiğinde her ürün için şu 12 adımlık süreç işletilir:

1.  **API Verisi Alımı**: Trendyol'dan ham JSON verisi çekilir.
2.  **Ön Filtreleme**: Onaysız (`approved=false`) veya kara listedeki (`blacklisted=true`) ürünler atlanır.
3.  **Kanal Eşleşmeleri**: Marka ve kategori isimleri üzerinden sistemdeki karşılıkları bulunur (`resolveChannelMappings`).
4.  **Attribute Normalizasyonu**: İsimler küçük harf yapılır, boşluklar temizlenir (`normalizeAttributes`).
5.  **Varyant Özellik Tespiti**: Kategorinin kurallarına bakılarak hangi özelliklerin varyant boyutunu oluşturduğu belirlenir.
6.  **Varyant Key Üretimi**: Varyant özelliklerinden tekil bir grup anahtarı (Hash) üretilir.
7.  **Parent Çözümleme**: `productMainId` kullanılarak ana ürün (`parent`) bulunur veya oluşturulur.
8.  **Varyant Upsert**: Ürün `sku` üzerinden güncellenir veya oluşturulur (`upsertVariantProduct`).
9.  **HIyerarşik Bağlantı**: Varyant ürün, Parent ürüne `parent_id` üzerinden bağlanır.
10. **Attribute Senkronizasyonu**: Özellikler `is_variant` bayrağıyla birlikte kaydedilir.
11. **Kanal Köprü Kaydı**: `channel_products` tablosu kanala özel fiyat ve stok ile güncellenir.
12. **Görsel Senkronizasyonu**: Ürün görselleri `product_images` tablosuna aktarılır.

---

## 5. Önemli Kurallar ve Güvenlik
- **Manüel Veri Koruması**: Parent ürün üzerinde manuel yapılan isim veya açıklama değişiklikleri, sync sırasında "non-destructive" (yıkıcı olmayan) mantık sayesinde ezilmez.
- **Hata Yönetimi**: Bir SKU'nun işlenmesi sırasında hata oluşursa, tüm batch durmaz; hata loglanır ve sistem bir sonraki ürüne geçer.
- **Unique Constraints**: Sistem `sku` bazlı tekilliği garanti eder. Aynı SKU'lu ürün farklı kanallardan gelse bile tek bir global ürün altında birleşir.

---

## 6. Yeni Özellik / Varyant Tanımlama
Sisteme yeni bir kategori eklendiğinde, hangi özelliklerin varyant oluşturacağını belirlemek için `category_attributes` tablosuna kayıt girilmelidir. 

**Örnek:** "Tişört" kategorisinde hem "Renk" hem "Beden"in varyant oluşturmasını istiyorsanız:
- `category_id`: Tişört Kategorisi ID
- `name`: "renk" (veya "beden")
- `is_variant`: 1

Bu tanımlama yapıldıktan sonraki ilk sync işleminde sistem bu iki özelliği kullanarak varyantları otomatik gruplayacaktır.
