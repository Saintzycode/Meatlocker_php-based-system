<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About MeatLocker - Premium Frozen Goods</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="frontend/css/cart.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php session_start(); ?>
    <?php include 'templates/navbar.php'; ?>

    <section class="about-hero">
        <div class="about-hero-content">
            <h1>About MeatLocker</h1>
            <p>Your trusted source for premium frozen meats and seafood</p>
        </div>
    </section>

    <section class="about-container">
        <div class="about-section">
            <h2>Our Story</h2>
            <p>MeatLocker is dedicated to bringing premium quality frozen meats and seafood directly to your doorstep. Founded with a passion for excellence, we've partnered with trusted suppliers to ensure every product meets our high standards of quality, freshness, and taste.</p>
            <p>Our mission is to make premium meats and seafood accessible to everyone, with the convenience of online shopping and fast delivery.</p>
        </div>

        <div class="about-section">
            <h2>Our Commitment</h2>
            <ul>
                <li>✓ 100% Fresh & Premium Quality Products</li>
                <li>✓ Fast & Reliable Delivery</li>
                <li>✓ Competitive Pricing</li>
                <li>✓ Professional Customer Service</li>
                <li>✓ Secure Online Shopping</li>
                <li>✓ Multiple Payment Options</li>
            </ul>
        </div>

        <div class="about-divider"></div>

        <div class="about-section">
            <h2>Privacy Policy</h2>
            <p><strong>Information We Collect:</strong></p>
            <ul>
                <li>Personal information (name, email, address) for orders</li>
                <li>Payment information (securely processed)</li>
                <li>Browsing and purchase history</li>
            </ul>
            <p><strong>How We Use Your Information:</strong></p>
            <ul>
                <li>Process and deliver your orders</li>
                <li>Send order updates and receipts</li>
                <li>Improve our services and user experience</li>
                <li>Send promotional offers (with your consent)</li>
            </ul>
            <p><strong>Data Security:</strong> We use industry-standard encryption and security measures to protect your personal information. Your data is never shared with third parties without your consent.</p>
        </div>

        <div class="about-section">
            <h2>Terms of Service</h2>
            <h3>1. Use of Website</h3>
            <p>You agree to use this website only for lawful purposes and in a way that does not infringe upon the rights of others or restrict their use and enjoyment of the website.</p>

            <h3>2. Product Information</h3>
            <p>We strive to provide accurate product descriptions and pricing. However, we do not warrant that product descriptions, pricing, or other content is accurate, complete, or free from errors.</p>

            <h3>3. Ordering & Payment</h3>
            <ul>
                <li>All prices are in Philippine Pesos (₱)</li>
                <li>We accept all major payment methods</li>
                <li>Orders are confirmed upon successful payment</li>
                <li>We reserve the right to refuse any order</li>
            </ul>

            <h3>4. Delivery</h3>
            <ul>
                <li>Delivery times are estimates and not guaranteed</li>
                <li>Customers are responsible for providing accurate delivery address</li>
                <li>Delivery must be to a residential or business address</li>
                <li>Undelivered items may be subject to restocking fees</li>
            </ul>

            <h3>5. Returns & Refunds</h3>
            <ul>
                <li>Products must be returned within 7 days of receipt</li>
                <li>Items must be in original condition and packaging</li>
                <li>Damaged items receive full refunds</li>
                <li>Refunds are processed within 5-7 business days</li>
            </ul>

            <h3>6. Limitation of Liability</h3>
            <p>MeatLocker shall not be liable for any indirect, incidental, special, consequential, or punitive damages resulting from your use of the website or products.</p>

            <h3>7. Changes to Terms</h3>
            <p>We reserve the right to modify these terms at any time. Continued use of the website constitutes acceptance of the updated terms.</p>
        </div>

        <div class="about-section">
            <h2>Contact Us</h2>
            <p>Have questions? We're here to help!</p>
            <p><strong>Email:</strong> support@meatlocker.com</p>
            <p><strong>Phone:</strong> +63 995 537 1731</p>
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

        updateCartUI();
    </script>
</body>
</html>
