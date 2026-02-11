<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Money\Money;

class CalculationResult
{
    public function __construct(
        public readonly Money $price,
        public readonly array $appliedRules
    ) {}
}