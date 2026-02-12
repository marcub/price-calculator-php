<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Strategy\StateTaxStrategy;
use Money\Money;
use PHPUnit\Framework\TestCase;

class StateTaxStrategyTest extends TestCase
{
    private StateTaxStrategy $strategy;
    private Product $product;

    protected function setUp(): void
    {
        $taxesConfig = [
            'SP' => ['ICMS' => 0.18, 'IPI' => 0.05],
            'RJ' => ['ICMS' => 0.20]
        ];
        $this->strategy = new StateTaxStrategy($taxesConfig);
        $this->product = new Product(
            id: 1,
            name: 'Test Product',
            basePrice: Money::BRL(10000),
            weight: 10.0
        );
    }

    public function testStateTaxForUnregisteredState(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(9, 'varejo', 'MG', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $this->assertTrue($price->equals($result->price));
    }

    public function testStateTaxForStateWithOneTax(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(10, 'varejo', 'RJ', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);
        $expected = Money::BRL(12000);

        $this->assertTrue($expected->equals($result->price));
    }

    public function testStateTaxForStateWithMultipleTaxes(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(10, 'varejo', 'SP', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(12300);
        $this->assertTrue($expected->equals($result->price));
    }
}
