@extends('layouts.vertical', ['title' => 'User Add'])

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
                <h4 class="card-title">معلومات المستخدم</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم المستخدم" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="أدخل رقم الهاتف" value="{{ old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="أدخل البريد الإلكتروني (اختياري)" value="{{ old('email') }}">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">نوع المستخدم <span class="text-danger">*</span></label>
                                <select id="type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="">اختر النوع</option>
                                    @foreach(\App\Enums\UserType::cases() as $userType)
                                        <option value="{{ $userType->value }}" {{ old('type') === $userType->value ? 'selected' : '' }}>
                                            {{ $userType->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6" id="employee_type_wrapper" style="display: none;">
                            <div class="mb-3">
                                <label for="employee_type_id" class="form-label">نوع الموظف <span class="text-danger">*</span></label>
                                <select id="employee_type_id" name="employee_type_id" class="form-select @error('employee_type_id') is-invalid @enderror">
                                    <option value="">اختر نوع الموظف</option>
                                    @if(isset($employeeTypes) && $employeeTypes->count() > 0)
                                        @foreach($employeeTypes as $employeeType)
                                            <option value="{{ $employeeType->id }}" {{ old('employee_type_id') == $employeeType->id ? 'selected' : '' }}>
                                                {{ $employeeType->name }}
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>لا توجد أنواع موظفين متاحة</option>
                                    @endif
                                </select>
                                @error('employee_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="أدخل كلمة المرور" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">كلمة المرور يجب أن تكون 8 أحرف على الأقل</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                       placeholder="أعد إدخال كلمة المرور" required>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_active">
                                        نشط
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="image" class="form-label">صورة البروفايل</label>
                                <input type="file" id="image" name="image" class="form-control @error('image') is-invalid @enderror"
                                       accept="image/jpeg,image/png,image/jpg,image/gif">
                                @error('image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">الصيغ المدعومة: JPEG, PNG, JPG, GIF. الحد الأقصى: 2MB</small>
                                <div id="imagePreview" class="mt-3 text-center" style="display: none;">
                                    <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>
            </div>
            <div class="card-footer border-top">
                <button type="submit" class="btn btn-primary">إنشاء المستخدم</button>
                <a href="{{ route('users.users.list') }}" class="btn btn-secondary">إلغاء</a>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('type');
        const employeeTypeWrapper = document.getElementById('employee_type_wrapper');
        const employeeTypeSelect = document.getElementById('employee_type_id');

        function toggleEmployeeType() {
            if (typeSelect.value === 'employee') {
                employeeTypeWrapper.style.display = 'block';
                employeeTypeSelect.setAttribute('required', 'required');
            } else {
                employeeTypeWrapper.style.display = 'none';
                employeeTypeSelect.removeAttribute('required');
                employeeTypeSelect.value = '';
            }
        }

        // Check on page load
        toggleEmployeeType();

        // Check on change
        typeSelect.addEventListener('change', toggleEmployeeType);

        // Image preview
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = document.getElementById('previewImg');

        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    }
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                }
            });
        }
    });
</script>
@endsection
