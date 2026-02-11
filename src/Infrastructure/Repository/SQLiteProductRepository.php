<?php

declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Domain\Repository\ProductRepositoryInterface;
use App\Domain\Entity\Product;
use PDO;
use Money\Money;

class SQLiteProductRepository implements ProductRepositoryInterface
{

    public function __construct(
        private readonly PDO $pdo
    ){}
    
    public function findById(int $id): ?Product
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Product(
            id: (int)$row['id'],
            name: $row['name'],
            basePrice: Money::BRL($row['base_price_cents']),
            weight: (float)$row['weight']
        );
    }
}