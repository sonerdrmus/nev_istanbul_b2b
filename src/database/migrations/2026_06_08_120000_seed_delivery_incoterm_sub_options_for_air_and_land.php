<?php

use App\Models\InterfaceDeliveryMethodSubOption;
use App\Models\InterfaceDeliveryMethodVariation;
use App\Support\ProductVariationOptionInterfaceSync;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var array<int, array{name: string, description: string, price_multiplier: float, sort_order: int, is_default: bool}> */
    private const INCOTERMS = [
        [
            'name' => 'EXW',
            'description' => 'Ex Works — Satıcı malı kendi tesisinde hazır halde teslim eder; yükleme ve taşıma alıcıya aittir.',
            'price_multiplier' => 1.04,
            'sort_order' => 10,
            'is_default' => true,
        ],
        [
            'name' => 'FOB',
            'description' => 'Free on Board — Satıcı malı gemiye yükler; navlun ve sigorta alıcıya aittir.',
            'price_multiplier' => 1.12,
            'sort_order' => 20,
            'is_default' => false,
        ],
        [
            'name' => 'CIF',
            'description' => 'Cost, Insurance and Freight — Satıcı navlun ve sigortayı karşılar; varış limanına kadar.',
            'price_multiplier' => 1.08,
            'sort_order' => 30,
            'is_default' => false,
        ],
        [
            'name' => 'DAP',
            'description' => 'Delivered at Place — Satıcı malı belirlenen varış noktasına taşır; boşaltma alıcıya aittir.',
            'price_multiplier' => 1.21,
            'sort_order' => 40,
            'is_default' => false,
        ],
        [
            'name' => 'DDP',
            'description' => 'Delivered Duty Paid — Satıcı gümrük ve tüm masraflar dahil varış noktasına teslim eder.',
            'price_multiplier' => 1.17,
            'sort_order' => 50,
            'is_default' => false,
        ],
    ];

    public function up(): void
    {
        if (! Schema::hasTable('interface_delivery_method_sub_options')) {
            return;
        }

        if (! Schema::hasColumn('interface_delivery_method_sub_options', 'price_multiplier')) {
            Schema::table('interface_delivery_method_sub_options', function (Blueprint $table) {
                $table->decimal('price_multiplier', 12, 4)->default(1)->after('description');
            });
        }

        InterfaceDeliveryMethodVariation::query()
            ->whereIn('name', array_column(self::INCOTERMS, 'name'))
            ->update(['is_active' => false]);

        $transportMethods = InterfaceDeliveryMethodVariation::query()
            ->whereIn('name', ['Uçak', 'Kara Yolu'])
            ->where('is_active', true)
            ->get();

        foreach ($transportMethods as $transport) {
            foreach (self::INCOTERMS as $incoterm) {
                InterfaceDeliveryMethodSubOption::query()->updateOrCreate(
                    [
                        'interface_delivery_method_variation_id' => $transport->id,
                        'name' => $incoterm['name'],
                    ],
                    [
                        'description' => $incoterm['description'],
                        'price_multiplier' => $incoterm['price_multiplier'],
                        'sort_order' => $incoterm['sort_order'],
                        'is_default' => $incoterm['is_default'],
                        'is_active' => true,
                    ]
                );
            }
        }

        ProductVariationOptionInterfaceSync::removeInactivePresetProductOptions('delivery_type');
    }

    public function down(): void
    {
        if (! Schema::hasTable('interface_delivery_method_sub_options')) {
            return;
        }

        $transportIds = InterfaceDeliveryMethodVariation::query()
            ->whereIn('name', ['Uçak', 'Kara Yolu'])
            ->pluck('id');

        InterfaceDeliveryMethodSubOption::query()
            ->whereIn('interface_delivery_method_variation_id', $transportIds)
            ->whereIn('name', array_column(self::INCOTERMS, 'name'))
            ->delete();

        InterfaceDeliveryMethodVariation::query()
            ->whereIn('name', array_column(self::INCOTERMS, 'name'))
            ->update(['is_active' => true]);

        if (Schema::hasColumn('interface_delivery_method_sub_options', 'price_multiplier')) {
            Schema::table('interface_delivery_method_sub_options', function (Blueprint $table) {
                $table->dropColumn('price_multiplier');
            });
        }
    }
};
