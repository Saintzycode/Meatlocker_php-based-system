<?php
session_start();
require_once 'backend/functions/ProductManager.php';
$productManager = new ProductManager();
$products = $productManager->loadProducts();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeatLocker - Frozen Goods Online Shop</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="frontend/css/cart.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <?php include 'templates/navbar.php'; ?>

    <section class="hero">
        <div class="slider">
            <div class="slide-track">
                <div class="slide" style="background-image: url('assets/images/slide1.jpg')">
                    <div class="slide-overlay"></div>
                </div>
                <div class="slide" style="background-image: url('assets/images/slide2.jpg')">
                    <div class="slide-overlay"></div>
                </div>
            </div>
            <!-- Slide indicators -->
            <div class="slide-indicators">
                <span class="indicator active" data-slide="0"></span>
                <span class="indicator" data-slide="1"></span>
            </div>
        </div>
        <div class="hero-content">
            <div class="hero-badge">Premium Quality Guaranteed</div>
            <h1 class="hero-title">Premium Frozen Quality</h1>
            <p class="hero-subtitle">Discover our selection of premium meats and seafood, delivered right to your door.</p>
            <a href="#products" class="cta-button">Shop Now</a>
        </div>
        <!-- Floating particles for ambiance -->
        <div class="hero-particles">
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
        </div>
    </section>

    <section class="products" id="products">
        <div class="products-header">
            <h2 class="section-title">Premium Selection</h2>
            <p class="section-subtitle">Discover our carefully curated collection of premium meats and seafood</p>
        </div>
        
        <div class="products-grid">
            <?php foreach (array_slice($products, 0, 4) as $product): ?>
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
                    <h3><?= htmlspecialchars($product['name']) ?> <span style="font-size: 0.85rem; color: #999;">(<?= htmlspecialchars($product['weight'] ?? '1kg') ?>)</span></h3>
                    <div class="product-stock-info">
                        <span class="product-stock <?= $product['stock'] > 10 ? 'in-stock' : ($product['stock'] > 0 ? 'low-stock' : 'out-of-stock') ?>">
                            Stock: <?= $product['stock'] ?> left
                        </span>
                    </div>
                    <div class="product-price-section">
                        <span class="product-price">₱<?= number_format($product['price'], 2) ?></span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="shop.php" class="cta-button">View All Products</a>
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
        // Hero slider - synchronized interactivity
        const indicators = document.querySelectorAll('.indicator');
        const slideTrack = document.querySelector('.slide-track');
        let currentSlide = 0;
        let slideInterval;
        const totalSlides = indicators.length;

        function updateSlide(index) {
            currentSlide = index % totalSlides;
            
            // Update slide position
            const offset = currentSlide * 50;
            slideTrack.style.transform = `translateX(-${offset}%)`;
            
            // Update indicator active state
            indicators.forEach((ind, i) => {
                ind.classList.toggle('active', i === currentSlide);
            });
        }

        function nextSlide() {
            updateSlide(currentSlide + 1);
        }

        // Indicator click listeners
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                clearInterval(slideInterval);
                updateSlide(index);
                startSlideshow();
            });
        });

        function startSlideshow() {
            slideInterval = setInterval(nextSlide, 8000);
        }

        // Initialize slideshow
        updateSlide(0);
        startSlideshow();

        // Pause slideshow on hover
        const slider = document.querySelector('.slider');
        slider.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        slider.addEventListener('mouseleave', () => {
            startSlideshow();
        });

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

        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                e.preventDefault();
                const target = document.querySelector(anchor.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });

        // Initialize
        updateCartUI();

        // Initialize AOS
        AOS.init({
            duration: 800,
            offset: 100,
            once: true
        });
    </script>
</body>
</html>
