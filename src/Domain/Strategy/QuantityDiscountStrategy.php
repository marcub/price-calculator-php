<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use Money\Money;

class QuantityDiscountStrategy implements PricingStrategyInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): CalculationResult {
        $quantity = $context->quantity;

        if ($quantity >= 50) {
            // 5% discount for 50 or more items
            return new CalculationResult($price->multiply("0.95"), ["5% discount for 50 or more items"]);
        }

        if ($quantity >= 10) {
            // 3% discount for 10-49 items
            return new CalculationResult($price->multiply("0.97"), ["3% discount for 10-49 items"]);
        }

        return new CalculationResult($price, []);
    }
}
