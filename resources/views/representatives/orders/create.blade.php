@extends('layouts.representative', ['title' => 'طلب جديد'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-light-subtle">
            <div class="card-header border-0">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <h4 class="card-title mb-0">إنشاء طلب جديد</h4>
                        <p class="mb-0 text-muted mt-1">عرض جميع <span class="text-dark fw-semibold">{{ $products->total() }}</span> منتج</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-md-end mt-3 mt-md-0 d-flex gap-2 justify-content-end">
                            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filtersModal">
                                <iconify-icon icon="solar:filter-bold-duotone"></iconify-icon>
                                الفلاتر
                            </button>
                            <a href="{{ route('representative.orders.cart') }}" class="btn btn-primary position-relative">
                                <iconify-icon icon="solar:cart-large-3-bold-duotone" class="fs-18"></iconify-icon>
                                السلة
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">0</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Categories Slider -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="mb-3">الأقسام الرئيسية</h6>
                <div class="categories-slider-wrapper" style="position: relative;">
                    <div class="categories-slider d-flex gap-2 overflow-auto pb-2" style="scroll-behavior: smooth; -webkit-overflow-scrolling: touch;">
                        <button type="button"
                                class="btn btn-outline-primary category-filter-btn flex-shrink-0 {{ !request('category_id') ? 'active' : '' }}"
                                data-category-id=""
                                style="white-space: nowrap;">
                            <iconify-icon icon="solar:widget-4-bold-duotone"></iconify-icon>
                            جميع الأقسام
                        </button>
                        @foreach($mainCategories as $category)
                            <button type="button"
                                    class="btn btn-outline-primary category-filter-btn flex-shrink-0 {{ request('category_id') == $category->id ? 'active' : '' }}"
                                    data-category-id="{{ $category->id }}"
                                    style="white-space: nowrap;">
                                <iconify-icon icon="solar:folder-bold-duotone"></iconify-icon>
                                {{ $category->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Products Grid -->
<div class="row" id="products-container">
    @forelse($products as $product)
        <div class="col-md-6 col-xl-3 mb-4" data-product-id="{{ $product->id }}">
            <div class="card h-100 product-card position-relative">
                @if($product->images->isNotEmpty())
                    <img src="{{ storage_url($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="img-fluid" style="height: 250px; object-fit: cover;">
                @else
                    <div class="bg-light-subtle d-flex align-items-center justify-content-center" style="height: 250px;">
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                    </div>
                @endif
                <div class="card-body bg-light-subtle rounded-bottom">
                    <h6 class="card-title mb-2">
                        <a href="{{ route('representative.products.show', $product) }}" class="text-dark fw-medium fs-16">
                            {{ $product->name }}
                        </a>
                    </h6>
                    @if($product->sku)
                        <p class="text-muted mb-2 small">SKU: {{ $product->sku }}</p>
                    @endif
                    <div class="mb-2">
                        <h5 class="fw-semibold text-dark mb-0">
                            {{ format_currency($product->wholesale_price ?? 0) }}
                            <small class="text-muted fs-13">سعر الجملة</small>
                        </h5>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">
                            <iconify-icon icon="solar:box-bold-duotone" class="align-middle"></iconify-icon>
                            الكمية المتاحة: <strong>{{ $product->quantity }}</strong>
                        </small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small mb-1">الكمية</label>
                        <input type="number" min="1" max="{{ $product->quantity }}" value="1"
                               class="form-control form-control-sm quantity-input"
                               data-product-id="{{ $product->id }}"
                               data-max-quantity="{{ $product->quantity }}">
                    </div>
                    <div class="mt-3">
                        <div class="d-flex gap-2">
                            <a href="{{ route('representative.products.show', $product) }}" class="btn btn-outline-dark border border-secondary-subtle d-flex align-items-center justify-content-center gap-1 flex-grow-1">
                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                التفاصيل
                            </a>
                            <button type="button" class="btn btn-primary d-flex align-items-center justify-content-center gap-1 flex-grow-1 add-to-cart-btn"
                                    data-product-id="{{ $product->id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-wholesale-price="{{ $product->wholesale_price ?? 0 }}"
                                    data-retail-price="{{ $product->retail_price ?? 0 }}">
                                <iconify-icon icon="solar:cart-plus-bold-duotone"></iconify-icon>
                                إضافة
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-32"></iconify-icon>
                <p class="mb-0 mt-2">لا توجد منتجات متاحة</p>
            </div>
        </div>
    @endforelse
</div>

<!-- Pagination -->
@if($products->hasPages())
    <div class="row">
        <div class="col-12">
            <div class="py-3 border-top">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-end mb-0">
                        {{ $products->links() }}
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endif

<!-- Filters Modal -->
<div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title d-flex align-items-center gap-2" id="filtersModalLabel">
                    <iconify-icon icon="solar:filter-bold-duotone" class="fs-20"></iconify-icon>
                    الفلاتر المتقدمة
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form method="GET" action="{{ route('representative.orders.create') }}" id="filter-form">
                    <!-- Search -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">البحث</label>
                        <div class="search-bar">
                            <span><iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon></span>
                            <input type="text" name="search" class="form-control" placeholder="ابحث عن منتج، SKU، مؤلف، أو ناشر..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Category and Subcategory -->
                    <div class="mb-4">
                        <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#filter-categories" aria-expanded="true">
                            الأقسام
                            <iconify-icon icon="solar:alt-arrow-down-bold-duotone" class="ms-auto fs-20"></iconify-icon>
                        </a>
                        <div id="filter-categories" class="collapse show mt-2">
                            <div class="mb-3">
                                <label class="form-label small">القسم الرئيسي</label>
                                <select name="category_id" id="modal_category_id" class="form-select form-select-sm">
                                    <option value="">جميع الأقسام</option>
                                    @foreach($mainCategories as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">القسم الفرعي</label>
                                <select name="subcategory_id" id="modal_subcategory_id" class="form-select form-select-sm" {{ !request('category_id') ? 'disabled' : '' }}>
                                    <option value="">جميع الأقسام الفرعية</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" {{ request('subcategory_id') == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Publisher and Author -->
                    <div class="mb-4">
                        <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#filter-publisher-author" aria-expanded="true">
                            الناشر والمؤلف
                            <iconify-icon icon="solar:alt-arrow-down-bold-duotone" class="ms-auto fs-20"></iconify-icon>
                        </a>
                        <div id="filter-publisher-author" class="collapse show mt-2">
                            <div class="mb-3">
                                <label class="form-label small">دار النشر</label>
                                <select name="publisher" class="form-select form-select-sm">
                                    <option value="">جميع دور النشر</option>
                                    @foreach($publishers as $publisher)
                                        <option value="{{ $publisher }}" {{ request('publisher') == $publisher ? 'selected' : '' }}>
                                            {{ $publisher }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">المؤلف</label>
                                <select name="author" class="form-select form-select-sm">
                                    <option value="">جميع المؤلفين</option>
                                    @foreach($authors as $author)
                                        <option value="{{ $author }}" {{ request('author') == $author ? 'selected' : '' }}>
                                            {{ $author }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Tags and Product Type -->
                    <div class="mb-4">
                        <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#filter-tags-type" aria-expanded="true">
                            التاغات ونوع المنتج
                            <iconify-icon icon="solar:alt-arrow-down-bold-duotone" class="ms-auto fs-20"></iconify-icon>
                        </a>
                        <div id="filter-tags-type" class="collapse show mt-2">
                            <div class="mb-3">
                                <label class="form-label small">التاغات</label>
                                <select name="tags[]" id="modal_filter_tags" class="form-select form-select-sm" multiple data-choices data-choices-removeItem>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ in_array($tag->id, request('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small">نوع المنتج</label>
                                <select name="product_type" class="form-select form-select-sm">
                                    <option value="">جميع الأنواع</option>
                                    <option value="original" {{ request('product_type') == 'original' ? 'selected' : '' }}>أصلي</option>
                                    <option value="normal" {{ request('product_type') == 'normal' ? 'selected' : '' }}>عادي</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="mb-4">
                        <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#filter-price" aria-expanded="true">
                            نطاق السعر
                            <iconify-icon icon="solar:alt-arrow-down-bold-duotone" class="ms-auto fs-20"></iconify-icon>
                        </a>
                        <div id="filter-price" class="collapse show mt-2">
                            <div class="row g-2">
                                <div class="col-6">
                                    <label class="form-label small">الحد الأدنى</label>
                                    <input type="number" name="min_price" class="form-control form-control-sm" step="0.01" min="0" placeholder="0.00" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <label class="form-label small">الحد الأقصى</label>
                                    <input type="number" name="max_price" class="form-control form-control-sm" step="0.01" min="0" placeholder="0.00" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a href="{{ route('representative.orders.create') }}" class="btn btn-secondary">
                    <iconify-icon icon="solar:refresh-bold-duotone"></iconify-icon>
                    إعادة تعيين
                </a>
                <button type="button" class="btn btn-primary" onclick="document.getElementById('filter-form').submit();">
                    <iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon>
                    تطبيق الفلاتر
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/components/order-cart.js', 'resources/js/pages/representative-orders-create.js'])
<style>
.categories-slider {
    scrollbar-width: thin;
    scrollbar-color: #dee2e6 transparent;
}
.categories-slider::-webkit-scrollbar {
    height: 6px;
}
.categories-slider::-webkit-scrollbar-track {
    background: transparent;
}
.categories-slider::-webkit-scrollbar-thumb {
    background-color: #dee2e6;
    border-radius: 3px;
}
.categories-slider::-webkit-scrollbar-thumb:hover {
    background-color: #adb5bd;
}
.category-filter-btn.active {
    background-color: var(--bs-primary);
    color: white;
    border-color: var(--bs-primary);
}
.search-bar {
    position: relative;
    display: flex;
    align-items: center;
}
.search-bar span {
    position: absolute;
    right: 12px;
    z-index: 10;
    color: #6c757d;
}
.search-bar input {
    padding-right: 40px;
}
.product-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
</style>
<script>
    // Add to cart functionality (without price - price will be added in cart page)
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const productId = parseInt(this.dataset.productId);
            const productName = this.dataset.productName;
            const wholesalePrice = parseFloat(this.dataset.wholesalePrice) || 0;
            const retailPrice = parseFloat(this.dataset.retailPrice) || 0;
            const quantityInput = document.querySelector(`.quantity-input[data-product-id="${productId}"]`);
            const quantity = parseInt(quantityInput.value) || 1;

            if (quantity <= 0) {
                alert('يرجى إدخال كمية صحيحة');
                return;
            }

            // Get cart from localStorage
            const CART_KEY = 'order_cart';
            const cart = JSON.parse(localStorage.getItem(CART_KEY) || '[]');

            // Check if product already exists
            const existingIndex = cart.findIndex(item => item.product_id === productId);

            if (existingIndex >= 0) {
                // Update quantity
                cart[existingIndex].quantity += quantity;
            } else {
                // Add new item (without customer_price - will be added in cart page)
                cart.push({
                    product_id: productId,
                    product_name: productName,
                    wholesale_price: wholesalePrice,
                    retail_price: retailPrice, // سعر المفرد للمنتج
                    customer_price: 0, // Will be set in cart page
                    quantity: quantity,
                    profit_per_item: 0,
                    subtotal: 0,
                    profit_subtotal: 0
                });
            }

            // Save cart
            localStorage.setItem(CART_KEY, JSON.stringify(cart));

            // Update cart count
            const badge = document.getElementById('cart-count');
            if (badge) {
                const count = cart.reduce((sum, item) => sum + item.quantity, 0);
                badge.textContent = count;
                badge.style.display = count > 0 ? 'block' : 'none';
            }

            // Show success message
            const toast = document.createElement('div');
            toast.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <strong>تم!</strong> تم إضافة المنتج إلى السلة.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        });
    });

</script>
@endsection
