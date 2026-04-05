# MultiSync — Sistem Teknik Özeti (AI Baz Dosyası)

> Bu dosya Antigravity AI asistanı tarafından okunacak ve her yeni işlemde baz alınacaktır.
> Büyük değişikliklerden sonra güncellenmesi gerekmektedir.

---

## 🛠 Teknik Altyapı
| Bileşen | Detay |
|---|---|
| Framework | Laravel 10+ |
| PHP | 8.1+ |
| Database | MySQL (XAMPP) |
| Dizin | `c:\xampp\htdocs\me` |
| Admin CSS/JS | Tailwind CSS (CDN), Alpine.js (CDN), FontAwesome 6 |
| Public CSS/JS | Tailwind CSS (CDN), Alpine.js (CDN), Splide.js |
| State Management | Alpine.js `$store` (cart, fav) — LocalStorage'a kaydedilir |
| Settings | `Setting` modeli — Key-Value DB tablosu |

---

## 📁 Kritik Dosya Haritası

```
routes/
  web.php                          ← Tüm rotalar burada

app/Http/Controllers/
  HomeController.php               ← Public: ana sayfa, ürün detay, favoriler
  Auth/LoginController.php         ← Giriş/çıkış
  Admin/
    DashboardController.php
    ProductController.php          ← index, edit, update
    BrandController.php            ← index, create, store, edit, update, destroy, toggleActive
    CategoryController.php         ← index, create, store, edit, update, destroy, toggleActive
    OrderController.php
    ChannelController.php          ← Pazaryeri bağlantıları
    AppearanceController.php       ← contact, marketplaces, social, general, tabSwitch
    BannerController.php

app/Models/
  Product.php     Brand.php     Category.php     Setting.php
  Banner.php      Order.php     OrderItem.php    User.php
  UserAddress.php Page.php      Channel.php      ChannelProduct.php  ChannelBrand.php  ChannelCategory.php
  ChannelCredential.php  ProductImage.php  ProductAttribute.php  CategoryAttribute.php

resources/views/
  home.blade.php           ← Ana sayfa (ürün grid, banner, slider)
  product_detail.blade.php ← Ürün detay sayfası
  favorites.blade.php      ← Favori listesi
  contact.blade.php
  layouts/
    app.blade.php           ← Public layout (cart drawer, tab switcher, Alpine stores)
    admin.blade.php         ← Admin layout (sidebar, navbar)
    user.blade.php          ← Kullanıcı paneli layout (Trendyol tarzı sidebar)
  admin/
    dashboard.blade.php
    products.blade.php / products/ (edit vb.)
    brands/    index.blade.php, create.blade.php, edit.blade.php
    categories/ index.blade.php, create.blade.php, edit.blade.php
    orders.blade.php
    appearance/ (tab_switch, contact, social, general, marketplaces, banner...)
    marketplaces/ settings.blade.php  logs.blade.php  sync/
  user/
    dashboard.blade.php      ← Özet sayfa (stats + son siparişler)
    orders.blade.php         ← Sipariş listesi (Trendyol tarzı filtreler)
    order-detail.blade.php   ← Sipariş detayı, ürünler, adres
    profile.blade.php        ← Kullanıcı bilgileri & Şifre güncelleme
    addresses.blade.php      ← Adres yönetimi (CRUD)
```

---

## 🗂 Model İlişkileri

```
Product → belongsTo Brand, Category
Product → hasMany ProductImage, ProductAttribute, ChannelProduct
Product → belongsToMany Channel (pivot: channel_products)
Product → self-referential (parent_id → variants)

Category → belongsTo Category (parent_id, hiyerarşik)
Category → hasMany Product, ChannelCategory

Brand → hasMany Product, ChannelBrand
```

### Product Alanları (fillable)
`parent_id, variant_key, brand_id, category_id, sku, barcode, name, brand_name, category_name, description, price, stock, active, attributes (JSON), raw_marketplace_data (JSON), marketplace_status, marketplace, external_id, platform_listing_id, product_content_id, supplier_id, views`

---

## 🌐 Route Haritası

### Public
| URL | Route Name | Controller@Method |
|---|---|---|
| `/` | `home` | HomeController@index |
| `/product/{id}` | `product.show` | HomeController@show |
| `/favorites` | `favorites` | HomeController@favorites |
| `/iletisim` | `contact` | view |
| `/p/{slug}` | `page.show` | HomeController@page |
| `/login` | `login` | LoginController@showLoginForm |
| `/admin/login` | `admin.login` | LoginController@showAdminLoginForm |

