<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

final class ProductService
{
    /**
     * Update the stock for a given product.
     *
     * @throws \InvalidArgumentException If stock is negative.
     */
    public function updateStock(Product $product, int $newStock): Product
    {
        if ($newStock < 0) {
            throw new \InvalidArgumentException('Stock cannot be negative.');
        }

        $product->update(['stock' => $newStock]);

        return $product->refresh(); // Return the updated product instance
    }

    // Add other product-related business logic methods here
    // e.g., creating products, handling image uploads, etc.
} 