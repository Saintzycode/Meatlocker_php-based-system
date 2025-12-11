<?php
/**
 * Get Products API
 * Returns all products as JSON
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../classes/ProductManager.php';

$productManager = new ProductManager();
$products = $productManager->loadProducts();

echo json_encode($products);
?>
