<?php

use PhpOffice\PhpPresentation\PhpPresentation;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Style\Alignment;
use PhpOffice\PhpPresentation\Style\Color;
use PhpOffice\PhpPresentation\Style\Fill;

require __DIR__.'/../vendor/autoload.php';

$navy = 'FF184E77';
$slate = 'FF334155';
$muted = 'FF64748B';
$white = 'FFFFFFFF';
$light = 'FFF1F5F9';

$presentation = new PhpPresentation();
$presentation->removeSlideByIndex(0);

$addTitleSlide = function (string $title, string $subtitle = '') use ($presentation, $navy, $muted, $white) {
    $slide = $presentation->createSlide();
    $slide->setBackground($bg = new \PhpOffice\PhpPresentation\Slide\Background\Color());
    $bg->setColor(new Color($navy));

    $shape = $slide->createRichTextShape()
        ->setHeight(120)->setWidth(860)->setOffsetX(50)->setOffsetY(220);
    $shape->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $run = $shape->createTextRun($title);
    $run->getFont()->setBold(true)->setSize(32)->setColor(new Color($white));

    if ($subtitle !== '') {
        $shape2 = $slide->createRichTextShape()
            ->setHeight(80)->setWidth(860)->setOffsetX(50)->setOffsetY(350);
        $shape2->getActiveParagraph()->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $run2 = $shape2->createTextRun($subtitle);
        $run2->getFont()->setSize(16)->setColor(new Color('FFCBD5E1'));
    }

    return $slide;
};

$addContentSlide = function (string $title, array $bullets, ?string $note = null) use ($presentation, $navy, $slate, $muted, $light) {
    $slide = $presentation->createSlide();

    $bar = $slide->createRichTextShape()
        ->setHeight(70)->setWidth(960)->setOffsetX(0)->setOffsetY(0);
    $bar->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($navy));
    $bar->getActiveParagraph()->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
    $barTitle = $bar->createTextRun('  '.$title);
    $barTitle->getFont()->setBold(true)->setSize(20)->setColor(new Color('FFFFFFFF'));

    $body = $slide->createRichTextShape()
        ->setHeight(400)->setWidth(860)->setOffsetX(50)->setOffsetY(100);
    foreach ($bullets as $i => $line) {
        if ($i > 0) {
            $body->createBreak();
        }
        $p = $i === 0 ? $body->getActiveParagraph() : $body->createParagraph();
        $run = $p->createTextRun('•  '.$line);
        $run->getFont()->setSize(16)->setColor(new Color($slate));
    }

    if ($note) {
        $noteBox = $slide->createRichTextShape()
            ->setHeight(70)->setWidth(860)->setOffsetX(50)->setOffsetY(520);
        $noteBox->getFill()->setFillType(Fill::FILL_SOLID)->setStartColor(new Color($light));
        $n = $noteBox->createTextRun($note);
        $n->getFont()->setSize(13)->setItalic(true)->setColor(new Color($muted));
    }

    return $slide;
};

$addTitleSlide('NEVİSTANBUL B2B', "Sipariş Platformu\nKullanım Sunumu");
$addContentSlide('İçindekiler', [
    'Platforma kısa bakış',
    'Bayiler için site kullanımı',
    'Yönetim paneli kullanımı',
    'Sipariş ve ödeme süreci',
    'Sık sorulan sorular',
]);

$addContentSlide('1. Platforma kısa bakış', [
    'Mağaza sitesi: bayilerin ürün görüp sipariş verdiği alan',
    'Yönetim paneli: ürün, bayi ve siparişlerin yönetildiği alan',
    'Ziyaretçi gezebilir; fiyat ve sipariş için onaylı bayi girişi gerekir',
    'Ödeme yöntemi: havale / EFT',
    'Sipariş sonrası proforma fatura (PDF ve Excel) indirilebilir',
], 'Ürünler siparişe özel üretilir; klasik raftan stok modeli değildir.');

$addContentSlide('Kim ne yapar?', [
    'Ziyaretçi: ürünleri gezer, bayilik başvurusu yapar',
    'Onaylı bayi: fiyat görür, sepete ekler, sipariş verir, proforma indirir',
    'Yönetici: ürün, bayi, sipariş, banka ve site içeriklerini yönetir',
]);

$addTitleSlide('Bayiler için site', 'Başvurudan siparişe kadar');

$addContentSlide('Dil, para birimi ve gezinme', [
    'Üstten dil seçilir: Türkçe, İngilizce, İtalyanca',
    'Para birimi seçilir (ör. TL, Euro, Dolar)',
    'Arama kutusu ve kategorilerle ürün bulunur',
    'Ana sayfada vitrin, banner ve ürün listesi yer alır',
], 'Bazı bayilere yalnızca belirli para birimleri açılmış olabilir.');

$addContentSlide('Bayilik başvurusu', [
    'Kayıt Ol / Bayi Ol sayfasından başvuru yapılır',
    '5 adım: iletişim, adres, işletme, tercihler, şartlar',
    'Başvuruda belirlenen şifre, onaydan sonra giriş için kullanılır',
    'Yönetim başvuruyu inceler ve onaylar',
    'Onaylanmadan siteye giriş yapılamaz',
]);

