<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Factory\ProductCalculatorFactory;
use Money\Money;

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');

$requestMethod = $_SERVER['REQUEST_METHOD'];
$requestEndpoint = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

if ($requestMethod !== 'POST' || $requestEndpoint !== '/api/calculate') {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found. Please POST to /api/calculate.']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE || empty($data)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON payload.']);
    exit;
}

try {

    $basePrice = Money::BRL((int) ($data['product']['base_price_cents'] ?? 0));
    $product = new Product(
        (int) ($data['product']['id'] ?? 0),
        (string) ($data['product']['name'] ?? ''),
        $basePrice,
        (float) ($data['product']['weight'] ?? 0)
    );

    $context = new CalculationContext(
        (int) ($data['context']['quantity'] ?? 0),
        (string) ($data['context']['customer_type'] ?? ''),
        (string) ($data['context']['state'] ?? ''),
        (bool) ($data['context']['is_premium'] ?? false)
    );

    $calculator = ProductCalculatorFactory::create();
    $finalPrice = $calculator->calculatePrice($product, $context);

    http_response_code(200);
    echo json_encode(
        [
            'success' => true,
            'data' => [
                'product_id' => $product->getId(),
                'original_price_cents' => $basePrice->getAmount(),
                'final_price_cents' => $finalPrice->getAmount(),
                'final_price_formatted' => 'R$ ' . number_format((float) $finalPrice->getAmount() / 100, 2, ',', '.')
            ]
        ]
    );

} catch (Throwable $e) {
    http_response_code(422);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}