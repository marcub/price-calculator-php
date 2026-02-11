<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Domain\Entity\CalculationContext;
use App\Domain\Entity\Product;
use App\Domain\Factory\ProductCalculatorFactory;
use App\Domain\Service\CachedProductCalculator;
use App\Infrastructure\Cache\FileCache;
use App\Infrastructure\Repository\SQLiteProductRepository;
use Money\Money;
use PHP_CodeSniffer\Util\Cache;

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

    $dbPath = __DIR__ . '/database/database.sqlite';
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $productRepository = new SQLiteProductRepository($pdo);

    $productId = (int) ($data['product_id'] ?? 0);
    $product = $productRepository->findById($productId);

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product with ID ' . $productId . ' not found.']);
        exit;
    }

    $context = new CalculationContext(
        (int) ($data['context']['quantity'] ?? 0),
        (string) ($data['context']['customer_type'] ?? ''),
        (string) ($data['context']['state'] ?? ''),
        (bool) ($data['context']['is_premium'] ?? false)
    );

    $cacheDir = __DIR__ . '/storage/cache';
    $fileCache = new FileCache($cacheDir);

    $baseCalculator = ProductCalculatorFactory::create();
    $calculator = new CachedProductCalculator($baseCalculator, $fileCache);

    $finalPrice = $calculator->calculatePrice($product, $context);

    http_response_code(200);
    echo json_encode(
        [
            'success' => true,
            'data' => [
                'product_id' => $product->getId(),
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