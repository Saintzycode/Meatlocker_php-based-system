<?php
session_start();
require_once 'backend/functions/Helpers.php';
require_once 'backend/functions/Components.php';

requireAdmin('login.php');

$user = getCurrentUser();
$role = $user['role'];
$message = '';
$message_type = '';
$productManager = new ProductManager();

// Handle product actions (only for admin)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'add') {
        $data = [
            'name' => FormHandler::sanitizeString($_POST['name'] ?? '', 100),
            'price' => FormHandler::sanitizeNumber($_POST['price'] ?? 0, 'float'),
            'stock' => FormHandler::sanitizeNumber($_POST['stock'] ?? 0, 'int'),
            'category' => FormHandler::sanitizeString($_POST['category'] ?? '', 50),
            'description' => FormHandler::sanitizeString($_POST['description'] ?? '', 500),
            'weight' => FormHandler::sanitizeString($_POST['weight'] ?? 'kg', 10)
        ];
        
        // Validate form
        $errors = validateProductData($data);

        if ($errors) {
            $message = implode(', ', $errors);
            $message_type = 'error';
        } else {
            $image = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $image = FileHandler::uploadImage($_FILES['image'], 'product');
                if (!$image) {
                    $message = 'Image upload failed.';
                    $message_type = 'error';
                }
            }

            if (!isset($_POST['error'])) {
                if ($productManager->addProduct($data['name'], $data['price'], $data['stock'], $data['category'], $data['description'], $data['weight'], $image)) {
                    $message = "Product '{$data['name']}' added successfully!";
                    $message_type = 'success';
                    logAction('add_product', $data['name']);
                } else {
                    $message = 'Error adding product.';
                    $message_type = 'error';
                }
            }
        }
    } elseif ($action === 'delete') {
        $product_id = FormHandler::sanitizeNumber($_POST['product_id'] ?? 0, 'int');
        if ($product_id > 0 && $productManager->deleteProduct($product_id)) {
            $message = 'Product deleted successfully!';
            $message_type = 'success';
            logAction('delete_product', "ID: $product_id");
        } else {
            $message = 'Error deleting product.';
            $message_type = 'error';
        }
    } elseif ($action === 'batch_update') {
        $updated_count = 0;
        $products = $productManager->loadProducts();
        
        foreach ($products as $product) {
            $product_id = $product['id'];
            $price_key = "price_$product_id";
            $stock_key = "stock_$product_id";
            $weight_key = "weight_$product_id";
            $description_key = "description_$product_id";
            
            if (isset($_POST[$price_key]) || isset($_POST[$stock_key]) || isset($_POST[$weight_key]) || isset($_POST[$description_key])) {
                $new_price = isset($_POST[$price_key]) ? FormHandler::sanitizeNumber($_POST[$price_key], 'float') : $product['price'];
                $new_stock = isset($_POST[$stock_key]) ? FormHandler::sanitizeNumber($_POST[$stock_key], 'int') : $product['stock'];
                $new_weight = isset($_POST[$weight_key]) ? FormHandler::sanitizeString($_POST[$weight_key], 10) : $product['weight'];
                $new_description = isset($_POST[$description_key]) ? FormHandler::sanitizeString($_POST[$description_key], 500) : $product['description'];
                
                if ($productManager->updateProduct($product_id, $product['name'], $new_price, $new_stock, $product['category'], $new_description, $new_weight, null)) {
                    $updated_count++;
                }
            }
        }
        
        $message = $updated_count > 0 ? "Updated $updated_count product(s)!" : 'No changes made.';
        $message_type = $updated_count > 0 ? 'success' : 'info';
        if ($updated_count > 0) logAction('batch_update_products', "$updated_count products");
    }
}

