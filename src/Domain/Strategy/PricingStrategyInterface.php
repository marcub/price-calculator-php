<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use App\Domain\Entity\Product;
use Money\Money;

interface PricingStrategyInterface
{
    /**
     * Calculate the price adjustment.
     * @param Money $price The price after previous adjustments
     * @param Product $product The product being priced
     * @param CalculationContext $context The context of the calculation
     * @return CalculationResult
     */
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): CalculationResult;
}
