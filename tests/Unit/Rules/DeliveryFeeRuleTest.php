<?php

namespace Tests\Unit;

use App\CartCheckout;
use App\ProductUtils;
use App\Rules\DeliveryFeeRule;
use PHPUnit\Framework\TestCase;
use Mockery;

class DeliveryFeeRuleTest extends TestCase
{
    protected $productUtilsMock;
    protected $cartCheckoutMock;
    protected $deliveryFeeRule;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productUtilsMock = Mockery::mock(ProductUtils::class);
        $this->cartCheckoutMock = Mockery::mock(CartCheckout::class);

        $feeConditions = [
            ['min' => 0, 'max' => 50, 'fee' => 4.95],
            ['min' => 50, 'max' => 90, 'fee' => 2.95],
            ['min' => 90, 'max' => PHP_INT_MAX, 'fee' => 0.00],
        ];

        $this->deliveryFeeRule = new DeliveryFeeRule(
            [DeliveryFeeRule::CHARGE_FEE_ON_PURCHASE_TOTAL => $feeConditions],
            $this->productUtilsMock
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_calculate_total_with_fee_range1()
    {
        $fee = $this->deliveryFeeRule->calculateTotal($this->cartCheckoutMock, '30.00');
        $this->assertEquals('4.95', $fee);
    }

    public function test_calculate_total_with_fee_range2()
    {
        $fee = $this->deliveryFeeRule->calculateTotal($this->cartCheckoutMock, '70.00');
        $this->assertEquals('2.95', $fee);
    }

    public function test_calculate_total_returns_free_delivery()
    {
        $fee = $this->deliveryFeeRule->calculateTotal($this->cartCheckoutMock, '91.00');
        $this->assertEquals('0.00', $fee);
    }

    public function test_calculate_total_with_unsupported_condition()
    {
        $this->deliveryFeeRule = new DeliveryFeeRule(
            ['unsupported_condition' => []],
            $this->productUtilsMock
        );

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unsupported condition exception');

        $this->deliveryFeeRule->calculateTotal($this->cartCheckoutMock, '30.00');
    }
}
