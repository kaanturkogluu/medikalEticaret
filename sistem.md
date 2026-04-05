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
  Page.php        Channel.php   ChannelProduct.php  ChannelBrand.php  ChannelCategory.php
  ChannelCredential.php  ProductImage.php  ProductAttribute.php  CategoryAttribute.php

resources/views/
  home.blade.php           ← Ana sayfa (ürün grid, banner, slider)
  product_detail.blade.php ← Ürün detay sayfası
  favorites.blade.php      ← Favori listesi
  contact.blade.php
  layouts/
    app.blade.php           ← Public layout (cart drawer, tab switcher, Alpine stores)
    admin.blade.php         ← Admin layout (sidebar, navbar)
  admin/
    dashboard.blade.php
    products.blade.php / products/ (edit vb.)
    brands/    index.blade.php, create.blade.php, edit.blade.php
    categories/ index.blade.php, create.blade.php, edit.blade.php
    orders.blade.php
    appearance/ (tab_switch, contact, social, general, marketplaces, banner...)
    marketplaces/ settings.blade.php  logs.blade.php  sync/
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

## ⚠️ Teknik Önemli Notlar

1. **Sidebar duplicate link riski:** Kategoriler linki daha önce 2 kere eklenmiş olabilir → admin.blade.php kontrol et.
2. **Boolean cast:** `active` alanı Product ve Brand modellerinde `bool` cast edilmiş.
3. **Görseller:** `storage/app/public/` altında, `asset('storage/...')` ile erişilir. `php artisan storage:link` gerekli.
4. **Ürün varyantları:** `parent_id` ile kendi kendine ilişki, `variant_key` ile tanımlanır.
5. **Marketplace verisi:** `raw_marketplace_data` JSON alanında ham API verisi tutulur.
6. **Alpine Store:** `cart` ve `fav` store'ları `layouts/app.blade.php` içinde tanımlanır.
7. **Tab Switcher JS:** Hem `layouts/admin.blade.php` hem `layouts/app.blade.php` içinde bulunur.

---

*Son güncelleme: 2026-04-05 — Marka + Kategori yönetimi eklendi, Tab Switcher tamamlandı.*
