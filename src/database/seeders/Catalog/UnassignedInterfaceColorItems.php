<?php

namespace Database\Seeders\Catalog;

/**
 * Pantone / kumaş tablosundan gelen hex renkler — Grup atanmamış (kumaş türü yok).
 *
 * Sıra: tabloda soldan sağa, yukarıdan aşağı; aynı hex tekrarlanmaz.
 */
final class UnassignedInterfaceColorItems
{
    /** @var array<string, string> hex (uppercase #RRGGBB) => Türkçe ad */
    private const DISPLAY_NAMES = [
        '#FFFFFF' => 'Beyaz',
        '#F2EFE6' => 'Kırık Beyaz',
        '#F2F2F2' => 'Açık Gri',
        '#F3EAD3' => 'Krema',
        '#F5E6C8' => 'Vanilya',
        '#F6C9B2' => 'Kum',
        '#E6D7B8' => 'Bej',
        '#6B3E26' => 'Kahverengi',
        '#CBB89C' => 'Açık Kahve',
        '#FFB800' => 'Sarı',
        '#D08B45' => 'Bronz',
        '#FFC1E3' => 'Bebek Pembesi',
        '#A56B3F' => 'Tarçın',
        '#FF69B4' => 'Pembe',
        '#7A4F2E' => 'Koyu Kahve',
        '#E60026' => 'Kırmızı',
        '#4A2E1E' => 'Espresso',
        '#FF6A00' => 'Turuncu',
        '#6B8E23' => 'Zeytin Yeşili',
        '#6A3D9A' => 'Mor',
        '#B2CDA0' => 'Açık Yeşil',
        '#1B5E20' => 'Orman Yeşili',
        '#A8E6CF' => 'Nane Yeşili',
        '#008080' => 'Teal',
        '#8FD6D1' => 'Turkuaz',
        '#1F3B2D' => 'Koyu Yeşil',
        '#ADD8E6' => 'Açık Mavi',
        '#87CEEB' => 'Gök Mavisi',
        '#0D47A1' => 'Koyu Mavi',
        '#0033A0' => 'Kraliyet Mavisi',
        '#0D1B3D' => 'Lacivert',
        '#001F3F' => 'Gece Mavisi',
        '#1C3D4D' => 'Petrol Mavisi',
        '#001A33' => 'Gece Laciverti',
        '#3C5A6E' => 'Çelik Mavisi',
        '#EADBC8' => 'Doğal',
        '#FFD1DC' => 'Pastel Pembe',
        '#B0B0B0' => 'Gri',
        '#FFC0CB' => 'Açık Pembe',
        '#808080' => 'Orta Gri',
        '#FF77B4' => 'Canlı Pembe',
        '#4A4A4A' => 'Koyu Gri',
        '#FF1493' => 'Fuşya',
        '#000000' => 'Siyah',
        '#E6E6FA' => 'Lavanta',
        '#800080' => 'Eflatun',
        '#4B0082' => 'İndigo',
        '#722F37' => 'Bordo',
        '#FF0000' => 'Parlak Kırmızı',
        '#F8C000' => 'Altın Sarı',
        '#D4A017' => 'Altın',
        '#FFD700' => 'Sarı Altın',
        '#C3B091' => 'Haki',
        '#808000' => 'Zeytin',
        '#556B2F' => 'Koyu Zeytin',
        '#009B77' => 'Zümrüt Yeşili',
        '#D3D3D3' => 'Gümüş Gri',
        '#A9A9A9' => 'Kül Rengi',
        '#36454F' => 'Kömür Gri',
        '#000080' => 'Deniz Laciverti',
    ];

