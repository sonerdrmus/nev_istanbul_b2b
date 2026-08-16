<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->string('title_it')->nullable()->after('title_en');
            $table->string('headline_en')->nullable()->after('headline');
            $table->string('headline_it')->nullable()->after('headline_en');
            $table->text('description_en')->nullable()->after('description');
            $table->text('description_it')->nullable()->after('description_en');
            $table->string('button_text_en')->nullable()->after('button_text');
            $table->string('button_text_it')->nullable()->after('button_text_en');
        });

        $this->seedWelcomeSlideTranslations();
    }

    public function down(): void
    {
        Schema::table('banner_slides', function (Blueprint $table) {
            $table->dropColumn([
                'title_en',
                'title_it',
                'headline_en',
                'headline_it',
                'description_en',
                'description_it',
                'button_text_en',
                'button_text_it',
            ]);
        });
    }

    private function seedWelcomeSlideTranslations(): void
    {
        $slides = DB::table('banner_slides')->get();

        foreach ($slides as $slide) {
            $blob = mb_strtolower(trim((string) ($slide->title.' '.$slide->headline)));
            if (! str_contains($blob, 'hoşgeldiniz') && ! str_contains($blob, 'hayal edin')) {
                continue;
            }

            DB::table('banner_slides')->where('id', $slide->id)->update([
                'title' => 'NEVİSTANBUL-B2B YE HOŞGELDİNİZ.',
                'title_en' => 'WELCOME TO NEVISTANBUL B2B.',
                'title_it' => 'BENVENUTI SU NEVISTANBUL B2B.',
                'headline' => 'Hayal Edin, Tasarlayın, Siparişinizi Verin.',
                'headline_en' => 'Imagine, Design, Place Your Order.',
                'headline_it' => 'Immagina, Progetta, Effettua il Tuo Ordine.',
                'description' => 'Tamamen size özel üretim! Nevistanbul’da standart stok yok, sizin tercihleriniz var. Teknik detaylarını tamamen sizin belirlediğiniz, size özel üretilen ve sertifikalandırılan ürünlerle kusursuz müşteri memnuniyeti hedefliyoruz. Eleştiri ve önerilerinizle sürekli yenileniyor ve büyüyoruz. Haydi başlayalım.',
                'description_en' => 'Completely custom production for you. Nevistanbul has no standard stock — only your preferences. We aim for complete customer satisfaction with products manufactured and certified specifically for you, with every technical detail decided by you. We keep growing and improving through your feedback. Let’s get started.',
                'description_it' => 'Produzione completamente su misura per te. Da Nevistanbul non c’è stock standard, ci sono le tue scelte. Puntiamo alla piena soddisfazione con prodotti realizzati e certificati per te, con ogni dettaglio tecnico deciso da te. Cresciamo e ci rinnoviamo grazie ai tuoi commenti e suggerimenti. Iniziamo.',
                'updated_at' => now(),
            ]);
        }
    }
};
