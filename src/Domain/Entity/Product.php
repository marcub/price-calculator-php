<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use Money\Money;

class Product
{
    public function __construct(
        private readonly int $id,
        private readonly string $name,
        private readonly Money $basePrice,
        private readonly float $weight,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getBasePrice(): Money
    {
        return $this->basePrice;
    }

    public function getWeight(): float
    {
        return $this->weight;
    }
}
