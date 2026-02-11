<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Strategy\PricingStrategyInterface;
use Money\Money;

class ProductCalculator implements ProductCalculatorInterface
{
    /**
     * @param PricingStrategyInterface[] $strategies
     */
    public function __construct(
        private readonly array $strategies
    ) {}

    public function calculatePrice(Product $product, CalculationContext $context): Money
    {
        $price = $product->getBasePrice();

        foreach ($this->strategies as $strategy) {
            $price = $strategy->calculate($price, $product, $context);
        }

        return $price;
    }
}