$addContentSlide('Giriş ve hesabım', [
    'Giriş sayfasından e-posta ve şifre ile oturum açılır',
    'Hesabım sayfasında profil ve sipariş listesi görülür',
    'Her sipariş için proforma PDF ve Excel indirilebilir',
    'Çıkış ile oturum kapatılır',
]);

$addContentSlide('Ürün seçimi ve sepet', [
    'Ürün detayında görseller ve açıklama incelenir',
    'Detaylı sipariş: renk, beden, ambalaj, baskı gibi seçenekler doldurulur',
    'Hızlı sipariş: not ve/veya görsel ile kısa talep oluşturulur',
    'Sepete eklenir; sepette adet güncellenebilir veya ürün silinebilir',
], 'Fiyat görmek ve sepete eklemek için giriş zorunludur.');

$addContentSlide('Siparişi tamamlama', [
    'Sepetten Ödeme sayfasına geçilir',
    'İletişim ve adres bilgileri girilir',
    'Kargo yöntemi ve havale bankası seçilir',
    'Sipariş onaylanır; sipariş numarası alınır',
    'Havale açıklamasına sipariş numarası yazılır',
    'Proforma PDF veya Excel indirilir',
]);

$addContentSlide('Proforma fatura nedir?', [
    'Sipariş kalemlerini, tutarı ve banka bilgilerini gösteren ön belgedir',
    'PDF: yazdırma ve paylaşma için',
    'Excel: muhasebe / iç kayıt için',
    'Resmi e-fatura yerine geçmez',
]);

$addTitleSlide('Yönetim paneli', 'Ürün, bayi ve sipariş yönetimi');

$addContentSlide('Panele giriş ve özet ekran', [
    'Yalnızca yönetici hesapları girebilir',
    'Özet kartlarda bayi, şirket, sipariş ve başvuru sayıları görülür',
    'Bekleyen başvurular ve siparişler vurgulanır',
    'Sol menü: E-Ticaret, Varyasyon yönetimi, B2B Yönetimi',
]);

$addContentSlide('Ürün, kategori ve fiyat', [
    'Ürün ekleme / düzenleme, görsel ve durum yönetimi',
    'Kategorilerle ürünler gruplanır',
    'Fiyatlar panelde yönetilir; sitede seçilen para biriminde gösterilir',
    'Kargo yöntemleri ve ücretleri tanımlanır',
    'Bayiye veya şirkete indirim oranı verilebilir',
]);

$addContentSlide('Site görünümü', [
    'Banner slaytlar: ana sayfa görselleri',
    'Footer menü ve ayarlar: alt linkler',
    'Sayfalar: gizlilik, ödeme koşulları, teslimat metinleri',
]);

$addContentSlide('Bayilik onay süreci', [
    'Bayi Talepleri menüsünden bekleyenler incelenir',
    'Bilgiler ve belgeler kontrol edilir',
    'Onayla ile firma ve kullanıcı hesabı açılır',
    'İstenirse indirim oranı veya para birimi kısıtı ayarlanır',
    'Bayi, başvuru şifresiyle siteye giriş yapar',
]);

$addContentSlide('Sipariş ve banka yönetimi', [
    'Siparişler listesinde yeni talepler takip edilir',
    'Detayda kalemler, kargo ve banka bilgisi görülür',
    'Havale gelince durum Ödendi yapılır; iptalde İptal',
    'Proforma belge yönetim panelinden de indirilebilir',
    'Banka bilgileri (IBAN, banka, para birimi) aktif tutulur',
]);

$addContentSlide('Düzenli kontroller', [
    'Her gün: bekleyen başvurular ve yeni siparişler',
    'Her gün: iletişim formundan gelen mesajlar',
    'Haftada bir: fiyat, kur ve kargo ücretleri',
    'Gerektiğinde: banner, menü ve sözleşme metinleri',
]);

$addTitleSlide('Uçtan uca süreç', 'Başvurudan ödemeye');

$addContentSlide('Sipariş akışı', [
    '1) Firma bayilik başvurusu yapar',
    '2) Yönetim onaylar',
    '3) Bayi giriş yapıp ürün seçer',
    '4) Siparişi tamamlar ve proforma indirir',
    '5) Sipariş numarası ile havale yapar',
    '6) Yönetim ödemeyi işaretler ve süreci yürütür',
]);

$addContentSlide('Sık sorulan sorular', [
    'Giriş yapılamıyor: başvuru onayı ve şifre kontrol edilmeli',
    'Fiyat görünmüyor: giriş yapmış onaylı bayi olunmalı',
    'Sepete eklenemiyor: giriş, ürün durumu ve zorunlu seçenekler',
    'Hangi bankaya ödeme?: onay sayfası ve proformadaki hesap',
    'Proforma resmi fatura mı?: hayır, ön bilgilendirme belgesidir',
]);

$addTitleSlide('Teşekkürler', "NEVİSTANBUL B2B\nMüşteri kullanım sunumu — ".date('d.m.Y'));

$out = __DIR__.'/../storage/app/docs/NEVISTANBUL-B2B-Musteri-Kullanim-Sunumu.pptx';
@mkdir(dirname($out), 0775, true);
$writer = IOFactory::createWriter($presentation, 'PowerPoint2007');
$writer->save($out);

echo $out.PHP_EOL;
echo 'slides='.$presentation->getSlideCount().PHP_EOL;
