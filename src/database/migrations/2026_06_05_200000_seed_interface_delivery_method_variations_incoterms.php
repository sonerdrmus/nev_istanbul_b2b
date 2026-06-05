<?php

use App\Models\InterfaceDeliveryMethodVariation;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('interface_delivery_method_variations')) {
            return;
        }

        $items = [
            [
                'name' => 'EXW',
                'description' => 'Ex Works — Satıcı malı kendi tesisinde hazır halde teslim eder; yükleme ve taşıma alıcıya aittir.',
                'price_multiplier' => 1.04,
                'sort_order' => 10,
            ],
            [
                'name' => 'FOB',
                'description' => 'Free on Board — Satıcı malı gemiye yükler; navlun ve sigorta alıcıya aittir.',
                'price_multiplier' => 1.12,
                'sort_order' => 20,
            ],
            [
                'name' => 'CIF',
                'description' => 'Cost, Insurance and Freight — Satıcı navlun ve sigortayı karşılar; varış limanına kadar.',
                'price_multiplier' => 1.08,
                'sort_order' => 30,
            ],
            [
                'name' => 'DAP',
                'description' => 'Delivered at Place — Satıcı malı belirlenen varış noktasına taşır; boşaltma alıcıya aittir.',
                'price_multiplier' => 1.21,
                'sort_order' => 40,
            ],
            [
                'name' => 'DDP',
                'description' => 'Delivered Duty Paid — Satıcı gümrük ve tüm masraflar dahil varış noktasına teslim eder.',
                'price_multiplier' => 1.17,
                'sort_order' => 50,
            ],
        ];

        foreach ($items as $item) {
            InterfaceDeliveryMethodVariation::query()->updateOrCreate(
                ['name' => $item['name']],
                [
                    'description' => $item['description'],
                    'image_path' => null,
                    'price_multiplier' => $item['price_multiplier'],
                    'sort_order' => $item['sort_order'],
                    'is_active' => true,
                ],
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_delivery_method_variations')) {
            return;
        }

        InterfaceDeliveryMethodVariation::query()
            ->whereIn('name', ['EXW', 'FOB', 'CIF', 'DAP', 'DDP'])
            ->delete();
    }
};
