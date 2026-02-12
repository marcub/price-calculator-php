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
        $testConfig = [
            'is_profit_margin_percentage' => true,
            'profit_margin' => 0.20,
            'state_taxes' => [
                'SP' => ['ICMS' => 0.18, 'IPI' => 0.05],
                'RJ' => ['ICMS' => 0.20]
            ]
        ];

        $this->calculator = ProductCalculatorFactory::create($testConfig);
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
