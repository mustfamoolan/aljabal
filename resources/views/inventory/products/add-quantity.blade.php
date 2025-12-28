@extends('layouts.vertical', ['title' => 'Add Quantity'])

@section('content')

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">إضافة كمية للمنتج: {{ $product->name }}</h4>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">الكمية الحالية</label>
                    <p class="form-control-plaintext fs-18 fw-semibold">{{ $product->quantity }} قطعة</p>
                </div>

                <form method="POST" action="{{ route('admin.inventory.products.add-quantity', $product) }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">الكمية المضافة <span class="text-danger">*</span></label>
                                <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       placeholder="0" min="1" value="{{ old('quantity') }}" required>
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">سعر الشراء</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" id="purchase_price" name="purchase_price" class="form-control @error('purchase_price') is-invalid @enderror"
                                           placeholder="0.00" value="{{ old('purchase_price', $product->purchase_price) }}">
                                    @error('purchase_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">المورد</label>
                                <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                    <option value="">اختر المورد</option>
                                    @foreach(\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                                        <option value="{{ $supplier->id }}" {{ old('supplier_id', $product->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('supplier_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="purchase_date" class="form-label">تاريخ الشراء</label>
                                <input type="date" id="purchase_date" name="purchase_date" class="form-control @error('purchase_date') is-invalid @enderror"
                                       value="{{ old('purchase_date', date('Y-m-d')) }}">
                                @error('purchase_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea id="notes" name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                          placeholder="ملاحظات إضافية عن عملية الشراء">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer border-top">
                <div class="row g-2">
                    <div class="col-lg-6">
                        <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-outline-secondary w-100">إلغاء</a>
                    </div>
                    <div class="col-lg-6">
                        <button type="submit" class="btn btn-primary w-100">إضافة الكمية</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection
