<?php
/**
 * OrderHandler Class
 * Handles order processing and inventory management
 */
require_once __DIR__ . '/../functions/ProductManager.php';

class OrderHandler {
    private $productManager;

    public function __construct() {
        $this->productManager = new ProductManager();
    }

    /**
     * Process order and reduce stock
     */
    public function processOrder($cart) {
        $logFile = __DIR__ . '/../../debug.log';
        
        if (empty($cart)) {
            return [
                'success' => false,
                'message' => 'Cart is empty'
            ];
        }

        try {
            // Get current products
            $products = $this->productManager->loadProducts();
            
            // Log for debugging
            $debugLog = "OrderHandler: Processing order with " . count($cart) . " items\n";
            $debugLog .= "Available products: " . json_encode(array_column($products, 'name')) . "\n";
            file_put_contents($logFile, $debugLog, FILE_APPEND);
            error_log('Available products: ' . json_encode(array_column($products, 'name')));
            
            // Process each item in the cart
            foreach ($cart as $item) {
                $itemId = intval($item['id'] ?? 0);
                $itemName = trim($item['name'] ?? '');
                $quantity = isset($item['quantity']) ? intval($item['quantity']) : 1;

                $debugLog = "Processing item ID: $itemId, name: '$itemName', quantity: $quantity\n";
                file_put_contents($logFile, $debugLog, FILE_APPEND);
                error_log("Processing item ID: $itemId, name: '$itemName', quantity: $quantity");

                // Find product by ID (much more reliable than name)
                $product = null;
                foreach ($products as $p) {
                    if (intval($p['id']) === $itemId) {
                        $product = $p;
                        break;
                    }
                }

                if (!$product) {
                    $debugLog = "ERROR: Product with ID $itemId not found.\n";
                    $debugLog .= "Available product IDs: " . json_encode(array_column($products, 'id')) . "\n";
                    file_put_contents($logFile, $debugLog, FILE_APPEND);
                    
                    error_log("Product with ID $itemId not found");
                    return [
                        'success' => false,
                        'message' => "Product not found in inventory"
                    ];
                }

                $debugLog = "Found product: " . $product['name'] . ", current stock: " . $product['stock'] . "\n";
                file_put_contents($logFile, $debugLog, FILE_APPEND);

                // Check if sufficient stock available
                if ($product['stock'] < $quantity) {
                    return [
                        'success' => false,
                        'message' => "Insufficient stock for {$itemName}. Available: {$product['stock']}, Requested: {$quantity}"
                    ];
                }

                // Reduce stock
                $newStock = $product['stock'] - $quantity;
                $this->productManager->updateProduct(
                    $product['id'],
                    $product['name'],
                    $product['price'],
                    $newStock,
                    $product['category'],
                    $product['description'],
                    $product['weight'],
                    $product['image'] ?? null
                );

                $debugLog = "SUCCESS: Updated stock for '{$product['name']}': {$product['stock']} -> $newStock\n";
                file_put_contents($logFile, $debugLog, FILE_APPEND);
                error_log("Updated stock for '{$product['name']}': {$product['stock']} -> $newStock");
            }

            $debugLog = "ORDER PROCESSING COMPLETE - All items processed successfully\n";
            file_put_contents($logFile, $debugLog, FILE_APPEND);

            // Record sales data
            $this->recordSale($cart);

            return [
                'success' => true,
                'message' => 'Order processed successfully'
            ];

        } catch (Exception $e) {
            $debugLog = "EXCEPTION: " . $e->getMessage() . "\n";
            file_put_contents($logFile, $debugLog, FILE_APPEND);
            error_log('OrderHandler Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Record sale for analytics
     */
    private function recordSale($cart) {
        $salesFile = __DIR__ . '/../data/sales.json';
        
        // Calculate total sales amount
        $totalAmount = 0;
        foreach ($cart as $item) {
            $qty = isset($item['quantity']) ? intval($item['quantity']) : 1;
            $price = floatval($item['price'] ?? 0);
            $totalAmount += ($price * $qty);
        }

        if ($totalAmount <= 0) {
            return;
        }

        // Get today's date
        $today = date('Y-m-d');

        // Load existing sales data
        $salesData = [];
        if (file_exists($salesFile)) {
            $salesData = json_decode(file_get_contents($salesFile), true) ?: [];
        }

        // Check if today's entry exists
        $entryExists = false;
        foreach ($salesData as &$entry) {
            if ($entry['date'] === $today) {
                $entry['sales'] += $totalAmount;
                $entryExists = true;
                break;
            }
        }

        // If no entry for today, create one
        if (!$entryExists) {
            $salesData[] = [
                'date' => $today,
                'sales' => $totalAmount
            ];
        }

        // Save updated sales data
        file_put_contents($salesFile, json_encode($salesData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
?>
