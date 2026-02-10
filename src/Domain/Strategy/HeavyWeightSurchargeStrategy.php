<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;

class HeavyWeightSurchargeStrategy implements PriceModifierInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): Money {
        $weight = $product->getWeight();

        if ($weight > 50.0) {
            // Add a surcharge of R$ 15.00 for products heavier than 50kg
            return $price->add(Money::BRL(1500));
        }

        return $price;
    }
}