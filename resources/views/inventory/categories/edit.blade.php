@extends('layouts.vertical', ['title' => 'Edit Category'])

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
                <h4 class="card-title">معلومات الفئة</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.inventory.categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الفئة <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم الفئة" value="{{ old('name', $category->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label class="form-label">نوع الفئة</label>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="category_type" id="category_type_main" value="main" {{ old('category_type', $category->parent_id ? 'sub' : 'main') == 'main' ? 'checked' : '' }} onchange="toggleParentSelect()">
                                    <label class="form-check-label" for="category_type_main">
                                        فئة رئيسية (قسم رئيسي)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="category_type" id="category_type_sub" value="sub" {{ old('category_type', $category->parent_id ? 'sub' : 'main') == 'sub' ? 'checked' : '' }} onchange="toggleParentSelect()">
                                    <label class="form-check-label" for="category_type_sub">
                                        فئة فرعية (قسم فرعي)
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12" id="parent_category_wrapper" style="display: {{ $category->parent_id ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="parent_id" class="form-label">الفئة الرئيسية (الأصل) <span class="text-danger">*</span></label>
                                <select id="parent_id" name="parent_id" class="form-select @error('parent_id') is-invalid @enderror">
                                    <option value="">اختر الفئة الرئيسية</option>
                                    @foreach($parentCategories as $parent)
                                        <option value="{{ $parent->id }}" {{ old('parent_id', $category->parent_id) == $parent->id ? 'selected' : '' }}>
                                            {{ $parent->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('parent_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">يجب اختيار فئة رئيسية عند إنشاء فئة فرعية</small>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                          placeholder="أدخل وصف الفئة">{{ old('description', $category->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer border-top">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('inventory.categories.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleParentSelect() {
    const mainRadio = document.getElementById('category_type_main');
    const subRadio = document.getElementById('category_type_sub');
    const parentWrapper = document.getElementById('parent_category_wrapper');
    const parentSelect = document.getElementById('parent_id');
    
    if (subRadio.checked) {
        parentWrapper.style.display = 'block';
        parentSelect.required = true;
    } else {
        parentWrapper.style.display = 'none';
        parentSelect.required = false;
        parentSelect.value = '';
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleParentSelect();
});
</script>

@endsection
