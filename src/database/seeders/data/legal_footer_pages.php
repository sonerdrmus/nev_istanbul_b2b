<?php

/**
 * Footer Legal menu pages (TR titles stored on the page + footer item).
 *
 * @return list<array{
 *     slug: string,
 *     sort_order: int,
 *     footer_label: string,
 *     title: string,
 *     title_en: string,
 *     title_it: string,
 *     body: string,
 *     body_en: string,
 *     body_it: string,
 *     overwrite_body: bool
 * }>
 */
return [
    [
        'slug' => 'kullanim-kosullari',
        'sort_order' => 10,
        'footer_label' => 'Kullanım Koşulları',
        'title' => 'B2B Web Sitesi Kullanım Koşulları',
        'title_en' => 'Terms & Conditions',
        'title_it' => 'Termini e condizioni',
        'overwrite_body' => false,
        'body' => '',
        'body_en' => '',
        'body_it' => '',
    ],
    [
        'slug' => 'gizlilik-politikasi',
        'sort_order' => 20,
        'footer_label' => 'Gizlilik Politikası',
        'title' => 'Gizlilik Politikası',
        'title_en' => 'Privacy Policy',
        'title_it' => 'Informativa sulla privacy',
        'overwrite_body' => false,
        'body' => '',
        'body_en' => '',
        'body_it' => '',
    ],
    [
        'slug' => 'cerez-politikasi',
        'sort_order' => 30,
        'footer_label' => 'Çerez Politikası',
        'title' => 'Çerez Politikası',
        'title_en' => 'Cookie Policy',
        'title_it' => 'Informativa sui cookie',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>Çerez Politikası</h1>
<p>Bu Çerez Politikası, Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVİSTANBUL”) tarafından işletilen B2B internet sitesinde çerezlerin ve benzeri teknolojilerin nasıl kullanıldığını açıklar.</p>
<h2>1. Çerez nedir?</h2>
<p>Çerezler, bir web sitesini ziyaret ettiğinizde tarayıcınıza kaydedilen küçük metin dosyalarıdır. Oturumun sürdürülmesi, dil ve para birimi tercihlerinin hatırlanması ve sitenin güvenli çalışması için kullanılabilir.</p>
<h2>2. Kullandığımız çerez türleri</h2>
<ul>
<li><strong>Zorunlu çerezler:</strong> Giriş, sepet, dil ve para birimi gibi temel işlevler için gereklidir.</li>
<li><strong>İşlevsel çerezler:</strong> Tercihlerinizi hatırlar ve tekrar ziyaretlerinizi kolaylaştırır.</li>
<li><strong>Analiz çerezleri:</strong> Sitenin performansını ölçmek ve iyileştirmek için kullanılabilir.</li>
</ul>
<h2>3. Yönetim</h2>
<p>Tarayıcı ayarlarınızdan çerezleri silebilir veya engelleyebilirsiniz. Zorunlu çerezlerin kapatılması sitenin bazı bölümlerinin çalışmamasına yol açabilir.</p>
<h2>4. İletişim</h2>
<p>Sorularınız için: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>Cookie Policy</h1>
<p>This Cookie Policy explains how Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVISTANBUL”) uses cookies and similar technologies on this B2B website.</p>
<h2>1. What is a cookie?</h2>
<p>Cookies are small text files stored on your browser when you visit a website. They may be used to keep you signed in, remember language and currency preferences, and keep the site secure.</p>
<h2>2. Types of cookies we use</h2>
<ul>
<li><strong>Strictly necessary cookies:</strong> Required for core functions such as login, cart, language and currency.</li>
<li><strong>Functional cookies:</strong> Remember your preferences to make return visits easier.</li>
<li><strong>Analytics cookies:</strong> May be used to measure and improve site performance.</li>
</ul>
<h2>3. Managing cookies</h2>
<p>You can delete or block cookies in your browser settings. Disabling strictly necessary cookies may prevent some parts of the site from working.</p>
<h2>4. Contact</h2>
<p>Questions: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Informativa sui cookie</h1>
<p>Questa informativa spiega come Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVISTANBUL”) utilizza i cookie e tecnologie simili su questo sito B2B.</p>
<h2>1. Che cos’è un cookie?</h2>
<p>I cookie sono piccoli file di testo salvati nel browser quando visiti un sito. Possono servire a mantenere l’accesso, ricordare lingua e valuta e garantire la sicurezza del sito.</p>
<h2>2. Tipi di cookie utilizzati</h2>
<ul>
<li><strong>Cookie necessari:</strong> indispensabili per login, carrello, lingua e valuta.</li>
<li><strong>Cookie funzionali:</strong> memorizzano le preferenze per le visite successive.</li>
<li><strong>Cookie analitici:</strong> possono essere usati per misurare e migliorare le prestazioni del sito.</li>
</ul>
<h2>3. Gestione</h2>
<p>Puoi eliminare o bloccare i cookie dalle impostazioni del browser. La disattivazione dei cookie necessari può impedire il funzionamento di alcune sezioni.</p>
<h2>4. Contatti</h2>
<p>Domande: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
    ],
    [
        'slug' => 'b2b-satis-kosullari',
        'sort_order' => 40,
        'footer_label' => 'B2B Satış Koşulları',
        'title' => 'B2B Satış Koşulları',
        'title_en' => 'B2B Sales Terms',
        'title_it' => 'Termini di vendita B2B',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>B2B Satış Koşulları</h1>
<p>Bu satış koşulları, Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVİSTANBUL”) ile iş müşterileri (“Alıcı”) arasındaki mal satışına uygulanır. Web sitesi kullanım koşulları ayrıca geçerlidir.</p>
<h2>1. Teklif ve sipariş</h2>
<p>Katalog, fiyat listesi ve web sitesindeki bilgiler davet niteliğindedir. Sözleşme, Alıcı’nın siparişi ile NEVİSTANBUL’un proforma faturayı ve teknik detayları teyit etmesi üzerine kurulur.</p>
<h2>2. Ürünler</h2>
<p>Malların cinsi, miktarı, kumaş, renk, beden, logo ve baskı özellikleri onaylanan teknik detaylara göre belirlenir. Özel üretim ve private-label ürünlerde numune ile seri üretim arasında normal üretim toleransları olabilir.</p>
<h2>3. Fiyat ve ödeme</h2>
<p>Fiyatlar, aksi yazılı kararlaştırılmadıkça proforma faturada belirtilen para birimi ve koşullara tabidir. Ödeme, NEVİSTANBUL’un bildirdiği banka hesapları üzerinden yapılır. Ayrıntılar için <a href="/sozlesme/odeme-kosullari">Ödeme Koşulları</a> sayfasına bakınız.</p>
<h2>4. Teslimat ve iade</h2>
<p>Teslimat ve iade kuralları ilgili sayfalarda açıklanmıştır: <a href="/sozlesme/teslimat-ve-kargo">Teslimat ve Kargo</a>, <a href="/sozlesme/iade-ve-talepler">İade ve Talepler</a>.</p>
<h2>5. İletişim</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / İstanbul, Türkiye<br><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>B2B Sales Terms</h1>
<p>These sales terms apply to goods sold by Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVISTANBUL”) to business buyers (“Buyer”). The website terms and conditions also apply.</p>
<h2>1. Quotations and orders</h2>
<p>Catalogues, price lists and website information are invitations to treat. A contract is formed when the Buyer places an order and NEVISTANBUL confirms the proforma invoice and technical details.</p>
<h2>2. Goods</h2>
<p>Type, quantity, fabric, colour, size, logo and print specifications are as approved in the technical details. For custom and private-label goods, normal manufacturing tolerances may apply between sample and bulk production.</p>
<h2>3. Price and payment</h2>
<p>Prices follow the currency and terms in the proforma invoice unless otherwise agreed in writing. Payment is made to the bank accounts notified by NEVISTANBUL. See also <a href="/sozlesme/odeme-kosullari">Payment Terms</a>.</p>
<h2>4. Delivery and returns</h2>
<p>Delivery and returns are described here: <a href="/sozlesme/teslimat-ve-kargo">Delivery &amp; Shipping</a>, <a href="/sozlesme/iade-ve-talepler">Returns &amp; Claims</a>.</p>
<h2>5. Contact</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / Istanbul, Türkiye<br><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Termini di vendita B2B</h1>
<p>Questi termini si applicano alla vendita di beni da Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company (“NEVISTANBUL”) agli acquirenti professionali (“Acquirente”). Si applicano anche i termini del sito.</p>
<h2>1. Offerte e ordini</h2>
<p>Cataloghi, listini e informazioni sul sito sono inviti a offrire. Il contratto si forma quando l’Acquirente invia l’ordine e NEVISTANBUL conferma la fattura proforma e i dettagli tecnici.</p>
<h2>2. Merce</h2>
<p>Tipo, quantità, tessuto, colore, taglia, logo e stampa sono quelli approvati nei dettagli tecnici. Per prodotti su misura e private label possono valere le normali tolleranze di produzione tra campione e serie.</p>
<h2>3. Prezzo e pagamento</h2>
<p>I prezzi seguono la valuta e le condizioni della fattura proforma, salvo diverso accordo scritto. Il pagamento avviene sui conti comunicati da NEVISTANBUL. Vedi anche <a href="/sozlesme/odeme-kosullari">Termini di pagamento</a>.</p>
<h2>4. Consegna e resi</h2>
<p>Consegna e resi: <a href="/sozlesme/teslimat-ve-kargo">Consegna e spedizione</a>, <a href="/sozlesme/iade-ve-talepler">Resi e reclami</a>.</p>
<h2>5. Contatti</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / Istanbul, Türkiye<br><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
    ],
    [
        'slug' => 'teslimat-ve-kargo',
        'sort_order' => 50,
        'footer_label' => 'Teslimat ve Kargo',
        'title' => 'Teslimat ve Kargo',
        'title_en' => 'Delivery & Shipping',
        'title_it' => 'Consegna e spedizione',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>Teslimat ve Kargo</h1>
<p>NEVİSTANBUL, B2B siparişlerde Türkiye’den uluslararası teslimat düzenler. Aksi yazılı kararlaştırılmadıkça teslim yeri ve Incoterm, proforma faturada veya sipariş teyidinde belirtilir.</p>
<h2>1. Teslim süresi</h2>
<p>Üretim ve sevkiyat süreleri ürüne, özelleştirmeye ve onaylanan teknik detaylara göre değişir. Tahmini süreler sipariş teyidinde bildirilir ve bağlayıcı taahhüt değildir; mücbir sebep halleri ayrıca saklıdır.</p>
<h2>2. Teslim noktası</h2>
<p>Varsayılan teslim noktası, aksi kararlaştırılmadıkça NEVİSTANBUL’un tesisleri veya fatura/teslimat adresinizdir. Depodan teslim alma (EXW) veya DDP/DAP gibi koşullar sipariş bazında teyit edilir.</p>
<h2>3. Risk ve hasar</h2>
<p>Malların hasar ve kayıp riski, seçilen teslim koşuluna göre geçer. Teslimatta görünür hasar derhal tutanakla bildirilmelidir.</p>
<h2>4. Gümrük ve vergiler</h2>
<p>İthalat vergileri, gümrük ve yerel harçlar, aksi kararlaştırılmadıkça Alıcı’ya aittir. Gerekli ihracat belgelerini NEVİSTANBUL sağlar.</p>
<h2>5. İletişim</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>Delivery &amp; Shipping</h1>
<p>NEVISTANBUL arranges international B2B delivery from Türkiye. Unless agreed otherwise in writing, the delivery place and Incoterm are those stated on the proforma invoice or order confirmation.</p>
<h2>1. Lead times</h2>
<p>Production and dispatch times depend on the product, customisation and approved technical details. Indicative dates on the confirmation are not binding guarantees; force majeure is reserved.</p>
<h2>2. Delivery point</h2>
<p>Unless otherwise agreed, the default delivery point is NEVISTANBUL’s premises or your invoice/delivery address. EXW collection or DAP/DDP terms are confirmed per order.</p>
<h2>3. Risk and damage</h2>
<p>Risk of loss or damage passes according to the agreed delivery term. Visible damage on receipt must be recorded and notified immediately.</p>
<h2>4. Customs and taxes</h2>
<p>Unless otherwise agreed, import duties, customs and local charges are for the Buyer. NEVISTANBUL provides the required export documents.</p>
<h2>5. Contact</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Consegna e spedizione</h1>
<p>NEVISTANBUL organizza la consegna B2B internazionale dalla Türkiye. Salvo diverso accordo scritto, luogo di consegna e Incoterm sono quelli indicati nella fattura proforma o nella conferma d’ordine.</p>
<h2>1. Tempi</h2>
<p>Produzione e spedizione dipendono dal prodotto, dalla personalizzazione e dai dettagli tecnici approvati. Le date indicative non sono garanzie vincolanti; resta salva la forza maggiore.</p>
<h2>2. Punto di consegna</h2>
<p>Salvo diverso accordo, il punto di consegna è lo stabilimento NEVISTANBUL o l’indirizzo di fatturazione/consegna. Ritiro EXW o termini DAP/DDP sono confermati per ordine.</p>
<h2>3. Rischio e danni</h2>
<p>Il rischio di perdita o danno passa secondo il termine di consegna concordato. I danni visibili alla ricezione devono essere verbalizzati e comunicati subito.</p>
<h2>4. Dogana e imposte</h2>
<p>Salvo diverso accordo, dazi, dogana e oneri locali sono a carico dell’Acquirente. NEVISTANBUL fornisce i documenti di esportazione necessari.</p>
<h2>5. Contatti</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
    ],
    [
        'slug' => 'iade-ve-talepler',
        'sort_order' => 60,
        'footer_label' => 'İade ve Talepler',
        'title' => 'İade ve Talepler',
        'title_en' => 'Returns & Claims',
        'title_it' => 'Resi e reclami',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>İade ve Talepler</h1>
<p>NEVİSTANBUL B2B satışlarında ürünler büyük ölçüde siparişe özel üretilir. Bu nedenle standart tüketici iade hakkı B2B sözleşmelere uygulanmaz. Aşağıdaki kurallar kalite ve sevkiyat talepleri içindir.</p>
<h2>1. İnceleme yükümlülüğü</h2>
<p>Alıcı, malları teslimde miktar, ölçü, kumaş, renk, baskı ve işçilik açısından makul özenle incelemek zorundadır.</p>
<h2>2. Bildirim süresi</h2>
<p>Görünür ayıplar teslimden sonra mümkün olan en kısa sürede, gizli ayıplar ise fark edilir edilmez yazılı olarak bildirilmelidir. Makul süreyi aşan bildirimler reddedilebilir.</p>
<h2>3. İade edilemeyen mallar</h2>
<p>Özel üretim, baskılı, nakışlı, etiketli veya Alıcı spesifikasyonuna göre üretilmiş mallar; kullanılmış, yıkanmış veya Alıcı’nın müdahale ettiği mallar kural olarak iade alınmaz.</p>
<h2>4. Çözüm</h2>
<p>Haklı taleplerde NEVİSTANBUL, uygun gördüğü ölçüde onarım, değiştirme veya ilgili bedelin iadesi/mahsubu yollarından birini uygular.</p>
<h2>5. İletişim</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>Returns &amp; Claims</h1>
<p>NEVISTANBUL B2B goods are largely made to order. Consumer withdrawal rights therefore do not apply to these contracts. The rules below cover quality and shipment claims.</p>
<h2>1. Inspection</h2>
<p>The Buyer must inspect the goods on delivery with reasonable care for quantity, measurements, fabric, colour, print and workmanship.</p>
<h2>2. Notice</h2>
<p>Visible defects must be notified in writing as soon as possible after delivery; hidden defects as soon as they are discovered. Late notices may be rejected.</p>
<h2>3. Non-returnable goods</h2>
<p>Custom, printed, embroidered, labelled or specification-made goods, and goods that have been used, washed or altered by the Buyer, are not returnable as a rule.</p>
<h2>4. Remedy</h2>
<p>For a valid claim NEVISTANBUL may, at its option, repair, replace, or refund/credit the relevant amount.</p>
<h2>5. Contact</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Resi e reclami</h1>
<p>I prodotti B2B NEVISTANBUL sono in larga parte realizzati su ordine. Il diritto di recesso del consumatore non si applica. Le regole seguenti riguardano qualità e spedizione.</p>
<h2>1. Ispezione</h2>
<p>L’Acquirente deve ispezionare la merce alla consegna con diligenza ragionevole su quantità, misure, tessuto, colore, stampa e lavorazione.</p>
<h2>2. Comunicazione</h2>
<p>I vizi visibili vanno comunicati per iscritto il prima possibile dopo la consegna; i vizi occulti appena scoperti. Le comunicazioni tardive possono essere rifiutate.</p>
<h2>3. Merce non restituibile</h2>
<p>Prodotti su misura, stampati, ricamati, etichettati o realizzati su specifica, nonché merce usata, lavata o modificata dall’Acquirente, di regola non sono restituibili.</p>
<h2>4. Rimedi</h2>
<p>Per un reclamo fondato NEVISTANBUL può, a propria scelta, riparare, sostituire o rimborsare/accreditare l’importo pertinente.</p>
<h2>5. Contatti</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
    ],
    [
        'slug' => 'veri-koruma',
        'sort_order' => 70,
        'footer_label' => 'Veri Koruma',
        'title' => 'Veri Koruma',
        'title_en' => 'Data Protection',
        'title_it' => 'Protezione dei dati',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>Veri Koruma</h1>
<p>NEVİSTANBUL, kişisel verileri 6698 sayılı KVKK, GDPR ve uygulanabilir diğer mevzuata uygun işler. Ayrıntılı açıklama <a href="/sozlesme/gizlilik-politikasi">Gizlilik Politikası</a> metnindedir.</p>
<h2>1. Veri sorumlusu</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / İstanbul, Türkiye<br>Vergi Dairesi: Güneşli — VN: 6310675047</p>
<h2>2. İşleme amaçları</h2>
<p>Veriler; B2B hesap açılışı, sipariş, teslimat, faturalama, müşteri iletişimi, güvenlik ve yasal yükümlülükler için işlenir.</p>
<h2>3. Haklarınız</h2>
<p>Erişim, düzeltme, silme, itiraz ve KVKK/GDPR kapsamındaki diğer haklarınızı kullanmak için bize yazabilirsiniz.</p>
<h2>4. İletişim</h2>
<p>Gizlilik / veri koruma: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>Data Protection</h1>
<p>NEVISTANBUL processes personal data in line with Türkiye’s KVKK, the GDPR and other applicable laws. Full details are in the <a href="/sozlesme/gizlilik-politikasi">Privacy Policy</a>.</p>
<h2>1. Controller</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / Istanbul, Türkiye<br>Tax Office: Güneşli — Tax No: 6310675047</p>
<h2>2. Purposes</h2>
<p>Data is processed for B2B account setup, orders, delivery, invoicing, customer communication, security and legal obligations.</p>
<h2>3. Your rights</h2>
<p>You may request access, rectification, erasure, objection and other rights under KVKK/GDPR by contacting us.</p>
<h2>4. Contact</h2>
<p>Privacy / data protection: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Protezione dei dati</h1>
<p>NEVISTANBUL tratta i dati personali in conformità alla KVKK turca, al GDPR e alle altre norme applicabili. I dettagli sono nell’<a href="/sozlesme/gizlilik-politikasi">Informativa sulla privacy</a>.</p>
<h2>1. Titolare</h2>
<p>Nevistanbul Textile &amp; Promotion Industry and Trade Limited Company<br>15 Temmuz Mahallesi 1432 Sokak No:26-30, Bağcılar / Istanbul, Türkiye<br>Ufficio fiscale: Güneşli — P. IVA/tax: 6310675047</p>
<h2>2. Finalità</h2>
<p>I dati sono trattati per account B2B, ordini, consegna, fatturazione, comunicazione, sicurezza e obblighi di legge.</p>
<h2>3. Diritti</h2>
<p>Puoi chiedere accesso, rettifica, cancellazione, opposizione e gli altri diritti KVKK/GDPR scrivendoci.</p>
<h2>4. Contatti</h2>
<p>Privacy / protezione dati: <a href="mailto:privacy@nevistanbul.com.tr">privacy@nevistanbul.com.tr</a></p>
HTML,
    ],
    [
        'slug' => 'odeme-kosullari',
        'sort_order' => 80,
        'footer_label' => 'Ödeme Koşulları',
        'title' => 'Ödeme Koşulları',
        'title_en' => 'Payment Terms',
        'title_it' => 'Termini di pagamento',
        'overwrite_body' => true,
        'body' => <<<'HTML'
<h1>Ödeme Koşulları</h1>
<p>NEVİSTANBUL B2B siparişlerinde ödeme, aksi proforma faturada yazılmadıkça havale / EFT ile yapılır. Kabul edilen para birimleri sitedeki seçiciye ve faturaya göre TRY, USD veya EUR olabilir.</p>
<h2>1. Proforma ve avans</h2>
<p>Sipariş, proforma faturanın onaylanmasıyla kesinleşir. NEVİSTANBUL üretim veya sevkiyattan önce tam veya kısmi peşinat isteyebilir.</p>
<h2>2. Banka hesabı</h2>
<p>Ödeme yalnızca sitede ve faturada belirtilen şirket hesaplarına yapılmalıdır. Hesap bilgileri footer’daki banka bölümünde de yer alır.</p>
<h2>3. Gecikme</h2>
<p>Vadesi geçen ödemelerde NEVİSTANBUL sevkiyatı durdurabilir, siparişi iptal edebilir ve yasal faiz / masraf talep edebilir.</p>
<h2>4. Vergiler</h2>
<p>KDV ve benzeri vergiler, uygulanabilir mevzuat ve fatura koşullarına göre ayrıca gösterilir. İhracat faturalarında istisna/ muafiyet belgelere bağlıdır.</p>
<h2>5. İletişim</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_en' => <<<'HTML'
<h1>Payment Terms</h1>
<p>Unless the proforma invoice states otherwise, NEVISTANBUL B2B orders are paid by bank transfer. Accepted currencies may be TRY, USD or EUR according to the site selector and the invoice.</p>
<h2>1. Proforma and deposit</h2>
<p>The order is confirmed when the proforma invoice is approved. NEVISTANBUL may require full or part payment before production or shipment.</p>
<h2>2. Bank account</h2>
<p>Pay only to the company accounts shown on the invoice and this website. Bank details are also listed in the footer.</p>
<h2>3. Late payment</h2>
<p>If payment is overdue NEVISTANBUL may hold shipment, cancel the order and claim statutory interest and costs.</p>
<h2>4. Taxes</h2>
<p>VAT and similar taxes are shown according to applicable law and the invoice. Export exemptions depend on the documents provided.</p>
<h2>5. Contact</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
        'body_it' => <<<'HTML'
<h1>Termini di pagamento</h1>
<p>Salvo diversa indicazione nella fattura proforma, gli ordini B2B NEVISTANBUL si pagano tramite bonifico. Le valute accettate possono essere TRY, USD o EUR in base al selettore del sito e alla fattura.</p>
<h2>1. Proforma e acconto</h2>
<p>L’ordine è confermato con l’approvazione della fattura proforma. NEVISTANBUL può chiedere il pagamento totale o parziale prima della produzione o della spedizione.</p>
<h2>2. Conto bancario</h2>
<p>Pagare solo sui conti aziendali indicati in fattura e sul sito. I dati bancari sono anche nel footer.</p>
<h2>3. Ritardo</h2>
<p>In caso di ritardo NEVISTANBUL può sospendere la spedizione, annullare l’ordine e chiedere interessi e spese di legge.</p>
<h2>4. Imposte</h2>
<p>IVA e imposte analoghe sono indicate secondo la legge applicabile e la fattura. Le esenzioni all’esportazione dipendono dai documenti.</p>
<h2>5. Contatti</h2>
<p><a href="mailto:info@nevistanbul.com.tr">info@nevistanbul.com.tr</a></p>
HTML,
    ],
];
