@extends('layouts.vertical', ['title' => 'Role Add'])

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
                <h4 class="card-title">معلومات الدور</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.roles.store') }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم الدور <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم الدور" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="permissions" class="form-label">الصلاحيات</label>
                                <select class="form-control" id="permissions" name="permissions[]"
                                        data-choices data-choices-removeItem multiple>
                                    @foreach($permissions as $permission)
                                        <option value="{{ $permission->name }}">{{ $permission->name }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">اختر الصلاحيات المرتبطة بهذا الدور</small>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer border-top">
                <button type="submit" class="btn btn-primary">إنشاء الدور</button>
                <a href="{{ route('users.role.list') }}" class="btn btn-secondary">إلغاء</a>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/app-ecommerce-product.js'])
<script>
    // Initialize Choices.js for permissions select
    document.addEventListener('DOMContentLoaded', function() {
        const permissionsSelect = document.querySelector('#permissions');
        if (permissionsSelect && typeof Choices !== 'undefined') {
            new Choices(permissionsSelect, {
                removeItemButton: true,
                searchEnabled: true,
                placeholder: true,
                placeholderValue: 'اختر الصلاحيات',
                searchPlaceholderValue: 'ابحث عن صلاحية...'
            });
        }
    });
</script>
@endsection
