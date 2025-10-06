<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Resin Jewelry',
                'slug' => 'resin-jewelry',
                'description' => 'Handcrafted resin jewelry collection',
                'parent_id' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Scented Candles',
                'slug' => 'scented-candles',
                'description' => 'Aromatic candles for relaxation and ambiance',
                'parent_id' => null,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            $parent = \App\Models\Category::create($category);

            if ($parent->slug === 'resin-jewelry') {
                $subcategories = [
                    [
                        'name' => 'Pendants',
                        'slug' => 'resin-jewelry-pendants',
                        'description' => 'Beautiful resin pendants',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Earrings',
                        'slug' => 'resin-jewelry-earrings',
                        'description' => 'Elegant resin earrings',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Rings',
                        'slug' => 'resin-jewelry-rings',
                        'description' => 'Unique resin rings',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                ];

                foreach ($subcategories as $subcategory) {
                    \App\Models\Category::create($subcategory);
                }
            }

            if ($parent->slug === 'scented-candles') {
                $subcategories = [
                    [
                        'name' => 'Soy Candles',
                        'slug' => 'soy-candles',
                        'description' => 'Natural soy wax candles',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Pillar Candles',
                        'slug' => 'pillar-candles',
                        'description' => 'Traditional pillar candles',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                    [
                        'name' => 'Tea Light Candles',
                        'slug' => 'tea-light-candles',
                        'description' => 'Small tea light candles',
                        'parent_id' => $parent->id,
                        'is_active' => true,
                    ],
                ];

                foreach ($subcategories as $subcategory) {
                    \App\Models\Category::create($subcategory);
                }
            }
        }
    }
}
