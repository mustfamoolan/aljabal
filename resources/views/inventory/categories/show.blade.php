@extends('layouts.vertical', ['title' => 'Category Details'])

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل الفئة: {{ $category->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">اسم الفئة</label>
                            <p class="form-control-plaintext fs-16 fw-semibold">{{ $category->name }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الفئة الأصل</label>
                            <p class="form-control-plaintext">
                                @if($category->parent)
                                    <span class="badge bg-info-subtle text-info py-1 px-2">{{ $category->parent->name }}</span>
                                @else
                                    <span class="text-muted">فئة رئيسية</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <p class="form-control-plaintext">{{ $category->description ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">عدد المنتجات</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-primary-subtle text-primary py-1 px-2">{{ $category->products->count() }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $category->is_active ? 'success' : 'danger' }}-subtle text-{{ $category->is_active ? 'success' : 'danger' }} py-1 px-2">
                                    {{ $category->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ route('inventory.categories.edit', $category) }}" class="btn btn-primary">تعديل</a>
                <a href="{{ route('inventory.categories.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </div>

        @if($category->children->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">الفئات الفرعية</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>اسم الفئة</th>
                                <th>عدد المنتجات</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->children as $child)
                            <tr>
                                <td>{{ $child->name }}</td>
                                <td><span class="badge bg-primary-subtle text-primary py-1 px-2">{{ $child->products->count() }}</span></td>
                                <td>
                                    <span class="badge bg-{{ $child->is_active ? 'success' : 'danger' }}-subtle text-{{ $child->is_active ? 'success' : 'danger' }} py-1 px-2">
                                        {{ $child->is_active ? 'نشط' : 'غير نشط' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('inventory.categories.edit', $child) }}" class="btn btn-soft-primary btn-sm">
                                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        @if($category->products->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">المنتجات في هذه الفئة</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>اسم المنتج</th>
                                <th>SKU</th>
                                <th>الكمية</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku ?? '-' }}</td>
                                <td><span class="badge bg-info-subtle text-info py-1 px-2">{{ $product->quantity }}</span></td>
                                <td>
                                    <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-soft-info btn-sm">
                                        <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

@endsection
