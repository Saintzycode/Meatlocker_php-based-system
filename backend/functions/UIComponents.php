<?php
/**
 * UIComponent Interface
 * Base interface for all reusable UI components
 */
interface IUIComponent {
    public function render();
}

/**
 * Dropdown Class
 * Handles dropdown rendering and behavior for cart, user menu, etc.
 * Eliminates duplicate dropdown code across shop.php and index.php
 */
class Dropdown implements IUIComponent {
    private $id;
    private $title;
    private $items;
    private $type;

    public function __construct($id, $title, $type = 'default') {
        $this->id = $id;
        $this->title = $title;
        $this->type = $type;
        $this->items = [];
    }

    public function addItem($html) {
        $this->items[] = $html;
        return $this;
    }

    public function setItems($items) {
        $this->items = $items;
        return $this;
    }

    public function render() {
        $itemsHtml = implode('', $this->items);
        
        $html = '<div class="dropdown" id="' . htmlspecialchars($this->id) . '">' .
                '<div class="dropdown-header"><h3>' . htmlspecialchars($this->title) . '</h3></div>' .
                '<div class="dropdown-items">' . $itemsHtml . '</div>' .
                '</div>';
        
        return $html;
    }
}

/**
 * Cart Component Class
 * Reusable shopping cart UI component
 */
class CartComponent implements IUIComponent {
    private $is_logged_in;
    private $cart_items;

    public function __construct($is_logged_in = false) {
        $this->is_logged_in = $is_logged_in;
        $this->cart_items = [];
    }

    public function render() {
        $logged_in_class = $this->is_logged_in ? 'logged-in' : 'logged-out';
        
        $html = '<div class="cart-container">' .
                '<button class="cart-icon-btn" id="cartIconBtn">' .
                '<i class="fas fa-shopping-cart"></i>' .
                '<span class="cart-badge" id="cartBadge" style="display: none;">0</span>' .
                '</button>' .
                '<div class="cart-dropdown" id="cartDropdown">' .
                '<div class="cart-header"><h3>Shopping Cart</h3></div>' .
                '<div class="cart-items" id="cartItems">' .
                '<div class="cart-empty"><div class="cart-empty-icon">🛒</div><p>Your cart is empty</p></div>' .
                '</div>' .
                '<div class="cart-footer" id="cartFooter" style="display: none;">' .
                '<div class="cart-total">' .
                '<span class="cart-total-label">Total:</span>' .
                '<span class="cart-total-price" id="cartTotal">₱0.00</span>' .
                '</div>' .
                '<button class="checkout-btn">Proceed to Checkout</button>' .
                '</div>' .
                '</div>' .
                '</div>';
        
        return $html;
    }

    public function isLoggedIn() {
        return $this->is_logged_in;
    }
}

/**
 * UserMenu Component Class
 * Reusable user menu UI component
 */
class UserMenuComponent implements IUIComponent {
    private $username;
    private $role;

    public function __construct($username, $role = 'customer') {
        $this->username = htmlspecialchars($username);
        $this->role = $role;
    }

    public function render() {
        $html = '<div class="user-menu">' .
                '<button class="user-icon-btn" id="userMenuBtn">' .
                '<i class="fas fa-user-circle"></i>' .
                '</button>' .
                '<div class="user-dropdown" id="userDropdown">' .
                '<p class="user-greeting">Welcome, ' . $this->username . '</p>' .
                '<a href="logout.php" class="logout-link">Logout</a>' .
                '</div>' .
                '</div>';
        
        return $html;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getRole() {
        return $this->role;
    }
}

/**
 * ProductCard Component Class
 * Reusable product card component for consistent display
 */
class ProductCardComponent implements IUIComponent {
    private $product;
    private $badge;
    private $is_shop_page;

    public function __construct($product, $badge = null, $is_shop_page = false) {
        $this->product = $product;
        $this->badge = $badge;
        $this->is_shop_page = $is_shop_page;
    }

    public function render() {
        $card_class = $this->is_shop_page ? 'shop-product-card' : 'product-card';
        $data_attrs = $this->is_shop_page ? 
            'data-category="' . $this->product['category'] . '" ' .
            'data-price="' . $this->product['price'] . '" ' .
            'data-name="' . htmlspecialchars($this->product['name']) . '"' 
            : '';

        $badge_html = $this->badge ? 
            '<div class="product-badge' . ($this->badge['type'] === 'premium' ? ' premium' : '') . '">' . 
            htmlspecialchars($this->badge['text']) . 
            '</div>' 
            : '';

        $button_html = $this->is_shop_page ?
            '<button class="add-to-cart" data-name="' . htmlspecialchars($this->product['name']) . '" ' .
            'data-price="' . $this->product['price'] . '" ' .
            'data-image="images/' . basename($this->product['image']) . '">Add to Cart</button>'
            :
            '<a href="shop.php" class="add-to-cart" style="display: block; text-align: center; cursor: pointer;">View Product</a>';

        $html = '<div class="' . $card_class . '" ' . $data_attrs . '>' .
                $badge_html .
                '<div class="product-image-wrapper">' .
                '<img src="assets/' . $this->product['image'] . '" alt="' . $this->product['name'] . '" class="product-image">' .
                '<div class="product-overlay">' .
                '<p class="overlay-description">' . $this->product['desc'] . '</p>' .
                '</div>' .
                '</div>' .
                '<div class="product-info">' .
                '<h3>' . $this->product['name'] . '</h3>' .
                '<div class="product-price-section">' .
                '<span class="product-price">₱' . $this->product['price'] . '</span>' .
                '<span class="product-unit">/kg</span>' .
                '</div>' .
                $button_html .
                '</div>' .
                '</div>';
        
        return $html;
    }
}
?>
