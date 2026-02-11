<?php

declare(strict_types=1);

namespace Tests\Integration\Domain\Service;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Factory\ProductCalculatorFactory;
use App\Domain\Service\ProductCalculatorInterface;
use PHPUnit\Framework\TestCase;
use Money\Money;

class ProductCalculatorTest extends TestCase
{

    private ProductCalculatorInterface $calculator;

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

        $result = $this->calculator->calculatePrice($product, $context);

        $expected = Money::BRL(141480);

        $this->assertTrue(
            $expected->equals($result->price),
            'The final integrated price did not match the expected R$ 1.414,80'
        );
    }
}

