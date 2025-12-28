@extends('layouts.vertical', ['title' => 'حسابات المندوبين'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">إجمالي الأرصدة</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($totalBalance) }}</p>
                    </div>
                    <div>
                        <div class="avatar-md bg-primary bg-opacity-10 rounded">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="card-title mb-2">إجمالي المعلق</h4>
                        <p class="text-muted fw-medium fs-22 mb-0">{{ format_currency($totalPending) }}</p>
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
</div>

<!-- Representatives Table -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">قائمة حسابات المندوبين</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>الاسم</th>
                                <th>رقم الهاتف</th>
                                <th>الرصيد المتوفر</th>
                                <th>الرصيد المعلق</th>
                                <th>آخر معاملة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($representatives as $rep)
                                <tr>
                                    <td>{{ $rep->name }}</td>
                                    <td>{{ $rep->phone }}</td>
                                    <td>{{ format_currency($rep->available_balance) }}</td>
                                    <td>{{ format_currency($rep->pending_withdrawals_amount) }}</td>
                                    <td>
                                        @if($rep->transactions->first())
                                            {{ $rep->transactions->first()->created_at->format('Y-m-d') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.accounts.show', $rep) }}" class="btn btn-sm btn-primary">
                                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $representatives->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

