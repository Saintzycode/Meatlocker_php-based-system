<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - MeatLocker</title>
    <link rel="stylesheet" href="frontend/css/styles.css">
    <link rel="stylesheet" href="frontend/css/cart.css">
    <link rel="stylesheet" href="assets/fonts/fonts.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDXZxhX4f3OfqDVfKDFuFeFhRc7PqQsN9M&libraries=places"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <style>
        .address-label-buttons {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        .label-btn {
            padding: 0.75rem 1.25rem;
            border: 2px solid #ddd;
            background: #fff;
            cursor: pointer;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .label-btn:hover {
            border-color: #473472;
            color: #473472;
        }
        .label-btn.active {
            background: #473472;
            color: #fff;
            border-color: #473472;
        }
        .pac-container {
            z-index: 10000;
            border-radius: 8px;
        }
        .pac-item {
            padding: 10px;
        }
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
</head>
<body>
    <?php session_start(); ?>
    <?php include 'templates/navbar.php'; ?>

    <!-- Custom Popup for Checkout -->
    <div id="checkoutPopup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 10000; align-items: center; justify-content: center;">
        <div style="background: white; padding: 2rem; border-radius: 12px; text-align: center; max-width: 400px; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3); animation: popupSlideIn 0.3s ease;">
            <div id="checkoutPopupIcon" style="font-size: 3rem; margin-bottom: 1rem;">✓</div>
            <h2 id="checkoutPopupTitle" style="color: #473472; margin: 0 0 0.5rem 0; font-family: 'Bebas Neue', 'Inter', sans-serif;">Success!</h2>
            <p id="checkoutPopupMessage" style="color: #666; margin: 0 0 1.5rem 0;">Your address has been saved successfully!</p>
            <button id="checkoutPopupBtn" onclick="closeCheckoutPopup()" style="background: linear-gradient(135deg, #473472, #6E8CFB); color: white; border: none; padding: 0.8rem 2rem; border-radius: 8px; font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: 0.5px;">OK</button>
        </div>
    </div>

    <section class="checkout-hero">
        <div class="checkout-hero-content">
            <h1>Complete Your Order</h1>
            <p>Secure checkout process</p>
        </div>
    </section>

    <section class="checkout-container">
        <div class="checkout-content">
            <!-- Order Summary -->
            <div class="checkout-summary">
                <h2>Order Summary</h2>
                <div class="order-items" id="orderItems">
                    <!-- Items will be populated here -->
                </div>
                <div class="order-total">
                    <h3>Total: <span id="orderTotal">₱0.00</span></h3>
                </div>
            </div>

            <!-- Checkout Form -->
            <div class="checkout-form">
                <form id="checkoutForm">
                    <!-- Address Section -->
                    <fieldset>
                        <legend>Address</legend>
                        
                        <!-- Saved Addresses -->
                        <div id="savedAddressesSection" style="margin-bottom: 1.5rem;">
                            <label style="display: block; margin-bottom: 0.75rem; font-weight: 600;">Select from saved addresses or add new:</label>
                            <div id="savedAddressesList" style="display: flex; flex-direction: column; gap: 0.75rem; margin-bottom: 1rem;"></div>
                            <button type="button" id="addNewAddressBtn" class="btn-add-address">+ Add New Address</button>
                        </div>

                        <!-- Address Form (shown when adding new) -->
                        <div id="addressForm" style="display: none; padding: 1.5rem; background: #f9f9f9; border-radius: 8px; margin-bottom: 1.5rem;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="fullName">Full Name *</label>
                                    <input type="text" id="fullName" name="fullName" value="<?= htmlspecialchars($_SESSION['username'] ?? '') ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email *</label>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group" style="flex: 1.5;">
                                    <label for="countryCode">Country Code *</label>
                                    <div style="display: flex; gap: 0.5rem;">
                                        <select id="countryCode" name="countryCode" required style="flex: 0 0 120px;">
                                            <option value="+63">🇵🇭 +63 (Philippines)</option>
                                            <option value="+1">🇺🇸 +1 (USA)</option>
                                            <option value="+44">🇬🇧 +44 (UK)</option>
                                            <option value="+81">🇯🇵 +81 (Japan)</option>
                                            <option value="+86">🇨🇳 +86 (China)</option>
                                            <option value="+88">🇹🇼 +886 (Taiwan)</option>
                                            <option value="+65">🇸🇬 +65 (Singapore)</option>
                                            <option value="+60">🇲🇾 +60 (Malaysia)</option>
                                            <option value="+66">🇹🇭 +66 (Thailand)</option>
                                            <option value="+84">🇻🇳 +84 (Vietnam)</option>
                                            <option value="+61">🇦🇺 +61 (Australia)</option>
                                            <option value="+64">🇳🇿 +64 (New Zealand)</option>
                                            <option value="+39">🇮🇹 +39 (Italy)</option>
                                            <option value="+33">🇫🇷 +33 (France)</option>
                                            <option value="+49">🇩🇪 +49 (Germany)</option>
                                            <option value="+34">🇪🇸 +34 (Spain)</option>
                                        </select>
                                        <input type="tel" id="phone" name="phone" placeholder="9XX-XXX-XXXX" required style="flex: 1;">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address">Street Address *</label>
                                    <input type="text" id="address" name="address" placeholder="Enter your street address..." required>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label for="state">State/Province *</label>
                                    <input type="text" id="state" name="state" required>
                                </div>
                                <div class="form-group">
                                    <label for="zip">ZIP/Postal Code *</label>
                                    <input type="text" id="zip" name="zip" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Address Label (Mark as favorite) *</label>
                                <div class="address-label-buttons">
                                    <button type="button" class="label-btn" data-label="Home" onclick="selectLabel(this, 'Home')">🏠 Home</button>
                                    <button type="button" class="label-btn" data-label="Office" onclick="selectLabel(this, 'Office')">🏢 Office</button>
                                    <button type="button" class="label-btn" data-label="Other" onclick="selectLabel(this, 'Other')">📍 Other</button>
                                </div>
                                <input type="hidden" id="addressLabel" name="addressLabel" required>
                            </div>

                            <div style="display: flex; gap: 1rem;">
                                <button type="button" id="saveAddressBtn" class="btn-save-address">Save Address</button>
                                <button type="button" id="cancelAddressBtn" class="btn-cancel-address">Cancel</button>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Payment Information -->
                    <fieldset>
                        <legend>Payment Method</legend>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="credit-card" checked>
                                <span class="payment-label">💳 Credit/Debit Card</span>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="paymentMethod" value="cod">
                                <span class="payment-label">📦 Cash on Delivery</span>
                            </label>
                        </div>

                        <!-- Card Details (shown when credit card is selected) -->
                        <div id="cardDetails" class="card-details">
                            <div class="form-group">
                                <label for="cardName">Cardholder Name</label>
                                <input type="text" id="cardName" name="cardName">
                            </div>
                            <div class="form-group">
                                <label for="cardNumber">Card Number</label>
                                <input type="text" id="cardNumber" name="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="expiry">Expiry Date</label>
                                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3">
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <!-- Submit Button -->
                    <button type="submit" class="checkout-button">Complete Purchase</button>
                    <a href="shop.php" class="continue-shopping">← Continue Shopping</a>
                </form>
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
        // Initialize all functionality when DOM is ready
        document.addEventListener('DOMContentLoaded', async function() {
            // Load cart from localStorage
            let cart = JSON.parse(localStorage.getItem('cart')) || [];
            let savedAddresses = JSON.parse(localStorage.getItem('meatlocker_addresses')) || [];
            let selectedAddressIndex = null;

            // Fix cart items that don't have product IDs by matching from server
            if (cart.length > 0 && !cart[0].id) {
                try {
                    const response = await fetch('backend/api/get_products.php');
                    const products = await response.json();
                    
                    cart = cart.map(item => {
                        if (!item.id) {
                            const matchedProduct = products.find(p => p.name === item.name);
                            if (matchedProduct) {
                                item.id = matchedProduct.id;
                            }
                        }
                        return item;
                    });
                    
                    localStorage.setItem('cart', JSON.stringify(cart));
                } catch (error) {
                    console.error('Error loading products:', error);
                }
            }

            // Hide cart button on checkout page
            const cartContainer = document.querySelector('.cart-container');
            if (cartContainer) {
                cartContainer.style.display = 'none';
            }

            // User menu dropdown
            const userMenuBtn = document.getElementById('userMenuBtn');
            const userDropdown = document.getElementById('userDropdown');
            
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

            // Display order summary
            function displayOrderSummary() {
                const orderItemsDiv = document.getElementById('orderItems');
                const orderTotalSpan = document.getElementById('orderTotal');

                if (cart.length === 0) {
                    orderItemsDiv.innerHTML = '<p class="empty-message">Your cart is empty. <a href="shop.php">Continue shopping</a></p>';
                    orderTotalSpan.textContent = '₱0.00';
                    return;
                }

                let totalHTML = '';
                let total = 0;

                cart.forEach((item, index) => {
                    const qty = item.quantity || 1;
                    const subtotal = parseFloat(item.price) * qty;
                    total += subtotal;

                    totalHTML += `
                        <div class="order-item">
                            <img src="assets/${item.image}" alt="${item.name}" class="order-item-image">
                            <div class="order-item-details">
                                <p class="order-item-name">${item.name}</p>
                                <p class="order-item-qty">Qty: ${qty}</p>
                            </div>
                            <p class="order-item-price">₱${subtotal.toFixed(2)}</p>
                        </div>
                    `;
                });

                orderItemsDiv.innerHTML = totalHTML;
                orderTotalSpan.textContent = '₱' + total.toFixed(2);
            }

            // Display saved addresses
            function displaySavedAddresses() {
                const savedAddressesList = document.getElementById('savedAddressesList');
                savedAddressesList.innerHTML = '';

                if (savedAddresses.length === 0) {
                    savedAddressesList.innerHTML = '<p style="color: #999;">No saved addresses yet</p>';
                    return;
                }

                savedAddresses.forEach((addr, index) => {
                    const addressCard = document.createElement('label');
                    addressCard.className = 'address-card';
                    addressCard.innerHTML = `
                        <input type="radio" name="savedAddress" value="${index}">
                        <div class="address-card-content">
                            <p class="address-label">${addr.label || 'Address'}</p>
                            <p class="address-text">${addr.address}, ${addr.city}, ${addr.state} ${addr.zip}</p>
                            <p class="address-contact">${addr.fullName} • ${addr.phone}</p>
                        </div>
                        <button type="button" class="btn-delete-address" data-index="${index}">Delete</button>
                    `;
                    savedAddressesList.appendChild(addressCard);
                });
            }

            // Handle address selection
            function handleAddressSelection(e) {
                if (e.target.name === 'savedAddress') {
                    selectedAddressIndex = parseInt(e.target.value);
                    const addr = savedAddresses[selectedAddressIndex];
                    document.getElementById('fullName').value = addr.fullName;
                    document.getElementById('email').value = addr.email;
                    document.getElementById('phone').value = addr.phone;
                    document.getElementById('address').value = addr.address;
                    document.getElementById('city').value = addr.city;
                    document.getElementById('state').value = addr.state;
                    document.getElementById('zip').value = addr.zip;
                    document.getElementById('addressForm').style.display = 'none';
                }
            }

            // Add address button click
            document.getElementById('addNewAddressBtn').addEventListener('click', (e) => {
                e.preventDefault();
                selectedAddressIndex = null;
                document.getElementById('addressForm').style.display = 'block';
                document.querySelectorAll('input[name="savedAddress"]').forEach(r => r.checked = false);
                // Clear form fields
                document.getElementById('address').value = '';
                document.getElementById('email').value = '';
                document.getElementById('phone').value = '';
                document.getElementById('city').value = '';
                document.getElementById('state').value = '';
                document.getElementById('zip').value = '';
                document.getElementById('addressLabel').value = '';
            });

            // Save new address
            document.getElementById('saveAddressBtn').addEventListener('click', (e) => {
                e.preventDefault();
                
                // Validate form
                const fullName = document.getElementById('fullName').value;
                const email = document.getElementById('email').value;
                const phone = document.getElementById('phone').value;
                const address = document.getElementById('address').value;
                const city = document.getElementById('city').value;
                const state = document.getElementById('state').value;
                const zip = document.getElementById('zip').value;
                
                if (!fullName || !email || !phone || !address || !city || !state || !zip) {
                    showCheckoutPopup('Incomplete Form', 'Please fill in all required fields', '⚠');
                    return;
                }

                const newAddress = {
                    label: document.getElementById('addressLabel').value || 'Address',
                    fullName: fullName,
                    email: email,
                    phone: phone,
                    address: address,
                    city: city,
                    state: state,
                    zip: zip
                };

                savedAddresses.push(newAddress);
                localStorage.setItem('meatlocker_addresses', JSON.stringify(savedAddresses));
                displaySavedAddresses();
                document.getElementById('addressForm').style.display = 'none';
                showCheckoutPopup('Address Saved!', 'Your address has been saved successfully!', '✓', () => {
                    location.reload();
                });
            });

            // Cancel adding address
            document.getElementById('cancelAddressBtn').addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('addressForm').style.display = 'none';
            });

            // Delete address
            document.addEventListener('click', (e) => {
                if (e.target.classList.contains('btn-delete-address')) {
                    const index = parseInt(e.target.getAttribute('data-index'));
                    savedAddresses.splice(index, 1);
                    localStorage.setItem('meatlocker_addresses', JSON.stringify(savedAddresses));
                    displaySavedAddresses();
                }
            });

            // Listen for address selection changes
            document.addEventListener('change', handleAddressSelection);

            // Generate E-Receipt
            function generateEReceipt(formData) {
                const receiptHTML = `
                    <div id="ereceipt" style="background: white; padding: 2rem; max-width: 600px; font-family: Arial, sans-serif;">
                        <div style="text-align: center; margin-bottom: 1.5rem; border-bottom: 2px solid #333; padding-bottom: 1rem;">
                            <h1 style="margin: 0; color: #473472;">🥩 MeatLocker</h1>
                            <p style="margin: 0.5rem 0 0 0; color: #666;">E-RECEIPT</p>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <p style="margin: 0; font-weight: bold;">ORDER DETAILS</p>
                            <p style="margin: 0.25rem 0; color: #666;">Order ID: #${Date.now()}</p>
                            <p style="margin: 0.25rem 0; color: #666;">Date: ${new Date().toLocaleString()}</p>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <p style="margin: 0; font-weight: bold;">DELIVERY ADDRESS</p>
                            <p style="margin: 0.25rem 0;">${formData.fullName}</p>
                            <p style="margin: 0.25rem 0;">${formData.address}</p>
                            <p style="margin: 0.25rem 0;">${formData.city}, ${formData.state} ${formData.zip}</p>
                            <p style="margin: 0.25rem 0;">📞 ${formData.phone}</p>
                        </div>

                        <div style="margin-bottom: 1.5rem; border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; padding: 1rem 0;">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr style="border-bottom: 1px solid #ddd;">
                                        <th style="text-align: left; padding: 0.5rem 0;">Item</th>
                                        <th style="text-align: center; padding: 0.5rem 0;">Qty</th>
                                        <th style="text-align: right; padding: 0.5rem 0;">Price</th>
                                        <th style="text-align: right; padding: 0.5rem 0;">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${cart.map(item => {
                                        const qty = item.quantity || 1;
                                        const subtotal = parseFloat(item.price) * qty;
                                        return `
                                            <tr>
                                                <td style="padding: 0.5rem 0;">${item.name}</td>
                                                <td style="text-align: center; padding: 0.5rem 0;">${qty}</td>
                                                <td style="text-align: right; padding: 0.5rem 0;">₱${parseFloat(item.price).toFixed(2)}</td>
                                                <td style="text-align: right; padding: 0.5rem 0;">₱${subtotal.toFixed(2)}</td>
                                            </tr>
                                        `;
                                    }).join('')}
                                </tbody>
                            </table>
                        </div>

                        <div style="margin-bottom: 1.5rem; text-align: right;">
                            <p style="margin: 0.5rem 0;"><span style="font-weight: bold;">TOTAL:</span> <span style="font-size: 1.5rem; color: #473472;">₱${cart.reduce((sum, item) => sum + (parseFloat(item.price) * (item.quantity || 1)), 0).toFixed(2)}</span></p>
                        </div>

                        <div style="margin-bottom: 1.5rem;">
                            <p style="margin: 0; font-weight: bold;">PAYMENT METHOD</p>
                            <p style="margin: 0.25rem 0; color: #666;">${formData.paymentMethod}</p>
                        </div>

                        <div style="text-align: center; margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #ddd; color: #999; font-size: 0.9rem;">
                            <p style="margin: 0;">Thank you for your purchase!</p>
                            <p style="margin: 0.25rem 0;">MeatLocker © 2025</p>
                        </div>
                    </div>
                `;
                return receiptHTML;
            }

            // Initialize address autocomplete when form is shown
            const addNewAddressBtn = document.getElementById('addNewAddressBtn');
            if (addNewAddressBtn) {
                addNewAddressBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    document.getElementById('addressForm').style.display = 'block';
                });
            }

            // Handle address label button selection
            window.selectLabel = function(button, label) {
                document.querySelectorAll('.label-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                button.classList.add('active');
                document.getElementById('addressLabel').value = label;
            };

            // Handle payment method change
            document.querySelectorAll('input[name="paymentMethod"]').forEach(radio => {
                radio.addEventListener('change', (e) => {
                    const cardDetails = document.getElementById('cardDetails');
                    if (e.target.value === 'credit-card') {
                        cardDetails.style.display = 'block';
                        document.getElementById('cardName').required = true;
                        document.getElementById('cardNumber').required = true;
                        document.getElementById('expiry').required = true;
                        document.getElementById('cvv').required = true;
                    } else {
                        cardDetails.style.display = 'none';
                        document.getElementById('cardName').required = false;
                        document.getElementById('cardNumber').required = false;
                        document.getElementById('expiry').required = false;
                        document.getElementById('cvv').required = false;
                    }
                });
            });

            // Handle form submission
            document.getElementById('checkoutForm').addEventListener('submit', (e) => {
                e.preventDefault();

                if (cart.length === 0) {
                    alert('Your cart is empty!');
                    return;
                }

                // Get form data
                const formData = {
                    fullName: document.getElementById('fullName').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    address: document.getElementById('address').value,
                    city: document.getElementById('city').value,
                    state: document.getElementById('state').value,
                    zip: document.getElementById('zip').value,
                    paymentMethod: document.querySelector('input[name="paymentMethod"]:checked').value
                };

                // Send order to backend to process stock reduction
                fetch('backend/api/process_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ cart: cart })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(orderResult => {
                    console.log('Order result:', orderResult);
                    if (!orderResult.success) {
                        alert('Error processing order: ' + orderResult.message);
                        return;
                    }

                    try {
                        // Generate e-receipt HTML
                        const receiptHTML = generateEReceipt(formData);

                        // Create order ID for success message
                        const orderId = Date.now();

                    // Create success modal
                    const successModal = document.createElement('div');
                    successModal.id = 'successModal';
                    successModal.style.cssText = `
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background-color: rgba(0, 0, 0, 0.7);
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        z-index: 2000;
                    `;

                    const modalContent = document.createElement('div');
                    modalContent.style.cssText = `
                        background: white;
                        border-radius: 12px;
                        max-width: 700px;
                        max-height: 85vh;
                        overflow-y: auto;
                        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                        width: 95%;
                    `;

                    // Success header
                    const successHeader = document.createElement('div');
                    successHeader.style.cssText = `
                        background: linear-gradient(135deg, #473472, #6E8CFB);
                        color: white;
                        padding: 2rem;
                        text-align: center;
                        border-radius: 12px 12px 0 0;
                    `;
                    successHeader.innerHTML = `
                        <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
                        <h2 style="margin: 0; font-size: 1.8rem;">Order Successful!</h2>
                        <p style="margin: 0.5rem 0 0 0; opacity: 0.9;">Thank you for your purchase</p>
                        <p style="margin: 0.5rem 0 0 0; font-size: 0.9rem; opacity: 0.8;">Order ID: #${orderId}</p>
                    `;

                    // E-receipt section
                    const receiptSection = document.createElement('div');
                    receiptSection.style.cssText = `
                        padding: 2rem;
                    `;
                    receiptSection.innerHTML = receiptHTML;

                    // Action buttons
                    const buttonsContainer = document.createElement('div');
                    buttonsContainer.style.cssText = `
                        display: flex;
                        gap: 1rem;
                        padding: 0 2rem 2rem 2rem;
                        justify-content: center;
                        flex-wrap: wrap;
                    `;

                    const printButton = document.createElement('button');
                    printButton.textContent = '📥 Download Receipt';
                    printButton.style.cssText = `
                        padding: 0.75rem 1.5rem;
                        background-color: #473472;
                        color: white;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 600;
                        transition: all 0.3s ease;
                    `;
                    printButton.addEventListener('mouseover', () => {
                        printButton.style.backgroundColor = '#35245A';
                        printButton.style.transform = 'translateY(-2px)';
                    });
                    printButton.addEventListener('mouseout', () => {
                        printButton.style.backgroundColor = '#473472';
                        printButton.style.transform = 'translateY(0)';
                    });
                    printButton.addEventListener('click', async () => {
                        const receiptElement = document.getElementById('ereceipt');
                        const canvas = await html2canvas(receiptElement, {
                            backgroundColor: '#ffffff',
                            scale: 2,
                            logging: false
                        });
                        const image = canvas.toDataURL('image/png');
                        const link = document.createElement('a');
                        link.href = image;
                        link.download = `MeatLocker-Receipt-${orderId}.png`;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                    });

                    // Auto-download receipt when success modal appears
                    setTimeout(async () => {
                        const receiptElement = document.getElementById('ereceipt');
                        if (receiptElement) {
                            try {
                                const canvas = await html2canvas(receiptElement, {
                                    backgroundColor: '#ffffff',
                                    scale: 2,
                                    logging: false
                                });
                                const image = canvas.toDataURL('image/png');
                                const link = document.createElement('a');
                                link.href = image;
                                link.download = `MeatLocker-Receipt-${orderId}.png`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                            } catch (error) {
                                console.error('Error auto-downloading receipt:', error);
                            }
                        }
                    }, 800);

                    const continueButton = document.createElement('button');
                    continueButton.textContent = '👜 Continue Shopping';
                    continueButton.style.cssText = `
                        padding: 0.75rem 1.5rem;
                        background-color: #6E8CFB;
                        color: white;
                        border: none;
                        border-radius: 8px;
                        cursor: pointer;
                        font-weight: 600;
                        transition: all 0.3s ease;
                    `;
                    continueButton.addEventListener('mouseover', () => {
                        continueButton.style.backgroundColor = '#5B77E8';
                        continueButton.style.transform = 'translateY(-2px)';
                    });
                    continueButton.addEventListener('mouseout', () => {
                        continueButton.style.backgroundColor = '#6E8CFB';
                        continueButton.style.transform = 'translateY(0)';
                    });
                    continueButton.addEventListener('click', () => {
                        localStorage.removeItem('cart');
                        window.location.href = 'shop.php';
                    });

                    buttonsContainer.appendChild(printButton);
                    buttonsContainer.appendChild(continueButton);

                    modalContent.appendChild(successHeader);
                    modalContent.appendChild(receiptSection);
                    modalContent.appendChild(buttonsContainer);
                    successModal.appendChild(modalContent);

                    document.body.appendChild(successModal);
                    } catch (receiptError) {
                        console.error('Error generating receipt:', receiptError);
                        alert('Order processed but there was an error generating the receipt. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    alert('An error occurred while processing your order. Please try again.\n\nError: ' + error.message);
                });
            });

            // Initialize displays
            displayOrderSummary();
            displaySavedAddresses();
        });

        // Custom Popup Functions
        function showCheckoutPopup(title, message, icon = '✓', callback = null) {
            document.getElementById('checkoutPopupTitle').textContent = title;
            document.getElementById('checkoutPopupMessage').textContent = message;
            document.getElementById('checkoutPopupIcon').textContent = icon;
            document.getElementById('checkoutPopup').style.display = 'flex';
            
            const popupBtn = document.getElementById('checkoutPopupBtn');
            popupBtn.onclick = function() {
                closeCheckoutPopup();
                if (callback) callback();
            };
        }

        function closeCheckoutPopup() {
            document.getElementById('checkoutPopup').style.display = 'none';
        }
    </script>
</body>
</html>
