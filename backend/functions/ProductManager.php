<?php
/**
 * ProductManager Class
 * Handles all product operations (CRUD)
 * Consolidates duplicate functions from dashboard.php and products.php
 */
require_once __DIR__ . '/FileHandler.php';

class ProductManager {
    private $products_file;

    public function __construct($products_file = null) {
        if ($products_file === null) {
            // Use absolute path to ensure it works from any directory
            $products_file = __DIR__ . '/../data/products.json';
        }
        $this->products_file = $products_file;
    }

    /**
     * Load all products from JSON file
     */
    public function loadProducts() {
        return FileHandler::readJSON($this->products_file);
    }

    /**
     * Save products to JSON file
     */
    private function saveProducts($products) {
        return FileHandler::writeJSON($this->products_file, $products);
    }

    /**
     * Add a new product with image support
     */
    public function addProduct($name, $price, $stock, $category, $description, $weight = '1kg', $image = null) {
        if (!$name || $price <= 0 || $stock < 0) {
            return false;
        }

        $products = $this->loadProducts();
        $next_id = count($products) > 0 ? max(array_column($products, 'id')) + 1 : 1;

        $products[] = [
            'id' => $next_id,
            'name' => $name,
            'weight' => $weight,
            'price' => $price,
            'stock' => $stock,
            'category' => $category,
            'description' => $description,
            'image' => $image ?? 'default-product.jpg'
        ];

        return $this->saveProducts($products);
    }

    /**
     * Get product by ID
     */
    public function getProductById($id) {
        $products = $this->loadProducts();

        foreach ($products as $product) {
            if ($product['id'] == $id) {
                return $product;
            }
        }

        return null;
    }

    /**
     * Update an existing product with image support
     */
    public function updateProduct($id, $name, $price, $stock, $category, $description, $weight = '1kg', $image = null) {
        if ($id <= 0 || !$name || $price <= 0) {
            return false;
        }

        $products = $this->loadProducts();

        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product['name'] = $name;
                $product['weight'] = $weight;
                $product['price'] = $price;
                $product['stock'] = $stock;
                $product['category'] = $category;
                $product['description'] = $description;
                if ($image) {
                    $product['image'] = $image;
                }
                break;
            }
        }

        return $this->saveProducts($products);
    }

    /**
     * Delete a product by ID
     */
    public function deleteProduct($id) {
        if ($id <= 0) {
            return false;
        }

        $products = $this->loadProducts();
        $products = array_filter($products, fn($p) => $p['id'] != $id);
        return $this->saveProducts(array_values($products));
    }

    /**
     * Get products by category
     */
    public function getProductsByCategory($category) {
        $products = $this->loadProducts();

        return array_filter($products, fn($p) => 
            strtolower($p['category']) === strtolower($category)
        );
    }

    /**
     * Get featured products (first N products)
     */
    public function getFeaturedProducts($limit = 4) {
        $products = $this->loadProducts();
        return array_slice($products, 0, $limit);
    }

    /**
     * Search products by name
     */
    public function searchProducts($query) {
        $products = $this->loadProducts();
        $query = strtolower($query);

        return array_filter($products, fn($p) => 
            strpos(strtolower($p['name']), $query) !== false ||
            strpos(strtolower($p['description']), $query) !== false
        );
    }

    /**
     * Sort products by price
     */
    public function sortByPrice($products, $order = 'ASC') {
        usort($products, function($a, $b) use ($order) {
            if ($order === 'DESC') {
                return $b['price'] - $a['price'];
            }
            return $a['price'] - $b['price'];
        });

        return $products;
    }

    /**
     * Validate product data
     */
    public function validateProduct($name, $price, $stock, $category) {
        if (empty($name) || empty($category)) {
            return 'Product name and category are required.';
        }

        if ($price <= 0) {
            return 'Price must be greater than 0.';
        }

        if ($stock < 0) {
            return 'Stock cannot be negative.';
        }

        return null; // No errors
    }

    /**
     * Generate real sales data from past 30 days (starts at zero)
     */
    public function getSalesData() {
        $salesFile = __DIR__ . '/../data/sales.json';
        $data = [];
        $salesRecords = [];

        // Load existing sales records if file exists
        if (file_exists($salesFile)) {
            $salesRecords = json_decode(file_get_contents($salesFile), true) ?: [];
        }

        // Generate data for last 30 days
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dateDisplay = date('M d', strtotime("-$i days"));
            
            // Find sales for this date
            $sales = 0;
            foreach ($salesRecords as $record) {
                if ($record['date'] === $date) {
                    $sales = floatval($record['sales']);
                    break;
                }
            }

            $data[] = [
                'date' => $dateDisplay,
                'sales' => $sales
            ];
        }

        return $data;
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue($salesData) {
        return array_sum(array_column($salesData, 'sales'));
    }

    /**
     * Get average daily sales
     */
    public function getAverageDailySales($salesData) {
        $total = $this->getTotalRevenue($salesData);
        return count($salesData) > 0 ? $total / count($salesData) : 0;
    }
}
?>
