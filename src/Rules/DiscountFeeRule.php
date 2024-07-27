<?php

namespace App\Rules;

use App\ProductUtils;
use App\CartCheckout;

class DiscountFeeRule implements RuleContract
{
    public const BUY_ONE_GET_SECOND_AS_DISCOUNT = 'buyOneGetSecondAsDiscount';

    public function __construct(
        public readonly array $conditions,
        public ProductUtils $productUtils = new ProductUtils()
    ) {
    }

    public function calculateTotal(CartCheckout $cartCheckout, string $subTotal): string
    {
        if (in_array(self::BUY_ONE_GET_SECOND_AS_DISCOUNT, array_keys($this->conditions), true)) {
            return $this->buyOneGetSecondAsDiscount(
                $cartCheckout,
                $this->conditions[self::BUY_ONE_GET_SECOND_AS_DISCOUNT]
            );
        }

        throw new \Exception('Unsupported condition exception');
    }

    private function buyOneGetSecondAsDiscount(CartCheckout $cartCheckout, array $discountConditions): string
    {
        $products = $cartCheckout->getProducts();
        $productsByQuantities = $this->productUtils->getProductQuantities(
            $cartCheckout->getCartItems()
        );

        $discount = 0;
        $discountedProducts = $discountConditions['discount_products'] ?? [];
        $discountPercentage = $discountConditions['discount_percentage'] ?? [];

        foreach ($productsByQuantities as $code => $quantity) {
            if (! in_array($code, $discountedProducts)) {
                continue;
            }

            $product = $products[$code] ?? null;
            $price = $product?->getPrice() ?? 0;

            for ($i = 1; $i <= $quantity; $i++) {
                if ($i % 2 == 0) {
                    $discount += $price * $discountPercentage;
                }
            }
        }

        if (! $discount) {
            return '0.00';
        } else {
            return number_format(-(float)$discount, 2);
        }
    }
}
