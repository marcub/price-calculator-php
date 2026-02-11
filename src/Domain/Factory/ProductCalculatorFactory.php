<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Service\ProductCalculator;
use App\Domain\Strategy\QuantityDiscountStrategy;
use App\Domain\Strategy\StateTaxStrategy;
use App\Domain\Strategy\HeavyWeightSurchargeStrategy;
use App\Domain\Strategy\CustomerDiscountStrategy;
use App\Domain\Strategy\ProfitMarginStrategy;

class ProductCalculatorFactory
{
    public static function create(): ProductCalculator
    {
        $taxesConfig = [
            'SP' => ['ICMS' => 0.18, 'IPI' => 0.05],
            'RJ' => ['ICMS' => 0.20],
        ];

        $strategies = [
            new ProfitMarginStrategy(profitMarginPercentage: 0.20),
            new QuantityDiscountStrategy(),
            new CustomerDiscountStrategy(),
            new HeavyWeightSurchargeStrategy(),
            new StateTaxStrategy($taxesConfig),
        ];

        return new ProductCalculator($strategies);
    }
}
