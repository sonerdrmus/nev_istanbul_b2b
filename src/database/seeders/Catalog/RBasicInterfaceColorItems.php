<?php

namespace Database\Seeders\Catalog;

/**
 * R BASIC pantone tablosundan üretilen dosya adları ile eşleşen Renk Varyasyonları kayıtları.
 */
final class RBasicInterfaceColorItems
{
    /**
     * @return array<int, array{name: string, image_path: string, sort_order: int}>
     */
    public static function records(): array
    {
        return [
            ['name' => 'WHITE', 'image_path' => 'interface_color_variations/01-white.png', 'sort_order' => 1],
            ['name' => 'OFF WHITE', 'image_path' => 'interface_color_variations/02-off-white.png', 'sort_order' => 2],
            ['name' => 'CREAM', 'image_path' => 'interface_color_variations/03-cream.png', 'sort_order' => 3],
            ['name' => 'SAND', 'image_path' => 'interface_color_variations/04-sand.png', 'sort_order' => 4],
            ['name' => 'BROWN', 'image_path' => 'interface_color_variations/05-brown.png', 'sort_order' => 5],
            ['name' => 'YELLOW', 'image_path' => 'interface_color_variations/06-yellow.png', 'sort_order' => 6],
            ['name' => 'BABY PINK', 'image_path' => 'interface_color_variations/07-baby-pink.png', 'sort_order' => 7],
            ['name' => 'PINK', 'image_path' => 'interface_color_variations/08-pink.png', 'sort_order' => 8],
            ['name' => 'BURGUNDY', 'image_path' => 'interface_color_variations/09-burgundy.png', 'sort_order' => 9],
            ['name' => 'RED', 'image_path' => 'interface_color_variations/10-red.png', 'sort_order' => 10],
            ['name' => 'ORANGE', 'image_path' => 'interface_color_variations/11-orange.png', 'sort_order' => 11],
            ['name' => 'PURPLE', 'image_path' => 'interface_color_variations/12-purple.png', 'sort_order' => 12],
            ['name' => 'FOREST GREEN', 'image_path' => 'interface_color_variations/13-forest-green.png', 'sort_order' => 13],
            ['name' => 'TEAL', 'image_path' => 'interface_color_variations/14-teal.png', 'sort_order' => 14],
            ['name' => 'DARK GREEN', 'image_path' => 'interface_color_variations/15-dark-green.png', 'sort_order' => 15],
            ['name' => 'OLIVE GREEN', 'image_path' => 'interface_color_variations/16-olive-green.png', 'sort_order' => 16],
            ['name' => 'SKY BLUE', 'image_path' => 'interface_color_variations/17-sky-blue.png', 'sort_order' => 17],
            ['name' => 'ROYAL BLUE', 'image_path' => 'interface_color_variations/18-royal-blue.png', 'sort_order' => 18],
            ['name' => 'BLUE', 'image_path' => 'interface_color_variations/19-blue.png', 'sort_order' => 19],
            ['name' => 'NAVY', 'image_path' => 'interface_color_variations/20-navy.png', 'sort_order' => 20],
            ['name' => 'DEEP NAVY', 'image_path' => 'interface_color_variations/21-deep-navy.png', 'sort_order' => 21],
            ['name' => 'NATURAL', 'image_path' => 'interface_color_variations/22-natural.png', 'sort_order' => 22],
            ['name' => 'HEATHER GRAY', 'image_path' => 'interface_color_variations/23-heather-gray.png', 'sort_order' => 23],
            ['name' => 'GREY', 'image_path' => 'interface_color_variations/24-grey.png', 'sort_order' => 24],
            ['name' => 'CHARCOAL', 'image_path' => 'interface_color_variations/25-charcoal.png', 'sort_order' => 25],
            ['name' => 'BLACK', 'image_path' => 'interface_color_variations/26-black.png', 'sort_order' => 26],
        ];
    }
}
