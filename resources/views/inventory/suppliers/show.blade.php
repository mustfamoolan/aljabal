@extends('layouts.vertical', ['title' => 'Supplier Details'])

@section('content')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل المورد: {{ $supplier->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">اسم المورد</label>
                            <p class="form-control-plaintext fs-16 fw-semibold">{{ $supplier->name }}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <p class="form-control-plaintext">
                                <span class="badge bg-{{ $supplier->is_active ? 'success' : 'danger' }}-subtle text-{{ $supplier->is_active ? 'success' : 'danger' }} py-1 px-2">
                                    {{ $supplier->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </p>
                        </div>
                    </div>
                    @if($supplier->phone)
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الهاتف</label>
                            <p class="form-control-plaintext">{{ $supplier->phone }}</p>
                        </div>
                    </div>
                    @endif
                    @if($supplier->email)
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">البريد الإلكتروني</label>
                            <p class="form-control-plaintext">{{ $supplier->email }}</p>
                        </div>
                    </div>
                    @endif
                    @if($supplier->address)
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">العنوان</label>
                            <p class="form-control-plaintext">{{ $supplier->address }}</p>
                        </div>
                    </div>
                    @endif
                    @if($supplier->contact_person)
                    <div class="col-lg-6">
                        <div class="mb-3">
                            <label class="form-label">الشخص المسؤول</label>
                            <p class="form-control-plaintext">{{ $supplier->contact_person }}</p>
                        </div>
                    </div>
                    @endif
                    @if($supplier->notes)
                    <div class="col-lg-12">
                        <div class="mb-3">
                            <label class="form-label">ملاحظات</label>
                            <p class="form-control-plaintext">{{ $supplier->notes }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            <div class="card-footer border-top">
                <a href="{{ route('inventory.suppliers.edit', $supplier) }}" class="btn btn-primary">تعديل</a>
                <a href="{{ route('inventory.suppliers.index') }}" class="btn btn-secondary">رجوع</a>
            </div>
        </div>

        @if($supplier->products->isNotEmpty())
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">المنتجات المرتبطة ({{ $supplier->products->count() }})</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>اسم المنتج</th>
                                <th>SKU</th>
                                <th>الكمية</th>
                                <th>سعر الشراء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($supplier->products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku ?? '-' }}</td>
                                <td><span class="badge bg-info-subtle text-info py-1 px-2">{{ $product->quantity }}</span></td>
                                <td>{{ $product->purchase_price ? format_currency($product->purchase_price) : '-' }}</td>
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
