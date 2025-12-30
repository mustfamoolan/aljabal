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
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم الهاتف الثاني (اختياري)</label>
                            <input type="text" name="customer_phone_2" class="form-control">
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">عنوان الزبون <span class="text-danger">*</span></label>
                            <textarea name="customer_address" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المحافظة <span class="text-danger">*</span></label>
                            <select name="governorate_id" id="governorate_id" class="form-select" required>
                                <option value="">اختر المحافظة</option>
                                @foreach($governorates as $governorate)
                                    <option value="{{ $governorate->id }}">{{ $governorate->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المنطقة</label>
                            <select name="district_id" id="district_id" class="form-select">
                                <option value="">اختر المنطقة</option>
                            </select>
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

            <!-- Gifts Selection -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">الهدايا (اختياري)</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اختر هدية</label>
                            <select name="gift_id" id="gift_id" class="form-select">
                                <option value="">لا يوجد</option>
                            </select>
                            <small class="text-muted">اختياري - يمكن إضافة هدية للطلب</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اختر بوكس هدية</label>
                            <select name="gift_box_id" id="gift_box_id" class="form-select">
                                <option value="">لا يوجد</option>
                            </select>
                            <small class="text-muted">اختياري - يعتمد على عدد الكتب في الطلب</small>
                        </div>
                    </div>
                    <div class="alert alert-info mb-0" id="gift-price-info" style="display: none;">
                        <strong>سعر الهدايا المختارة: <span id="gift-price-display">0.00 د.ع</span></strong>
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
                    <div class="d-flex justify-content-between mb-2">
                        <span>سعر التوصيل:</span>
                        <strong class="text-info" id="summary-delivery-fee">0.00 د.ع</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2" id="summary-gift-price-row" style="display: none;">
                        <span>سعر الهدايا:</span>
                        <strong class="text-warning" id="summary-gift-price">0.00 د.ع</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">السعر الكلي:</span>
                        <strong class="text-dark fs-18" id="summary-grand-total">0.00 د.ع</strong>
                    </div>
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

        // Helper function for formatting currency
        function formatCurrency(amount) {
            const num = parseFloat(amount) || 0;
            const formatted = num % 1 === 0 ? num.toString() : num.toFixed(2).replace(/\.?0+$/, '');
            const parts = formatted.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.') + ' د.ع';
        }

        // Calculate summary
        let deliveryFee = 0;
        let basraId = null;

        // Get Basra ID for delivery fee calculation
        const governorateSelect = document.getElementById('governorate_id');
        console.log('Governorate select element:', governorateSelect);
        if (governorateSelect) {
            console.log('Governorate options count:', governorateSelect.options.length);
            const basraOption = Array.from(governorateSelect.options).find(opt => opt.text === 'البصرة');
            if (basraOption) {
                basraId = parseInt(basraOption.value);
                console.log('Basra ID found:', basraId);
            }
        }

        // Gift settings
        const gifts = @json($gifts ?? []);
        const giftBoxes = @json($giftBoxes ?? []);
        let giftPrice = 0;

        console.log('Gifts loaded:', gifts);
        console.log('Gifts count:', gifts.length);
        console.log('Gift boxes loaded:', giftBoxes);
        console.log('Gift boxes count:', giftBoxes.length);

        // Populate gifts select
        const giftSelect = document.getElementById('gift_id');
        if (giftSelect && gifts && gifts.length > 0) {
            gifts.forEach(gift => {
                const option = document.createElement('option');
                option.value = gift.id;
                option.textContent = `${gift.name} - ${formatCurrency(gift.price)}`;
                giftSelect.appendChild(option);
            });
            console.log('Gifts populated:', gifts.length);
        } else {
            console.log('No gifts to populate');
        }

        // Calculate total books in cart
        function getTotalBooks() {
            return cart.reduce((sum, item) => sum + (item.quantity || 0), 0);
        }

        // Populate gift boxes select based on book count
        function updateGiftBoxes() {
            const giftBoxSelect = document.getElementById('gift_box_id');
            if (!giftBoxSelect || !giftBoxes) return;

            const totalBooks = getTotalBooks();
            giftBoxSelect.innerHTML = '<option value="">لا يوجد</option>';

            if (totalBooks > 0) {
                giftBoxes.forEach(box => {
                    const minBooks = box.min_books || 0;
                    const maxBooks = box.max_books || 999999;
                    
                    if (totalBooks >= minBooks && totalBooks <= maxBooks) {
                        const option = document.createElement('option');
                        option.value = box.id;
                        let text = `${box.name} - ${formatCurrency(box.box_price)}`;
                        if (minBooks || maxBooks) {
                            text += ` (${minBooks || '0'}-${maxBooks || '∞'} كتاب)`;
                        }
                        option.textContent = text;
                        giftBoxSelect.appendChild(option);
                    }
                });
            }
        }

        // Initial populate gift boxes
        updateGiftBoxes();
        updateGiftPriceDisplay();

        // Update gift boxes when cart changes (if items are added/removed)
        // This will be called when items are loaded
        setTimeout(() => {
            updateGiftBoxes();
        }, 100);

        // Calculate gift price
        function calculateGiftPrice() {
            const selectedGiftId = giftSelect?.value;
            const selectedGiftBoxId = document.getElementById('gift_box_id')?.value;
            
            let price = 0;
            
            if (selectedGiftId) {
                const gift = gifts.find(g => g.id == selectedGiftId);
                if (gift) {
                    price += parseFloat(gift.price) || 0;
                }
            }
            
            if (selectedGiftBoxId) {
                const box = giftBoxes.find(b => b.id == selectedGiftBoxId);
                if (box) {
                    price += parseFloat(box.box_price) || 0;
                }
            }
            
            giftPrice = price;
            return price;
        }

        // Update gift price display
        function updateGiftPriceDisplay() {
            const price = calculateGiftPrice();
            const giftPriceInfo = document.getElementById('gift-price-info');
            const giftPriceDisplay = document.getElementById('gift-price-display');
            const summaryGiftPriceRow = document.getElementById('summary-gift-price-row');
            const summaryGiftPrice = document.getElementById('summary-gift-price');
            
            if (price > 0) {
                if (giftPriceInfo) giftPriceInfo.style.display = 'block';
                if (giftPriceDisplay) giftPriceDisplay.textContent = formatCurrency(price);
                if (summaryGiftPriceRow) summaryGiftPriceRow.style.display = 'flex';
                if (summaryGiftPrice) summaryGiftPrice.textContent = formatCurrency(price);
            } else {
                if (giftPriceInfo) giftPriceInfo.style.display = 'none';
                if (summaryGiftPriceRow) summaryGiftPriceRow.style.display = 'none';
            }
        }

        // Handle gift selection change
        if (giftSelect) {
            giftSelect.addEventListener('change', function() {
                updateGiftPriceDisplay();
                updateSummary();
            });
        }

        // Handle gift box selection change
        const giftBoxSelect = document.getElementById('gift_box_id');
        if (giftBoxSelect) {
            giftBoxSelect.addEventListener('change', function() {
                updateGiftPriceDisplay();
                updateSummary();
            });
        }

        // Function to update summary
        function updateSummary() {
            const itemsTotal = cart.reduce((sum, item) => sum + (item.subtotal || 0), 0);
            const itemsProfit = cart.reduce((sum, item) => sum + (item.profit_subtotal || 0), 0);
            const grandTotal = itemsTotal + deliveryFee + giftPrice;

            // Calculate commission from server
            fetch('{{ route("representative.orders.calculate-commission") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({
                    total_amount: itemsTotal
                })
            })
            .then(response => response.json())
            .then(data => {
                const commission = parseFloat(data.commission) || 0;
                const netProfit = Math.max(0, itemsProfit - commission);
                
                document.getElementById('summary-total').textContent = formatCurrency(itemsTotal);
                document.getElementById('summary-profit').textContent = formatCurrency(itemsProfit);
                document.getElementById('summary-commission').textContent = formatCurrency(commission);
                document.getElementById('summary-delivery-fee').textContent = formatCurrency(deliveryFee);
                if (giftPrice > 0) {
                    document.getElementById('summary-gift-price').textContent = formatCurrency(giftPrice);
                }
                document.getElementById('summary-grand-total').textContent = formatCurrency(grandTotal);
                document.getElementById('summary-net-profit').textContent = formatCurrency(netProfit);
            })
            .catch(error => {
                console.error('Error calculating commission:', error);
                // Fallback: show without commission
                document.getElementById('summary-total').textContent = formatCurrency(itemsTotal);
                document.getElementById('summary-profit').textContent = formatCurrency(itemsProfit);
                document.getElementById('summary-commission').textContent = formatCurrency(0);
                document.getElementById('summary-delivery-fee').textContent = formatCurrency(deliveryFee);
                if (giftPrice > 0) {
                    document.getElementById('summary-gift-price').textContent = formatCurrency(giftPrice);
                }
                document.getElementById('summary-grand-total').textContent = formatCurrency(grandTotal);
                document.getElementById('summary-net-profit').textContent = formatCurrency(itemsProfit);
            });
        }

        // Initial summary update
        updateSummary();

        // Handle governorate change
        const districtSelect = document.getElementById('district_id');

        if (governorateSelect) {
            console.log('Adding change event listener to governorate select');
            governorateSelect.addEventListener('change', function() {
                const governorateId = this.value;
                console.log('Governorate changed to:', governorateId);
                
                // Reset district
                districtSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                
                // Calculate delivery fee
                if (governorateId) {
                    // Get Basra ID if not set
                    if (!basraId) {
                        const basraOption = Array.from(this.options).find(opt => opt.text === 'البصرة');
                        if (basraOption) {
                            basraId = parseInt(basraOption.value);
                        }
                    }
                    
                    // Calculate delivery fee: Basra = 3000, others = 5000
                    deliveryFee = parseInt(governorateId) === basraId ? 3000 : 5000;
                } else {
                    deliveryFee = 0;
                }
                
                // Update summary
                updateSummary();
                
                // Load districts
                if (governorateId && districtSelect) {
                    // Show loading state
                    districtSelect.disabled = true;
                    districtSelect.innerHTML = '<option value="">جاري التحميل...</option>';
                    
                    // Build URL - use base URL + route path
                    const baseUrl = '{{ url("/") }}';
                    const url = baseUrl + '/representative/orders/districts/' + governorateId;
                    console.log('Fetching districts from:', url);
                    
                    fetch(url, {
                        method: 'GET',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                        },
                        credentials: 'same-origin'
                    })
                        .then(response => {
                            console.log('Response status:', response.status);
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(districts => {
                            console.log('Districts received:', districts);
                            districtSelect.innerHTML = '<option value="">اختر المنطقة</option>';
                            districtSelect.disabled = false;
                            
                            if (districts && Array.isArray(districts) && districts.length > 0) {
                                districts.forEach(district => {
                                    const option = document.createElement('option');
                                    option.value = district.id;
                                    option.textContent = district.name;
                                    districtSelect.appendChild(option);
                                });
                            } else {
                                districtSelect.innerHTML = '<option value="">لا توجد مناطق متاحة</option>';
                            }
                        })
                        .catch(error => {
                            console.error('Error loading districts:', error);
                            districtSelect.innerHTML = '<option value="">خطأ في تحميل المناطق</option>';
                            districtSelect.disabled = false;
                        });
                }
            });
        }
    });
</script>
@endsection

