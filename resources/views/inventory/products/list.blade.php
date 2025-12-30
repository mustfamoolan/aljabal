@extends('layouts.vertical', ['title' => 'Products List'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">All Products</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $products->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-success bg-opacity-10 rounded">
                        <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">In Stock</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $products->where('quantity', '>', 0)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-danger bg-opacity-10 rounded">
                        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-32 text-danger avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Low Stock</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">
                        @php
                            $lowStockCount = $products->filter(function($product) {
                                return $product->min_quantity !== null && $product->quantity <= $product->min_quantity;
                            })->count();
                        @endphp
                        {{ $lowStockCount }}
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-warning bg-opacity-10 rounded">
                        <iconify-icon icon="solar:box-minimalistic-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">Out of Stock</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $products->where('quantity', '<=', 0)->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('inventory.products.index') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن منتج..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="category_id" class="form-select">
                            <option value="">جميع الفئات الرئيسية</option>
                            @foreach(\App\Models\Category::whereNull('parent_id')->where('is_active', true)->get() as $category)
                                <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="tag_id" class="form-select">
                            <option value="">جميع التاغات</option>
                            @foreach(\App\Models\Tag::orderBy('name')->get() as $tag)
                                <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100">تصفية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">قائمة المنتجات</h4>

                <a href="{{ route('inventory.products.create') }}" class="btn btn-sm btn-primary">
                    <iconify-icon icon="solar:add-circle-bold-duotone" class="me-1"></iconify-icon>
                    إضافة منتج
                </a>

                <a href="{{ route('inventory.products.low-stock') }}" class="btn btn-sm btn-warning">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" class="me-1"></iconify-icon>
                    منخفضة المخزون
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th style="width: 20px;">
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input" id="customCheckAll">
                                        <label class="form-check-label" for="customCheckAll"></label>
                                    </div>
                                </th>
                                <th>المنتج</th>
                                <th>النوع</th>
                                <th>الفئة</th>
                                <th>المورد</th>
                                <th>الأسعار</th>
                                <th>المخزون</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td>
                                    <div class="form-check ms-1">
                                        <input type="checkbox" class="form-check-input product-checkbox" id="product-{{ $product->id }}" value="{{ $product->id }}">
                                        <label class="form-check-label" for="product-{{ $product->id }}">&nbsp;</label>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded bg-light avatar-md d-flex align-items-center justify-content-center">
                                            @if($product->images->isNotEmpty())
                                                <img src="{{ storage_url($product->images->first()->image_path) }}" alt="{{ $product->name }}" class="avatar-md">
                                            @else
                                                <iconify-icon icon="solar:box-bold-duotone" class="fs-20 text-muted"></iconify-icon>
                                            @endif
                                        </div>
                                        <div>
                                            <a href="{{ route('inventory.products.show', $product) }}" class="text-dark fw-medium fs-15">{{ $product->name }}</a>
                                            <p class="text-muted mb-0 mt-1 fs-13">
                                                @if($product->sku)
                                                    <span>SKU: </span>{{ $product->sku }}
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->product_type->value === 'original' ? 'primary' : 'secondary' }}-subtle text-{{ $product->product_type->value === 'original' ? 'primary' : 'secondary' }} py-1 px-2">
                                        {{ $product->product_type->label() }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($product->category)
                                            <span class="badge bg-info-subtle text-info py-1 px-2">{{ $product->category->name }}</span>
                                        @endif
                                        @if($product->subcategory)
                                            <span class="badge bg-secondary-subtle text-secondary py-1 px-2">{{ $product->subcategory->name }}</span>
                                        @endif
                                        @if(!$product->category && !$product->subcategory)
                                            <span class="text-muted">-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($product->supplier)
                                        <span class="text-muted">{{ $product->supplier->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($product->purchase_price)
                                            <small class="text-muted">شراء: {{ format_currency($product->purchase_price) }}</small>
                                        @endif
                                        @if($product->retail_price)
                                            <small class="text-dark fw-medium">مفرد: {{ format_currency($product->retail_price) }}</small>
                                        @endif
                                        @if($product->wholesale_price)
                                            <small class="text-muted">جملة: {{ format_currency($product->wholesale_price) }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        <p class="mb-0">
                                            <span class="text-dark fw-medium">{{ $product->quantity }}</span>
                                            <span class="text-muted fs-12">قطعة</span>
                                        </p>
                                        @if($product->min_quantity !== null && $product->quantity <= $product->min_quantity)
                                            <span class="badge bg-danger-subtle text-danger py-1 px-2 fs-11">
                                                <iconify-icon icon="solar:danger-triangle-bold-duotone" class="align-middle"></iconify-icon>
                                                منخفض
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}-subtle text-{{ $product->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-light btn-sm" title="عرض">
                                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-soft-primary btn-sm" title="تعديل">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                        <button type="button"
                                                class="btn btn-soft-danger btn-sm"
                                                title="حذف"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmModal"
                                                data-action="{{ route('admin.inventory.products.destroy', $product) }}"
                                                data-message="هل أنت متأكد من حذف هذا المنتج؟">
                                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <p class="text-muted mb-0">لا توجد منتجات</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- end table-responsive -->
            </div>
            @if($products->hasPages())
            <div class="row g-0 align-items-center justify-content-between text-center text-sm-start p-3 border-top">
                <div class="col-sm">
                    <div class="text-muted">
                        عرض <span class="fw-semibold">{{ $products->firstItem() }}</span> إلى <span class="fw-semibold">{{ $products->lastItem() }}</span> من <span class="fw-semibold">{{ $products->total() }}</span> نتيجة
                    </div>
                </div>
                <div class="col-sm-auto mt-3 mt-sm-0">
                    {!! $products->links() !!}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@endsection
