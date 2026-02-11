<?php

declare(strict_types=1);

$dbPath = __DIR__ . '/database.sqlite';

try {
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $db->exec("
        CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            base_price_cents INTEGER NOT NULL,
            weight REAL NOT NULL
        );
    ");

    $db->exec("DELETE FROM products");

    $db->exec("
        INSERT INTO products (name, base_price_cents, weight) VALUES
            ('Cimento Votorantim 50kg', 1600, 50.0),
            ('Tijolo 8 furos (Milheiro)', 60000, 1500.0),
            ('Furadeira Bosh 700W', 45000, 2.5)
    ");

    echo "Database setup completed successfully.";
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}