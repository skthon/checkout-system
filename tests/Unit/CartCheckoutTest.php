<?php

namespace Tests\Unit;

use App\CartCheckout;
use App\Models\Product;
use App\Rules\DeliveryFeeRule;
use App\Rules\DiscountFeeRule;
use PHPUnit\Framework\TestCase;
use Mockery;

class CartCheckoutTest extends TestCase
{
    protected array $products;

    public function setUp(): void
    {
        $productMock1 = Mockery::mock(Product::class)
            ->shouldReceive('getCode')->andReturn('R01')
            ->shouldReceive('getPrice')->andReturn(32.95)
            ->getMock();

        $productMock2 = Mockery::mock(Product::class)
            ->shouldReceive('getCode')->andReturn('G01')
            ->shouldReceive('getPrice')->andReturn(24.95)
            ->getMock();

        $productMock3 = Mockery::mock(Product::class)
            ->shouldReceive('getCode')->andReturn('B01')
            ->shouldReceive('getPrice')->andReturn(7.95)
            ->getMock();

        $this->products = [
            'R01' => $productMock1,
            'G01' => $productMock2,
            'B01' => $productMock3,
        ];
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_add_cart_item()
    {
        $rules = [];

        $cart = new CartCheckout($this->products, $rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('G01');
        $cartItems = $cart->getCartItems();

        $this->assertCount(2, $cartItems);

        $this->assertSame($this->products['R01'], $cartItems[0]);
        $this->assertSame($this->products['G01'], $cartItems[1]);
        $this->assertEquals('R01', $cartItems[0]->getCode());
        $this->assertEquals('G01', $cartItems[1]->getCode());
    }

    public function test_total_price_breakdown()
    {
        $discountRuleMock = Mockery::mock(DiscountFeeRule::class)
            ->shouldReceive('calculateTotal')
            ->with(Mockery::type(CartCheckout::class), '65.85')
            ->andReturn('0.00')
            ->getMock();

        $deliveryRuleMock = Mockery::mock(DeliveryFeeRule::class)
            ->shouldReceive('calculateTotal')
            ->with(Mockery::type(CartCheckout::class), '65.85')
            ->andReturn('2.95')
            ->getMock();

        $rules = [
            'discount_rules' => $discountRuleMock,
            'delivery_rules' => $deliveryRuleMock,
        ];

        $cart = new CartCheckout($this->products, $rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('B01');
        $cart->addCartItem('G01');

        $this->assertEquals([
            'ProductsTotal'    => '65.85',
            'DiscountTotal'    => '0.00',
            'DeliveryFeeTotal' => '2.95',
            'GrandTotal'       => '68.80',
        ], $cart->getPriceBreakdown());
    }

    public function test_unsupported_rule_exception()
    {
        $rules = ['unsupported_rule'];

        $cart = new CartCheckout($this->products, $rules);
        $cart->addCartItem('R01');
        $cart->addCartItem('B01');
        $cart->addCartItem('G01');

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported Rule Exception');

        $cart->getTotal();
    }
}
