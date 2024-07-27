<?php

namespace App\Rules;

use App\CartCheckout;
use App\ProductUtils;

class DeliveryFeeRule implements RuleContract
{
    public const CHARGE_FEE_ON_PURCHASE_TOTAL = 'getFeeBasedOnTotal';

    public function __construct(
        public readonly array $conditions,
        public ProductUtils $productUtils = new ProductUtils()
    ) {
    }

    public function calculateTotal(CartCheckout $cartCheckout, string $subTotal): string
    {
        if (in_array(self::CHARGE_FEE_ON_PURCHASE_TOTAL, array_keys($this->conditions))) {
            return $this->getFeeBasedOnTotal(
                $subTotal,
                $this->conditions[self::CHARGE_FEE_ON_PURCHASE_TOTAL]
            );
        } else {
            throw new \Exception('Unsupported condition exception');
        }
    }

    private function getFeeBasedOnTotal(string $total, array $feeConditions): string
    {
        foreach ($feeConditions as $range) {
            if ($total != 0 && $total >= $range['min'] && $total < $range['max']) {
                return number_format($range['fee'], 2);
            }
        }

        return '0.00';
    }

    /*
     * This can be expanded to other use-cases as well like the below one
     *  private const CHARGE_FEE_BASED_ON_AREA = 'getFeeBasedOnArea';
     *  private function getFeeBasedOnArea(): void {}
     */
}
