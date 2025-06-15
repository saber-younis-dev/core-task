<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Articles about technology and innovation',
            ],
            [
                'name' => 'Health',
                'slug' => 'health',
                'description' => 'Articles about health and wellness',
            ],
            [
                'name' => 'Business',
                'slug' => 'business',
                'description' => 'Articles about business and finance',
            ],
            [
                'name' => 'Lifestyle',
                'slug' => 'lifestyle',
                'description' => 'Articles about lifestyle and culture',
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Articles about education and learning',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
