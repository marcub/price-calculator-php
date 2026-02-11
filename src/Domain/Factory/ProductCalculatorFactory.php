<?php

declare(strict_types=1);

namespace App\Domain\Factory;

use App\Domain\Service\ProductCalculator;
use App\Domain\Service\ProductCalculatorInterface;
use App\Domain\Strategy\QuantityDiscountStrategy;
use App\Domain\Strategy\StateTaxStrategy;
use App\Domain\Strategy\HeavyWeightSurchargeStrategy;
use App\Domain\Strategy\CustomerDiscountStrategy;
use App\Domain\Strategy\ProfitMarginStrategy;

class ProductCalculatorFactory
{
    public static function create(?array $customConfig = null): ProductCalculatorInterface
    {
        $config = $customConfig ?? require __DIR__ . '/../../../config/pricing.php';

        $marginValue = (float) ($config['profit_margin'] ?? 0.0);
        $isPercentage = (bool) ($config['is_profit_margin_percentage'] ?? true);

        $profitMarginPercentage = $isPercentage ? $marginValue : null;
        $profitMarginAmount = $isPercentage ? null : $marginValue;

        $strategies = [
            new ProfitMarginStrategy(profitMarginPercentage: $profitMarginPercentage, profitMarginAmount: $profitMarginAmount),
            new QuantityDiscountStrategy(),
            new CustomerDiscountStrategy(),
            new HeavyWeightSurchargeStrategy(),
            new StateTaxStrategy($config['state_taxes'] ?? []),
        ];

        return new ProductCalculator($strategies);
    }
}
