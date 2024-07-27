<?php

namespace App;

use App\Models\Product;

class ProductUtils
{
    /**
     * @param Product[] $products An array of Product objects.
     * @return array<string, int>
     */
    public function getProductQuantities(array $products): array
    {
        $productQuantities = [];

        foreach ($products as $product) {
            $code = $product->getCode();
            $productQuantities[$code] = ($productQuantities[$code] ?? 0) + 1;
        }

        return $productQuantities;
    }

    /**
     * @param Product[] $products An array of Product objects.
     */
    public function getProductsPriceTotal(array $products): string
    {
        $total = '0.00';
        foreach ($products as $product) {
            $total += $product->getPrice();
        }

        return number_format($total, 2);
    }
}
