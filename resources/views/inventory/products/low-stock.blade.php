@extends('layouts.vertical', ['title' => 'Low Stock Products'])

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
                    <div class="avatar-md bg-danger bg-opacity-10 rounded">
                        <iconify-icon icon="solar:danger-triangle-bold-duotone" class="fs-32 text-danger avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">منخفضة المخزون</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $products->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" class="text-danger"></iconify-icon>
                    منتجات منخفضة المخزون
                </h4>

                <a href="{{ route('inventory.products.index') }}" class="btn btn-sm btn-secondary">
                    <iconify-icon icon="solar:arrow-left-bold-duotone" class="me-1"></iconify-icon>
                    رجوع
                </a>
            </div>
            <div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>المنتج</th>
                                <th>الكمية الحالية</th>
                                <th>الحد الأدنى</th>
                                <th>النقص</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
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
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-danger-subtle text-danger py-1 px-2 fs-14">{{ $product->quantity }}</span>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $product->min_quantity ?? '-' }}</span>
                                </td>
                                <td>
                                    @if($product->min_quantity !== null)
                                        @php
                                            $shortage = $product->min_quantity - $product->quantity;
                                        @endphp
                                        <span class="badge bg-warning-subtle text-warning py-1 px-2">
                                            {{ $shortage > 0 ? 'نقص ' . $shortage : 'في الحد الأدنى' }}
                                        </span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
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
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <p class="text-muted mb-0">لا توجد منتجات منخفضة المخزون</p>
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
