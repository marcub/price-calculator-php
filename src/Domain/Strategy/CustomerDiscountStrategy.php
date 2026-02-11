<?php

declare(strict_types=1);

namespace App\Domain\Strategy;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;

class CustomerDiscountStrategy implements PricingStrategyInterface
{
    public function calculate(
        Money $price,
        Product $product,
        CalculationContext $context
    ): Money {
        $discountedPrice = $price;

        if ($context->customerType === 'atacado') {
            // 10% discount for atacado
            $discountedPrice = $discountedPrice->multiply("0.90");
        }

        if ($context->customerType === 'revendedor') {
            // 15% discount for revendedor
            $discountedPrice = $discountedPrice->multiply("0.85");
        }

        if ($context->isPremiumCustomer) {
            // Extra 2% discount for premium customers
            $discountedPrice = $discountedPrice->multiply("0.98");
        }

        return $discountedPrice;
    }
}