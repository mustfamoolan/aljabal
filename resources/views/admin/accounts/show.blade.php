@extends('layouts.vertical', ['title' => 'تفاصيل حساب المندوب'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Representative Info -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-3">{{ $representative->name }}</h4>
                <p class="text-muted mb-1"><strong>رقم الهاتف:</strong> {{ $representative->phone }}</p>
                @if($representative->email)
                    <p class="text-muted mb-0"><strong>البريد:</strong> {{ $representative->email }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">الرصيد المتوفر</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($representative->available_balance) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">الرصيد المعلق</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($representative->pending_withdrawals_amount) }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">إجمالي الإيداعات</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($statistics['total_deposits']) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBalanceModal">
                        إضافة رصيد
                    </button>
                    <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#directWithdrawModal">
                        سحب مباشر
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">سجل المعاملات</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الوصف</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($representative->transactions as $transaction)
                                <tr>
                                    <td><span class="badge bg-primary">{{ $transaction->type_label }}</span></td>
                                    <td>{{ format_currency($transaction->amount) }}</td>
                                    <td><span class="badge bg-success">{{ $transaction->status_label }}</span></td>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $transaction->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا توجد معاملات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Balance Modal -->
<div class="modal fade" id="addBalanceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounts.add-balance', $representative) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة رصيد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ</label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">النوع</label>
                        <select class="form-control" name="type" required>
                            <option value="deposit">إيداع</option>
                            <option value="commission">عمولة</option>
                            <option value="bonus">مكافأة</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الوصف (اختياري)</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
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

<!-- Direct Withdraw Modal -->
<div class="modal fade" id="directWithdrawModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.accounts.direct-withdraw', $representative) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">سحب مباشر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المبلغ <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="amount" step="0.01" min="0.01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات (اختياري)</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">سحب</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