### Kullanıcı Paneli (`/hesabim/*`, middleware: auth)
| URL | Route Name | Açıklama |
|---|---|---|
| `/hesabim` | `user.dashboard` | Dashboard (Özet) |
| `/hesabim/siparislerim` | `user.orders` | Tüm siparişlerim |
| `/hesabim/siparislerim/{id}` | `user.orders.show` | Sipariş detayı |
| `/hesabim/adreslerim` | `user.addresses` | Adres yönetimi |
| `/hesabim/bilgilerim` | `user.profile` | Profil & Şifre |

### Admin (`/admin/*`, middleware: auth)
| URL | Route Name |
|---|---|
| `/admin` | `admin.dashboard` |
| `/admin/products` | `admin.products` |
| `/admin/products/{id}/edit` | `admin.products.edit` |
| `/admin/orders` | `admin.orders` |
| `/admin/brands` | `admin.brands.index` |
| `/admin/brands/create` | `admin.brands.create` |
| `/admin/brands/{id}/edit` | `admin.brands.edit` |
| `/admin/categories` | `admin.categories.index` |
| `/admin/categories/create` | `admin.categories.create` |
| `/admin/categories/{id}/edit` | `admin.categories.edit` |
| `/admin/pages` | `admin.pages.index` |
| `/admin/pages/create` | `admin.pages.create` |
| `/admin/pages/{slug}/edit` | `admin.pages.edit` |
| `/admin/appearance` | `admin.appearance` |
| `/admin/appearance/general` | `admin.appearance.general` |
| `/admin/appearance/contact` | `admin.appearance.contact` |
| `/admin/appearance/social` | `admin.appearance.social` |
| `/admin/appearance/marketplaces` | `admin.appearance.marketplaces` |
| `/admin/appearance/tab-switch` | `admin.appearance.tab_switch` |
| `/admin/appearance/banner` | `admin.appearance.banner.index` |
| `/admin/marketplaces` | `admin.marketplaces` |
| `/admin/logs` | `admin.logs` |
| `/admin/settings` | `admin.settings` |

---

## ⚙️ Setting Modeli Kullanımı

```php
Setting::getValue('key', $default)   // DB'den okur
Setting::setValue('key', $value)     // DB'ye yazar (updateOrCreate)
```

### Bilinen Setting Anahtarları
| Key | Açıklama |
|---|---|
| `tab_switch_active` | Tab switcher aktif mi (bool) |
| `tab_switch_away_title` | Sekme dışındayken gösterilecek başlık |
| `tab_switch_back_title` | Geri dönünce gösterilecek başlık |
| `site_name` | Site adı |
| `primary_color` | Ana renk (CSS var) |
| `contact_*` | İletişim bilgileri |
| `social_*` | Sosyal medya linkleri |
| `footer_*` | Footer ayarları |

---

## 🔐 Yetkilendirme Sistemi (Role-Based Access Control)

**Gerekli Kolon:** `users.role` (default: `user`, values: `admin`, `user`)

### Middleware
1. **`admin`**: Sadece `role === 'admin'` olanlar erişebilir. Aksi halde `/` ana sayfasına hata mesajıyla yönlendirir.
2. **`user`**: Sadece `role === 'user'` olanlar erişebilir. Eğer `admin` erişmeye çalışırsa `/admin` paneline yönlendirilir.

### Kullanılan Dosyalar
- `database/migrations/2026_04_05_181124_add_role_to_users_table.php`
- `app/Http/Middleware/AdminMiddleware.php`
- `app/Http/Middleware/UserMiddleware.php`
- `app/Models/User.php` (`isAdmin()`, `isUser()` helperları)
- `LoginController.php`: Giriş anında rol kontrolü ve smart redirect.
- `database/seeders/RoleSeeder.php`: Başlangıç rolleri (`kaantrrkoglu@gmail.com` → admin).

### Rota Koruması
- `/admin/*` → `middleware(['auth', 'admin'])`
- `/hesabim/*` → `middleware(['auth', 'user'])`

---

## 🎨 Tasarım Sistemi

