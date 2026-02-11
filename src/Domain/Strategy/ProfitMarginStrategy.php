<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use App\Domain\Entity\Product;
use Money\Money;

class ProfitMarginStrategy implements PricingStrategyInterface
{
    public function __construct(
        private readonly ?float $profitMarginPercentage = null,
        private readonly ?Money $profitMarginAmount = null
    ) {
        if ($this->profitMarginPercentage === null && $this->profitMarginAmount === null) {
            throw new \InvalidArgumentException("You must provide either a profit margin percentage or a profit margin amount.");
        }
    }

    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): CalculationResult {

        $rulesApplied = [];

        if ($this->profitMarginPercentage !== null) {
            $multiplier = (string) (1 + $this->profitMarginPercentage);
            $price = $price->multiply($multiplier);
            $rulesApplied[] = "Profit margin percentage applied";
        }

        if ($this->profitMarginAmount !== null) {
            $price = $price->add($this->profitMarginAmount);
            $rulesApplied[] = "Profit margin amount applied";
        }

        return new CalculationResult($price, $rulesApplied);
    }
}