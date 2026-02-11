<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;
use Money\Money;

class CustomerDiscountStrategy implements PricingStrategyInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): CalculationResult {
        $discountedPrice = $price;
        $rulesApplied = [];

        if ($context->customerType === 'atacado') {
            // 10% discount for atacado
            $discountedPrice = $discountedPrice->multiply("0.90");
            $rulesApplied[] = "10% discount for atacado";
        }

        if ($context->customerType === 'revendedor') {
            // 15% discount for revendedor
            $discountedPrice = $discountedPrice->multiply("0.85");
            $rulesApplied[] = "15% discount for revendedor";
        }

        if ($context->isPremiumCustomer) {
            // Extra 2% discount for premium customers
            $discountedPrice = $discountedPrice->multiply("0.98");
            $rulesApplied[] = "2% extra discount for premium customers";
        }

        return new CalculationResult($discountedPrice, $rulesApplied);
    }
}