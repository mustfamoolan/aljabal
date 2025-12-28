@extends('layouts.vertical', ['title' => 'تفاصيل طلب السحب'])

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

<!-- Representative Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">{{ $withdrawalRequest->representative->name }}</h4>
                <p class="text-muted mb-1"><strong>رقم الهاتف:</strong> {{ $withdrawalRequest->representative->phone }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal Details -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">تفاصيل الطلب</h4>
                <p><strong>المبلغ:</strong> {{ format_currency($withdrawalRequest->amount) }}</p>
                <p><strong>الحالة:</strong> <span class="badge {{ $withdrawalRequest->status->getBadgeClass() }}">{{ $withdrawalRequest->status_label }}</span></p>
                <p><strong>تاريخ الطلب:</strong> {{ $withdrawalRequest->requested_at->format('Y-m-d H:i') }}</p>
                @if($withdrawalRequest->notes)
                    <p><strong>ملاحظات:</strong> {{ $withdrawalRequest->notes }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
@if($withdrawalRequest->isPending() && auth()->user()->can('representatives.update'))
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
                        الموافقة
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        الرفض
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@elseif($withdrawalRequest->isPending() && !auth()->user()->can('representatives.update'))
<div class="row mb-4">
    <div class="col-12">
        <div class="alert alert-warning">
            <strong>تنبيه:</strong> ليس لديك صلاحية للموافقة أو رفض طلبات السحب. يرجى التواصل مع المدير.
        </div>
    </div>
</div>
@endif

<!-- Approve Modal -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.withdrawals.approve', $withdrawalRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">الموافقة على طلب السحب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success">موافقة</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.withdrawals.reject', $withdrawalRequest) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">رفض طلب السحب</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

