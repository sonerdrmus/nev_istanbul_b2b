# B2B Admin ve Şirket Yönetimi Kurulumu

## Yapılanlar

- **Admin panel** (`/admin`): Şirket, kullanıcı (müşteri), ürün ve sipariş yönetimi. Sadece admin ürün ekleyebilir/düzenleyebilir. Ürünlere dinamik varyasyonlar (Renk, Beden vb.) tanımlanabilir (combobox, checkbox; seçenekler serbest).
- **Müşteri paneli** (`/panel`): Müşteriler (şirkete bağlı, admin olmayan kullanıcılar) burada sadece mağazaya gidip ürünleri görüntüleyebilir ve sipariş verebilir; ürün ekleme/çıkarma yok.
- **E-ticaret** (http://localhost:8010): Ana sayfa ürün listesi, sepet, sipariş oluşturma. Ürünler herkese görünür; fiyatlar ve sepete ekleme/sipariş sadece giriş yapmış müşterilere açıktır. Misafir ürünleri görür ama fiyat görmez; Giriş Yap ile müşteri paneline gidip giriş yaptıktan sonra fiyat görür ve sipariş verebilir. Ödeme yöntemi: **sadece Havale / EFT**. Sepete ekle → Ödemeye geç → Sipariş tamamla → Onay sayfasında havale bilgisi veya "e-posta ile iletilecek" mesajı.
- **Şirket / Kullanıcı**: Admin şirket ve kullanıcı (müşteri) CRUD; sadece `is_admin = true` olanlar `/admin`, diğerleri `/panel` kullanır.

## Hostinger'da Yayına Alma

Projeyi Hostinger paylaşımlı hosting veya VPS'e yüklemek için adım adım rehber: **[HOSTINGER_KURULUM.md](src/HOSTINGER_KURULUM.md)**.

---

## Kurulum (Docker ile)

**1. PHP intl eklentisi:** Filament `ext-intl` istiyor. Dockerfile'a eklendi; imajı yeniden build edin:

```bash
docker compose build b2b_app --no-cache
docker compose up -d
```

**2. Filament ve migration:**

```bash
# Filament'i yükle
docker compose exec b2b_app composer update filament/filament --with-all-dependencies

# İmajı yeniden build etmeden sadece kurulum yapmak isterseniz (intl olmadan):
# docker compose exec b2b_app composer update filament/filament --with-all-dependencies --ignore-platform-req=ext-intl

# Migration'ları çalıştır (companies, users, kâr marjı vb. için zorunlu)
docker compose exec b2b_app php /var/www/artisan migrate --force

# Filament CSS/JS asset'lerini public'e kopyala (giriş sayfası tasarımı için zorunlu)
docker compose exec b2b_app php artisan filament:assets

# İlk admin, demo şirket ve demo müşteri (migrate'ten sonra çalıştırın)
docker compose exec b2b_app php artisan db:seed

# Ürün görselleri için storage link (opsiyonel)
docker compose exec b2b_app php artisan storage:link
```

**3. doctrine/dbal (MySQL'de migration `change()` için):** Sütun tipi değiştiren migration'lar (örn. stok alanını nullable yapma) MySQL'de `doctrine/dbal` ister. Docker içinde kurmak için:

```bash
docker compose exec b2b_app composer require doctrine/dbal --working-dir=/var/www
```

Kurulumdan sonra `php artisan migrate` tekrar çalıştırılabilir; `change()` kullanan migration'lar hatasız tamamlanır.

**.env** içinde giriş sayfası ve asset URL'leri doğru çalışsın diye: `APP_URL=http://localhost:8010` (port ile). CSS/JS hâlâ bozuksa `ASSET_URL=http://localhost:8010` ekleyin.

## İlk giriş

**Önce `php artisan db:seed` çalıştırın;** aksi halde kullanıcı yoktur ve e-posta/şifre kabul edilmez.

**Admin panel** (`/admin`):
- **URL**: http://localhost:8010/admin
- **E-posta**: admin@admin.com | **Şifre**: password

**Müşteri paneli** (`/panel`):
- **URL**: http://localhost:8010/panel
- **E-posta**: customer@demo.com | **Şifre**: password

İlk admin kullanıcısını komutla da oluşturabilirsiniz:

```bash
docker compose exec b2b_app php artisan make:filament-user
```

Oluşturulan kullanıcıya sonradan admin yetkisi vermek için veritabanında `users.is_admin = 1` yapın (bu kullanıcı varsayılan olarak admin değildir). Veya Filament'te başka bir admin ile giriş yapıp kullanıcıyı düzenleyin.

## Menü

**Admin panel** (`/admin`):
- **Anasayfa** grubu:
  - **Banner Slaytlar**: Anasayfadaki hero carousel slaytları. Yeni slayt ekleyebilir, görsel yükleyebilir, üst etiket / başlık / açıklama / buton metni ve linki / metin hizası (sol, orta, sağ) düzenleyebilir, sıralayabilir.
  - **Anasayfa Alanları**: Anasayfadaki kampanya kutuları (örn. Kampanya – Üst Giyim – Tişört… – İncele). Yeni alan ekleyebilir, görsel (arka plan), etiket, başlık, alt metin, buton metni ve link URL'si girebilir, sıralayabilir.
- **B2B Yönetimi** grubu:
- **Şirketler**: Şirket ekleme/düzenleme, kod, aktif/pasif, kâr marjı (%)
- **Kullanıcılar**: Müşteri (kullanıcı) ekleme, giriş bilgisi (e-posta/şifre), şirket atama, admin checkbox. Şirket atanmış kullanıcıda "Kâr marjı (indirim) %" alanı ile o şirketin mağaza indirim oranı düzenlenir (bayi talebi onaylanan müşteriler için buradan kâr marjı verilir).
- **Ürünler**: Ürün ekleme/düzenleme; her ürüne dinamik varyasyonlar (örn. Renk, Beden) — combobox/checkbox, seçenekler serbest
- **Siparişler**: Tüm siparişlerin listesi ve detayı

**Müşteri paneli** (`/panel`):
- **Dashboard**: Özet bilgi
- **Mağazaya git**: E-ticaret sayfasına link; müşteri sadece ürünleri görüntüleyip sipariş verebilir (ürün yönetimi yok)

**E-ticaret** (`/`): Yayındaki ürünler listelenir; admin panelde eklenen ve "yayında" işaretlenen ürünler burada görünür. Varyasyonlu ürünlerde renk/beden vb. seçilerek sepete eklenir.

## Kâr marjı (müşteri indirimi)

- **Şirket:** Admin panel → B2B Yönetimi → Şirketler → Düzenle → "Kâr marjı (%)". Bu oran, o şirkete bağlı müşteri giriş yaptığında mağazada gördüğü tüm ürün fiyatlarına indirim olarak uygulanır (örn. 10 = %10 indirim).
- **Bayilik talebi onayı:** Bayilik Talepleri → Onayla ile onaylanır; kâr marjı onay sırasında sorulmaz. Onaylanan müşteri Kullanıcılar sayfasında düzenlenirken "Kâr marjı (indirim) %" alanından şirketin indirim oranı verilir.
- **Mağaza:** Giriş yapmış müşteri liste, ürün detay, sepet ve siparişte indirimli fiyat görür ve öder. Şirketi "Aktif" kapalı yaparak müşteriyi pasife alabilirsiniz.

## Veritabanı

- `banner_slides`: image_path, title, headline, description, button_text, button_url, text_align, sort_order, is_active (anasayfa hero slider)
- `home_sections`: image_path, label, title, subtitle, button_text, link_url, sort_order, is_active (anasayfa kampanya kutuları)
- `companies`: name, code, is_active, profit_margin_percentage (müşteri indirim %)
- `users`: company_id (nullable), is_admin (boolean)
- `products`: company_id, name, slug, description, price, image, is_active, sort_order
- `product_variations`: product_id, name (Renk, Beden vb.), type (select/checkbox), options (JSON dizi), sort_order
- `orders`: order_number, customer_name, customer_email, customer_phone, customer_address, payment_method (havale), status, total, notes
- `order_items`: order_id, product_id, product_name, price, quantity, subtotal, variation_data (JSON, seçilen varyasyonlar)

## E-ticaret: Havale bilgisi

Sipariş onay sayfasında banka bilgisi göstermek için `.env` içine örnek:

```
STORE_BANK_TRANSFER_ENABLED=true
STORE_BANK_NAME=Örnek Bank
STORE_BANK_IBAN=TR00 0000 0000 0000 0000 0000 00
STORE_BANK_ACCOUNT_HOLDER=Şirket Unvanı
STORE_BANK_BRANCH=Merkez
STORE_BANK_DESCRIPTION=Sipariş numarası açıklama kısmına yazılmalıdır.
```

Bunlar doldurulmazsa sayfada "Banka bilgileri e-posta ile iletilecektir" mesajı çıkar.