### Admin
- Koyu sidebar (`bg-slate-900`) + açık içerik alanı
- Glassmorphism kartlar: `bg-white/50 backdrop-blur-xl rounded-[40px] shadow-2xl`
- Butonlar: `px-10 py-5 font-black italic uppercase tracking-tighter rounded-2xl`
- Aktif sidebar öğesi: `.sidebar-item-active` class
- Başlıklar: `font-black italic tracking-tighter uppercase underline decoration-[var(--primary-color)]`

### Public
- CSS değişkenleri: `var(--primary-color)`, `var(--primary-hover)`
- Ürün kartları: `rounded-3xl hover:shadow-2xl hover:-translate-y-2`
- Renk: `bg-slate-900` (koyu), `text-[var(--primary-color)]` (vurgu)

---

## 📊 Admin Sidebar Yapısı (Güncel)

```
Genel
  ├── Dashboard
  ├── Ürünler
  └── Siparişler

Sistem Ayarları
  ├── Markalar
  ├── Kategoriler
  └── Site Görünümü

Senkronizasyon
  ├── Stok Senkronize
  └── Fiyat Senkronize

Altyapı
  ├── Pazaryeri Bağlantıları
  ├── Loglar & Debug
  └── Ayarlar
```

---

## ✅ Tamamlanan Özellikler

- [x] Admin kimlik doğrulama (login/logout, middleware: auth)
- [x] Dashboard
- [x] Ürün listeleme ve düzenleme (marka güncellenebilir)
- [x] Sipariş listeleme ve detay
- [x] Marka Yönetimi (CRUD + logo upload + toggleActive)
- [x] Kategori Yönetimi (CRUD + hiyerarşi + toggleActive)
- [x] Appearance Hub (banner, iletişim, sosyal medya, genel, tab-switch)
- [x] Tab Title Switcher (30s gecikme, 3s döngü, `visibilitychange` event)
- [x] Public: Ürün kartları yeni sekmede açılıyor (`target="_blank"`)
- [x] Public: Favoriler, ürün detay benzer ürünler yeni sekmede açılıyor
- [x] Pazaryeri bağlantıları (Trendyol entegrasyonu altyapısı)
- [x] **BUG FIX:** Kategori düzenleme — üst kategori dropdown sadece kök kategorileri gösteriyordu, artık tüm kategorileri gösteriyor
- [x] **Üye Kayıt & E-posta Doğrulama:** 6 haneli kod ile doğrulama, 30dk süre, tekrar gönder
- [x] **Sözleşmeler & Politikalar:** Admin CRUD yönetimi + dinamik public sayfa + 10 adet seeder içeriği
- [x] **Kullanıcı Paneli (User Panel):** Trendyol tarzı hesap yönetimi, siparişlerim, adreslerim, bilgilerim
- [x] **Yetkilendirme Altyapısı (RBAC):** Admin ve Kullanıcı rolleri, middleware koruması ve smart login redirect

---

## 🐛 Bilinen Buglar ve Çözümler

### ✅ Kategori Üst Kategori Dropdown Sorunu (Çözüldü — 2026-04-05)
**Sorun:** `CategoryController@edit()` ve `@create()` metodları `whereNull('parent_id')` ile sadece kök kategorileri getiriyordu. Ara seviye kategoriler (ör: YÜZ BAKIM → VÜCUT KREMİ gibi) üst kategori olarak seçilemiyordu ve dropdown "Anakategori (Yok)" olarak görünüyordu.

**Çözüm:**
- `create()`: `Category::orderBy('name')->get()` — tüm kategoriler listelenir.
- `edit()`: `getDescendantIds()` private metodu ile kendi ID'si ve tüm alt dalları hariç tutulur, geri kalan tüm kategoriler listelenir (döngüsel referans engeli).
- View dropdown: Ara kategoriler `└ KATEGORİ` formatıyla gösterilir.

**Etkilenen dosyalar:**
- `app/Http/Controllers/Admin/CategoryController.php`
- `resources/views/admin/categories/edit.blade.php`

---

## 👤 Üye Kayıt & E-posta Doğrulama Sistemi

**Akış:** `/register` → Form → 6 haneli kod e-posta → `/verify-email` → Doğrula → Otomatik giriş → Ana Sayfa

### Veritabanı Değişiklikleri
- `users` tablosuna 2 yeni kolon eklendi:
  - `email_verification_code` (varchar 6, nullable)
  - `email_verification_expires_at` (timestamp, nullable)
- Migration: `2026_04_05_171400_add_verification_code_to_users_table.php`

