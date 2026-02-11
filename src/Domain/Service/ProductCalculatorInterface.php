<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Entity\Product;
use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\CalculationResult;

interface ProductCalculatorInterface
{
    public function calculatePrice(Product $product, CalculationContext $context): CalculationResult;
}