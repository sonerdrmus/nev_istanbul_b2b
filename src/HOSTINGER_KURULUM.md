# Hostinger’da B2B E-Ticaret Projesini Yayına Alma

Bu rehber, Laravel + Filament projesini Hostinger paylaşımlı hosting veya VPS’e yüklemeniz için adım adım anlatır.

---

## Proje Docker’da

Yerelde proje **Docker** ile çalışıyor (`docker-compose.yml`, `b2b_app`, `b2b_db`, `b2b_web`). Hostinger’da ise **Docker kullanılmaz**; paylaşımlı hosting’de sadece PHP + MySQL ortamı vardır.

- **Yüklenecek olan:** Repodaki **`src/`** klasörünün içeriği — yani Laravel uygulaması (Docker’da `/var/www` olarak mount edilen klasör). `docker-compose.yml`, `docker/` vb. **Hostinger’a yüklenmez**.
- **Özet:** Yerelde geliştirme = Docker. Hostinger’da canlı = sadece `src/` içindeki Laravel projesi, Hostinger’ın PHP/MySQL’i ile çalışır.

---

## Gereksinimler

- **PHP:** 8.2 veya üzeri (Laravel 12 için)
- **PHP eklentileri:** BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML, Intl (Filament için)
- **MySQL:** 5.7+ veya MariaDB 10.3+
- **Composer:** Sunucuda veya yerelde kurulu (yerelde build edip yükleyebilirsiniz)
- **SSH:** Hostinger’da SSH açıksa işlemler daha kolay olur (Business veya üzeri planlarda genelde vardır)

---

## 1. Veritabanı Oluşturma (Hostinger)

1. **hPanel** → **Databases** → **MySQL Databases**
2. Yeni veritabanı oluşturun (örn. `u123456789_b2b`)
3. Kullanıcı oluşturup bu veritabanına **tüm yetkilerle** bağlayın
4. **Hostname:** `localhost` (veya panelde yazan, örn. `mysql.hostinger.com`)
5. Veritabanı adı, kullanıcı adı ve şifreyi bir yere not edin; `.env` için kullanacaksınız.

---

## 2. Projeyi Sunucuya Yükleme

### Seçenek A: Yerelde build edip FTP/SFTP ile yüklemek (önerilen)

Sunucuda Composer / Node yoksa veya kullanmak istemiyorsanız:

1. **Yerelde** (bilgisayarınızda) proje klasöründe:

```bash
cd src
composer install --no-dev --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

2. `node_modules` ve `.env` **yüklemeyin**; sunucuda yeni `.env` oluşturacaksınız.
3. `.git`, `node_modules`, `tests`, `.phpunit.result.cache` gibi gereksiz klasörleri yüklemeden bırakabilirsiniz.
4. Tüm `src/` içeriğini (app, bootstrap, config, database, public, resources, routes, storage, vendor, artisan, composer.json, composer.lock, .env.example vb.) **FTP/SFTP** ile Hostinger’daki **public_html** içine yükleyin.  
   Yani `public_html` altında şunlar olmalı: `app/`, `bootstrap/`, `config/`, `database/`, `public/`, `storage/`, `vendor/`, `artisan`, `.env.example`, vb.

### Seçenek B: SSH ile sunucuda Composer çalıştırmak

1. Sadece uygulama dosyalarını yükleyin (**vendor** ve `.env` hariç; `.env.example` yükleyin).
2. SSH ile bağlanıp `public_html` (veya sitenin kök dizini) içinde:

```bash
cd public_html   # veya paneldeki gerçek yol
composer install --no-dev --optimize-autoloader
```

### Seçenek C: Git ile deploy (en kolay — SSH gerekli)

Projeyi **GitHub / GitLab**'a atıp Hostinger'da **SSH** ile clone ederseniz hem ilk kurulum hem güncelleme çok kolay olur.

**1. Projeyi Git'e atın (yerelde, bir kez):**

```bash
cd /path/to/new_istanbul_b2b
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/KULLANICI/repo-adi.git
git push -u origin main
```

(GitHub'da yeni repo oluşturup `.env`'in `.gitignore`'da olduğundan emin olun.)

**2. Hostinger'da SSH açın** (hPanel → Advanced → SSH Access). Business / VPS planlarda vardır.

**3. Sunucuda clone + kurulum (ilk kez):**

```bash
# Örnek: Hostinger'da site kökü
cd ~/domains/siteniz.com   # veya hPanel'de yazan private_html üst dizini
git clone https://github.com/KULLANICI/repo-adi.git .
# veya clone'u alt dizine alıp document root'u oraya verirsiniz:
# git clone https://github.com/KULLANICI/repo-adi.git repo && cd repo

