<?php
/**
 * Components.php
 * Reusable template components for common UI elements
 * Reduces HTML duplication across pages
 */

/**
 * Product card component
 */
function productCard($product, $showAddCart = true) {
    $id = $product['id'];
    $name = esc($product['name']);
    $price = number_format($product['price'], 2);
    $stock = $product['stock'];
    $weight = esc($product['weight']);
    $image = getImagePath($product['image']);
    $description = esc($product['description']);
    $stockClass = getStockClass($stock);
    $stockText = getStockText($stock);
    $isOutOfStock = $stock <= 0;
    
    $addCartBtn = '';
    if ($showAddCart) {
        $btnText = $isOutOfStock ? 'Out of Stock' : 'Add to Cart';
        $btnDisabled = $isOutOfStock ? 'disabled' : '';
        $addCartBtn = <<<HTML
            <button class="add-to-cart" data-id="$id" data-name="$name" data-price="{$product['price']}" data-image="images/{$product['image']}" $btnDisabled>
                $btnText
            </button>
        HTML;
    }
    
    return <<<HTML
        <div class="shop-product-card" data-category="$id" data-price="{$product['price']}" data-name="$name">
            <div class="product-image-wrapper">
                <img src="$image" alt="$name" class="product-image">
                <div class="product-overlay">
                    <p class="overlay-description">$description</p>
                </div>
            </div>
            <div class="product-info">
                <h3>$name <span style="font-size: 0.85rem; color: #999;">($weight)</span></h3>
                <div class="product-stock-info">
                    <span class="product-stock $stockClass">
                        $stockText
                    </span>
                </div>
                <div class="product-price-section">
                    <span class="product-price">₱$price</span>
                </div>
                $addCartBtn
            </div>
        </div>
    HTML;
}

/**
 * Alert component
 */
function alert($message, $type = 'info', $dismissible = true) {
    $colors = [
        'success' => ['bg' => '#D1FAE5', 'border' => '#10B981', 'text' => '#065F46'],
        'error' => ['bg' => '#FEE2E2', 'border' => '#EF4444', 'text' => '#7F1D1D'],
        'info' => ['bg' => '#DBEAFE', 'border' => '#3B82F6', 'text' => '#1E40AF'],
        'warning' => ['bg' => '#FEF3C7', 'border' => '#F59E0B', 'text' => '#92400E']
    ];
    
    $color = $colors[$type] ?? $colors['info'];
    $dismissBtn = $dismissible ? '<button onclick="this.parentElement.style.display=\'none\';" style="float:right; background:none; border:none; cursor:pointer; font-size:1.5rem;">&times;</button>' : '';
    
    return <<<HTML
        <div style="
            background-color: {$color['bg']};
            border-left: 4px solid {$color['border']};
            color: {$color['text']};
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        ">
            $dismissBtn
            $message
        </div>
    HTML;
}

/**
 * Form input component
 */
function formInput($name, $label, $type = 'text', $value = '', $required = false, $placeholder = '') {
    $reqAttr = $required ? 'required' : '';
    $reqMark = $required ? ' *' : '';
    $value = esc($value);
    $placeholder = esc($placeholder);
    
    return <<<HTML
        <div class="form-group">
            <label for="$name">$label$reqMark</label>
            <input type="$type" id="$name" name="$name" value="$value" placeholder="$placeholder" $reqAttr>
        </div>
    HTML;
}

/**
 * Form select component
 */
function formSelect($name, $label, $options, $selected = '', $required = false) {
    $reqAttr = $required ? 'required' : '';
    $reqMark = $required ? ' *' : '';
    
    $optionsHtml = '';
    foreach ($options as $value => $text) {
        $sel = $value == $selected ? 'selected' : '';
        $optionsHtml .= "<option value=\"$value\" $sel>" . esc($text) . "</option>\n";
    }
    
    return <<<HTML
        <div class="form-group">
            <label for="$name">$label$reqMark</label>
            <select id="$name" name="$name" $reqAttr>
                $optionsHtml
            </select>
        </div>
    HTML;
}

/**
 * Form textarea component
 */
function formTextarea($name, $label, $value = '', $rows = 4, $required = false, $placeholder = '') {
    $reqAttr = $required ? 'required' : '';
    $reqMark = $required ? ' *' : '';
    $value = esc($value);
    $placeholder = esc($placeholder);
    
    return <<<HTML
        <div class="form-group">
            <label for="$name">$label$reqMark</label>
            <textarea id="$name" name="$name" rows="$rows" placeholder="$placeholder" $reqAttr>$value</textarea>
        </div>
    HTML;
}

/**
 * Button component
 */
function button($text, $type = 'button', $class = 'btn btn-primary', $onclick = '', $disabled = false) {
    $disabledAttr = $disabled ? 'disabled' : '';
    $onclickAttr = $onclick ? "onclick=\"$onclick\"" : '';
    
    return <<<HTML
        <button type="$type" class="$class" $onclickAttr $disabledAttr>$text</button>
    HTML;
}

/**
 * Badge component
 */
function badge($text, $type = 'primary') {
    $colors = [
        'primary' => '#3B82F6',
        'success' => '#10B981',
        'danger' => '#EF4444',
        'warning' => '#F59E0B'
    ];
    
    $bgColor = $colors[$type] ?? $colors['primary'];
    
    return <<<HTML
        <span style="
            background-color: $bgColor;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
        ">$text</span>
    HTML;
}

/**
 * Price display component
 */
function priceDisplay($price, $currency = '₱') {
    return sprintf('<span style="font-size: 1.25rem; color: #473472; font-weight: bold;">%s%s</span>', 
        $currency, 
        number_format($price, 2)
    );
}

/**
 * Loading spinner component
 */
function spinner($text = 'Loading...') {
    return <<<HTML
        <div style="text-align: center; padding: 2rem;">
            <div style="display: inline-block; width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #473472; border-radius: 50%; animation: spin 1s linear infinite;"></div>
            <p style="margin-top: 1rem; color: #666;">$text</p>
        </div>
        <style>
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    HTML;
}

/**
 * Table header component
 */
function tableHeader($columns) {
    $headers = '';
    foreach ($columns as $col) {
        $headers .= "<th style=\"padding: 1rem; text-align: left; border: 1px solid #ddd; background: #f5f5f5; font-weight: 600;\">" . esc($col) . "</th>";
    }
    
    return <<<HTML
        <thead>
            <tr>
                $headers
            </tr>
        </thead>
    HTML;
}

/**
 * Modal component
 */
function modal($id, $title, $content, $actions = '') {
    return <<<HTML
        <div id="$id" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
            <div class="modal-content" style="background-color: white; margin: 5% auto; padding: 2rem; border-radius: 8px; width: 90%; max-width: 600px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <span class="close" onclick="document.getElementById('$id').style.display='none';" style="color: #aaa; float: right; font-size: 2rem; cursor: pointer;">&times;</span>
                <h2>$title</h2>
                <div style="margin: 1.5rem 0;">
                    $content
                </div>
                <div style="text-align: right; margin-top: 2rem;">
                    $actions
                </div>
            </div>
        </div>
    HTML;
}
?>
