/*
Order Cart Management
Manages cart items in localStorage
*/
document.addEventListener('DOMContentLoaded', function() {
    const CART_KEY = 'order_cart';

    // Initialize cart from localStorage
    function getCart() {
        return JSON.parse(localStorage.getItem(CART_KEY) || '[]');
    }

    // Save cart to localStorage
    function saveCart(cart) {
        localStorage.setItem(CART_KEY, JSON.stringify(cart));
        updateCartCount();
    }

    // Update cart count badge
    function updateCartCount() {
        const cart = getCart();
        const count = cart.reduce((sum, item) => sum + item.quantity, 0);
        const badge = document.getElementById('cart-count');
        if (badge) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
    }

    // Add to cart (without price - price will be added in cart page)
    // This is handled in the create page script now

    // Load cart items on cart page
    if (document.getElementById('cart-items-container')) {
        loadCartItems();
    }

    // Load cart items on checkout page
    if (document.getElementById('checkout-items-container')) {
        loadCheckoutItems();
    }

    function loadCartItems() {
        const cart = getCart();
        const container = document.getElementById('cart-items-container');
        const checkoutBtn = document.getElementById('checkout-btn');
        
        if (cart.length === 0) {
            container.innerHTML = `
                <div class="text-center py-5">
                    <iconify-icon icon="solar:cart-large-3-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                    <p class="text-muted mt-3">السلة فارغة</p>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">إضافة منتجات</a>
                </div>
            `;
            if (checkoutBtn) checkoutBtn.style.display = 'none';
            return;
        }

        let html = '';
        let total = 0;
        let profit = 0;

        cart.forEach((item, index) => {
            // Calculate if customer_price is not set
            const customerPrice = item.customer_price || 0;
            const profitPerItem = Math.max(0, customerPrice - (item.wholesale_price || 0));
            const subtotal = customerPrice * item.quantity;
            const profitSubtotal = profitPerItem * item.quantity;
            
            total += subtotal;
            profit += profitSubtotal;
            
            html += `
                <div class="card mb-3 cart-item" data-index="${index}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">${item.product_name}</h6>
                                <div class="d-flex gap-3 mb-2">
                                    <small class="text-muted">سعر الجملة: ${formatCurrency(item.wholesale_price || 0)}</small>
                                    <small class="text-muted">الكمية: ${item.quantity}</small>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-danger remove-item-btn" data-index="${index}">
                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small">سعر البيع للزبون <span class="text-danger">*</span></label>
                                <input type="number" 
                                       step="0.01" 
                                       min="${item.wholesale_price || 0}" 
                                       class="form-control form-control-sm customer-price-input" 
                                       data-index="${index}"
                                       data-wholesale-price="${item.wholesale_price || 0}"
                                       value="${customerPrice}"
                                       placeholder="أدخل السعر"
                                       required>
                                <small class="text-muted">يجب أن يكون أكبر من أو يساوي سعر الجملة</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small">الربح للوحدة</label>
                                <input type="text" 
                                       class="form-control form-control-sm profit-per-item-display" 
                                       data-index="${index}"
                                       value="${formatCurrency(profitPerItem)}"
                                       readonly
                                       style="background-color: #f8f9fa;">
                            </div>
                        </div>
                        <div class="mt-2 d-flex justify-content-between align-items-center">
                            <small class="text-muted">الإجمالي: <strong>${formatCurrency(subtotal)}</strong></small>
                            <small class="text-success">الربح الإجمالي: <strong>${formatCurrency(profitSubtotal)}</strong></small>
                        </div>
                    </div>
                </div>
            `;
        });

        container.innerHTML = html;
        
        // Update summary
        updateCartSummary(total, profit);
        
        if (checkoutBtn) {
            // Enable checkout only if all items have customer_price
            const allPricesSet = cart.every(item => item.customer_price > 0);
            checkoutBtn.style.display = allPricesSet ? 'block' : 'none';
            if (!allPricesSet) {
                checkoutBtn.disabled = true;
            }
        }

        // Add remove item listeners
        document.querySelectorAll('.remove-item-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const index = parseInt(this.dataset.index);
                const cart = getCart();
                cart.splice(index, 1);
                saveCart(cart);
                loadCartItems();
            });
        });

        // Add price input listeners
        document.querySelectorAll('.customer-price-input').forEach(input => {
            input.addEventListener('input', function() {
                const index = parseInt(this.dataset.index);
                const cart = getCart();
                const wholesalePrice = parseFloat(this.dataset.wholesalePrice) || 0;
                const customerPrice = parseFloat(this.value) || 0;
                const quantity = cart[index].quantity;

                if (customerPrice < wholesalePrice) {
                    this.classList.add('is-invalid');
                    return;
                } else {
                    this.classList.remove('is-invalid');
                }

                // Update cart item
                const profitPerItem = Math.max(0, customerPrice - wholesalePrice);
                cart[index].customer_price = customerPrice;
                cart[index].profit_per_item = profitPerItem;
                cart[index].subtotal = customerPrice * quantity;
                cart[index].profit_subtotal = profitPerItem * quantity;

                saveCart(cart);

                // Update display
                const profitDisplay = document.querySelector(`.profit-per-item-display[data-index="${index}"]`);
                if (profitDisplay) {
                    profitDisplay.value = formatCurrency(profitPerItem);
                }

                // Update item totals
                const cartItem = this.closest('.cart-item');
                const subtotalDisplay = cartItem.querySelector('.text-muted strong');
                const profitSubtotalDisplay = cartItem.querySelector('.text-success strong');
                if (subtotalDisplay) {
                    subtotalDisplay.textContent = formatCurrency(cart[index].subtotal);
                }
                if (profitSubtotalDisplay) {
                    profitSubtotalDisplay.textContent = formatCurrency(cart[index].profit_subtotal);
                }

                // Recalculate and update summary
                const newTotal = cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
                const newProfit = cart.reduce((sum, item) => sum + (item.profit_subtotal || 0), 0);
                updateCartSummary(newTotal, newProfit);

                // Check if all prices are set
                const allPricesSet = cart.every(item => item.customer_price > 0);
                if (checkoutBtn) {
                    checkoutBtn.style.display = allPricesSet ? 'block' : 'none';
                    checkoutBtn.disabled = !allPricesSet;
                }
            });
        });
    }

    function updateCartSummary(total, profit) {
        const totalEl = document.getElementById('cart-total');
        const profitEl = document.getElementById('cart-profit');
        const netProfitEl = document.getElementById('cart-net-profit');
        
        if (totalEl) totalEl.textContent = formatCurrency(total);
        if (profitEl) profitEl.textContent = formatCurrency(profit);
        if (netProfitEl) netProfitEl.textContent = formatCurrency(profit);
    }

    function loadCheckoutItems() {
        const cart = getCart();
        if (cart.length === 0) {
            window.location.href = '{{ route("representative.orders.cart") }}';
            return;
        }

        // Check if all items have customer_price set
        const allPricesSet = cart.every(item => item.customer_price > 0);
        if (!allPricesSet) {
            alert('يرجى إدخال سعر البيع لجميع المنتجات في صفحة السلة أولاً');
            window.location.href = '{{ route("representative.orders.cart") }}';
            return;
        }

        const total = cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
        const profit = cart.reduce((sum, item) => sum + (item.profit_subtotal || 0), 0);

        document.getElementById('summary-total').textContent = formatCurrency(total);
        document.getElementById('summary-profit').textContent = formatCurrency(profit);
        document.getElementById('summary-net-profit').textContent = formatCurrency(profit);
    }

    function formatCurrency(amount) {
        // Remove trailing zeros
        const num = parseFloat(amount);
        const formatted = num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
        
        // Format with thousand separators (keep English numerals)
        const parts = formatted.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const withSeparators = parts.join('.');
        
        return withSeparators + ' د.ع';
    }

    // Initialize cart count
    updateCartCount();
});

