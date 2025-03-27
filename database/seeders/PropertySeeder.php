<?php

namespace Database\Seeders;

use App\Models\Property;
use App\Models\Category;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    public function run()
    {
        // Get all child categories
        $categories = Category::whereNotNull('parent_id')->get();
        
        // Base coordinates for different areas
        $areas = [
            'Manhattan' => ['lat' => 40.7831, 'lng' => -73.9712],
            'Brooklyn' => ['lat' => 40.6782, 'lng' => -73.9442],
            'Queens' => ['lat' => 40.7282, 'lng' => -73.7949],
            'Bronx' => ['lat' => 40.8448, 'lng' => -73.8648],
            'Staten Island' => ['lat' => 40.5795, 'lng' => -74.1502],
        ];

        // Generate properties
        foreach ($areas as $areaName => $coordinates) {
            // Generate 20 properties per area
            for ($i = 0; $i < 20; $i++) {
                $category = $categories->random();
                
                // Random offset for coordinates (creates spread within area)
                $latOffset = (rand(-100, 100) / 1000); // ±0.1 degree
                $lngOffset = (rand(-100, 100) / 1000);

                Property::create([
                    'title' => "Property in $areaName #" . ($i + 1),
                    'description' => "Beautiful property located in $areaName with amazing views",
                    'price' => rand(200000, 2000000),
                    'category_id' => $category->id,
                    'address' => rand(1, 999) . " $areaName Street",
                    'bedrooms' => rand(1, 5),
                    'bathrooms' => rand(1, 3),
                    'area' => rand(50, 500),
                    'status' => 'available',
                    'is_featured' => rand(0, 1) == 1,
                    'latitude' => $coordinates['lat'] + $latOffset,
                    'longitude' => $coordinates['lng'] + $lngOffset,
                ]);
            }
        }
    }
} 