    /**
     * @return list<string>
     */
    public static function hexCodesInOrder(): array
    {
        $rows = [
            ['#FFFFFF', '#FFFFFF', '#FFFFFF', '#FFFFFF'],
            ['#F2EFE6', '#F2F2F2', '#F2EFE6', '#F2EFE6'],
            ['#F3EAD3', '#F5E6C8', '#F3EAD3', '#F3EAD3'],
            ['#F6C9B2', '#E6D7B8', '#F6C9B2', '#F6C9B2'],
            ['#6B3E26', '#CBB89C', '#6B3E26', '#6B3E26'],
            ['#FFB800', '#D08B45', '#FFB800', '#FFB800'],
            ['#FFC1E3', '#A56B3F', '#FFC1E3', '#FFC1E3'],
            ['#FF69B4', '#7A4F2E', '#FF69B4', '#FF69B4'],
            ['#E60026', '#4A2E1E', '#E60026', '#E60026'],
            ['#FF6A00', '#6B8E23', '#FF6A00', '#FF6A00'],
            ['#6A3D9A', '#B2CDA0', '#6A3D9A', '#6A3D9A'],
            ['#1B5E20', '#A8E6CF', '#1B5E20', '#1B5E20'],
            ['#008080', '#8FD6D1', '#008080', '#008080'],
            ['#1F3B2D', '#008080', '#1F3B2D', '#1F3B2D'],
            ['#6B8E23', '#ADD8E6', '#6B8E23', '#6B8E23'],
            ['#87CEEB', '#87CEEB', '#87CEEB', '#87CEEB'],
            ['#0D47A1', '#0033A0', '#0D47A1', '#0D47A1'],
            ['#0033A0', '#0D1B3D', '#0033A0', '#0033A0'],
            ['#001F3F', '#1C3D4D', '#001F3F', '#001F3F'],
            ['#001A33', '#3C5A6E', '#001A33', '#001A33'],
            ['#EADBC8', '#FFD1DC', '#EADBC8', '#EADBC8'],
            ['#B0B0B0', '#FFC0CB', '#B0B0B0', '#B0B0B0'],
            ['#808080', '#FF77B4', '#808080', '#808080'],
            ['#4A4A4A', '#FF1493', '#4A4A4A', '#4A4A4A'],
            ['#000000', '#E6E6FA', '#000000', '#000000'],
            ['', '#800080', '', ''],
            ['', '#4B0082', '', ''],
            ['', '#722F37', '', ''],
            ['', '#FF0000', '', ''],
            ['', '#F8C000', '', ''],
            ['', '#D4A017', '', ''],
            ['', '#FFD700', '', ''],
            ['', '#C3B091', '', ''],
            ['', '#808000', '', ''],
            ['', '#556B2F', '', ''],
            ['', '#009B77', '', ''],
            ['', '#D3D3D3', '', ''],
            ['', '#B0B0B0', '', ''],
            ['', '#808080', '', ''],
            ['', '#4A4A4A', '', ''],
            ['', '#A9A9A9', '', ''],
            ['', '#36454F', '', ''],
            ['', '#000080', '', ''],
            ['', '#000000', '', ''],
        ];

        $seen = [];
        $ordered = [];

        foreach ($rows as $cells) {
            foreach ($cells as $cell) {
                $cell = strtoupper(trim((string) $cell));
                if ($cell === '') {
                    continue;
                }
                if (! str_starts_with($cell, '#')) {
                    $cell = '#'.$cell;
                }
                if (isset($seen[$cell])) {
                    continue;
                }
                $seen[$cell] = true;
                $ordered[] = $cell;
            }
        }

        return $ordered;
    }

    public static function displayNameForHex(string $hex): string
    {
        $hex = strtoupper(trim($hex));
        if (! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }

        return self::DISPLAY_NAMES[$hex] ?? $hex;
    }

    public static function imagePathForHex(string $hex): string
    {
        $hex = strtoupper(trim($hex));
        if (! str_starts_with($hex, '#')) {
            $hex = '#'.$hex;
        }

        return 'interface_color_variations/hex-'.strtolower(ltrim($hex, '#')).'.png';
    }

    /**
     * @return array<int, array{name: string, hex: string, image_path: string, sort_order: int}>
     */
    public static function records(): array
    {
        $records = [];
        $sort = 100;

        foreach (self::hexCodesInOrder() as $hex) {
            $records[] = [
                'name' => self::displayNameForHex($hex),
                'hex' => $hex,
                'image_path' => self::imagePathForHex($hex),
                'sort_order' => $sort,
            ];
            $sort++;
        }

        return $records;
    }
}
