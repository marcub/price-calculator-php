<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;
use App\Domain\Cache\CacheInterface;

class CachedProductCalculator implements ProductCalculatorInterface
{
    public function __construct(
        private readonly ProductCalculatorInterface $calculator,
        private readonly CacheInterface $cache
    ) {}

    public function calculatePrice(Product $product, CalculationContext $context): Money
    {
        $cacheKey = serialize([
            $product->getId(),
            $context->quantity,
            $context->customerType,
            $context->state,
            $context->isPremiumCustomer
        ]);

        if ($this->cache->has($cacheKey)) {
            $cachedFinalPrice = $this->cache->get($cacheKey);
            return Money::BRL($cachedFinalPrice);
        }

        $finalPrice = $this->calculator->calculatePrice($product, $context);

        $this->cache->set($cacheKey, $finalPrice->getAmount());

        return $finalPrice;
    }
}