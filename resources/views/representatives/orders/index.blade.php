@extends('layouts.representative', ['title' => 'طلباتي'])

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">طلباتي</h4>
                    <a href="{{ route('representative.orders.create') }}" class="btn btn-primary">
                        <iconify-icon icon="solar:document-add-bold-duotone"></iconify-icon>
                        طلب جديد
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('representative.orders.index') }}" class="row g-3">
                    <div class="col-md-4">
                        <select name="status" class="form-select">
                            <option value="">جميع الحالات</option>
                            @foreach(\App\Enums\OrderStatus::cases() as $status)
                                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                                    {{ $status->label() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">تصفية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Orders List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم الزبون</th>
                                <th>رقم الهاتف</th>
                                <th>إجمالي المبلغ</th>
                                <th>الربح الصافي</th>
                                <th>الحالة</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    <td>{{ $order->customer_name }}</td>
                                    <td>{{ $order->customer_phone }}</td>
                                    <td>{{ format_currency($order->total_amount) }}</td>
                                    <td class="text-success">{{ format_currency($order->final_profit) }}</td>
                                    <td>
                                        <span class="badge {{ $order->status->badgeClass() }}">
                                            {{ $order->status->label() }}
                                        </span>
                                    </td>
                                    <td>{{ $order->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('representative.orders.show', $order) }}" class="btn btn-sm btn-primary">
                                            <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا توجد طلبات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

