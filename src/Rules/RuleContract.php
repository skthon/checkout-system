<?php

namespace App\Rules;

use App\CartCheckout;

interface RuleContract
{
    public function calculateTotal(CartCheckout $cartCheckout, string $subTotal): string;
}
