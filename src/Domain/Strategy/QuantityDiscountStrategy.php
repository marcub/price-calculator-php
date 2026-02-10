<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;

class QuantityDiscountStrategy implements PriceModifierInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): Money {
        $quantity = $context->quantity;

        if ($quantity >= 50) {
            // 5% discount for 50 or more items
            return $price->multiply("0.95");
        }

        if ($quantity >= 10) {
            // 3% discount for 10-49 items
            return $price->multiply("0.97");
        }

        return $price;
    }
}