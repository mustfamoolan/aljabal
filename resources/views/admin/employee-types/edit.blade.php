@extends('layouts.vertical', ['title' => 'Employee Type Edit'])

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

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">معلومات نوع الموظف</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.employee-types.update', $employeeType) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم النوع <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم نوع الموظف" value="{{ old('name', $employeeType->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $employeeType->is_active) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="description" class="form-label">الوصف</label>
                                <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="3"
                                          placeholder="أدخل وصف نوع الموظف">{{ old('description', $employeeType->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="roles" class="form-label">الأدوار المرتبطة</label>
                                <select class="form-control" id="roles" name="roles[]"
                                        data-choices data-choices-removeItem multiple>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" {{ $employeeType->roles->contains($role->id) ? 'selected' : '' }}>
                                            {{ $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">اختر الأدوار المرتبطة بهذا النوع (سيتم إعطاؤها تلقائياً للمستخدمين من هذا النوع)</small>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer border-top">
                <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                <a href="{{ route('admin.employee-types.index') }}" class="btn btn-secondary">إلغاء</a>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js'])
<script>
    // Initialize Choices.js for roles select
    document.addEventListener('DOMContentLoaded', function() {
        const rolesSelect = document.querySelector('#roles');
        if (rolesSelect && typeof Choices !== 'undefined') {
            new Choices(rolesSelect, {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'اختر الأدوار',
                searchPlaceholderValue: 'ابحث عن دور...'
            });
        }
    });
</script>
@endsection
