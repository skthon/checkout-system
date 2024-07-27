<?php

require __DIR__ . '/vendor/autoload.php';

use App\CartCheckout;
use App\Models\Product;
use App\Rules\DeliveryFeeRule;
use App\Rules\DiscountFeeRule;

$products = [
    'R01' => new Product('R01', 'Red Widget', '32.95'),
    'G01' => new Product('G01', 'Green Widget', '24.95'),
    'B01' => new Product('B01', 'Blue Widget', '7.95'),
];

$rules = [
    'discount_rules' => new DiscountFeeRule([
        DiscountFeeRule::BUY_ONE_GET_SECOND_AS_DISCOUNT => [
            'discount_percentage' => 0.5,
            'discount_products' => ['R01'],
        ]
    ]),
    'delivery_rules' => new DeliveryFeeRule([
        DeliveryFeeRule::CHARGE_FEE_ON_PURCHASE_TOTAL => [
            ['min' => 0, 'max' => 50, 'fee' => 4.95],
            ['min' => 50, 'max' => 90, 'fee' => 2.95],
            ['min' => 90, 'max' => PHP_INT_MAX, 'fee' => 0.00],
        ]
    ]),
];

$cartCheckout = new CartCheckout($products, $rules);
$cartCheckout->addCartItem('B01');
$cartCheckout->addCartItem('G01');
echo "Grand Total: " . $cartCheckout->getTotal() . "\n";

$cartCheckout = new CartCheckout($products, $rules);
$cartCheckout->addCartItem('R01');
$cartCheckout->addCartItem('R01');
echo "Grand Total: " . $cartCheckout->getTotal() . "\n";

$cartCheckout = new CartCheckout($products, $rules);
$cartCheckout->addCartItem('R01');
$cartCheckout->addCartItem('G01');
echo "Grand Total: " . $cartCheckout->getTotal() . "\n";

$cartCheckout = new CartCheckout($products, $rules);
$cartCheckout->addCartItem('B01');
$cartCheckout->addCartItem('B01');
$cartCheckout->addCartItem('R01');
$cartCheckout->addCartItem('R01');
$cartCheckout->addCartItem('R01');
echo "Grand Total: " . $cartCheckout->getTotal() . "\n";

