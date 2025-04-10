<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = DB::table('categories')->get();
        
        $products = [
            // Electronics
            [
                'name' => 'Smartphone X',
                'description' => 'Latest smartphone with advanced features and high-performance camera',
                'price' => 999.99,
                'stock' => 50,
                'image' => 'products/smartphone-x.jpg',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => 'Laptop Pro',
                'description' => 'Powerful laptop for professionals with long battery life',
                'price' => 1499.99,
                'stock' => 30,
                'image' => 'products/laptop-pro.jpg',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => 'Wireless Earbuds',
                'description' => 'Premium wireless earbuds with noise cancellation',
                'price' => 199.99,
                'stock' => 100,
                'image' => 'products/wireless-earbuds.jpg',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => 'Smart Watch',
                'description' => 'Fitness tracker and smartwatch with health monitoring',
                'price' => 299.99,
                'stock' => 75,
                'image' => 'products/smart-watch.jpg',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],
            [
                'name' => '4K Monitor',
                'description' => '32-inch 4K monitor with HDR support',
                'price' => 499.99,
                'stock' => 25,
                'image' => 'products/4k-monitor.jpg',
                'category_id' => $categories->where('name', 'Electronics')->first()->id,
            ],

            // Clothing
            [
                'name' => 'Classic Denim Jacket',
                'description' => 'Timeless denim jacket with modern fit',
                'price' => 89.99,
                'stock' => 40,
                'image' => 'products/denim-jacket.jpg',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
            ],
            [
                'name' => 'Premium T-Shirt',
                'description' => 'Comfortable cotton t-shirt with premium finish',
                'price' => 29.99,
                'stock' => 150,
                'image' => 'products/premium-tshirt.jpg',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
            ],
            [
                'name' => 'Slim Fit Jeans',
                'description' => 'Modern slim fit jeans with stretch comfort',
                'price' => 59.99,
                'stock' => 80,
                'image' => 'products/slim-jeans.jpg',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
            ],
            [
                'name' => 'Winter Scarf',
                'description' => 'Warm and stylish winter scarf',
                'price' => 39.99,
                'stock' => 60,
                'image' => 'products/winter-scarf.jpg',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
            ],
            [
                'name' => 'Running Shoes',
                'description' => 'Lightweight running shoes with cushioning',
                'price' => 129.99,
                'stock' => 45,
                'image' => 'products/running-shoes.jpg',
                'category_id' => $categories->where('name', 'Clothing')->first()->id,
            ],

            // Books
            [
                'name' => 'The Art of Programming',
                'description' => 'Comprehensive guide to modern programming techniques',
                'price' => 49.99,
                'stock' => 35,
                'image' => 'products/programming-book.jpg',
                'category_id' => $categories->where('name', 'Books')->first()->id,
            ],
            [
                'name' => 'Business Strategy',
                'description' => 'Essential guide to business strategy and management',
                'price' => 39.99,
                'stock' => 50,
                'image' => 'products/business-book.jpg',
                'category_id' => $categories->where('name', 'Books')->first()->id,
            ],
            [
                'name' => 'Science Fiction Collection',
                'description' => 'Collection of classic science fiction stories',
                'price' => 29.99,
                'stock' => 70,
                'image' => 'products/sci-fi-book.jpg',
                'category_id' => $categories->where('name', 'Books')->first()->id,
            ],
            [
                'name' => 'Cookbook Deluxe',
                'description' => 'Gourmet recipes from world-class chefs',
                'price' => 44.99,
                'stock' => 40,
                'image' => 'products/cookbook.jpg',
                'category_id' => $categories->where('name', 'Books')->first()->id,
            ],
            [
                'name' => 'History of Art',
                'description' => 'Comprehensive guide to art history',
                'price' => 59.99,
                'stock' => 30,
                'image' => 'products/art-history-book.jpg',
                'category_id' => $categories->where('name', 'Books')->first()->id,
            ],

            // Home & Garden
            [
                'name' => 'Garden Tool Set',
                'description' => 'Complete set of essential garden tools',
                'price' => 79.99,
                'stock' => 25,
                'image' => 'products/garden-tools.jpg',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
            ],
            [
                'name' => 'Indoor Plant',
                'description' => 'Low-maintenance indoor plant with decorative pot',
                'price' => 34.99,
                'stock' => 60,
                'image' => 'products/indoor-plant.jpg',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
            ],
            [
                'name' => 'Smart LED Bulbs',
                'description' => 'WiFi-enabled smart LED bulbs with app control',
                'price' => 24.99,
                'stock' => 100,
                'image' => 'products/smart-bulbs.jpg',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
            ],
            [
                'name' => 'Throw Pillows',
                'description' => 'Decorative throw pillows for sofa or bed',
                'price' => 19.99,
                'stock' => 80,
                'image' => 'products/throw-pillows.jpg',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
            ],
            [
                'name' => 'Garden Bench',
                'description' => 'Weather-resistant garden bench with storage',
                'price' => 149.99,
                'stock' => 15,
                'image' => 'products/garden-bench.jpg',
                'category_id' => $categories->where('name', 'Home & Garden')->first()->id,
            ],
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'stock' => $product['stock'],
                'image' => $product['image'],
                'category_id' => $product['category_id'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 