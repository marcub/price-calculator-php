<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Strategy;

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Strategy\HeavyWeightSurchargeStrategy;
use Money\Money;
use PHPUnit\Framework\TestCase;

class HeavyWeightSurchargeStrategyTest extends TestCase
{

    private HeavyWeightSurchargeStrategy $strategy;
    private CalculationContext $context;

    protected function setUp(): void
    {
        $this->strategy = new HeavyWeightSurchargeStrategy();
        $this->context = new CalculationContext(1, 'varejo', 'AL', false);
    }

    public function testSurchargeForNoHeavyProduct(): void
    {
        $price = Money::BRL(10000);
        $lightProduct = new Product(
            id: 1,
            name: 'Light Product',
            basePrice: $price,
            weight: 49.9
        );

        $result  = $this->strategy->calculate($price, $lightProduct, $this->context);

        $this->assertTrue($price->equals($result));
    }

    public function testSurchargeForProductExactly50kg(): void
    {
        $price = Money::BRL(10000);
        $exactWeightProduct = new Product(
            id: 3,
            name: 'Exact Weight Product',
            basePrice: $price,
            weight: 50.0
        );

        $result  = $this->strategy->calculate($price, $exactWeightProduct, $this->context);

        $this->assertTrue($price->equals($result));
    }

    public function testSurchargeForHeavyProduct(): void
    {
        $price = Money::BRL(10000);
        $heavyProduct = new Product(
            id: 2,
            name: 'Heavy Product',
            basePrice: $price,
            weight: 50.1
        );

        $result  = $this->strategy->calculate($price, $heavyProduct, $this->context);

        $expected = $price->add(Money::BRL(1500));
        $this->assertTrue($expected->equals($result));
    }

}