### User Modeli Değişikliği
- `implements MustVerifyEmail` eklendi
- `fillable` güncellendi: `email_verification_code`, `email_verification_expires_at`, `email_verified_at`

### Yeni Dosyalar
| Dosya | Açıklama |
|---|---|
| `app/Http/Controllers/Auth/RegisterController.php` | register(), showVerifyForm(), verify(), resend() |
| `resources/views/auth/register.blade.php` | Şifre gücü ölçer, show/hide toggle |
| `resources/views/auth/verify-email.blade.php` | 30dk geri sayım, OTP input |
| `resources/views/emails/verify-email.blade.php` | HTML e-posta şablonu (6 haneli kod) |

### Yeni Rotalar
| URL | Method | Route Name | Açıklama |
|---|---|---|---|
| `/register` | GET | `register` | Kayıt formu (guest) |
| `/register` | POST | — | Kayıt işle (guest) |
| `/verify-email` | GET | `verify.form` | Doğrulama formu |
| `/verify-email` | POST | `verify.submit` | Kod doğrula |
| `/verify-email/resend` | POST | `verify.resend` | Kodu tekrar gönder |

### Önemli Notlar
- Kod süresi: **30 dakika**
- Doğrulama başarılı olduğunda `email_verified_at` güncellenir, kullanıcı otomatik login olur.
- Mail gönderimi için `.env` SMTP ayarı gereklidir.
- **Test için:** `MAIL_MAILER=log` bırakılırsa kodlar `storage/logs/laravel.log` dosyasına çıktı verir.
- Login sayfasındaki "Üye Ol" linki `/register` yönlendiriyor.

---

## 📄 Sözleşmeler & Politikalar Sistemi

**Yapı:** Veritabanında `pages` tablosu — Admin panelden CRUD — Public dinamik sayfa `/p/{slug}`

### Veritabanı
| Alan | Tip | Açıklama |
|---|---|---|
| `id` | bigint | Primary key |
| `title` | string | Sayfa başlığı |
| `slug` | string (unique) | SEO dostu URL parçası |
| `content` | longText | HTML destekli içerik |
| `is_active` | boolean | Yayın durumu |
- Migration: `2026_04_05_172951_create_pages_table.php`

### Seeder — Varsayılan Sayfalar
`database/seeders/PageSeeder.php` çalıştırılarak aşağıdaki 10 sayfa DB'ye eklendi:
1. Kullanım Koşulları (`kullanim-kosullari`)
2. Mesafeli Satış Sözleşmesi (`mesafeli-satis-sozlesmesi`)
3. Gizlilik Politikası (`gizlilik-politikasi`)
4. KVKK Aydınlatma Metni (`kvkk-aydinlatma-metni`)
5. Çerez Politikası (`cerez-politikasi`)
6. İade & İptal Politikası (`iade-iptal-politikasi`)
7. Tıbbi Sorumluluk Reddi (`tibbi-sorumluluk-reddi`)
8. Teslimat Politikası (`teslimat-politikasi`)
9. Ödeme Politikası (`odeme-politikasi`)
10. Açık Rıza Metni (`acik-riza-metni`)

> **Güncelleme:** `php artisan db:seed --class=PageSeeder` komutu `updateOrCreate` kullanır, var olan kayıtları siler/yeniden yazmadan güvenle günceller.

### Yeni Dosyalar
| Dosya | Açıklama |
|---|---|
| `app/Models/Page.php` | Model, slug route key |
| `app/Http/Controllers/Admin/PageController.php` | index, create, store, edit, update, destroy, toggle |
| `resources/views/admin/pages/index.blade.php` | Kart grid, toggle, silme |
| `resources/views/admin/pages/edit.blade.php` | HTML içerik editörü, aktif toggle |
| `resources/views/admin/pages/create.blade.php` | Yeni sayfa formu |
| `resources/views/pages/show.blade.php` | Public dinamik görüntüleme, sidebar nav, yazdır |
| `resources/views/terms.blade.php` | Statik Kullanım Koşulları (kullanıcı tarafından eklendi, sabit HTML) |
| `database/seeders/PageSeeder.php` | 10 sayfa için zengin HTML içerikli seeder |

