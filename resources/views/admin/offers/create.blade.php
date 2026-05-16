@extends('layouts.vertical', ['title' => 'إضافة عرض جديد'])

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">إضافة عرض جديد</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label">عنوان العرض <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="price" class="form-label">سعر العرض (اختياري)</label>
                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price') }}">
                            @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">نص العرض (الوصف)</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">صورة البنر (العرض)</label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/*">
                            @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="gallery_images" class="form-label">صور إضافية خاصة بصفحة العرض (متعدد)</label>
                            <input type="file" class="form-control @error('gallery_images') is-invalid @enderror" id="gallery_images" name="gallery_images[]" accept="image/*" multiple>
                            @error('gallery_images')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="order" class="form-label">الترتيب</label>
                            <input type="number" class="form-control @error('order') is-invalid @enderror" id="order" name="order" value="{{ old('order', 0) }}">
                            @error('order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-3 mb-3 d-flex align-items-end">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">تفعيل العرض</label>
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="product_ids" class="form-label">المنتجات (الكتب) المشمولة في العرض</label>
                            <select class="form-control select2 @error('product_ids') is-invalid @enderror" id="product_ids" name="product_ids[]" multiple="multiple">
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }} {{ $product->author ? ' - ' . $product->author : '' }}</option>
                                @endforeach
                            </select>
                            @error('product_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">حفظ وإرسال إشعار</button>
                    <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary mt-2 ms-2">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            placeholder: "اختر الكتب",
            allowClear: true
        });
    });
</script>
@endsection
