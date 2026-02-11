<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Strategy\ProfitMarginStrategy;
use Money\Money;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class ProfitMarginStrategyTest extends TestCase
{
    private CalculationContext $context;
    private Product $product;

    protected function setUp(): void
    {
        $this->context = new CalculationContext(1, 'varejo', 'SP', false);
        $this->product = new Product(
            id: 1,
            name: 'Test Product',
            basePrice: Money::BRL(10000),
            weight: 10.0
        );
    }

    public function testCalculateProfitMarginPercentage(): void
    {
        $strategy = new ProfitMarginStrategy(profitMarginPercentage: 0.2);
        $price = Money::BRL(10000);

        $result = $strategy->calculate($price, $this->product, $this->context);

        $expected = Money::BRL(12000);
        $this->assertEquals($expected, $result->price);
    }

    public function testCalculateProfitMarginAmount(): void
    {
        $strategy = new ProfitMarginStrategy(profitMarginAmount: Money::BRL(2000));
        $price = Money::BRL(10000);

        $result = $strategy->calculate($price, $this->product, $this->context);

        $expected = Money::BRL(12000);
        $this->assertEquals($expected, $result->price);
    }

    public function testCalculateNoProfitMarginConfigured(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new ProfitMarginStrategy();
    }
}