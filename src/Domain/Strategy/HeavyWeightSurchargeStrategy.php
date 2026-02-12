<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use Money\Money;

class HeavyWeightSurchargeStrategy implements PricingStrategyInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): CalculationResult {
        $weight = $product->getWeight();

        if ($weight > 50.0) {
            // Add a surcharge of R$ 15.00 for products heavier than 50kg
            return new CalculationResult($price->add(Money::BRL(1500)), ["Heavy Weight Surcharge Applied"]);
        }

        return new CalculationResult($price, []);
    }
}
