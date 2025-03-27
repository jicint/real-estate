<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Residential',
                'description' => 'Residential properties',
                'children' => [
                    [
                        'name' => 'Apartments',
                        'description' => 'Modern apartments and flats'
                    ],
                    [
                        'name' => 'Houses',
                        'description' => 'Single family homes'
                    ],
                    [
                        'name' => 'Villas',
                        'description' => 'Luxury villas'
                    ],
                    [
                        'name' => 'Penthouses',
                        'description' => 'Top floor luxury apartments'
                    ],
                    [
                        'name' => 'Studios',
                        'description' => 'Studio apartments'
                    ]
                ]
            ],
            [
                'name' => 'Commercial',
                'description' => 'Commercial properties',
                'children' => [
                    [
                        'name' => 'Offices',
                        'description' => 'Office spaces'
                    ],
                    [
                        'name' => 'Retail',
                        'description' => 'Retail spaces'
                    ],
                    [
                        'name' => 'Industrial',
                        'description' => 'Industrial properties'
                    ],
                    [
                        'name' => 'Warehouses',
                        'description' => 'Storage facilities'
                    ]
                ]
            ],
            [
                'name' => 'Land',
                'description' => 'Land properties',
                'children' => [
                    [
                        'name' => 'Residential Plots',
                        'description' => 'For housing development'
                    ],
                    [
                        'name' => 'Commercial Plots',
                        'description' => 'For commercial development'
                    ],
                    [
                        'name' => 'Agricultural Land',
                        'description' => 'Farming land'
                    ]
                ]
            ]
        ];

        foreach ($categories as $category) {
            $children = $category['children'] ?? [];
            unset($category['children']);
            
            $parent = Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description']
            ]);

            foreach ($children as $child) {
                Category::create([
                    'name' => $child['name'],
                    'slug' => Str::slug($child['name']),
                    'description' => $child['description'],
                    'parent_id' => $parent->id
                ]);
            }
        }
    }
}
