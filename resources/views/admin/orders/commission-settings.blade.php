@extends('layouts.vertical', ['title' => 'إعدادات عمولة التجهيز'])

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-0">إعدادات عمولة التجهيز</h4>
                <p class="text-muted mb-0">قم بإدارة إعدادات عمولة التجهيز للطلبات</p>
            </div>
        </div>
    </div>
</div>

<!-- Add New Setting -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">إضافة إعداد جديد</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.order-commission.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">مبلغ العمولة <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="commission_value" class="form-control" required>
                            <small class="text-muted">يمكن أن يكون صفر</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon>
                        إضافة
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Existing Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">الإعدادات العامة</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>مبلغ العمولة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settings as $setting)
                                <tr>
                                    <td>{{ format_currency($setting->commission_value) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $setting->is_active ? 'success' : 'danger' }}">
                                            {{ $setting->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.order-commission.update', $setting->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="commission_value" value="{{ $setting->commission_value }}">
                                            <input type="hidden" name="is_active" value="{{ $setting->is_active ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-sm btn-{{ $setting->is_active ? 'warning' : 'success' }}">
                                                {{ $setting->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.settings.order-commission.destroy', $setting->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعداد؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">لا توجد إعدادات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Exception -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">إضافة استثناء</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.order-commission.exceptions.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">المندوب (اختياري)</label>
                            <select name="representative_id" class="form-select" id="exception-representative">
                                <option value="">اختر مندوب</option>
                                @foreach($representatives as $rep)
                                    <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">الموظف (اختياري)</label>
                            <select name="user_id" class="form-select" id="exception-user">
                                <option value="">اختر موظف</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">مبلغ العمولة <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="commission_value" class="form-control" required>
                            <small class="text-muted">يمكن أن يكون صفر</small>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon>
                        إضافة استثناء
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Existing Exceptions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">الاستثناءات</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المندوب/الموظف</th>
                                <th>مبلغ العمولة</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exceptions as $exception)
                                <tr>
                                    <td>
                                        @if($exception->representative)
                                            <strong>مندوب:</strong> {{ $exception->representative->name }}
                                        @elseif($exception->user)
                                            <strong>موظف:</strong> {{ $exception->user->name }}
                                        @endif
                                    </td>
                                    <td>{{ format_currency($exception->commission_value) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $exception->is_active ? 'success' : 'danger' }}">
                                            {{ $exception->is_active ? 'نشط' : 'غير نشط' }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.settings.order-commission.exceptions.update', $exception->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="commission_value" value="{{ $exception->commission_value }}">
                                            <input type="hidden" name="is_active" value="{{ $exception->is_active ? 0 : 1 }}">
                                            <button type="submit" class="btn btn-sm btn-{{ $exception->is_active ? 'warning' : 'success' }}">
                                                {{ $exception->is_active ? 'تعطيل' : 'تفعيل' }}
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.settings.order-commission.exceptions.destroy', $exception->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاستثناء؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                حذف
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">لا توجد استثناءات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Ensure only one of representative or user is selected
    document.getElementById('exception-representative').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('exception-user').value = '';
        }
    });

    document.getElementById('exception-user').addEventListener('change', function() {
        if (this.value) {
            document.getElementById('exception-representative').value = '';
        }
    });
</script>
@endsection
