@extends('layouts.vertical', ['title' => 'User Edit'])

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
                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">اسم المستخدم <span class="text-danger">*</span></label>
                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                                       placeholder="أدخل اسم المستخدم" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                                <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                       placeholder="أدخل رقم الهاتف" value="{{ old('phone', $user->phone) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       placeholder="أدخل البريد الإلكتروني (اختياري)" value="{{ old('email', $user->email) }}">
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
                                        <option value="{{ $userType->value }}" {{ old('type', $user->type->value) === $userType->value ? 'selected' : '' }}>
                                            {{ $userType->label() }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-lg-6" id="employee_type_wrapper" style="display: {{ old('type', $user->type->value) === 'employee' ? 'block' : 'none' }};">
                            <div class="mb-3">
                                <label for="employee_type_id" class="form-label">نوع الموظف</label>
                                <select id="employee_type_id" name="employee_type_id" class="form-select @error('employee_type_id') is-invalid @enderror">
                                    <option value="">اختر نوع الموظف</option>
                                    @if(isset($employeeTypes) && $employeeTypes->count() > 0)
                                        @foreach($employeeTypes as $employeeType)
                                            <option value="{{ $employeeType->id }}" {{ old('employee_type_id', $user->employee_type_id) == $employeeType->id ? 'selected' : '' }}>
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
                                <label for="password" class="form-label">كلمة المرور</label>
                                <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="اتركه فارغاً إذا لم ترغب في التغيير">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">اتركه فارغاً إذا لم ترغب في تغيير كلمة المرور</small>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                                       placeholder="أعد إدخال كلمة المرور">
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
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
            } else {
                employeeTypeWrapper.style.display = 'none';
                employeeTypeSelect.value = '';
            }
        }

        // Check on change
        typeSelect.addEventListener('change', toggleEmployeeType);
    });
</script>
@endsection
