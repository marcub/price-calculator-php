<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Service;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Factory\ProductCalculatorFactory;
use App\Domain\Service\ProductCalculator;
use PHPUnit\Framework\TestCase;
use Money\Money;

class ProductCalculatorTest extends TestCase
{

    private ProductCalculator $calculator;

    protected function setUp(): void
    {
        $this->calculator = ProductCalculatorFactory::create();
    }

    public function testCalculatorPriceForVarejoNonPremiumCustomerHeavyProduct10Items(): void
    {
        $basePrice = Money::BRL(100000);
        $product = new Product(
            id: 1,
            name: 'Test Product',
            basePrice: $basePrice,
            weight: 55.0
        );

        $context = new CalculationContext(10, 'varejo', 'RJ', false);

        $finalPrice = $this->calculator->calculatePrice($product, $context);

        $expected = Money::BRL(141480);

        $this->assertTrue(
            $finalPrice->equals($expected),
            'The final integrated price did not match the expected R$ 1.414,80'
        );
    }
}

