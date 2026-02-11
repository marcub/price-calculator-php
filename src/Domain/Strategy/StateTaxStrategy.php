<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;

class StateTaxStrategy implements PricingStrategyInterface
{

    /**
     * @param array<string, array<string, float>> $taxRates 
     * Example: ['SP' => ['ICMS' => 0.18, 'IPI' => 0.05], 'RJ' => ['ICMS' => 0.20]]
     */
    public function __construct(
        private readonly array $taxRates
    ) {}

    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): Money {
        $state = $context->state;

        if (!array_key_exists($state, $this->taxRates)) {
            return $price;
        }

        $stateTaxes = $this->taxRates[$state];
        $totalTaxRate = 0.0;

        foreach ($stateTaxes as $taxName => $rate) {
            $totalTaxRate += $rate;
        }

        $multiplier = (string) (1 + $totalTaxRate);

        return $price->multiply($multiplier);
    }
}