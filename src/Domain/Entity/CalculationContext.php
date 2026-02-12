<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use InvalidArgumentException;

class CalculationContext
{
    public function __construct(
        public readonly int $quantity,
        public readonly string $customerType,
        public readonly string $state,
        public readonly bool $isPremiumCustomer
    ) {
        if ($this->quantity < 1) {
            throw new InvalidArgumentException('Quantity must be at least 1.');
        }
    }
}
