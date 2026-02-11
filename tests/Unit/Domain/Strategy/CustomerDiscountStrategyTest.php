<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Strategy\CustomerDiscountStrategy;
use Money\Money;
use PHPUnit\Framework\TestCase;

class CustomerDiscountStrategyTest extends TestCase
{

    private CustomerDiscountStrategy $strategy;
    private Product $product;

    protected function setUp(): void
    {
        $this->strategy = new CustomerDiscountStrategy();
        $this->product = new Product(
            id: 1,
            name: 'Test Product',
            basePrice: Money::BRL(10000),
            weight: 10.0
        );
    }

    public function testDiscountForNoPremiumVarejoCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'varejo', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $this->assertTrue($price->equals($result));
    }

    public function testDiscountForPremiumVarejoCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'varejo', 'AL', true);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(9800);
        $this->assertTrue($expected->equals($result));
    }

    public function testDiscountForNoPremiumAtacadoCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'atacado', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(9000);
        $this->assertTrue($expected->equals($result));
    }

    public function testDiscountForPremiumAtacadoCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'atacado', 'AL', true);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(8820);
        $this->assertTrue($expected->equals($result));
    }

    public function testDiscountForNoPremiumRevendedorCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'revendedor', 'AL', false);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(8500);
        $this->assertTrue($expected->equals($result));
    }

    public function testDiscountForPremiumRevendedorCustomer(): void
    {
        $price = Money::BRL(10000);
        $context = new CalculationContext(1, 'revendedor', 'AL', true);

        $result  = $this->strategy->calculate($price, $this->product, $context);

        $expected = Money::BRL(8330);
        $this->assertTrue($expected->equals($result));
    }

}