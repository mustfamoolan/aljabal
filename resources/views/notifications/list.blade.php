@extends('layouts.vertical', ['title' => 'الإشعارات'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-primary bg-opacity-10 rounded">
                        <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-32 text-primary avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">جميع الإشعارات</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $notifications->total() ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-warning bg-opacity-10 rounded">
                        <iconify-icon icon="solar:bell-off-bold-duotone" class="fs-32 text-warning avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">غير مقروءة</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ $unreadCount ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="avatar-md bg-success bg-opacity-10 rounded">
                        <iconify-icon icon="solar:check-circle-bold-duotone" class="fs-32 text-success avatar-title"></iconify-icon>
                    </div>
                    <div>
                        <h4 class="mb-0">مقروءة</h4>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <p class="text-muted fw-medium fs-22 mb-0">{{ ($notifications->total() ?? 0) - ($unreadCount ?? 0) }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center gap-1">
                <h4 class="card-title flex-grow-1">قائمة الإشعارات</h4>

                <div class="btn-group" role="group">
                    <a href="{{ route('notifications.index', ['filter' => 'all']) }}"
                       class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-outline-primary' }}">
                        جميع
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                       class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-outline-primary' }}">
                        غير مقروءة
                    </a>
                    <a href="{{ route('notifications.index', ['filter' => 'read']) }}"
                       class="btn btn-sm {{ $filter === 'read' ? 'btn-primary' : 'btn-outline-primary' }}">
                        مقروءة
                    </a>
                </div>

                <form action="{{ route('notifications.read-all') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-success">
                        <iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon>
                        تحديد الكل كمقروء
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
                            <tr>
                                <th>النوع</th>
                                <th>العنوان</th>
                                <th>الوصف</th>
                                <th>التاريخ</th>
                                <th>الحالة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($notifications as $notification)
                                <tr class="{{ $notification->read_at ? '' : 'table-light' }}">
                                    <td>
                                        @if($notification->type === 'low_stock')
                                            <iconify-icon icon="solar:box-bold-duotone" class="fs-20 text-danger"></iconify-icon>
                                        @else
                                            <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-20 text-primary"></iconify-icon>
                                        @endif
                                    </td>
                                    <td>
                                        <strong>{{ $notification->title }}</strong>
                                    </td>
                                    <td>
                                        <p class="mb-0 text-muted">{{ Str::limit($notification->body, 50) }}</p>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                        <br>
                                        <small class="text-muted">{{ $notification->created_at->format('Y-m-d H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($notification->read_at)
                                            <span class="badge bg-success-subtle text-success">مقروء</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">غير مقروء</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <a href="{{ route('notifications.show', $notification) }}"
                                               class="btn btn-sm btn-info" title="عرض">
                                                <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                            </a>
                                            @if($notification->read_at)
                                                <form action="{{ route('notifications.unread', $notification) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-warning" title="تحديد كغير مقروء">
                                                        <iconify-icon icon="solar:bell-off-bold-duotone"></iconify-icon>
                                                    </button>
                                                </form>
                                            @else
                                                <form action="{{ route('notifications.read', $notification) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-success" title="تحديد كمقروء">
                                                        <iconify-icon icon="solar:check-circle-bold-duotone"></iconify-icon>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <p class="text-muted mb-0">لا توجد إشعارات</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($notifications->hasPages())
                    <div class="mt-3">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection
