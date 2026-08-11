<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Catalog label translations (TR source → EN / IT display)
    | Keys must match stored admin/option values (case-insensitive match at runtime).
    |--------------------------------------------------------------------------
    */
    'labels' => [
        // Gender / audience
        'Erkek' => ['en' => 'Men', 'it' => 'Uomo'],
        'Kadın' => ['en' => 'Women', 'it' => 'Donna'],
        'Bayan' => ['en' => 'Women', 'it' => 'Donna'],
        'Unisex' => ['en' => 'Unisex', 'it' => 'Unisex'],
        'Çocuk' => ['en' => 'Kids', 'it' => 'Bambini'],
        'Çoçuk' => ['en' => 'Kids', 'it' => 'Bambini'],
        'Erkek/Unisex' => ['en' => 'Men/Unisex', 'it' => 'Uomo/Unisex'],
        'Erkek/Bayan' => ['en' => 'Men/Women', 'it' => 'Uomo/Donna'],

        // Size tables
        'Erkek/Unisex Beden Tablosu' => ['en' => 'Men/Unisex size chart', 'it' => 'Tabella taglie uomo/unisex'],
        'Erkek Beden Tablosu' => ['en' => 'Men size chart', 'it' => 'Tabella taglie uomo'],
        'Kadın Beden Tablosu' => ['en' => 'Women size chart', 'it' => 'Tabella taglie donna'],
        'Kadın Beden Seçiniz' => ['en' => 'Choose women sizes', 'it' => 'Scegli taglie donna'],
        'BEDEN TABLOSU (ERKEK)' => ['en' => 'SIZE CHART (MEN)', 'it' => 'TABELLA TAGLIE (UOMO)'],
        'BEDEN TABLOSU (ERKEK/UNISEX)' => ['en' => 'SIZE CHART (MEN/UNISEX)', 'it' => 'TABELLA TAGLIE (UOMO/UNISEX)'],
        'BEDEN - ÇOCUK' => ['en' => 'KIDS SIZES', 'it' => 'TAGLIE BAMBINI'],

        // Common variation names
        'Cinsiyet' => ['en' => 'Gender', 'it' => 'Genere'],
        'Cinsiyet Seçiniz' => ['en' => 'Select gender', 'it' => 'Seleziona genere'],
        'Renk' => ['en' => 'Color', 'it' => 'Colore'],
        'Beden' => ['en' => 'Size', 'it' => 'Taglia'],
        'Kumaş' => ['en' => 'Fabric', 'it' => 'Tessuto'],
        'Kumaş Türü' => ['en' => 'Fabric type', 'it' => 'Tipo di tessuto'],
        'Kalıp' => ['en' => 'Fit / mold', 'it' => 'Modello'],
        'Kalıp Model' => ['en' => 'Fit model', 'it' => 'Modello vestibilità'],
        'Kalıp / Model' => ['en' => 'Fit / Model', 'it' => 'Modello'],
        'Etiket' => ['en' => 'Label', 'it' => 'Etichetta'],
        'Etiket Tipi' => ['en' => 'Label type', 'it' => 'Tipo etichetta'],
        'Paketleme' => ['en' => 'Packaging', 'it' => 'Imballaggio'],
        'Paketleme Tipi' => ['en' => 'Packaging type', 'it' => 'Tipo di imballaggio'],
        'Teslimat' => ['en' => 'Delivery', 'it' => 'Consegna'],
        'Teslim Şekli' => ['en' => 'Delivery method', 'it' => 'Metodo di consegna'],
        'Sipariş Adeti' => ['en' => 'Order quantity', 'it' => 'Quantità ordine'],
        'Beden Seçiniz' => ['en' => 'Select size', 'it' => 'Seleziona taglia'],
        'Renk Seçiniz' => ['en' => 'Select color', 'it' => 'Seleziona colore'],

        // Print technique labels often stored in DB
        'Emprime' => ['en' => 'Screen print', 'it' => 'Serigrafia'],
        'Emprime Baskı' => ['en' => 'Screen print', 'it' => 'Serigrafia'],
        'Nakış' => ['en' => 'Embroidery', 'it' => 'Ricamo'],
        'DTF' => ['en' => 'DTF print', 'it' => 'Stampa DTF'],
        'DTF Baskı' => ['en' => 'DTF print', 'it' => 'Stampa DTF'],
        'Direkt Dijital' => ['en' => 'Direct digital print', 'it' => 'Stampa digitale diretta'],
        'Direkt Dijital Baskı' => ['en' => 'Direct digital print', 'it' => 'Stampa digitale diretta'],
        'Dijital Baskı' => ['en' => 'Digital print', 'it' => 'Stampa digitale'],
        'Serigrafi' => ['en' => 'Screen print', 'it' => 'Serigrafia'],

        // Print positions (common)
        'Sağ Ön Göğüs' => ['en' => 'Right front chest', 'it' => 'Petto anteriore destro'],
        'Sol Ön Göğüs' => ['en' => 'Left front chest', 'it' => 'Petto anteriore sinistro'],
        'Sağ Göğüs' => ['en' => 'Right chest', 'it' => 'Petto destro'],
        'Sol Göğüs' => ['en' => 'Left chest', 'it' => 'Petto sinistro'],
        'Ön Göğüs' => ['en' => 'Front chest', 'it' => 'Petto anteriore'],
        'Orta Göğüs' => ['en' => 'Center chest', 'it' => 'Petto centrale'],
        'Sırt' => ['en' => 'Back', 'it' => 'Schiena'],
        'Sırt Ortası' => ['en' => 'Center back', 'it' => 'Schiena centrale'],
        'Sağ Kol' => ['en' => 'Right sleeve', 'it' => 'Manica destra'],
        'Sol Kol' => ['en' => 'Left sleeve', 'it' => 'Manica sinistra'],
        'Ense' => ['en' => 'Nape / neck back', 'it' => 'Nuca'],
        'Boyun' => ['en' => 'Neck', 'it' => 'Collo'],

        // Generic UI catalog words
        'Evet' => ['en' => 'Yes', 'it' => 'Sì'],
        'Hayır' => ['en' => 'No', 'it' => 'No'],
        'Yok' => ['en' => 'None', 'it' => 'Nessuno'],
        'Standart' => ['en' => 'Standard', 'it' => 'Standard'],
        'Özel' => ['en' => 'Custom', 'it' => 'Personalizzato'],
        'Beyaz' => ['en' => 'White', 'it' => 'Bianco'],
        'Siyah' => ['en' => 'Black', 'it' => 'Nero'],
        'Lacivert' => ['en' => 'Navy', 'it' => 'Blu navy'],
        'Gri' => ['en' => 'Grey', 'it' => 'Grigio'],
        'Kırmızı' => ['en' => 'Red', 'it' => 'Rosso'],
        'Mavi' => ['en' => 'Blue', 'it' => 'Blu'],
        'Yeşil' => ['en' => 'Green', 'it' => 'Verde'],
        'Sarı' => ['en' => 'Yellow', 'it' => 'Giallo'],
        'Pembe' => ['en' => 'Pink', 'it' => 'Rosa'],
        'Mor' => ['en' => 'Purple', 'it' => 'Viola'],
        'Bordo' => ['en' => 'Burgundy', 'it' => 'Bordeaux'],
        'Bej' => ['en' => 'Beige', 'it' => 'Beige'],
        'Kahverengi' => ['en' => 'Brown', 'it' => 'Marrone'],
        'Turuncu' => ['en' => 'Orange', 'it' => 'Arancione'],
        'Antrasit' => ['en' => 'Anthracite', 'it' => 'Antracite'],
        'Camel' => ['en' => 'Camel', 'it' => 'Cammello'],
        'Eflatun' => ['en' => 'Lilac', 'it' => 'Lilla'],
        'Navy' => ['en' => 'Navy', 'it' => 'Blu navy'],
    ],
];
