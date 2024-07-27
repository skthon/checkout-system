<?php

namespace App;

use App\Rules\DeliveryFeeRule;
use App\Rules\DiscountFeeRule;

class CartCheckout
{
    private array $cartItems = [];

    public function __construct(
        private readonly array $products,
        private readonly array $rules,
        private readonly ProductUtils $productUtils = new ProductUtils()
    ) {
    }

    public function addCartItem(string $code): void
    {
        foreach ($this->products as $product) {
            if ($product->getCode() == $code) {
                $this->cartItems[] = $product;
            }
        }
    }

    public function getCartItems(): array
    {
        return $this->cartItems;
    }

    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @throws \Exception
     */
    public function getTotal(): string
    {
        return $this->getPriceBreakdown()['GrandTotal'];
    }

    /**
     * @throws \Exception
     */
    public function getPriceBreakdown(): array
    {
        $productsTotal = $this->productUtils->getProductsPriceTotal($this->cartItems);
        $priceBreakDown = ['ProductsTotal' => $productsTotal];

        $validRuleKeys = ['discount_rules', 'delivery_rules'];
        if (! empty(array_diff(array_keys($this->rules), $validRuleKeys))) {
            throw new \Exception('Unsupported Rule Exception');
        }

        if (isset($this->rules['discount_rules']) && $this->rules['discount_rules'] instanceof DiscountFeeRule) {
            $rule = $this->rules['discount_rules'];
            $discountTotal = $rule->calculateTotal($this, $productsTotal);
            $priceBreakDown['DiscountTotal'] = $discountTotal;
        }

        if (isset($this->rules['delivery_rules']) && $this->rules['delivery_rules'] instanceof DeliveryFeeRule) {
            $rule = $this->rules['delivery_rules'];
            $subTotal = $productsTotal + ($priceBreakDown['DiscountTotal'] ?? 0);
            $deliveryTotal = $rule->calculateTotal($this, $subTotal);
            $priceBreakDown['DeliveryFeeTotal'] = $deliveryTotal;
        }

        $priceBreakDown['GrandTotal'] = array_sum(array_values($priceBreakDown));
        $priceBreakDown['GrandTotal'] = number_format(floor($priceBreakDown['GrandTotal'] * 100) / 100, 2);

        return $priceBreakDown;
    }
}
