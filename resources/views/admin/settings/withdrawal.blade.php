@extends('layouts.vertical', ['title' => 'إعدادات السحب'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- General Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">الإعدادات العامة</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.settings.withdrawal.update-general') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى للسحب (دينار عراقي)</label>
                        <input type="number" class="form-control" name="min_withdrawal_amount" 
                               value="{{ $generalSetting->min_withdrawal_amount ?? 0 }}" 
                               step="0.01" min="0" required>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Exceptions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">الاستثناءات</h4>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExceptionModal">
                    إضافة استثناء
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المندوب</th>
                                <th>الحد الأدنى</th>
                                <th>تاريخ الإنشاء</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exceptions as $exception)
                                <tr>
                                    <td>{{ $exception->representative->name }}</td>
                                    <td>{{ format_currency($exception->min_withdrawal_amount) }}</td>
                                    <td>{{ $exception->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editExceptionModal{{ $exception->id }}">
                                            تعديل
                                        </button>
                                        <form action="{{ route('admin.settings.withdrawal.delete-exception', $exception) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('هل أنت متأكد؟')">
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

<!-- Add Exception Modal -->
<div class="modal fade" id="addExceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.settings.withdrawal.create-exception') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة استثناء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المندوب</label>
                        <select class="form-control" name="representative_id" required>
                            <option value="">اختر المندوب</option>
                            @foreach(\App\Models\Representative::all() as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى (دينار عراقي)</label>
                        <input type="number" class="form-control" name="min_withdrawal_amount" 
                               step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

