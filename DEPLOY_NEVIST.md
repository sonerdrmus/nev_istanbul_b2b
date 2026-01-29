# nevist.sonerdurmus.com — Git ile Hostinger’a Deploy

Bu rehber, projeyi **SSH + Git** ile **https://nevist.sonerdurmus.com** adresine yayına almak için adım adım anlatır.

---

## SSH bilgileri (Hostinger)

| Alan        | Değer            |
|------------|-------------------|
| **IP**     | 82.198.228.144    |
| **Port**   | 65002             |
| **Kullanıcı** | u934472865     |
| **Domain** | https://nevist.sonerdurmus.com |
| **Repo**   | git@github.com:sonerdrmus/nev_istanbul_b2b.git |

---

## 1. Hostinger’da veritabanı oluşturun

1. **hPanel** → **Databases** → **MySQL Databases**
2. Yeni veritabanı oluşturun (örn. `u934472865_nevist`)
3. Kullanıcı oluşturup bu veritabanına **tüm yetkilerle** bağlayın
4. **Hostname:** panelde yazan (genelde `localhost` veya `mysql.hostinger.com`)
5. Veritabanı adı, kullanıcı adı ve şifreyi not edin; `.env` için kullanacaksınız.

---

## 2. SSH ile sunucuya bağlanın

Bilgisayarınızda terminal açıp:

```bash
ssh -p 65002 u934472865@82.198.228.144
```

Şifre sorulursa Hostinger’daki SSH şifrenizi (veya SSH key kullanıyorsanız key’inizi) girin.

---

## 3. Sunucuda site dizinini bulun

Bağlandıktan sonra genelde şu dizinlerden biri olur:

```bash
cd ~
pwd
# Örnek: /home/u934472865

ls
# domains, public_html vb. görebilirsiniz
```

Hostinger’da domain için klasör genelde:

- `~/domains/nevist.sonerdurmus.com/public_html`  
veya  
- `~/public_html` (ana domain ise)

Örnek (domains varsa):

```bash
cd ~/domains/nevist.sonerdurmus.com
# veya
cd ~/domains/nevist.sonerdurmus.com/public_html
```

**Emin değilseniz:** hPanel → **Files** → File Manager’da nevist.sonerdurmus.com için hangi yol gösteriliyorsa (örn. `domains/nevist.sonerdurmus.com/public_html`), SSH’da `~/` ile başlayan karşılığını kullanın: `~/domains/nevist.sonerdurmus.com/public_html`.

---

## 4. Repoyu clone edin

**Önemli:** Mevcut `public_html` içinde “Default page” dosyaları varsa önce yedekleyin veya repoyu boş bir alt dizine clone edin.

**Seçenek A — Repoyu doğrudan `public_html` içeriği yapmak (önerilen):**

```bash
# Örnek: site kökü public_html
cd ~/domains/nevist.sonerdurmus.com/public_html
# Mevcut dosyaları temizleyin (index.html vb. varsa)
# rm -f index.html  vb. — dikkatli olun, gerekirse önce yedek alın

git clone git@github.com:sonerdrmus/nev_istanbul_b2b.git .
# Sonundaki nokta (.) "bu klasöre clone et" demek
```

**Seçenek B — Alt dizine clone etmek:**

```bash
cd ~/domains/nevist.sonerdurmus.com/public_html
git clone git@github.com:sonerdrmus/nev_istanbul_b2b.git nev_istanbul_b2b
cd nev_istanbul_b2b
```

Aşağıdaki adımlarda Laravel **src** klasörü içinde çalışacağız; Seçenek B kullandıysanız tüm `cd` komutlarında `public_html/nev_istanbul_b2b` altında olduğunuzu varsayın.

---

## 5. Laravel kurulumu (src içinde)

```bash
# Repo kökündeyken (public_html veya public_html/nev_istanbul_b2b)
cd src

composer install --no-dev --optimize-autoloader
cp .env.example .env
nano .env
```

**`.env` içinde mutlaka düzenleyin:**

```env
APP_NAME="Nev Istanbul B2B"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://nevist.sonerdurmus.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u934472865_nevist
DB_USERNAME=u934472865_user
DB_PASSWORD=VERITABANI_SIFRESI
```

Kaydedip çıkın (nano: `Ctrl+O`, Enter, `Ctrl+X`).

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan filament:assets
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 6. Document root’u Laravel `public` klasörüne verin

Hostinger’da bu domain’in **document root**’u Laravel’in **public** klasörüne işaret etmeli.

- Repoyu **public_html** içine clone ettiyseniz (Seçenek A):  
  **Document root:** `public_html/src/public`
- Repoyu **public_html/nev_istanbul_b2b** içine clone ettiyseniz (Seçenek B):  
  **Document root:** `public_html/nev_istanbul_b2b/src/public`

**Ayarlama:**

1. **hPanel** → **Domains** → **nevist.sonerdurmus.com** → **Manage**
2. **Advanced** veya **Document root** bölümüne girin
3. Document root alanına yukarıdaki yollardan uygun olanı yazın (Hostinger bazen tam path ister, örn. `/home/u934472865/domains/nevist.sonerdurmus.com/public_html/src/public`)
4. Kaydedin.

---

## 7. Klasör izinleri

SSH’da:

```bash
cd ~/domains/nevist.sonerdurmus.com/public_html/src
# veya nev_istanbul_b2b/src
chmod -R 775 storage bootstrap/cache
```

---

## 8. Kontrol

- Tarayıcıda açın: **https://nevist.sonerdurmus.com**
- Admin panel: **https://nevist.sonerdurmus.com/admin**
- İlk giriş için veritabanında kullanıcı yoksa: sunucuda `cd src` → `php artisan db:seed` (demo kullanıcılar) veya `php artisan make:filament-user` ile admin oluşturun.

---

## 9. Sonraki güncellemeler (Git push sonrası)

Kod değişikliği yapıp GitHub’a push ettikten sonra sunucuda:

```bash
ssh -p 65002 u934472865@82.198.228.144
cd ~/domains/nevist.sonerdurmus.com/public_html
# veya cd ~/domains/nevist.sonerdurmus.com/public_html/nev_istanbul_b2b
git pull
cd src
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## Özet komutlar (ilk kurulum)

```bash
ssh -p 65002 u934472865@82.198.228.144
cd ~/domains/nevist.sonerdurmus.com/public_html
git clone git@github.com:sonerdrmus/nev_istanbul_b2b.git .
cd src
composer install --no-dev --optimize-autoloader
cp .env.example .env
nano .env   # DB_* ve APP_URL doldur
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan filament:assets
php artisan config:cache
php artisan route:cache
php artisan view:cache
chmod -R 775 storage bootstrap/cache
```

Ardından hPanel’den document root’u **public_html/src/public** yapın.
