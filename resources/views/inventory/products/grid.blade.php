@extends('layouts.vertical', ['title' => 'Products Grid'])

@section('css')
@vite(['node_modules/nouislider/dist/nouislider.min.css'])
@endsection

@section('content')

<div class="row">
    <div class="col-lg-3">
        <div class="card bg-light-subtle">
            <div class="card-header border-0">
                <div class="search-bar me-3 mb-1">
                    <span><i class="bx bx-search-alt"></i></span>
                    <input type="search" class="form-control" id="search" placeholder="بحث..." value="{{ request('search') }}">
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body border-light">
                <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#categories" aria-expanded="false" aria-controls="categories">الفئات
                    <i class='bx bx-chevron-down ms-auto fs-20'></i>
                </a>
                <div id="categories" class="collapse show">
                    <div class="categories-list d-flex flex-column gap-2 mt-2">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input category-filter" id="all-categories" value="" checked>
                            <label class="form-check-label" for="all-categories">جميع الفئات</label>
                        </div>
                        @foreach($categories ?? [] as $category)
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input category-filter" id="category-{{ $category->id }}" value="{{ $category->id }}">
                            <label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4">
                    <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#price" aria-expanded="false" aria-controls="price">السعر
                        <i class='bx bx-chevron-down ms-auto fs-20'></i>
                    </a>
                    <div id="price" class="collapse show">
                        <div class="categories-list d-flex flex-column gap-2 mt-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="all-price">
                                <label class="form-check-label" for="all-price">جميع الأسعار</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="price-1">
                                <label class="form-check-label" for="price-1">أقل من $200</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="price-2">
                                <label class="form-check-label" for="price-2">$200 - $500</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="price-3">
                                <label class="form-check-label" for="price-3">$500 - $800</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="price-4">
                                <label class="form-check-label" for="price-4">$800 - $1000</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="price-5">
                                <label class="form-check-label" for="price-5">أكثر من $1000</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="#" class="btn-link d-flex align-items-center text-dark bg-light p-2 rounded fw-medium fs-16 mb-0" data-bs-toggle="collapse" data-bs-target="#stock" aria-expanded="false" aria-controls="stock">المخزون
                        <i class='bx bx-chevron-down ms-auto fs-20'></i>
                    </a>
                    <div id="stock" class="collapse show">
                        <div class="categories-list d-flex flex-column gap-2 mt-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stock-all">
                                <label class="form-check-label" for="stock-all">الكل</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stock-in">
                                <label class="form-check-label" for="stock-in">متوفر</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stock-low">
                                <label class="form-check-label" for="stock-low">منخفض</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="stock-out">
                                <label class="form-check-label" for="stock-out">نفد</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <a href="#!" class="btn btn-primary w-100" onclick="applyFilters()">تطبيق</a>
            </div>
        </div>
    </div>
    <div class="col-lg-9">
        <div class="card bg-light-subtle">
            <div class="card-header border-0">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-6">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item fw-medium"><a href="{{ route('inventory.products.index') }}" class="text-dark">المنتجات</a></li>
                            <li class="breadcrumb-item active">عرض الشبكة</li>
                        </ol>
                        <p class="mb-0 text-muted">عرض <span class="text-dark fw-semibold">{{ $products->total() ?? 0 }}</span> منتج</p>
                    </div>
                    <div class="col-lg-6">
                        <div class="text-md-end mt-3 mt-md-0">
                            <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary me-1">
                                <iconify-icon icon="solar:list-bold-duotone" class="me-1"></iconify-icon>
                                قائمة
                            </a>
                            <a href="{{ route('inventory.products.create') }}" class="btn btn-success me-1">
                                <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                                منتج جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            @forelse($products as $product)
            <div class="col-md-6 col-xl-3">
                <div class="card">
                    @if($product->images->isNotEmpty())
                        <img src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="img-fluid">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <iconify-icon icon="solar:box-bold-duotone" class="fs-48 text-muted"></iconify-icon>
                        </div>
                    @endif
                    <div class="card-body bg-light-subtle rounded-bottom">
                        <a href="{{ route('inventory.products.show', $product) }}" class="text-dark fw-medium fs-16">{{ $product->name }}</a>
                        @if($product->sku)
                            <p class="text-muted mb-1 fs-13">SKU: {{ $product->sku }}</p>
                        @endif
                        <div class="my-1">
                            <span class="badge bg-{{ $product->product_type->value === 'original' ? 'primary' : 'secondary' }}-subtle text-{{ $product->product_type->value === 'original' ? 'primary' : 'secondary' }} py-1 px-2">
                                {{ $product->product_type->label() }}
                            </span>
                            @if($product->min_quantity !== null && $product->quantity <= $product->min_quantity)
                                <span class="badge bg-danger-subtle text-danger py-1 px-2">
                                    <iconify-icon icon="solar:danger-triangle-bold-duotone"></iconify-icon>
                                    منخفض
                                </span>
                            @endif
                        </div>
                        <h4 class="fw-semibold text-dark mt-2">
                            @if($product->retail_price)
                                {{ format_currency($product->retail_price) }}
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </h4>
                        <div class="mt-3">
                            <div class="d-flex gap-2">
                                <div class="dropdown">
                                    <a href="#" class="btn btn-soft-primary border border-primary-subtle" data-bs-toggle="dropdown" aria-expanded="false">
                                        <iconify-icon icon="solar:menu-dots-bold-duotone"></iconify-icon>
                                    </a>
                                    <div class="dropdown-menu">
                                        <a href="{{ route('inventory.products.show', $product) }}" class="dropdown-item">عرض</a>
                                        <a href="{{ route('inventory.products.edit', $product) }}" class="dropdown-item">تعديل</a>
                                        <button type="button"
                                                class="dropdown-item text-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-action="{{ route('admin.inventory.products.destroy', $product) }}"
                                                data-message="هل أنت متأكد من حذف هذا المنتج؟">
                                            حذف
                                        </button>
                                    </div>
                                </div>
                                <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-outline-dark border border-secondary-subtle d-flex align-items-center justify-content-center gap-1 w-100">
                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                    عرض
                                </a>
                            </div>
                        </div>
                    </div>
                    <span class="position-absolute top-0 end-0 p-3">
                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}-subtle text-{{ $product->is_active ? 'success' : 'danger' }}">
                            {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                        </span>
                    </span>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-64 text-muted mb-3"></iconify-icon>
                        <p class="text-muted mb-0">لا توجد منتجات</p>
                    </div>
                </div>
            </div>
            @endforelse
        </div>

        @if($products->hasPages())
        <div class="py-3 border-top">
            <nav aria-label="Page navigation example">
                {!! $products->links() !!}
            </nav>
        </div>
        @endif
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/ecommerce-product.js'])
<script>
    function applyFilters() {
        const search = document.getElementById('search').value;
        const categoryIds = Array.from(document.querySelectorAll('.category-filter:checked')).map(cb => cb.value).filter(v => v);
        const params = new URLSearchParams();

        if (search) params.append('search', search);
        if (categoryIds.length) params.append('category_id', categoryIds[0]);

        window.location.href = '{{ route('inventory.products.grid') }}?' + params.toString();
    }
</script>
@endsection
