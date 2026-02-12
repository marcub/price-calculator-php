<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use App\Domain\Strategy\PricingStrategyInterface;

class ProductCalculator implements ProductCalculatorInterface
{
    /**
     * @param PricingStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly array $strategies
    ) {
    }

    public function calculatePrice(Product $product, CalculationContext $context): CalculationResult
    {
        $price = $product->getBasePrice();
        $appliedRules = [];

        foreach ($this->strategies as $strategy) {
            $result = $strategy->calculate($price, $product, $context);
            $price = $result->price;
            $appliedRules = array_merge($appliedRules, $result->appliedRules);
        }

        return new CalculationResult($price, $appliedRules);
    }
}
