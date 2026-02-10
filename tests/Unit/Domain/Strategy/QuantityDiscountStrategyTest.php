<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Strategy\QuantityDiscountStrategy;
use Money\Money;
use PHPUnit\Framework\TestCase;

class QuantityDiscountStrategyTest extends TestCase
{

    private QuantityDiscountStrategy $strategy;
    private Product $product;

    protected function setUp(): void
    {
        $this->strategy = new QuantityDiscountStrategy();
        $this->product = new Product(
            id: 1,
            name: 'Test Product',
            basePrice: Money::BRL(10000),
            weight: 10.0
        );
    }

    public function testNoDiscountForLessThan10Items(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(9, 'varejo', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $this->assertTrue($price->equals($result));
    }

    public function test3percentDiscountFor10To49Items(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(10, 'varejo', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = $price->multiply("0.97");
        $this->assertTrue($expected->equals($result));
    }

    public function test5percentDiscountFor50OrMoreItems(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(50, 'varejo', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = $price->multiply("0.95");
        $this->assertTrue($expected->equals($result));
    }

}