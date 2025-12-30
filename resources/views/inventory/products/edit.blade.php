@extends('layouts.vertical', ['title' => 'Edit Product'])

@section('css')
@vite(['node_modules/choices.js/public/assets/styles/choices.min.css'])
@endsection

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

<form method="POST" action="{{ route('admin.inventory.products.update', $product) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    <div class="row">
        <div class="col-xl-9 col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">معلومات المنتج الأساسية</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم المنتج <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم المنتج" value="{{ old('name', $product->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">كود المنتج (SKU)</label>
                                <input type="text" id="sku" name="sku" class="form-control @error('sku') is-invalid @enderror"
                                       placeholder="سيتم توليده تلقائياً إذا تركت فارغاً" value="{{ old('sku', $product->sku) }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="product_type" class="form-label">نوع المنتج <span class="text-danger">*</span></label>
                                <select id="product_type" name="product_type" class="form-select @error('product_type') is-invalid @enderror" required>
                                    <option value="">اختر النوع</option>
                                    @foreach(\App\Enums\ProductType::cases() as $type)
                                        <option value="{{ $type->value }}" {{ old('product_type', $product->product_type->value) === $type->value ? 'selected' : '' }}>
                                            {{ $type->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('product_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">الفئة الرئيسية</label>
                                <select id="category_id" name="category_id" class="form-select @error('category_id') is-invalid @enderror" data-old-subcategory-id="{{ old('subcategory_id', $product->subcategory_id) }}">
                                    <option value="">اختر الفئة الرئيسية</option>
                                    @foreach($mainCategories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="subcategory_id" class="form-label">الفئة الفرعية</label>
                                <select id="subcategory_id" name="subcategory_id" class="form-select @error('subcategory_id') is-invalid @enderror" {{ !$product->category_id ? 'disabled' : '' }}>
                                    <option value="">اختر الفئة الفرعية</option>
                                </select>
                                @error('subcategory_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">المورد</label>
                                <select id="supplier_id" name="supplier_id" class="form-select @error('supplier_id') is-invalid @enderror">
                                    <option value="">اختر المورد</option>
                                    @foreach($suppliers as $supplier)
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
                                <label for="tags" class="form-label">التاغات</label>
                                <select id="tags" name="tags[]" class="form-control @error('tags') is-invalid @enderror"
                                        data-choices data-choices-removeItem multiple>
                                    @foreach($tags as $tag)
                                        <option value="{{ $tag->id }}" {{ $product->tags->contains($tag->id) || in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                            {{ $tag->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tags')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="author" class="form-label">المؤلف</label>
                                <input type="text" id="author" name="author" class="form-control @error('author') is-invalid @enderror"
                                       placeholder="أدخل اسم المؤلف" value="{{ old('author', $product->author) }}">
                                @error('author')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="publisher" class="form-label">دار النشر</label>
                                <input type="text" id="publisher" name="publisher" class="form-control @error('publisher') is-invalid @enderror"
                                       placeholder="أدخل دار النشر" value="{{ old('publisher', $product->publisher) }}">
                                @error('publisher')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الأسعار</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
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
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="retail_price" class="form-label">سعر البيع مفرد</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" id="retail_price" name="retail_price" class="form-control @error('retail_price') is-invalid @enderror"
                                           placeholder="0.00" value="{{ old('retail_price', $product->retail_price) }}">
                                    @error('retail_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="wholesale_price" class="form-label">سعر البيع جملة</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" id="wholesale_price" name="wholesale_price" class="form-control @error('wholesale_price') is-invalid @enderror"
                                           placeholder="0.00" value="{{ old('wholesale_price', $product->wholesale_price) }}">
                                    @error('wholesale_price')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">المخزون</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">الكمية</label>
                                <input type="number" id="quantity" name="quantity" class="form-control @error('quantity') is-invalid @enderror"
                                       placeholder="0" value="{{ old('quantity', $product->quantity) }}" min="0">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="min_quantity" class="form-label">الحد الأدنى للتنبيه</label>
                                <input type="number" id="min_quantity" name="min_quantity" class="form-control @error('min_quantity') is-invalid @enderror"
                                       placeholder="0" value="{{ old('min_quantity', $product->min_quantity) }}" min="0">
                                @error('min_quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">نوع الوحدة</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="unit_type" class="form-label">نوع الوحدة</label>
                                <select id="unit_type" name="unit_type" class="form-select @error('unit_type') is-invalid @enderror">
                                    <option value="">اختر نوع الوحدة</option>
                                    @foreach(\App\Enums\UnitType::cases() as $unitType)
                                        <option value="{{ $unitType->value }}" {{ old('unit_type', $product->unit_type?->value) === $unitType->value ? 'selected' : '' }}>
                                            {{ $unitType->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6" id="weight_fields" style="display: {{ old('unit_type', $product->unit_type?->value) === 'weight' ? 'block' : 'none' }};">
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="weight_unit" class="form-label">وحدة الوزن</label>
                                        <select id="weight_unit" name="weight_unit" class="form-select">
                                            @foreach(\App\Enums\WeightUnit::cases() as $weightUnit)
                                                <option value="{{ $weightUnit->value }}" {{ old('weight_unit', $product->weight_unit?->value) === $weightUnit->value ? 'selected' : '' }}>
                                                    {{ $weightUnit->label() }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="weight_value" class="form-label">قيمة الوزن</label>
                                        <input type="number" step="0.01" id="weight_value" name="weight_value" class="form-control"
                                               placeholder="0.00" value="{{ old('weight_value', $product->weight_value) }}" min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6" id="carton_fields" style="display: {{ old('unit_type', $product->unit_type?->value) === 'carton' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="carton_quantity" class="form-label">عدد القطع في الكارتون</label>
                                <input type="number" id="carton_quantity" name="carton_quantity" class="form-control"
                                       placeholder="0" value="{{ old('carton_quantity', $product->carton_quantity) }}" min="1">
                            </div>
                        </div>
                        <div class="col-lg-6" id="set_fields" style="display: {{ old('unit_type', $product->unit_type?->value) === 'set' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="set_quantity" class="form-label">عدد القطع في المجموعة</label>
                                <input type="number" id="set_quantity" name="set_quantity" class="form-control"
                                       placeholder="0" value="{{ old('set_quantity', $product->set_quantity) }}" min="1">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">مكان التخزين</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="shelf" class="form-label">الرف</label>
                                <input type="text" id="shelf" name="shelf" class="form-control @error('shelf') is-invalid @enderror"
                                       placeholder="حرف أو رقم" value="{{ old('shelf', $product->shelf) }}">
                                @error('shelf')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="compartment" class="form-label">الخانة</label>
                                <input type="text" id="compartment" name="compartment" class="form-control @error('compartment') is-invalid @enderror"
                                       placeholder="حرف أو رقم" value="{{ old('compartment', $product->compartment) }}">
                                @error('compartment')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">المواصفات الفيزيائية</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="weight" class="form-label">الوزن (كيلوغرام)</label>
                                <input type="number" step="0.01" id="weight" name="weight" class="form-control @error('weight') is-invalid @enderror"
                                       placeholder="0.00" value="{{ old('weight', $product->weight) }}" min="0">
                                @error('weight')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">الوزن بالكيلوغرام</small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="size" class="form-label">الحجم</label>
                                <input type="text" id="size" name="size" class="form-control @error('size') is-invalid @enderror"
                                       placeholder="مثال: 20x15x2 سم" value="{{ old('size', $product->size) }}">
                                @error('size')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">مثال: 20x15x2 سم أو 17x24 سم</small>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="mb-3">
                                <label for="page_count" class="form-label">عدد الصفحات</label>
                                <input type="number" id="page_count" name="page_count" class="form-control @error('page_count') is-invalid @enderror"
                                       placeholder="0" value="{{ old('page_count', $product->page_count) }}" min="1">
                                @error('page_count')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الوصف</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="short_description" class="form-label mb-0">وصف قصير</label>
                                    <button type="button" id="generate-short-description-btn" class="btn btn-sm btn-outline-primary" data-original-text="توليد نص تلقائي">
                                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="fs-18 me-1"></iconify-icon>
                                        توليد نص تلقائي
                                    </button>
                                </div>
                                <textarea id="short_description" name="short_description" class="form-control @error('short_description') is-invalid @enderror" rows="3"
                                          placeholder="وصف مختصر للمنتج">{{ old('short_description', $product->short_description) }}</textarea>
                                @error('short_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="long_description" class="form-label mb-0">وصف طويل</label>
                                    <button type="button" id="generate-long-description-btn" class="btn btn-sm btn-outline-primary" data-original-text="توليد نص تلقائي">
                                        <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="fs-18 me-1"></iconify-icon>
                                        توليد نص تلقائي
                                    </button>
                                </div>
                                <textarea id="long_description" name="long_description" class="form-control @error('long_description') is-invalid @enderror" rows="5"
                                          placeholder="وصف تفصيلي للمنتج">{{ old('long_description', $product->long_description) }}</textarea>
                                @error('long_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">اللون</label>
                                <input type="text" id="color" name="color" class="form-control @error('color') is-invalid @enderror"
                                       placeholder="أدخل اللون" value="{{ old('color', $product->color) }}">
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">صور المنتج (4 صور كحد أقصى)</h4>
                </div>
                <div class="card-body">
                    @if($product->images->isNotEmpty())
                        <div class="mb-3">
                            <label class="form-label">الصور الحالية</label>
                            <div class="row g-2">
                                @foreach($product->images as $image)
                                    <div class="col-3">
                                        <div class="position-relative">
                                            <img src="{{ storage_url($image->image_path) }}" class="img-fluid rounded" alt="Image {{ $image->image_order }}">
                                            <small class="d-block text-center mt-1">صورة {{ $image->image_order }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <small class="form-text text-muted">رفع صور جديدة سيستبدل الصور الحالية</small>
                        </div>
                    @endif
                    <div class="mb-3">
                        <input type="file" id="images" name="images[]" class="form-control @error('images') is-invalid @enderror"
                               accept="image/*" multiple>
                        @error('images')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">يمكن رفع 4 صور كحد أقصى. حجم كل صورة يجب أن يكون أقل من 2 ميجابايت</small>
                    </div>
                    <div id="image-preview" class="row g-2"></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الحالة</h4>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            نشط
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="p-3 bg-light mb-3 rounded">
                <div class="row justify-content-end g-2">
                    <div class="col-lg-2">
                        <a href="{{ route('inventory.products.index') }}" class="btn btn-outline-secondary w-100">إلغاء</a>
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary w-100">حفظ التغييرات</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js', 'resources/js/pages/product-ai-generator.js', 'resources/js/pages/categories-dynamic.js'])
<script>
    // Pass old values to JavaScript
    window.oldCategoryId = {{ old('category_id', $product->category_id ?? 'null') }};
    window.oldSubcategoryId = {{ old('subcategory_id', $product->subcategory_id ?? 'null') }};
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize Choices.js for tags
        const tagsSelect = document.querySelector('#tags');
        if (tagsSelect && typeof Choices !== 'undefined') {
            new Choices(tagsSelect, {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'اختر التاغات',
                searchPlaceholderValue: 'ابحث عن تاغ...'
            });
        }

        // Toggle unit type fields
        const unitTypeSelect = document.getElementById('unit_type');
        const weightFields = document.getElementById('weight_fields');
        const cartonFields = document.getElementById('carton_fields');
        const setFields = document.getElementById('set_fields');

        function toggleUnitFields() {
            const value = unitTypeSelect.value;

            // Hide all
            weightFields.style.display = 'none';
            cartonFields.style.display = 'none';
            setFields.style.display = 'none';

            // Show relevant fields
            if (value === 'weight') {
                weightFields.style.display = 'block';
            } else if (value === 'carton') {
                cartonFields.style.display = 'block';
            } else if (value === 'set') {
                setFields.style.display = 'block';
            }
        }

        unitTypeSelect.addEventListener('change', toggleUnitFields);
        toggleUnitFields(); // Check on page load

        // Image preview
        const imagesInput = document.getElementById('images');
        const imagePreview = document.getElementById('image-preview');

        imagesInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = Array.from(e.target.files).slice(0, 4);

            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-6';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid rounded" alt="Preview ${index + 1}">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" onclick="removeImage(${index})">
                                    <iconify-icon icon="solar:trash-bin-minimalistic-2-broken"></iconify-icon>
                                </button>
                            </div>
                        `;
                        imagePreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });

        window.removeImage = function(index) {
            const dt = new DataTransfer();
            const files = Array.from(imagesInput.files);
            files.splice(index, 1);
            files.forEach(file => dt.items.add(file));
            imagesInput.files = dt.files;
            imagesInput.dispatchEvent(new Event('change'));
        };
    });
</script>
@endsection