### Rotalar
| URL | Method | Route Name | Açıklama |
|---|---|---|---|
| `/p/{slug}` | GET | `page.show` | Dinamik yasal sayfa |
| `/admin/pages` | GET | `admin.pages.index` | Sayfa listesi |
| `/admin/pages/create` | GET | `admin.pages.create` | Yeni sayfa |
| `/admin/pages/store` | POST | `admin.pages.store` | Sayfa kaydet |
| `/admin/pages/{slug}/edit` | GET | `admin.pages.edit` | Düzenle |
| `/admin/pages/{slug}/update` | PUT | `admin.pages.update` | Güncelle |
| `/admin/pages/{slug}/delete` | DELETE | `admin.pages.destroy` | Sil |
| `/admin/pages/{slug}/toggle` | POST | `admin.pages.toggle` | Aktif/Pasif |

### Önemli Notlar
- `PageController` slug üzerinden route model binding yapar (`getRouteKeyName() = 'slug'`).
- Admin sidebar'da "Sistem Ayarları" altında "Sözleşmeler & Politikalar" linki eklendi.
- Kayıt formundaki Kullanım Koşulları & Gizlilik Politikası bağlantıları dinamik `page.show` rotasını kullanır.
- Tıbbi Sorumluluk Reddi sayfası kırmızı uyarı kutusuyla stilize edilmiştir (medikal site için kritik).

---

## 👤 Kullanıcı Paneli (User Account Panel)

**Yapı:** Trendyol tasarım dili — Sidebar navigasyon — Kart tabanlı içerik — `/hesabim` prefix

### Veritabanı (Adresler)
| Alan | Tip | Açıklama |
|---|---|---|
| `id` | bigint | Primary key |
| `user_id` | bigint | User FK |
| `title` | string | Adres başlığı (Ev, İş vb.) |
| `full_name` | string | Alıcı adı |
| `phone` | string | Telefon |
| `city` / `district` | string | Şehir / İlçe |
| `address` | text | Açık adres |
| `is_default` | boolean | Varsayılan adres mi |
- Migration: `2026_04_05_180449_create_user_addresses_table.php`

### Özellikler & Akış
1.  **Dashboard:** Toplam sipariş/adres sayısı ve son 5 sipariş özeti.
2.  **Siparişlerim:** `customer_email` üzerinden eşleşen siparişler. Trendyol tarzı filtre hapları (Tümü, Devam Eden vb.).
3.  **Adres Yönetimi:** Yeni adres ekleme (form kartı) ve mevcut adresleri silme.
4.  **Profil:** İsim güncelleme ve şifre değiştirme (şifre göster/gizle özelliği dahil).
5.  **Layout:** `layouts/user.blade.php` — Sol tarafta kullanıcı özeti ve kategorize edilmiş menü.

### Önemli Teknik Not
- **Sipariş Eşleşmesi:** Siparişler `user_id` yerine `customer_email` üzerinden çekilir. Bu sayede pazaryerinden (Trendyol vb.) gelen siparişler, kullanıcının kayıtlı e-postasıyla otomatik eşleşir.
- **Login Redirect:** Başarılı giriş sonrası kullanıcı `HomeController@index` yerine `user.dashboard`'a yönlendirilir.
- **Navbar Dinamik Etiket:** Giriş yapmış kullanıcılar için navbarda 'Hesabım', yöneticiler için 'Yönetim Paneli' metni ve ilgili linkler dinamik olarak gösterilir (Role-based).
- **UI/UX Polishing:** Kullanıcı panelindeki sidebar çakışması (overlapping) giderildi, tasarım sistemi (renkler, fontlar, logo) ana site ile senkronize edildi.

---

## ⚠️ Teknik Önemli Notlar

1. **Sidebar duplicate link riski:** Kategoriler linki daha önce 2 kere eklenmiş olabilir → admin.blade.php kontrol et.
2. **Boolean cast:** `active` alanı Product ve Brand modellerinde `bool` cast edilmiş.
3. **Görseller:** `storage/app/public/` altında, `asset('storage/...')` ile erişilir. `php artisan storage:link` gerekli.
4. **Ürün varyantları:** `parent_id` ile kendi kendine ilişki, `variant_key` ile tanımlanır.
5. **Marketplace verisi:** `raw_marketplace_data` JSON alanında ham API verisi tutulur.
6. **Alpine Store:** `cart` ve `fav` store'ları `layouts/app.blade.php` içinde tanımlanır.
7. **Tab Switcher JS:** Hem `layouts/admin.blade.php` hem `layouts/app.blade.php` içinde bulunur.

---

*Son güncelleme: 2026-04-05 — Yetkilendirme ve Kullanıcı Paneli tamamlandı, UI/UX polish yapıldı.*
