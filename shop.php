<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - MeatLocker</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="frontend/css/cart.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <?php 
    session_start();
    require_once 'backend/functions/ProductManager.php';
    $productManager = new ProductManager();
    $products = $productManager->loadProducts();
    ?>
    <?php include 'templates/navbar.php'; ?>

    <section class="shop-hero">
        <div class="shop-hero-content">
            <h1>Premium Meats & Seafood</h1>
            <p>Browse our complete collection of premium frozen goods</p>
        </div>
    </section>

    <section class="shop-container">
        <div class="shop-sidebar">
            <div class="filter-group">
                <h3>Categories</h3>
                <label><input type="radio" class="category-filter" name="category" value="all" checked> All Products</label>
                <label><input type="radio" class="category-filter" name="category" value="beef"> Beef</label>
                <label><input type="radio" class="category-filter" name="category" value="seafood"> Seafood</label>
                <label><input type="radio" class="category-filter" name="category" value="poultry"> Poultry</label>
            </div>

            <div class="filter-group">
                <h3>Sort By</h3>
                <select class="sort-select" id="sortSelect">
                    <option value="featured">Featured</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                </select>
            </div>
        </div>

        <div class="shop-products">
            <div class="shop-grid">
                <?php foreach ($products as $product): ?>
                    <div class="shop-product-card" data-category="<?= strtolower($product['category']) ?>" data-price="<?= $product['price'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>">
                        <?php if ($product['id'] === 1): ?>
                            <div class="product-badge">Featured</div>
                        <?php elseif ($product['id'] === 5): ?>
                            <div class="product-badge premium">Premium</div>
                        <?php endif; ?>
                        <div class="product-image-wrapper">
                            <img src="assets/images/<?= $product['image'] ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">
                            <div class="product-overlay">
                                <p class="overlay-description"><?= htmlspecialchars($product['description']) ?></p>
                            </div>
                        </div>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['name']) ?> <span style="font-size: 0.85rem; color: #999;">(<?= htmlspecialchars($product['weight']) ?>)</span></h3>
                            <div class="product-stock-info">
                                <span class="product-stock <?= $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock') ?>">
                                    Stock: <?= $product['stock'] ?> left
                                </span>
                            </div>
                            <div class="product-price-section">
                                <span class="product-price">₱<?= number_format($product['price'], 2) ?></span>
                            </div>
                            <button class="add-to-cart" data-id="<?= $product['id'] ?>" data-name="<?= htmlspecialchars($product['name']) ?>" data-price="<?= $product['price'] ?>" data-image="images/<?= $product['image'] ?>" <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-left">
                <p><a href="about.php" class="footer-link">About Us</a></p>
            </div>
            <div class="footer-right">
                <p>© 2025 MeatLocker. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="assets/libs/aos-offline.js"></script>
    <script>
        // Cart functionality
        const isLoggedIn = <?= isset($_SESSION['user_id']) ? 'true' : 'false' ?>;
        const cartIconBtn = document.getElementById('cartIconBtn');
        const cartDropdown = document.getElementById('cartDropdown');
        const cartItems = document.getElementById('cartItems');
        const cartBadge = document.getElementById('cartBadge');
        const cartFooter = document.getElementById('cartFooter');
        const cartTotal = document.getElementById('cartTotal');
        const userMenuBtn = document.getElementById('userMenuBtn');
        const userDropdown = document.getElementById('userDropdown');

        let cart = JSON.parse(localStorage.getItem('cart')) || [];

        // Cart dropdown toggle
        if (cartIconBtn && cartDropdown) {
            cartIconBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                cartDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (cartDropdown && !cartDropdown.contains(e.target) && !cartIconBtn.contains(e.target)) {
                    cartDropdown.classList.remove('show');
                }
            });
        }

        // User menu dropdown toggle
        if (userMenuBtn && userDropdown) {
            userMenuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                userDropdown.classList.toggle('show');
            });

            document.addEventListener('click', (e) => {
                if (userDropdown && !userDropdown.contains(e.target) && !userMenuBtn.contains(e.target)) {
                    userDropdown.classList.remove('show');
                }
            });
        }

        // Update cart UI
        function updateCartUI() {
            if (cart.length === 0) {
                cartBadge.style.display = 'none';
                cartItems.innerHTML = '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Your cart is empty</p></div>';
                cartFooter.style.display = 'none';
            } else {
                cartBadge.textContent = cart.reduce((sum, item) => sum + (item.quantity || 1), 0);
                cartBadge.style.display = 'flex';
                
                cartItems.innerHTML = cart.map((item, index) => `
                    <div class="cart-item">
                        <img src="assets/${item.image}" alt="${item.name}" class="cart-item-image">
                        <div class="cart-item-details">
                            <p class="cart-item-name">${item.name}</p>
                            <p class="cart-item-price">₱${parseFloat(item.price).toFixed(2)}</p>
                            <p class="cart-item-qty">Qty: ${item.quantity || 1}</p>
                        </div>
                        <button class="cart-remove-btn" onclick="event.stopPropagation(); removeFromCart(${index})" title="Remove">✕</button>
                    </div>
                `).join('');
                
                const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * (item.quantity || 1)), 0);
                cartTotal.textContent = '₱' + total.toFixed(2);
                cartFooter.style.display = 'block';
            }
        }

        function removeFromCart(index) {
            cart.splice(index, 1);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        }

        // Add to cart buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            if (button.tagName === 'BUTTON') {
                button.addEventListener('click', (e) => {
                    e.preventDefault();
                    
                    // Check if user is logged in
                    if (!isLoggedIn) {
                        showLoginPopup();
                        return;
                    }

                    const product = {
                        id: button.dataset.id,
                        name: button.dataset.name,
                        price: button.dataset.price,
                        image: button.dataset.image,
                        quantity: 1
                    };

                    // Check if product already exists in cart
                    const existingProduct = cart.find(item => item.id === product.id);
                    if (existingProduct) {
                        existingProduct.quantity++;
                    } else {
                        cart.push(product);
                    }

                    animateToCart(button, product);
                    localStorage.setItem('cart', JSON.stringify(cart));
                    updateCartUI();
                });
            }
        });

        // Animate product to cart
        function animateToCart(button, product) {
            const flyingElement = document.createElement('img');
            const rect = button.getBoundingClientRect();
            flyingElement.src = 'assets/' + product.image;
            flyingElement.style.left = rect.left + 'px';
            flyingElement.style.top = rect.top + 'px';
            flyingElement.style.width = rect.width + 'px';
            flyingElement.style.height = rect.height + 'px';
            flyingElement.style.position = 'fixed';
            flyingElement.style.borderRadius = '8px';
            flyingElement.style.objectFit = 'cover';
            flyingElement.style.zIndex = '2000';
            flyingElement.style.boxShadow = '0 8px 32px rgba(0, 0, 0, 0.3)';
            document.body.appendChild(flyingElement);

            const cartBtn = document.getElementById('cartIconBtn');
            const cartRect = cartBtn.getBoundingClientRect();

            setTimeout(() => {
                flyingElement.style.transition = 'all 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94)';
                flyingElement.style.left = cartRect.left + 'px';
                flyingElement.style.top = cartRect.top + 'px';
                flyingElement.style.width = '30px';
                flyingElement.style.height = '30px';
                flyingElement.style.opacity = '0';
            }, 10);

            setTimeout(() => {
                flyingElement.remove();
                showSuccessState(button);
            }, 800);
        }

        function showSuccessState(button) {
            const originalText = button.innerHTML;
            button.innerHTML = '✓ Added to Cart';
            button.style.backgroundColor = '#4CAF50';
            button.style.color = 'white';
            button.disabled = true;

            setTimeout(() => {
                button.innerHTML = originalText;
                button.style.backgroundColor = '';
                button.style.color = '';
                button.disabled = false;
            }, 1000);
        }

        // Login popup for non-logged-in users
        function showLoginPopup() {
            const popup = document.createElement('div');
            popup.className = 'login-popup';
            popup.innerHTML = `
                <div class="popup-content">
                    <div class="popup-close" onclick="this.closest('.login-popup').remove()">✕</div>
                    <h2>Login Required</h2>
                    <p>You need to be logged in to add items to your cart.</p>
                    <div class="popup-buttons">
                        <a href="login.php" class="btn-login">Login</a>
                        <a href="register.php" class="btn-register">Register</a>
                    </div>
                </div>
            `;
            document.body.appendChild(popup);

            // Close popup when clicking outside
            popup.addEventListener('click', (e) => {
                if (e.target === popup) {
                    popup.remove();
                }
            });
        }
        // FILTERING AND SORTING - OPTIMIZED
        const shopGrid = document.querySelector('.shop-grid');
        const categoryFilters = document.querySelectorAll('.category-filter');
        const sortSelect = document.getElementById('sortSelect');

        // Validate elements exist
        if (!shopGrid || !sortSelect) {
            console.warn('Shop grid or sort select not found');
        } else {
            // Store all original products once
            const allProductCards = Array.from(shopGrid.querySelectorAll('.shop-product-card'));
            let filterTimeout; // Debounce timer

            function filterAndSort() {
                // Clear any pending filter operations
                clearTimeout(filterTimeout);

                const selectedCategory = document.querySelector('input[name="category"]:checked')?.value || 'all';
                const sortValue = sortSelect?.value || 'default';

                // Filter products
                let filtered = allProductCards.filter(card => {
                    const category = card.getAttribute('data-category');
                    return selectedCategory === 'all' || category === selectedCategory;
                });

                // Sort products
                if (sortValue === 'price-low') {
                    filtered.sort((a, b) => 
                        parseFloat(a.getAttribute('data-price') || 0) - parseFloat(b.getAttribute('data-price') || 0)
                    );
                } else if (sortValue === 'price-high') {
                    filtered.sort((a, b) => 
                        parseFloat(b.getAttribute('data-price') || 0) - parseFloat(a.getAttribute('data-price') || 0)
                    );
                }

                // Hide all cards first
                allProductCards.forEach(card => {
                    card.style.display = 'none';
                    card.style.opacity = '0';
                });

                // Debounce the display update
                filterTimeout = setTimeout(() => {
                    // Show only filtered cards
                    filtered.forEach((card, index) => {
                        card.style.display = '';
                        card.style.transition = 'opacity 0.4s ease';
                        card.style.opacity = '0';
                        
                        // Staggered fade in
                        setTimeout(() => {
                            card.style.opacity = '1';
                        }, index * 30);
                    });

                    // Reset transitions after animation
                    setTimeout(() => {
                        filtered.forEach(card => {
                            card.style.transition = 'all 0.3s ease';
                        });
                    }, filtered.length * 30 + 400);
                }, 50);
            }

            // Attach listeners
            categoryFilters.forEach(filter => {
                filter.addEventListener('change', filterAndSort);
            });

            if (sortSelect) {
                sortSelect.addEventListener('change', filterAndSort);
            }

            // Initialize
            updateCartUI();

            // Initialize AOS
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    offset: 100,
                    once: true
                });
            }
        }
    </script>
</body>
</html>
