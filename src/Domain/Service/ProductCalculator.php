<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Strategy\PriceModifierInterface;
use Money\Money;

class ProductCalculator
{
    /**
     * @param PriceModifierInterface[] $strategies
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