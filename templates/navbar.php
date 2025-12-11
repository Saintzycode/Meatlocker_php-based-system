<?php
// Navbar template - included on all pages
?>
<nav class="navbar">
    <div class="nav-content">
        <a href="index.php" class="logo-brand">
            <img src="assets/icons/meatlocker-logo.png" alt="MeatLocker Logo" class="logo-image">
            <span class="logo-text">MeatLocker</span>
        </a>
        <div class="nav-links">
            <a href="index.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'class="active"' : ''; ?>>Home</a>
            <a href="shop.php" <?php echo (basename($_SERVER['PHP_SELF']) === 'shop.php') ? 'class="active"' : ''; ?>>Shop</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="user-menu">
                    <button class="user-icon-btn" id="userMenuBtn">
                        <i class="fas fa-user-circle"></i>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <p class="user-greeting">Welcome, <?= htmlspecialchars($_SESSION['username']) ?></p>
                        <a href="logout.php" class="logout-link">Logout</a>
                    </div>
                </div>
                <div class="cart-container">
                    <button class="cart-icon-btn" id="cartIconBtn">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cartBadge" style="display: none;">0</span>
                    </button>
                    <div class="cart-dropdown" id="cartDropdown">
                        <div class="cart-header"><h3>Shopping Cart</h3></div>
                        <div class="cart-items" id="cartItems">
                            <div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Your cart is empty</p></div>
                        </div>
                        <div class="cart-footer" id="cartFooter" style="display: none;">
                            <div class="cart-total">
                                <span class="cart-total-label">Total:</span>
                                <span class="cart-total-price" id="cartTotal">₱0.00</span>
                            </div>
                            <a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
