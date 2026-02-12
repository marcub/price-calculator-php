<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use Money\Money;
use App\Domain\Cache\CacheInterface;
use App\Domain\Entity\CalculationResult;

class CachedProductCalculator implements ProductCalculatorInterface
{
    public function __construct(
        private readonly ProductCalculatorInterface $calculator,
        private readonly CacheInterface $cache
    ) {
    }

    public function calculatePrice(Product $product, CalculationContext $context): CalculationResult
    {
        $cacheKey = serialize([
            $product->getId(),
            $context->quantity,
            $context->customerType,
            $context->state,
            $context->isPremiumCustomer
        ]);

        if ($this->cache->has($cacheKey)) {
            $cachedResult = $this->cache->get($cacheKey);
            return new CalculationResult(Money::BRL($cachedResult['price']), $cachedResult['appliedRules']);
        }

        $result = $this->calculator->calculatePrice($product, $context);

        $this->cache->set($cacheKey, [
            'price' => $result->price->getAmount(),
            'appliedRules' => $result->appliedRules
        ]);

        return $result;
    }
}
