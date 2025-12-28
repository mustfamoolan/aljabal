@extends('layouts.representative', ['title' => 'إكمال الطلب'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">إكمال الطلب</h4>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('representative.orders.store') }}" method="POST" id="checkout-form">
    @csrf
    <div class="row">
        <div class="col-lg-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">معلومات الزبون</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اسم الزبون <span class="text-danger">*</span></label>
                            <input type="text" name="customer_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="text" name="customer_phone" class="form-control" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">عنوان الزبون <span class="text-danger">*</span></label>
                            <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف الثاني (اختياري)</label>
                            <input type="text" name="customer_phone_2" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رابط السوشال ميديا (اختياري)</label>
                            <input type="url" name="customer_social_media" class="form-control" placeholder="https://...">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">ملاحظات (اختياري)</label>
                            <textarea name="customer_notes" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">ملخص المنتجات</h5>
                </div>
                <div class="card-body">
                    <div id="checkout-items-container">
                        <!-- Items will be loaded from cart -->
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">ملخص الطلب</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>إجمالي المبلغ:</span>
                        <strong id="summary-total">0.00 د.ع</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>إجمالي الربح:</span>
                        <strong class="text-success" id="summary-profit">0.00 د.ع</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>عمولة التجهيز:</span>
                        <strong class="text-warning" id="summary-commission">0.00 د.ع</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">الربح الصافي:</span>
                        <strong class="text-primary fs-18" id="summary-net-profit">0.00 د.ع</strong>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <a href="{{ route('representative.orders.cart') }}" class="btn btn-secondary w-100 mb-2">
                        <iconify-icon icon="solar:arrow-left-bold-duotone"></iconify-icon>
                        العودة للسلة
                    </a>
                    <button type="submit" class="btn btn-success w-100" id="submit-order-btn">
                        <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                        تأكيد الطلب
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('script')
@vite(['resources/js/components/order-cart.js'])
<script>
    // Load cart items and populate form
    document.addEventListener('DOMContentLoaded', function() {
        const CART_KEY = 'order_cart';
        const cart = JSON.parse(localStorage.getItem(CART_KEY) || '[]');
        
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

        // Populate items in form
        const itemsContainer = document.getElementById('checkout-items-container');
        const itemsInput = document.createElement('div');
        itemsInput.id = 'items-input-container';
        
        cart.forEach((item, index) => {
            const itemDiv = document.createElement('div');
            itemDiv.className = 'd-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded';
            itemDiv.innerHTML = `
                <div>
                    <strong>${item.product_name}</strong>
                    <p class="text-muted mb-0 small">الكمية: ${item.quantity} | السعر: ${formatCurrency(item.customer_price)}</p>
                </div>
                <div class="text-end">
                    <strong>${formatCurrency(item.subtotal || 0)}</strong>
                    <p class="text-success mb-0 small">ربح: ${formatCurrency(item.profit_subtotal || 0)}</p>
                </div>
            `;
            itemsContainer.appendChild(itemDiv);

            // Add hidden inputs
            const productInput = document.createElement('input');
            productInput.type = 'hidden';
            productInput.name = `items[${index}][product_id]`;
            productInput.value = item.product_id;
            itemsInput.appendChild(productInput);

            const quantityInput = document.createElement('input');
            quantityInput.type = 'hidden';
            quantityInput.name = `items[${index}][quantity]`;
            quantityInput.value = item.quantity;
            itemsInput.appendChild(quantityInput);

            const priceInput = document.createElement('input');
            priceInput.type = 'hidden';
            priceInput.name = `items[${index}][customer_price]`;
            priceInput.value = item.customer_price;
            itemsInput.appendChild(priceInput);
        });

        document.getElementById('checkout-form').appendChild(itemsInput);

        // Calculate summary
        const total = cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
        const profit = cart.reduce((sum, item) => sum + (item.profit_subtotal || 0), 0);
        
        // Calculate commission from server
        fetch('{{ route("representative.orders.calculate-commission") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            body: JSON.stringify({
                total_amount: total
            })
        })
        .then(response => response.json())
        .then(data => {
            const commission = parseFloat(data.commission) || 0;
            const netProfit = Math.max(0, profit - commission);
            
            document.getElementById('summary-total').textContent = formatCurrency(total);
            document.getElementById('summary-profit').textContent = formatCurrency(profit);
            document.getElementById('summary-commission').textContent = formatCurrency(commission);
            document.getElementById('summary-net-profit').textContent = formatCurrency(netProfit);
        })
        .catch(error => {
            console.error('Error calculating commission:', error);
            // Fallback: show without commission
            document.getElementById('summary-total').textContent = formatCurrency(total);
            document.getElementById('summary-profit').textContent = formatCurrency(profit);
            document.getElementById('summary-commission').textContent = formatCurrency(0);
            document.getElementById('summary-net-profit').textContent = formatCurrency(profit);
        });
    });

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
</script>
@endsection

