<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadProductImages extends Command
{
    protected $signature = 'products:download-images';
    protected $description = 'Download placeholder images for products';

    public function handle()
    {
        $this->info('Starting to download product images...');

        $products = [
            'smartphone-x' => 'electronics/smartphone',
            'laptop-pro' => 'electronics/laptop',
            'wireless-earbuds' => 'electronics/earbuds',
            'smart-watch' => 'electronics/smartwatch',
            '4k-monitor' => 'electronics/monitor',
            
            'denim-jacket' => 'fashion/jacket',
            'premium-tshirt' => 'fashion/tshirt',
            'slim-jeans' => 'fashion/jeans',
            'winter-scarf' => 'fashion/scarf',
            'running-shoes' => 'fashion/shoes',
            
            'programming-book' => 'book/programming',
            'business-book' => 'book/business',
            'sci-fi-book' => 'book/scifi',
            'cookbook' => 'book/cooking',
            'art-history-book' => 'book/art',
            
            'garden-tools' => 'home/tools',
            'indoor-plant' => 'home/plant',
            'smart-bulbs' => 'home/bulb',
            'throw-pillows' => 'home/pillow',
            'garden-bench' => 'home/bench'
        ];

        // Create products directory if it doesn't exist
        Storage::disk('public')->makeDirectory('products');

        foreach ($products as $filename => $category) {
            $this->info("Downloading image for {$filename}...");

            try {
                // Get a random image from Lorem Picsum
                $width = 800;
                $height = 600;
                $response = Http::get("https://picsum.photos/{$width}/{$height}");

                if ($response->successful()) {
                    $imageContent = $response->body();
                    Storage::disk('public')->put("products/{$filename}.jpg", $imageContent);
                    $this->info("✓ Successfully downloaded {$filename}.jpg");
                } else {
                    $this->error("Failed to download image for {$filename}");
                }

                // Add a small delay to avoid rate limiting
                usleep(250000); // 0.25 seconds
            } catch (\Exception $e) {
                $this->error("Error downloading {$filename}: " . $e->getMessage());
            }
        }

        $this->info('All product images have been downloaded!');
        $this->info('Don\'t forget to run "php artisan storage:link" if you haven\'t already.');
    }
} 