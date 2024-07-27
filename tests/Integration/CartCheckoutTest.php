<?php

namespace Tests\Integration;

use App\CartCheckout;
use App\Models\Product;
use App\Rules\DeliveryFeeRule;
use App\Rules\DiscountFeeRule;
use PHPUnit\Framework\TestCase;

class CartCheckoutTest extends TestCase
{
    protected array $products = [];

    protected array $rules = [];

    public function setUp(): void
    {
        $this->products = [
            'R01' => new Product('R01', 'Red Widget', '32.95'),
            'G01' => new Product('G01', 'Green Widget', '24.95'),
            'B01' => new Product('B01', 'Blue Widget', '7.95'),
        ];

        $this->rules = [
            'discount_rules' => new DiscountFeeRule([
                DiscountFeeRule::BUY_ONE_GET_SECOND_AS_DISCOUNT => [
                    'discount_percentage' => 0.5,
                    'discount_products'   => ['R01'],
                ],
            ]),
            'delivery_rules' => new DeliveryFeeRule([
                DeliveryFeeRule::CHARGE_FEE_ON_PURCHASE_TOTAL => [
                    ['min' => 0, 'max' => 50, 'fee' => 4.95],
                    ['min' => 50, 'max' => 90, 'fee' => 2.95],
                    ['min' => 90, 'max' => PHP_INT_MAX, 'fee' => 0.00],
                ],
            ]),
        ];
    }

    public function tearDown(): void
    {
        parent::tearDown();
        $this->products = [];
        $this->rules = [];
    }

    public function test_blue_and_green_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('B01');
        $cart->addCartItem('G01');
        $this->assertSame('37.85', $cart->getTotal());
    }

    public function test_red_and_green_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('G01');
        $this->assertSame('60.85', $cart->getTotal());
    }

    public function test_red_and_blue_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('B01');
        $this->assertSame('45.85', $cart->getTotal());
    }

    public function test_red_blue_green_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('G01');
        $cart->addCartItem('B01');
        $this->assertSame('68.80', $cart->getTotal());
    }

    public function test3_red_and2_blue_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('B01');
        $cart->addCartItem('B01');
        $cart->addCartItem('R01');
        $cart->addCartItem('R01');
        $cart->addCartItem('R01');
        $this->assertSame('98.27', $cart->getTotal());
    }

    public function test4_red_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('R01');
        $cart->addCartItem('R01');
        $cart->addCartItem('R01');
        $this->assertSame('98.85', $cart->getTotal());
    }

    public function test_no_widgets()
    {
        $cart = new CartCheckout($this->products, $this->rules);
        $this->assertSame('0.00', $cart->getTotal());
    }
}