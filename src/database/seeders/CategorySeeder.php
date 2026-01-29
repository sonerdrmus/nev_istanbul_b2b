<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tree = [
            [
                'name' => 'Üst Giyim',
                'slug' => 'ust-giyim',
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Tişört', 'slug' => 'tisort', 'sort_order' => 1],
                    ['name' => 'Gömlek', 'slug' => 'gomlek', 'sort_order' => 2],
                    ['name' => 'Sweatshirt', 'slug' => 'sweatshirt', 'sort_order' => 3],
                    ['name' => 'Kazak', 'slug' => 'kazak', 'sort_order' => 4],
                    ['name' => 'Ceket', 'slug' => 'ceket', 'sort_order' => 5],
                    ['name' => 'Bluz', 'slug' => 'bluz', 'sort_order' => 6],
                    ['name' => 'Triko Yelek', 'slug' => 'triko-yelek', 'sort_order' => 7],
                ],
            ],
            [
                'name' => 'Alt Giyim',
                'slug' => 'alt-giyim',
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Pantolon', 'slug' => 'pantolon', 'sort_order' => 1],
                    ['name' => 'Şort', 'slug' => 'short', 'sort_order' => 2],
                    ['name' => 'Etek', 'slug' => 'etek', 'sort_order' => 3],
                ],
            ],
            [
                'name' => 'Dış Giyim',
                'slug' => 'dis-giyim',
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Kaban', 'slug' => 'kaban', 'sort_order' => 1],
                    ['name' => 'Mont', 'slug' => 'mont', 'sort_order' => 2],
                ],
            ],
            [
                'name' => 'Kadın Giyim',
                'slug' => 'kadin-giyim',
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Elbise', 'slug' => 'elbise', 'sort_order' => 1],
                ],
            ],
            [
                'name' => 'Aksesuar',
                'slug' => 'aksesuar',
                'sort_order' => 5,
                'children' => [
                    ['name' => 'Atkı', 'slug' => 'atki', 'sort_order' => 1],
                    ['name' => 'Kravat', 'slug' => 'kravat', 'sort_order' => 2],
                ],
            ],
        ];

        foreach ($tree as $parentData) {
            $children = $parentData['children'] ?? [];
            unset($parentData['children']);

            $parent = Category::updateOrCreate(
                ['slug' => $parentData['slug']],
                array_merge($parentData, ['parent_id' => null, 'is_active' => true])
            );

            foreach ($children as $childData) {
                Category::updateOrCreate(
                    ['slug' => $childData['slug']],
                    array_merge($childData, ['parent_id' => $parent->id, 'is_active' => true])
                );
            }
        }
    }
}
