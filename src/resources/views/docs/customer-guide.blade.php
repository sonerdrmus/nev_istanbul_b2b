<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <title>NEVİSTANBUL B2B — Kullanım Rehberi</title>
    <style>
        @page { margin: 26px 28px; }
        * { box-sizing: border-box; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10.5px; color: #1e293b; line-height: 1.5; }
        h1 { font-size: 18px; color: #184E77; margin: 0 0 8px; }
        h2 { font-size: 13px; color: #184E77; margin: 16px 0 6px; border-bottom: 2px solid #184E77; padding-bottom: 3px; page-break-after: avoid; }
        h3 { font-size: 11.5px; color: #0f172a; margin: 12px 0 5px; page-break-after: avoid; }
        p { margin: 0 0 7px; }
        ul, ol { margin: 0 0 8px; padding-left: 16px; }
        li { margin-bottom: 3px; }
        .cover { text-align: center; padding: 70px 20px 40px; page-break-after: always; }
        .cover .brand { font-size: 28px; font-weight: bold; color: #184E77; }
        .cover .sub { font-size: 15px; margin-top: 12px; color: #475569; }
        .cover .meta { margin-top: 40px; color: #64748b; font-size: 9.5px; }
        .toc { page-break-after: always; }
        .box { background: #f8fafc; border: 1px solid #e2e8f0; border-left: 4px solid #184E77; padding: 8px 10px; margin: 8px 0 12px; }
        .tip { border-left-color: #059669; background: #ecfdf5; }
        .note { border-left-color: #d97706; background: #fffbeb; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0 12px; font-size: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 6px 7px; text-align: left; vertical-align: top; }
        th { background: #184E77; color: #fff; }
        tr:nth-child(even) td { background: #f8fafc; }
        .part { page-break-before: always; }
        .shot { margin: 8px 0 14px; page-break-inside: avoid; }
        .shot img { width: 100%; border: 1px solid #cbd5e1; }
        .cap { font-size: 9px; color: #64748b; margin-top: 4px; font-style: italic; }
        .footer-note { margin-top: 18px; font-size: 9px; color: #64748b; border-top: 1px solid #e2e8f0; padding-top: 6px; }
        .num { display: inline-block; width: 16px; height: 16px; line-height: 16px; text-align: center; background: #184E77; color: #fff; border-radius: 50%; font-size: 9px; font-weight: bold; margin-right: 3px; }
    </style>
</head>
<body>
@php
    $img = function (string $file) {
        $path = storage_path('app/docs/screens/pdf/'.$file);
        if (! is_file($path)) {
            $path = storage_path('app/docs/screens/'.$file);
        }
        if (! is_file($path)) {
            return null;
        }

        return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
    };
@endphp

<div class="cover">
    <div class="brand">NEVİSTANBUL</div>
    <div class="sub">B2B Sipariş Platformu<br>Kullanım Rehberi</div>
    <p style="margin-top:22px;color:#64748b;">Canlı site ekran görüntüleriyle — nevistanbul.sonerdurmus.com</p>
    <div class="meta">{{ $date }} · Müşteri kullanım dökümanı</div>
</div>

<div class="toc">
    <h1>İçindekiler</h1>
    <ol>
        <li>Platforma kısa bakış</li>
        <li>Bayiler için site kullanımı</li>
        <li>Yönetim paneli kullanımı</li>
        <li>Sipariş ve ödeme süreci</li>
        <li>Sık sorulan sorular</li>
    </ol>
</div>

<h2>1. Platforma kısa bakış</h2>
<p>Platform iki yüzeyden oluşur: bayilerin sipariş verdiği <strong>mağaza sitesi</strong> ve yönetimin her şeyi yönettiği <strong>yönetim paneli</strong>.</p>
<table>
    <tr><th>Kim?</th><th>Ne yapar?</th></tr>
    <tr><td>Ziyaretçi</td><td>Ürünleri gezer, bayilik başvurusu yapar. Fiyat görmez, sipariş veremez.</td></tr>
    <tr><td>Onaylı bayi</td><td>Fiyat görür, sepete ekler, sipariş verir, proforma indirir.</td></tr>
    <tr><td>Yönetici</td><td>Ürün, bayi, sipariş, banka ve site içeriklerini yönetir.</td></tr>
</table>
<div class="box tip">Ödeme havale / EFT ile yapılır. Sipariş sonrası proforma fatura PDF ve Excel olarak indirilebilir.</div>
<div class="box note">Ürünler siparişe özel üretilir; klasik “raftan stok” modeli değildir.</div>
<p>Canlı adres: <strong>https://nevistanbul.sonerdurmus.com</strong></p>

<div class="part">
    <h1>2. Bayiler için site kullanımı</h1>

    <h2>2.1 Ana sayfa</h2>
    <p>Üstte dil ve para birimi seçilir. Arama, kategoriler, banner ve ürün listesi burada yer alır.</p>
    @if($src = $img('01-ana-sayfa.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Ana sayfa (canlı site)</div></div>
    @endif

    <h2>2.2 Giriş</h2>
    <p>Onaylı bayi e-posta ve şifresiyle giriş yapar. Onay bekleyen başvurular giriş yapamaz.</p>
    @if($src = $img('02-giris.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Bayi giriş ekranı (canlı site)</div></div>
    @endif

    <h2>2.3 Bayilik başvurusu</h2>
    <p><strong>Kayıt Ol</strong> ile 5 adımlı form doldurulur. Yönetim onayından sonra belirlediğiniz şifre ile giriş açılır.</p>
    @if($src = $img('03-bayi-ol.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Bayilik başvurusu (canlı site)</div></div>
    @endif

    <h2>2.4 Ürün sayfası</h2>
    <p>Görseller, açıklama ve seçenekler burada. Detaylı veya hızlı sipariş seçilebilir. Fiyat ve sepete ekleme için giriş gerekir.</p>
    @if($src = $img('04-urun.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Ürün detayı (canlı site)</div></div>
    @endif

    <h2>2.5 Giriş sonrası: hesap, sepet, ödeme</h2>
    <ol>
        <li><strong>Hesabım:</strong> profil ve sipariş listesi; proforma PDF / Excel indirme</li>
        <li><strong>Sepet:</strong> ürünleri kontrol etme, adet güncelleme</li>
        <li><strong>Ödeme:</strong> adres, kargo, banka seçimi ve sipariş onayı</li>
    </ol>
    <div class="box tip">Bu sayfalar yalnızca giriş yapmış onaylı bayilere açıktır. Sipariş numarası havale açıklamasına yazılmalıdır.</div>

    <h2>2.6 İletişim</h2>
    <p>İletişim formundan mesaj ve dosya gönderilebilir.</p>
    @if($src = $img('05-iletisim.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">İletişim sayfası (canlı site)</div></div>
    @endif
</div>

<div class="part">
    <h1>3. Yönetim paneli kullanımı</h1>
    <p>Yalnızca yönetici hesapları girer. Sol menüden ürün, bayi ve sipariş işlemleri yapılır.</p>

    <h2>3.1 Panel özeti</h2>
    @if($src = $img('09-admin-panel.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Yönetim paneli özet ekranı (canlı site)</div></div>
    @endif

    <h2>3.2 Ürünler</h2>
    @if($src = $img('12-admin-urunler.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Ürün listesi (canlı site)</div></div>
    @endif

    <h2>3.3 Siparişler</h2>
    @if($src = $img('10-admin-siparisler.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Sipariş listesi (canlı site)</div></div>
    @endif

    <h2>3.4 Bayilik talepleri</h2>
    <p>Bekleyen başvuruları inceleyip <strong>Onayla</strong> ile hesabı açın.</p>
    @if($src = $img('11-admin-bayi-talepleri.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Bayilik talepleri (canlı site)</div></div>
    @endif

    <h2>3.5 Banka bilgileri</h2>
    @if($src = $img('13-admin-banka.png'))
    <div class="shot"><img src="{{ $src }}"><div class="cap">Banka bilgileri (canlı site)</div></div>
    @endif

    <h3>Düzenli kontroller</h3>
    <table>
        <tr><th>Ne sıklıkla?</th><th>Ne yapılmalı?</th></tr>
        <tr><td>Her gün</td><td>Bekleyen başvurular ve yeni siparişler</td></tr>
        <tr><td>Her gün</td><td>İletişim formundan gelen mesajlar</td></tr>
        <tr><td>Haftada bir</td><td>Fiyat, kur ve kargo ücretleri</td></tr>
    </table>
</div>

<div class="part">
    <h1>4. Sipariş ve ödeme süreci</h1>
    <ol>
        <li><span class="num">1</span> Firma bayilik başvurusu yapar</li>
        <li><span class="num">2</span> Yönetim onaylar</li>
        <li><span class="num">3</span> Bayi giriş yapıp ürün seçer</li>
        <li><span class="num">4</span> Siparişi tamamlar ve proforma indirir</li>
        <li><span class="num">5</span> Sipariş numarası ile havale yapar</li>
        <li><span class="num">6</span> Yönetim ödemeyi işaretler ve süreci yürütür</li>
    </ol>
    <div class="box tip">Proforma ön bilgilendirme belgesidir; resmi e-fatura yerine geçmez.</div>

    <h1>5. Sık sorulan sorular</h1>
    <h3>Giriş yapamıyorum</h3>
    <p>Başvuru onaylandı mı? E-posta / şifre doğru mu?</p>
    <h3>Fiyatlar görünmüyor</h3>
    <p>Fiyatlar yalnızca giriş yapmış onaylı bayilere açıktır.</p>
    <h3>Proforma resmi fatura mı?</h3>
    <p>Hayır — ödeme ve sipariş teyidi içindir.</p>

    <div class="footer-note">
        Bu rehber NEVİSTANBUL B2B platformunun müşteri kullanımı içindir.
        Ekran görüntüleri canlı siteden alınmıştır: https://nevistanbul.sonerdurmus.com
        Tarih: {{ $date }}
    </div>
</div>
</body>
</html>