$products = $productManager->loadProducts();
$salesData = getSalesData();
$totalRevenue = getTotalRevenue();
$avgDailySales = getAverageDailySales();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MeatLocker</title>
    <link rel="stylesheet" href="frontend/css/login.css">
    <link rel="stylesheet" href="frontend/css/dashboard.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body>
    <div class="dashboard-container">
        <div class="dashboard-header">
            <h1>🥩 MeatLocker Dashboard</h1>
            <div class="user-info">
                <div>
                    <p>Welcome, <strong><?= esc($user['username']) ?></strong></p>
                    <p style="font-size: 0.9rem; color: #999; margin-top: 0.25rem;">Role: <?= ucfirst($user['role']) ?></p>
                </div>
                <form method="GET" action="logout.php" style="margin: 0;">
                    <button type="submit" class="logout-btn">Logout</button>
                </form>
            </div>
        </div>

        <?php if ($message): ?>
            <?= alert($message, $message_type) ?>
        <?php endif; ?>

        <!-- Sales Analytics Section -->
        <div class="content-section">
            <h2 class="section-title">📊 Sales Analytics</h2>
            <div class="analytics-grid">
                <div class="analytics-card">
                    <h3>Total Revenue (30 days)</h3>
                    <p class="analytics-value">₱<?= number_format($totalRevenue, 2) ?></p>
                </div>
                <div class="analytics-card">
                    <h3>Average Daily Sales</h3>
                    <p class="analytics-value">₱<?= number_format($avgDailySales, 2) ?></p>
                </div>
                <div class="analytics-card">
                    <h3>Total Products</h3>
                    <p class="analytics-value"><?= count($products) ?></p>
                </div>
                <div class="analytics-card">
                    <h3>Total Stock</h3>
                    <p class="analytics-value"><?= array_sum(array_column($products, 'stock')) ?></p>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="chart-container">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <?php if ($role === 'admin'): ?>
            <!-- Add Product Section -->
            <div class="content-section">
                <h2 class="section-title">➕ Add New Product</h2>
                <form method="POST" class="add-product-form" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="add">

                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Product Name *</label>
                            <input type="text" id="name" name="name" placeholder="e.g., Premium Ribeye Steak" required>
                        </div>

                        <div class="form-group">
                            <label for="price">Price (₱) *</label>
                            <input type="number" id="price" name="price" step="0.01" placeholder="599.99" required>
                        </div>

                        <div class="form-group">
                            <label for="weight">Weight Unit *</label>
                            <select id="weight" name="weight" required>
                                <option value="g">g</option>
                                <option value="kg" selected>kg</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="stock">Stock Quantity *</label>
                            <input type="number" id="stock" name="stock" placeholder="15" required>
                        </div>

                        <div class="form-group">
                            <label for="category">Category *</label>
                            <input type="text" id="category" name="category" placeholder="e.g., Beef, Seafood, Poultry" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description *</label>
                        <textarea id="description" name="description" placeholder="Detailed product description..." required rows="4"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="image">Product Image</label>
                        <div class="image-upload-container">
                            <input type="file" id="image" name="image" accept="image/*" onchange="previewImage(event)">
                            <div class="image-preview-area">
                                <img id="imagePreview" style="display: none; max-width: 200px; max-height: 200px; border-radius: 8px;">
                                <p id="imagePlaceholder" style="color: #999; text-align: center;">No image selected</p>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Product
                        </button>
                    </div>
                </form>
            </div>
        <?php endif; ?>

        <!-- Product Inventory Section -->
        <div class="content-section">
            <h2 class="section-title">📦 Product Inventory</h2>

            <?php if (empty($products)): ?>
                <p style="text-align: center; color: #999; padding: 2rem;">No products yet. <?php if ($role === 'admin') echo 'Add one above!'; ?></p>
            <?php else: ?>
                <form method="POST" id="inventoryForm" style="margin-bottom: 2rem;">
                    <table style="width: 100%; border-collapse: collapse; background: #fff;">
                        <thead>
                            <tr style="background: #f5f5f5; border-bottom: 2px solid #ddd;">
                                <th style="padding: 1rem; text-align: left; border: 1px solid #ddd;">Product Name</th>
                                <th style="padding: 1rem; text-align: left; border: 1px solid #ddd;">Category</th>
                                <th style="padding: 1rem; text-align: center; border: 1px solid #ddd;">Price (₱)</th>
                                <th style="padding: 1rem; text-align: center; border: 1px solid #ddd;">Stock</th>
                                <th style="padding: 1rem; text-align: center; border: 1px solid #ddd;">Weight</th>
                                <th style="padding: 1rem; text-align: left; border: 1px solid #ddd;">Description</th>
                                <th style="padding: 1rem; text-align: center; border: 1px solid #ddd;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr style="border-bottom: 1px solid #eee;" data-product-id="<?= $product['id'] ?>">
                                <td style="padding: 1rem; border: 1px solid #ddd;">
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd;">
                                    <?= htmlspecialchars($product['category']) ?>
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd; text-align: center;">
                                    <input type="number" name="price_<?= $product['id'] ?>" value="<?= $product['price'] ?>" step="0.01" style="width: 100px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd; text-align: center;">
                                    <input type="number" name="stock_<?= $product['id'] ?>" value="<?= $product['stock'] ?>" style="width: 80px; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd; text-align: center;">
                                    <select name="weight_<?= $product['id'] ?>" style="padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px;">
                                        <option value="g" <?= $product['weight'] === 'g' ? 'selected' : '' ?>>g</option>
                                        <option value="kg" <?= $product['weight'] === 'kg' ? 'selected' : '' ?>>kg</option>
                                    </select>
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd;">
                                    <textarea name="description_<?= $product['id'] ?>" style="width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; font-family: inherit; font-size: 0.9rem;" rows="2"><?= htmlspecialchars($product['description']) ?></textarea>
                                </td>
                                <td style="padding: 1rem; border: 1px solid #ddd; text-align: center;">
                                    <button type="button" onclick="viewDetails(<?= htmlspecialchars(json_encode($product)) ?>)" class="btn btn-edit" style="padding: 0.5rem 1rem; font-size: 0.9rem; margin-right: 0.5rem;">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.9rem;">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-size: 1rem;">
                            <i class="fas fa-save"></i> Save All Changes
                        </button>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <!-- Details Modal (View Only) -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="detailsModalContent"></div>
        </div>
    </div>

    <!-- Custom Popup -->
    <div id="customPopup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); animation: popupSlideIn 0.3s ease;">
            <div id="popupIcon" style="font-size: 3rem; margin-bottom: 1rem;">✓</div>
            <h2 id="popupTitle" style="color: #473472; margin: 0 0 0.5rem 0; font-family: 'Bebas Neue', 'Inter', sans-serif;">Success!</h2>
            <p id="popupMessage" style="color: #666; margin: 0 0 1.5rem 0;">Your changes have been saved successfully!</p>
            <button id="popupBtn" onclick="closePopup()" style="background: linear-gradient(135deg, #473472, #6E8CFB); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;">OK</button>
        </div>
    </div>

    <style>
        @keyframes popupSlideIn {
            from {
                transform: translateY(-30px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
    </style>

    <script>
        function showPopup(title, message, icon = '✓', callback = null) {
            document.getElementById('popupTitle').textContent = title;
            document.getElementById('popupMessage').textContent = message;
            document.getElementById('popupIcon').textContent = icon;
            document.getElementById('customPopup').style.display = 'flex';
            
            const popupBtn = document.getElementById('popupBtn');
            popupBtn.onclick = function() {
                closePopup();
                if (callback) callback();
            };
        }

        function closePopup() {
            document.getElementById('customPopup').style.display = 'none';
        }
    </script>

    <script>
        // Make image upload container clickable
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.image-upload-container');
            const fileInput = document.getElementById('image');
            
            if (container && fileInput) {
                container.addEventListener('click', function() {
                    fileInput.click();
                });
            }
        });

        // Image preview for add product
        function previewImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('imagePreview');
            const placeholder = document.getElementById('imagePlaceholder');
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    placeholder.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }

        // View product details modal
        function viewDetails(product) {
            let detailsContent = `
                <h3>${escapeHtml(product.name)}</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-top: 1rem;">
                    <div>
                        <p><strong>Category:</strong> ${escapeHtml(product.category)}</p>
                        <p><strong>Price:</strong> ₱${parseFloat(product.price).toFixed(2)}</p>
                        <p><strong>Stock:</strong> ${product.stock}</p>
                        <p><strong>Weight Unit:</strong> ${product.weight}</p>
                    </div>
                    <img src="assets/images/${escapeHtml(product.image || 'default-product.jpg')}" style="max-width: 100%; border-radius: 8px;">
                </div>
                <p style="margin-top: 1rem;"><strong>Description:</strong></p>
                <p>${escapeHtml(product.description)}</p>
            `;
            document.getElementById('detailsModalContent').innerHTML = detailsContent;
            document.getElementById('detailsModal').style.display = 'block';
        }

        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, m => map[m]);
        }

        // Close modals
        document.querySelectorAll('.close-modal, .close').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.modal').style.display = 'none';
            });
        });

        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        });

        // Submit all inventory changes
        const inventoryForm = document.getElementById('inventoryForm');
        if (inventoryForm) {
            inventoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('action', 'batch_update');
                
                fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.text())
                .then(data => {
                    if (data.includes('success') || data.includes('updated')) {
                        showPopup('Success!', 'All changes saved successfully!', '✓', () => location.reload());
                    } else {
                        showPopup('Success!', 'Changes saved!', '✓', () => location.reload());
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPopup('Error', 'Error saving changes', '✕');
                });
            });
        }

        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesData = <?= json_encode($salesData) ?>;
        
        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: salesData.map(d => d.date),
                datasets: [{
                    label: 'Daily Sales (₱)',
                    data: salesData.map(d => d.sales),
                    borderColor: '#473472',
                    backgroundColor: 'rgba(71, 52, 114, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#6E8CFB',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { size: 14, weight: 'bold' },
                            color: '#473472',
                            padding: 15
                        }
                    },
                    tooltip: {
                        enabled: true,
                        backgroundColor: 'rgba(71, 52, 114, 0.9)',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Sales: ₱' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '₱' + value.toLocaleString();
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
