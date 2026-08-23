<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>NEVİSTANBUL B2B — Kullanım Kılavuzu</title>
    <style>
        @page { margin: 28px 32px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1e293b; line-height: 1.45; }
        h1 { font-size: 22px; color: #184E77; margin: 0 0 8px; }
        h2 { font-size: 15px; color: #184E77; margin: 22px 0 8px; border-bottom: 2px solid #184E77; padding-bottom: 4px; page-break-after: avoid; }
        h3 { font-size: 12px; color: #0f172a; margin: 14px 0 6px; page-break-after: avoid; }
        h4 { font-size: 11px; color: #334155; margin: 10px 0 4px; }
        p { margin: 0 0 7px; }
        ul, ol { margin: 0 0 8px; padding-left: 18px; }
        li { margin-bottom: 3px; }
        .cover { text-align: center; padding: 80px 20px 40px; page-break-after: always; }
        .cover .brand { font-size: 28px; font-weight: bold; color: #184E77; letter-spacing: 0.04em; }
        .cover .sub { font-size: 16px; margin-top: 12px; color: #475569; }
        .cover .meta { margin-top: 40px; color: #64748b; font-size: 10px; }
        .toc { page-break-after: always; }
        .toc a { color: #184E77; text-decoration: none; }
        .toc li { margin-bottom: 5px; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #184E77; padding: 8px 10px; margin: 8px 0 12px; }
        .warn { border-left-color: #d97706; background: #fffbeb; }
        .ok { border-left-color: #059669; background: #ecfdf5; }
        .path { font-family: DejaVu Sans Mono, monospace; font-size: 9.5px; background: #f1f5f9; padding: 1px 4px; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 9.5px; }
        th, td { border: 1px solid #cbd5e1; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #184E77; color: #fff; }
        tr:nth-child(even) td { background: #f8fafc; }
        .part { page-break-before: always; }
        .part-first { page-break-before: avoid; }
        .footer-note { margin-top: 18px; font-size: 9px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        .step { margin: 4px 0 4px 0; }
        .kbd { display: inline-block; border: 1px solid #94a3b8; border-radius: 3px; padding: 0 4px; font-size: 9px; background: #fff; }
    </style>
</head>
<body>

{{-- KAPAK --}}
<div class="cover">
    <div class="brand">NEVİSTANBUL</div>
    <div class="sub">B2B Platformu — Kullanım Kılavuzu</div>
    <p style="margin-top:24px;color:#64748b;">Mağaza (Bayi Sitesi) ve Yönetim Paneli</p>
    <div class="meta">
        Sürüm: {{ $version }}<br>
        Tarih: {{ $date }}<br>
        Dil: Türkçe
    </div>
</div>

{{-- İÇİNDEKİLER --}}
<div class="toc">
    <h1>İçindekiler</h1>
    <ol>
        <li>Giriş ve genel bakış</li>
        <li>
            Bölüm A — Mağaza (Bayi Sitesi)
            <ol>
                <li>Erişim, dil ve para birimi</li>
                <li>Ana sayfa, arama ve ürünler</li>
                <li>Ürün detayı, varyasyon ve özelleştirme</li>
                <li>Bayilik başvurusu ve giriş</li>
                <li>Hesabım</li>
                <li>Sepet, ödeme ve sipariş</li>
                <li>Proforma fatura (PDF / Excel)</li>
                <li>İletişim ve yasal sayfalar</li>
            </ol>
        </li>
        <li>
            Bölüm B — Yönetim Paneli (/admin)
            <ol>
                <li>Giriş ve panoya genel bakış</li>
                <li>E-Ticaret menüsü</li>
                <li>Varyasyon yönetimi</li>
                <li>B2B Yönetimi</li>
                <li>Bayilik onay süreci</li>
                <li>Sipariş yönetimi ve proforma</li>
                <li>Önerilen günlük / haftalık işler</li>
            </ol>
        </li>
        <li>Sık sorulan sorular ve sorun giderme</li>
    </ol>
</div>

{{-- GİRİŞ --}}
<h2>1. Giriş ve genel bakış</h2>
<p>
    NEVİSTANBUL B2B platformu iki ana yüzeyden oluşur:
</p>
<ul>
    <li><strong>Mağaza (site):</strong> Bayilerin ürünleri incelediği, sepete eklediği, sipariş verdiği ve proforma indirdiği vitrin.</li>
    <li><strong>Yönetim paneli:</strong> Yönetici kullanıcıların ürün, kategori, bayi, sipariş, banka, kargo, varyasyon ve arayüz içeriklerini yönettiği Filament paneli (<span class="path">/admin</span>).</li>
</ul>
<div class="box">
    <strong>Temel iş modeli:</strong> Platform B2B odaklıdır. Sepet, ödeme ve sipariş işlemleri <em>giriş yapmış ve onaylı</em> bayiler içindir.
    Ödeme yöntemi havale / EFT’tir. Sipariş sonrası proforma fatura PDF ve Excel olarak indirilebilir.
</div>
<table>
    <tr><th>Rol</th><th>Nereye girer?</th><th>Ne yapar?</th></tr>
    <tr><td>Misafir</td><td>Ana site</td><td>Ürünleri görür, bayilik başvurusu yapar, iletişim formunu kullanır. Sepet/sipariş yok.</td></tr>
    <tr><td>Bayi (onaylı)</td><td><span class="path">/giris</span> → site</td><td>Sepet, ödeme, sipariş, hesabım, proforma indirir.</td></tr>
    <tr><td>Yönetici (admin)</td><td><span class="path">/admin</span></td><td>Tüm katalog, bayi onayları, siparişler, banka/kargo/arayüz.</td></tr>
</table>

{{-- BÖLÜM A --}}
<div class="part">
    <h1>Bölüm A — Mağaza (Bayi Sitesi)</h1>
    <p>Bu bölüm, bayilerin ve ziyaretçilerin kullandığı web sitesini açıklar.</p>

    <h2>A.1 Erişim, dil ve para birimi</h2>
    <h3>Üst bar</h3>
    <ul>
        <li><strong>Para birimi seçici:</strong> Aktif para birimleri (ör. TRY, USD, EUR). Seçim oturumda saklanır; fiyatlar TCMB spot kurları (USD/EUR) ile güncellenebilir.</li>
        <li><strong>Dil seçici:</strong> Türkçe, İngilizce, İtalyanca (<span class="path">/locale/{tr|en|it}</span>).</li>
        <li><strong>Logo:</strong> Ana sayfaya döner.</li>
        <li><strong>Ürün arama:</strong> Yazarken öneriler getirir (API: <span class="path">/api/store/search-products</span>).</li>
        <li><strong>Sepet ikonu:</strong> Giriş zorunlu. Misafir tıklarsa girişe yönlendirilir.</li>
        <li><strong>Giriş / Bayi Ol / Hesabım / Admin:</strong> Oturum durumuna göre görünür.</li>
    </ul>
    <div class="box">
        Bayiye özel para birimi kısıtı varsa (panelde “Görünecek para birimleri”), yalnızca atanmış para birimleri listelenir.
        Şirket kâr marjı / indirim oranı tanımlanmışsa mağaza fiyatlarına yansır.
        <strong>Fiyatlar yalnızca giriş yapmış kullanıcılara gösterilir;</strong> misafir “Fiyatları görmek için giriş yapın” görür.
    </div>
    <div class="box warn">
        İlk ziyarette üretim modeli bilgilendirme modalı çıkar: ürünler klasik stoklu perakende modeli değildir; siparişe özel üretim yapılır.
    </div>

    <h2>A.2 Ana sayfa, arama ve ürünler</h2>
    <p><strong>Adres:</strong> <span class="path">/</span></p>
    <ul>
        <li>Banner carousel ve vitrin alanları (Banner Slaytlar; anasayfa ürün/kategori vitrinleri ürün/kategori ayarlarından).</li>
        <li>Kategori filtresi: <span class="path">/?category={slug}</span> (üst kategori ile <span class="path">parent</span>).</li>
        <li>Arama: üst arama kutusu veya <span class="path">?q=</span>; canlı öneri için en az 3 karakter.</li>
        <li>Sıralama: varsayılan, isim, fiyat, en yeni; sayfa başına 12/20/40/60.</li>
        <li>Diğer filtreler: stokta, satista / yakında, şirket (marka).</li>
        <li>Mobilde alt menü: Ürünler, Kategoriler, Sepet, Giriş/Hesap.</li>
    </ul>
    <table>
        <tr><th>Özellik</th><th>Açıklama</th></tr>
        <tr><td>Stokta yok / yakında</td><td>“Yakında gelecek” ürünler sepete eklenemez; satista + stok koşulları aranır.</td></tr>
        <tr><td>Varyasyon belirle</td><td>Ürün kartından detaya giderek seçenekleri tamamlamak gerekir.</td></tr>
        <tr><td>Footer</td><td>CMS menü grupları, şirket/vergi bilgisi, aktif banka listesi, Havale/EFT etiketi.</td></tr>
    </table>

    <h2>A.3 Ürün detayı, varyasyon ve özelleştirme</h2>
    <p><strong>Adres:</strong> <span class="path">/urun/{ürün-id}</span> (route ürün ID ile bağlanır).</p>
    <h3>İki sipariş modu</h3>
    <ul>
        <li><strong>Detaylı sipariş:</strong> bağımlı varyasyon adımları (kumaş, renk, etiket, ambalaj, sertifika, teslim, kalıp, beden dağılımı…), isteğe bağlı baskı/özelleştirme tablosu.</li>
        <li><strong>Hızlı sipariş:</strong> varyasyon adımları atlanır; not ve/veya görsel (max ~4 MB) zorunlu.</li>
    </ul>
    <h3>Bayi ne yapar?</h3>
    <ol>
        <li>Görselleri inceler (galeri / büyütme).</li>
        <li>Moduna göre varyasyon veya hızlı sipariş alanlarını doldurur.</li>
        <li>Beden dağılımı ve özelleştirme varsa ölçü / baskı tekniği / renk sayısını girer.</li>
        <li>Adet, min. sipariş ve fiyat özetini kontrol eder → <strong>Sepete Ekle</strong>.</li>
    </ol>
    <div class="box warn">
        Sepete ekleme için giriş zorunludur. Aynı ürün sepet anahtarı ürün ID’sidir; yeni seçim aynı satırı günceller (ayrı varyasyon satırı açmaz).
        Fiyat farkları (varyasyon / özelleştirme / beden çarpanı) toplama yansır.
    </div>

    <h2>A.4 Bayilik başvurusu ve giriş</h2>
    <h3>Bayi ol</h3>
    <p><strong>Adres:</strong> <span class="path">/bayi-ol</span> — form gönderimi: <span class="path">POST /bayilik-talebi</span></p>
    <p>5 adımlı sihirbaz:</p>
    <ol>
        <li><strong>İletişim:</strong> Ad, soyad, e-posta, telefon, şifre (onay sonrası bu şifre ile giriş).</li>
        <li><strong>İş adresi:</strong> Firma adresi; isteğe bağlı farklı teslimat adresi.</li>
        <li><strong>İşletme bilgisi:</strong> Tip, unvan, sicil, KDV, web / sosyal.</li>
        <li><strong>Tercihler:</strong> Müşteri tipi, ilgi alanları, bizi nereden duydunuz.</li>
        <li><strong>Şartlar:</strong> Sözleşme / gizlilik onayı; isteğe bağlı belge yükleme.</li>
    </ol>
    <div class="box">
        Başvuru sonrası durum <em>bekleyen</em>dir. Yönetici panelden <strong>Onayla</strong> dediğinde şirket + kullanıcı oluşturulur ve giriş onayı açılır (<span class="kbd">is_approved</span>).
        Onaylanmadan mağaza girişi yapılamaz (bekleyen başvuru + doğru şifre bile “incelemede” mesajı verir).
        Panelde başvuruyu <em>Reddet</em> butonu yoktur; operasyonel red süreci e-posta / kayıt dışı yönetilir.
    </div>

    <h3>Giriş / çıkış</h3>
    <ul>
        <li>Giriş: <span class="path">/giris</span></li>
        <li>Çıkış: <span class="path">POST /cikis</span></li>
        <li>Admin hesabı siteden giriş yapınca yönetim paneline (<span class="path">/admin</span>) yönlendirilebilir; bayi <span class="path">/hesabim</span> sayfasına gider.</li>
    </ul>

    <h2>A.5 Hesabım</h2>
    <p><strong>Adres:</strong> <span class="path">/hesabim</span> (auth gerekli)</p>
    <ul>
        <li>Profil özeti ve sipariş listesi (numara, tarih, durum, tutar).</li>
        <li>Her sipariş için onay sayfasına gitme, <strong>Proforma PDF</strong> ve <strong>Proforma Excel</strong> indirme.</li>
        <li>Sipariş durumları: Beklemede, Ödendi, İptal.</li>
    </ul>

    <h2>A.6 Sepet, ödeme ve sipariş</h2>
    <table>
        <tr><th>Adım</th><th>URL</th><th>İşlem</th></tr>
        <tr><td>Sepet</td><td><span class="path">/sepet</span></td><td>Kalemleri gör, adet güncelle, sil, özete bak.</td></tr>
        <tr><td>Ödeme</td><td><span class="path">/odeme</span></td><td>Teslimat / iletişim bilgileri, kargo yöntemi, banka seçimi, not.</td></tr>
        <tr><td>Sipariş oluştur</td><td><span class="path">POST /siparis-olustur</span></td><td>Sipariş kaydı; sepet temizlenir.</td></tr>
        <tr><td>Onay</td><td><span class="path">/siparis/{siparis-no}</span></td><td>Özet + havale bilgisi + proforma indirme.</td></tr>
    </table>
    <h3>Ödeme kuralları</h3>
    <ul>
        <li>Ödeme yöntemi: <strong>Havale / EFT</strong>.</li>
        <li>Aktif banka hesapları gösterilir; seçilen hesap siparişe bağlanır.</li>
        <li>Kargo yöntemi seçilir; ücret (veya ücretsiz) toplama eklenir.</li>
        <li>Sipariş numarası genelde <span class="path">SIP-YYYYMMDD…</span> formatındadır; havale açıklamasına yazılmalıdır.</li>
        <li>Onay sayfası yalnızca sipariş sahibi (veya admin) tarafından görülebilir.</li>
    </ul>
    <div class="box ok">
        Sipariş tamamlandıktan sonra bayi, havale dekontunu şirketin bildirdiği e-posta ile ileterek ödemeyi teyit eder.
        Yönetici panelde sipariş durumunu <em>Ödendi</em> veya <em>İptal</em> yapar.
    </div>

    <h2>A.7 Proforma fatura (PDF / Excel)</h2>
    <ul>
        <li>PDF: <span class="path">/siparis/{siparis-no}/proforma.pdf</span></li>
        <li>Excel: <span class="path">/siparis/{siparis-no}/proforma.xlsx</span></li>
    </ul>
    <p>Belgede yer alanlar (şablon uyumlu):</p>
    <ul>
        <li>Logo / kaşe, unvan, adres, PROFORMA INVOICE kutusu (tarih, fatura no PF-…, sipariş no, proje no).</li>
        <li>Bill To, Production Times, Delivery Type (EXW/FOB…).</li>
        <li>Kalemler, FOB PRICE, SHIPPING COST, TOTAL (KDV satırı eklenmez; toplam sipariş tutarıdır).</li>
        <li>Ödeme satırı: tutar rakam + yazı ile.</li>
        <li>Banka bilgileri (TL / EUR / USD IBAN alanları), iletişim footer.</li>
    </ul>
    <p>İndirme: sipariş onay sayfası, hesabım listesi ve admin sipariş detayından yapılabilir. Para birimi seçili mağaza para birimine göredir.</p>

    <h2>A.8 İletişim ve yasal sayfalar</h2>
    <ul>
        <li>İletişim: <span class="path">/iletisim</span> — form + harita; ek dosya yüklenebilir (sınırlı boyut/adet). Gönderim dakikada throttled.</li>
        <li>Yasal / bilgilendirme: <span class="path">/sozlesme/{slug}</span> (şartlar, ödeme koşulları, gizlilik vb. — panelde Sayfalar).</li>
    </ul>
</div>

{{-- BÖLÜM B --}}
<div class="part">
    <h1>Bölüm B — Yönetim Paneli</h1>
    <p><strong>Adres:</strong> <span class="path">https://…/admin</span> — yalnızca <span class="kbd">is_admin = true</span> kullanıcılar.</p>

    <h2>B.1 Giriş ve panoya genel bakış</h2>
    <ol>
        <li><span class="path">/admin</span> adresine gidin.</li>
        <li>Admin e-posta ve şifre ile giriş yapın.</li>
        <li>Sol menü grupları: <strong>E-Ticaret</strong>, <strong>Varyasyon yönetimi</strong>, <strong>B2B Yönetimi</strong>.</li>
    </ol>
    <h3>Dashboard (Özet Rapor)</h3>
    <ul>
        <li>Bayi sayısı, şirket sayısı, toplam sipariş (bekleyen rozeti), bu ay sipariş + tutar (₺).</li>
        <li>Bayilik talepleri (bekleyen vurgusu), mağazada listelenen ürün sayısı.</li>
        <li>Kartlar ilgili liste sayfalarına kısayoldur.</li>
    </ul>

    <h2>B.2 E-Ticaret menüsü</h2>

    <h3>Ürünler</h3>
    <ul>
        <li>Ürün Listesi / Yeni Ürün.</li>
        <li>Temel alanlar: ad (çok dilli), slug, açıklama, görseller, kategori, fiyat (₺ baz), stok / görünürlük / aktiflik.</li>
        <li>Varyasyon akışı: ürüne bağlanan renk, kumaş, beden tablosu, etiket, ambalaj, sertifika, kalıp, teslim şekli, özelleştirme satırları.</li>
        <li>Fiyat farkları ve boyut çarpanları ürün formundan yönetilir.</li>
    </ul>
    <div class="box warn">
        Mağazada görünmesi için ürünün aktif ve vitrine uygun olması gerekir. Varyasyon tanımları “Varyasyon yönetimi”nde hazırlanıp ürüne bağlanır.
    </div>

    <h3>Kategoriler</h3>
    <ul>
        <li>Hiyerarşik kategori ağacı (üst / alt).</li>
        <li>Slug, sıralama, görünürlük; mağaza yan menü ve filtrelerde kullanılır.</li>
    </ul>

    <h3>Vergi Yönetimi</h3>
    <ul>
        <li><strong>Vergi Sınıfları</strong> ve <strong>Vergi Oranları</strong> (yüzde veya sabit).</li>
        <li>Not: Proforma çıktısında KDV satırı şablona göre gösterilmez; sipariş toplamı değişmez.</li>
    </ul>

    <h3>Para Birimleri</h3>
    <ul>
        <li>Kod, sembol, kur, ondalık, varsayılan, aktif, sıra.</li>
        <li>USD/EUR için site TCMB kurunu kısa önbellekle birleştirebilir.</li>
    </ul>

    <h3>Kargo</h3>
    <ul>
        <li>Kargo yöntemleri: ad, ücret, aktiflik — checkout’ta seçilir.</li>
    </ul>

    <h3>Anasayfa Alanı / Arayüz Yönetimi</h3>
    <ul>
                <li><strong>Banner Slaytlar:</strong> hero / vitrin görselleri (çok dilli metin, buton, sıra, yayında).</li>
        <li><strong>Footer Menü:</strong> grup tipi menü / kategoriler / banka bilgisi; link repeater.</li>
        <li><strong>Footer Ayarları:</strong> sütun sayısı, marka alanı (tek kayıt; create kapalı).</li>
        <li><strong>Sayfalar:</strong> yasal içerikler — örn. <span class="path">/sozlesme/gizlilik-politikasi</span>, <span class="path">kullanim-kosullari</span>, <span class="path">odeme-kosullari</span>, <span class="path">teslimat-ve-kargo</span> vb.</li>
    </ul>
    <div class="box warn">
        İletişim formu mesajları için ayrı bir admin menüsü yoktur; mesajlar yapılandırılan e-postaya gider.
        “Anasayfa Alanları” resource’u kodda vardır fakat panelde şu an kapalıdır (<span class="kbd">canViewAny = false</span>).
    </div>

    <h2>B.3 Varyasyon yönetimi</h2>
    <p>Ürün formlarında kullanılan ortak kataloglar:</p>
    <table>
        <tr><th>Menü</th><th>Amaç</th></tr>
        <tr><td>Renk Varyasyonları</td><td>Renk seçenekleri ve etiketler</td></tr>
        <tr><td>Kumaş Türü Varyasyonları</td><td>Kumaş seçenekleri</td></tr>
        <tr><td>Beden tabloları</td><td>Beden / adet dağılımı şablonları</td></tr>
        <tr><td>Etiket Türü Yönetimi</td><td>Etiket tipi seçenekleri</td></tr>
        <tr><td>Ambalaj Tercih Yönetimi</td><td>Ambalaj tercihleri (özel sayfa)</td></tr>
        <tr><td>Sertifika Yönetimi</td><td>Sertifika seçenekleri</td></tr>
        <tr><td>Kalıp Modeli Yönetimi</td><td>Kalıp / model seçenekleri</td></tr>
        <tr><td>Teslim Şeklini Yönet</td><td>EXW, FOB, CIF, DAP, DDP vb.</td></tr>
        <tr><td>Ürün Özelleştirme</td><td>Özelleştirme matrisleri / boyut çarpanları</td></tr>
    </table>
    <div class="box">
        Önce varyasyon kataloğunu doldurun, sonra ürün kartında ilgili seçenekleri açın.
        Dil alanları (TR/EN/IT) mağaza diline göre etikete dönüşür.
    </div>

    <h2>B.4 B2B Yönetimi</h2>

    <h3>Siparişler</h3>
    <ul>
        <li>Liste: sipariş no, müşteri, tutar, durum, tarih. Bekleyen siparişlerde menü rozeti.</li>
        <li>Detay: müşteri bilgileri, kargo, banka, kalemler (ürün, birim, adet, ara toplam, varyasyon verisi).</li>
        <li>Durum güncelleme: Beklemede / Ödendi / İptal.</li>
        <li>Üst aksiyonlar: <strong>Proforma PDF</strong> ve <strong>Proforma Excel</strong> (yeni sekmede).</li>
    </ul>

    <h3>Bayi Listesi (Kullanıcılar)</h3>
    <ul>
        <li>Ad, e-posta, şirket, admin mi, giriş onayı.</li>
        <li>Şirket kâr marjı (% indirim), görünecek para birimleri, şifre belirleme/sıfırlama.</li>
        <li><span class="kbd">Giriş onayı</span> kapalıysa bayi mağazaya giremez.</li>
    </ul>

    <h3>Şirketler</h3>
    <ul>
        <li>Bayi firmaları: ad, kod, aktiflik, varsayılan kâr marjı.</li>
        <li>Kullanıcılar şirkete bağlanır; fiyatlandırma ve raporlama için temeldir.</li>
    </ul>

    <h3>Bayilik Talepleri</h3>
    <ul>
        <li>Bekleyen başvurularda rozet.</li>
        <li>Başvuru salt okunur incelenir; belgeler indirilebilir.</li>
        <li><strong>Onayla</strong> aksiyonu: kullanıcı + şirket kaydı / bağlantı, <span class="kbd">is_approved</span> açılır, talep “approved” olur.</li>
    </ul>

    <h3>Banka Bilgileri</h3>
    <ul>
        <li>Banka adı, şube, IBAN, hesap sahibi, para birimi (TRY/EUR/USD), sıra, aktif.</li>
        <li>Aktif hesaplar footer, checkout ve proformada kullanılır.</li>
        <li>Swift genel ayardan gelir (<span class="path">config/store.php</span> → <span class="kbd">KTEFTRISXXX</span> varsayılan).</li>
    </ul>

    <h2>B.5 Bayilik onay süreci (adım adım)</h2>
    <ol>
        <li>Aday site üzerinden <span class="path">/bayi-ol</span> formunu doldurur (status: pending).</li>
        <li>Yönetici: <strong>B2B Yönetimi → Bayilik Talepleri</strong> (bekleyen rozeti).</li>
        <li>Kaydı açar; iletişim, adres, işletme, tercihler, PDF/JPEG belgeler kontrol edilir.</li>
        <li><strong>Onayla</strong> (liste veya detay): e-posta ile kullanıcı yoksa Company (<span class="path">BAYI-…</span>) + User oluşturulur; başvuru şifresi varsa o kullanılır, yoksa rastgele şifre üretilip bildirimde gösterilir.</li>
        <li>Talep <em>approved</em> olur; <span class="kbd">is_approved = true</span>.</li>
        <li>İsteğe bağlı: Bayi Listesi’nden marj ve para birimi kısıtı ayarlanır; bayi <span class="path">/giris</span> ile girer.</li>
    </ol>

    <h2>B.6 Sipariş yönetimi ve proforma</h2>
    <ol>
        <li>Bayi siparişi tamamlar → durum genelde <em>pending</em>.</li>
        <li>Yönetici siparişi açar; kalemleri / notları / bankayı kontrol eder.</li>
        <li>Proforma PDF/Excel indirip müşteriye iletebilir (veya bayi kendi indirir).</li>
        <li>Havale gelince durumu <em>paid</em> yapar; iptalde <em>cancelled</em>.</li>
    </ol>

    <h2>B.7 Önerilen günlük / haftalık işler</h2>
    <table>
        <tr><th>Sıklık</th><th>İş</th></tr>
        <tr><td>Günlük</td><td>Bekleyen bayilik talepleri ve bekleyen siparişleri kontrol et; ödemeleri işaretle.</td></tr>
        <tr><td>Günlük</td><td>İletişim formundan gelen mesajları yanıtla (e-posta).</td></tr>
        <tr><td>Haftalık</td><td>Ürün / stok / fiyat ve kur (USD-EUR) doğrulaması.</td></tr>
        <tr><td>Haftalık</td><td>Banka IBAN’ları ve kargo ücretlerini güncelle.</td></tr>
        <tr><td>İhtiyaç</td><td>Banner, footer, yasal sayfa içerik güncellemesi.</td></tr>
    </table>
</div>

{{-- SSS --}}
<div class="part">
    <h2>4. Sık sorulan sorular ve sorun giderme</h2>
    <h3>Bayi giriş yapamıyor</h3>
    <ul>
        <li>Bayilik talebi onaylandı mı?</li>
        <li>Kullanıcıda “Giriş onayı” açık mı?</li>
        <li>E-posta / şifre doğru mu? (Şifre panelden sıfırlanabilir.)</li>
    </ul>
    <h3>Sepete ekleyemiyor / 401–yönlendirme</h3>
    <ul>
        <li>Oturum açık mı? Sepet ve ödeme auth ister.</li>
    </ul>
    <h3>Fiyatlar beklediği gibi değil</h3>
    <ul>
        <li>Seçili para birimi ve kur.</li>
        <li>Şirket / kullanıcı kâr marjı (indirim).</li>
        <li>Varyasyon ve özelleştirme fiyat farkları.</li>
    </ul>
    <h3>Proforma boş banka / yanlış IBAN</h3>
    <ul>
        <li>Banka Bilgileri’nde ilgili para biriminde aktif IBAN var mı?</li>
        <li>Aynı banka adı + hesap sahibi altında TRY/EUR/USD kayıtları gruplanır.</li>
    </ul>
    <h3>Admin paneline giremiyor</h3>
    <ul>
        <li>Kullanıcıda <span class="kbd">is_admin</span> işaretli olmalı.</li>
        <li>Adres <span class="path">/admin</span> olmalıdır (mağaza girişi değil).</li>
    </ul>
    <h3>Sayfa / menü görünmüyor</h3>
    <ul>
        <li>Footer Menü ve Sayfalar kayıtları aktif / doğru URL ile mi?</li>
        <li>Önbellek: sunucuda <span class="path">php artisan optimize:clear</span> gerekebilir.</li>
    </ul>

    <div class="footer-note">
        Bu kılavuz, NEVİSTANBUL B2B uygulamasının mevcut kod tabanına (mağaza rotaları + Filament admin paneli) göre hazırlanmıştır.
        Canlı ortam URL’si, SMTP ve banka bilgileri kurulumunuza göre değişir.
        Doküman tarihi: {{ $date }} — sürüm: {{ $version }}.
    </div>
</div>

</body>
</html>
