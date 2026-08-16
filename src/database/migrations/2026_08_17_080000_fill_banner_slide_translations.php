<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $slides = DB::table('banner_slides')->get();

        foreach ($slides as $slide) {
            $blob = mb_strtolower(trim(implode(' ', [
                (string) $slide->title,
                (string) ($slide->title_en ?? ''),
                (string) ($slide->headline ?? ''),
                (string) ($slide->headline_en ?? ''),
                (string) ($slide->description ?? ''),
                (string) ($slide->description_en ?? ''),
            ])), 'UTF-8');

            $payload = null;

            if (
                str_contains($blob, '01')
                && (str_contains($blob, 'select') || str_contains($blob, 'seç') || str_contains($blob, 'scegli'))
            ) {
                $payload = [
                    'title' => 'NEVİSTANBUL-B2B’YE HOŞ GELDİNİZ.',
                    'title_en' => 'Welcome to NEVİSTANBUL-B2B.',
                    'title_it' => 'Benvenuti su NEVİSTANBUL-B2B.',
                    'headline' => 'Hayal edin, siparişinizi verin.',
                    'headline_en' => 'Imagine it - place your order.',
                    'headline_it' => 'Immaginalo e invia l’ordine.',
                    'description' => '01 — SEÇİN Ürününüzü seçin. 02 — ÖZELLEŞTİRİN Kumaş, renk, beden, logo, baskı ve özellikleri belirleyin. 03 — ÜRETİM Siparişinizi Türkiye’de üretiyoruz. 04 — TESLİMAT Uluslararası B2B teslimatı organize ediyoruz.',
                    'description_en' => '01 — SELECT Choose your product. 02 — CUSTOMIZE Choose fabric, colour, size, logo, printing and specifications. 03 — PRODUCE We manufacture your order in Türkiye. 04 — DELIVER We arrange international B2B delivery.',
                    'description_it' => '01 — SCEGLI Scegli il prodotto. 02 — PERSONALIZZA Tessuto, colore, taglia, logo, stampa e specifiche. 03 — PRODUZIONE Produciamo il tuo ordine in Türkiye. 04 — CONSEGNA Organizziamo la consegna B2B internazionale.',
                ];
            } elseif (
                str_contains($blob, 'hayal edin')
                || str_contains($blob, 'tamamen size özel')
                || str_contains($blob, 'completely custom')
            ) {
                $payload = [
                    'title' => 'NEVİSTANBUL-B2B’YE HOŞ GELDİNİZ.',
                    'title_en' => 'WELCOME TO NEVISTANBUL B2B.',
                    'title_it' => 'BENVENUTI SU NEVISTANBUL B2B.',
                    'headline' => 'Hayal Edin, Tasarlayın, Siparişinizi Verin.',
                    'headline_en' => 'Imagine, Design, Place Your Order.',
                    'headline_it' => 'Immagina, Progetta, Effettua il Tuo Ordine.',
                    'description' => 'Tamamen size özel üretim! Nevistanbul’da standart stok yok, sizin tercihleriniz var. Teknik detaylarını tamamen sizin belirlediğiniz, size özel üretilen ve sertifikalandırılan ürünlerle kusursuz müşteri memnuniyeti hedefliyoruz. Eleştiri ve önerilerinizle sürekli yenileniyor ve büyüyoruz. Haydi başlayalım.',
                    'description_en' => 'Completely custom production for you. Nevistanbul has no standard stock — only your preferences. We aim for complete customer satisfaction with products manufactured and certified specifically for you, with every technical detail decided by you. We keep growing and improving through your feedback. Let’s get started.',
                    'description_it' => 'Produzione completamente su misura per te. Da Nevistanbul non c’è stock standard, ci sono le tue scelte. Puntiamo alla piena soddisfazione con prodotti realizzati e certificati per te, con ogni dettaglio tecnico deciso da te. Cresciamo e ci rinnoviamo grazie ai tuoi commenti e suggerimenti. Iniziamo.',
                ];
            }

            if ($payload === null) {
                continue;
            }

            $payload['updated_at'] = now();
            DB::table('banner_slides')->where('id', $slide->id)->update($payload);
        }
    }

    public function down(): void
    {
        // Content seed only.
    }
};
