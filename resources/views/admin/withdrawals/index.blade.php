@extends('layouts.vertical', ['title' => 'طلبات السحب'])

@section('content')

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">طلبات معلقة</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ $pendingCount }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">موافق عليها</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ $approvedCount }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-2">مرفوضة</h4>
                <p class="text-muted fw-medium fs-22 mb-0">{{ $rejectedCount }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Withdrawals Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">قائمة طلبات السحب</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>المندوب</th>
                                <th>المبلغ</th>
                                <th>الحالة</th>
                                <th>تاريخ الطلب</th>
                                <th>النوع</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($withdrawals as $withdrawal)
                                <tr>
                                    <td>{{ $withdrawal->representative->name }}</td>
                                    <td>{{ format_currency($withdrawal->amount) }}</td>
                                    <td><span class="badge {{ $withdrawal->status->getBadgeClass() }}">{{ $withdrawal->status_label }}</span></td>
                                    <td>{{ $withdrawal->requested_at->format('Y-m-d H:i') }}</td>
                                    <td>{{ $withdrawal->is_direct_withdrawal ? 'مباشر' : 'طلب' }}</td>
                                    <td>
                                        <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="btn btn-sm btn-primary">
                                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $withdrawals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

