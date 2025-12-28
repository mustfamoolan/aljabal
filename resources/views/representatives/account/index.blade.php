@extends('layouts.representative', ['title' => 'الحسابات'])

@section('css')
@vite(['node_modules/gridjs/dist/theme/mermaid.min.css'])
@endsection

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

<!-- Balance Cards -->
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">الرصيد المتوفر</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($representative->available_balance) }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-success bg-opacity-10 rounded">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">الرصيد المعلق</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($representative->pending_withdrawals_amount) }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-warning bg-opacity-10 rounded">
                            <iconify-icon icon="solar:clock-circle-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">آخر معاملة</h4>
                        <p class="text-muted fw-medium fs-14 mb-0">
                            @if($lastTransaction)
                                {{ $lastTransaction->type_label }} - {{ format_currency($lastTransaction->amount) }}
                                <br>
                                <small class="text-muted">{{ $lastTransaction->created_at->format('Y-m-d H:i') }}</small>
                            @else
                                لا توجد معاملات
                            @endif
                        </p>
                    </div>
                    <div>
                        <div class="avatar-md bg-info bg-opacity-10 rounded">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="fs-32 text-info avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawal Request Button -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="card-title mb-2">طلب سحب رصيد</h4>
                        <p class="text-muted mb-0">الحد الأدنى للسحب: {{ format_currency($minWithdrawalAmount) }}</p>
                    </div>
                    <div>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#withdrawModal">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="me-1"></iconify-icon>
                            طلب سحب جديد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">سجل الحركات</h4>
            </div>
            <div class="card-body">
                <div id="transactions-table"></div>
            </div>
        </div>
    </div>
</div>

<!-- Withdraw Modal -->
<div class="modal fade" id="withdrawModal" tabindex="-1" aria-labelledby="withdrawModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('representative.account.withdraw') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">طلب سحب رصيد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="amount" class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="amount" name="amount" step="0.01" min="{{ $minWithdrawalAmount }}" required>
                        <small class="text-muted">الحد الأدنى: {{ format_currency($minWithdrawalAmount) }}</small>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظات (اختياري)</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('script')
@vite(['resources/js/components/transactions-table.js'])
<script>
    window.transactionsUrl = "{{ route('representative.account.transactions') }}";
</script>
@endsection

