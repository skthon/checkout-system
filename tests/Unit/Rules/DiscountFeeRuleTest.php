<?php

namespace Tests\Unit\Rules;

use App\CartCheckout;
use App\Models\Product;
use App\Rules\DiscountFeeRule;
use App\ProductUtils;
use PHPUnit\Framework\TestCase;
use Mockery;

class DiscountFeeRuleTest extends TestCase
{
    protected $productMock;
    protected $productUtilsMock;
    protected $cartCheckoutMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productMock = Mockery::mock(Product::class)
            ->shouldReceive('getCode')->andReturn('R01')
            ->shouldReceive('getPrice')->andReturn('32.95')
            ->getMock();

        $this->productUtilsMock = Mockery::mock(ProductUtils::class)
            ->shouldReceive('getProductQuantities')
            ->andReturn(['R01' => 3])
            ->getMock();

        $this->cartCheckoutMock = Mockery::mock(CartCheckout::class)
            ->shouldReceive('getProducts')
            ->andReturn(['R01' => $this->productMock])
            ->shouldReceive('getCartItems')
            ->andReturn([$this->productMock, $this->productMock, $this->productMock])
            ->getMock();
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_total_with_discount()
    {
        $discountConditions = [
            'buyOneGetSecondAsDiscount' => [
                'discount_products'   => ['R01'],
                'discount_percentage' => '0.50',
            ],
        ];

        $discountFeeRule = new DiscountFeeRule($discountConditions, $this->productUtilsMock);
        $discountTotal = $discountFeeRule->calculateTotal($this->cartCheckoutMock, '68.85');
        $this->assertEquals('-16.48', $discountTotal);
    }

    public function test_calculate_total_with_no_discount()
    {
        $discountConditions = [
            'buyOneGetSecondAsDiscount' => [
                'discount_products'   => ['R01'],
                'discount_percentage' => '0.5',
            ],
        ];

        $productUtilsMock = Mockery::mock(ProductUtils::class)
            ->shouldReceive('getProductQuantities')
            ->andReturn(['R01' => 1])
            ->getMock();

        $discountFeeRule = new DiscountFeeRule($discountConditions, $productUtilsMock);
        $discountTotal = $discountFeeRule->calculateTotal($this->cartCheckoutMock, '0.00');
        $this->assertEquals('0.00', $discountTotal);
    }

    public function test_calculate_total_with_unsupported_condition()
    {
        $discountConditions = ['unsupported_condition' => []];
        $discountFeeRule = new DiscountFeeRule($discountConditions, $this->productUtilsMock);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported condition exception');

        $discountFeeRule->calculateTotal($this->cartCheckoutMock, '0.00');
    }
}