# Laravel uygulaması bu repoda src/ içinde
cd src
composer install --no-dev --optimize-autoloader
cp .env.example .env
# .env içine DB_*, APP_URL vb. doldurun (nano .env veya FTP ile)
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan filament:assets
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**4. Document root:** Domain'in document root'unu repodaki **Laravel public** klasörüne verin. Repoyu `public_html` içine clone ettiyseniz: **`public_html/src/public`**. (Hostinger'da "Document root" alanına bu yolu yazın.)

**5. Güncelleme (her seferinde):**

```bash
cd ~/domains/siteniz.com   # veya reponun olduğu yer
git pull
cd src
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Böylece projeyi Git'e atıp sunucuda `git pull` + birkaç komutla güncelleyebilirsiniz; FTP ile dosya taşımaya gerek kalmaz.

---

## 3. Document Root’u `public` Yapma

Laravel’in giriş noktası `public` klasörüdür. Hostinger’da domain’in document root’unu bu klasöre yönlendirin:

1. **hPanel** → **Domains** → ilgili domain → **Manage**
2. **Advanced** / **Dosya Yöneticisi** veya **Document root** ayarına gidin
3. Document root’u **`public_html/public`** olacak şekilde ayarlayın (veya dosyaları `public_html` yerine başka bir dizine attıysanız: `o_dizin/public`).

Böylece tüm istekler `public/index.php` üzerinden Laravel’e gider; `app/`, `config/`, `.env` vb. dışarıdan erişilemez.

---

## 4. Ortam Dosyası (.env)

1. Sunucuda `public_html` (veya proje kökü) içinde `.env.example` dosyasını kopyalayıp `.env` yapın:

```bash
cp .env.example .env
```

(FTP ile yüklüyorsanız `.env.example`’ı indirip `.env` adıyla tekrar yükleyebilirsiniz.)

2. `.env` içinde şunları kendi bilgilerinizle doldurun:

```env
APP_NAME="B2B Mağaza"
APP_ENV=production
APP_KEY=           # 5. adımda üretilecek
APP_DEBUG=false
APP_URL=https://siteniz.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=u123456789_b2b
DB_USERNAME=u123456789_user
DB_PASSWORD=veritabani_sifresi

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

- `APP_URL`: Canlı site adresiniz (https ile).
- Veritabanı bilgileri: Hostinger’da oluşturduğunuz veritabanı ve kullanıcı.

---

## 5. Uygulama Anahtarı ve Migration

SSH varsa `public_html` (proje kökü) içinde:

```bash
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

SSH yoksa:

- **Uygulama anahtarı:** Yerelde `php artisan key:generate` çalıştırıp çıkan `APP_KEY` değerini kopyalayıp sunucudaki `.env` dosyasına yapıştırın.
- **Migration:** Hostinger’da “PHP” veya “Run PHP script” gibi bir araç varsa kullanabilirsiniz; yoksa geçici bir `migrate.php` script’i oluşturup tarayıcıdan bir kez çalıştırıp sonra silmek gibi yöntemlere başvurabilirsiniz (güvenlik için çok dikkatli olun).

---

## 6. Klasör İzinleri

Aşağıdaki klasörlerin yazılabilir olması gerekir (genelde **755** veya **775**; gerekirse **storage** ve **bootstrap/cache** için **775**):

- `storage`
- `storage/app/public`
- `storage/framework/cache`
- `storage/framework/sessions`
- `storage/framework/views`
- `storage/logs`
- `bootstrap/cache`

Hostinger File Manager’da sağ tık → **Permissions** ile ayarlayabilirsiniz.

---

## 7. Filament Asset’leri (Admin Paneli)

Admin paneli (`/admin`) düzgün görünsün diye Filament asset’lerini yayına alın. SSH’da:

```bash
php artisan filament:assets
```

Böylece gerekli CSS/JS `public` içine kopyalanır.

---

## 8. Cron (Zorunlu Değil; Önerilir)

TCMB kuru güncellemesi vb. zamanlanmış işler için Hostinger’da cron ekleyin:

1. **hPanel** → **Advanced** → **Cron Jobs**
2. Örnek: Her saat başı  
   **Command:** `php /home/u123456789/domains/siteniz.com/public_html/artisan schedule:run`

Yol, Hostinger’daki gerçek yol ile değiştirilmelidir (panelde veya SSH’da `pwd` ile kontrol edin).

---

## 9. Kontrol Listesi

- [ ] Veritabanı oluşturuldu, `.env` içinde doğru yazıldı
- [ ] Document root = `public_html/public` (veya `.../public`)
- [ ] `.env` oluşturuldu, `APP_KEY` ve `APP_URL` ayarlandı
- [ ] `php artisan migrate --force` çalıştırıldı
- [ ] `php artisan storage:link` çalıştırıldı
- [ ] `storage` ve `bootstrap/cache` yazılabilir
- [ ] `php artisan filament:assets` çalıştırıldı
- [ ] Tarayıcıda site açılıyor: `https://siteniz.com`
- [ ] Admin panel: `https://siteniz.com/admin`
- [ ] Müşteri paneli: `https://siteniz.com/panel`

---

## Sorun Giderme

- **500 Internal Server Error:**  
  `storage/logs/laravel.log` dosyasına bakın. Sık nedenler: yanlış izinler, eksik `.env`, `APP_KEY` boş, veya PHP sürümü 8.2’den düşük.

- **CSS/JS yüklenmiyor:**  
  `APP_URL` ve (gerekirse) `ASSET_URL` değerinin `https://siteniz.com` ile uyumlu olduğundan emin olun. `php artisan config:clear` sonra `php artisan config:cache` deneyin.

- **Filament sayfaları boş / hatalı:**  
  `php artisan filament:assets` çalıştırıldığından ve `public` içinde Filament dosyalarının oluştuğundan emin olun.

- **Veritabanı bağlantı hatası:**  
  `.env` içindeki `DB_*` değerlerini ve Hostinger’da veritabanı kullanıcısının bu veritabanına yetkili olduğunu kontrol edin. Paylaşımlı hosting’de bazen `DB_HOST` farklı olur (panelde yazanı kullanın).

Bu adımlarla projeyi Hostinger’da yayına alabilirsiniz. Hosting planınız (Shared / Business / VPS) ve PHP sürümüne göre paneldeki menü isimleri hafifçe farklı olabilir; Hostinger destek dokümanlarından “document root” ve “cron” bölümlerine de bakabilirsiniz.

MYSQL EXPORT
docker exec filament_b2b_db mysqldump -u b2b_user -psecret --no-tablespaces new_istanbul_b2b > nev_istanbul_yedek.sql

git push
cd "/Users/sonerdurmus/Projects/Development/B2B Nev İstanbul/new_istanbul_b2b" && git status && git push