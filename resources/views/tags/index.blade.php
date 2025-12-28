@extends('layouts.vertical', ['title' => 'التاغات'])

@section('content')
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <h4 class="card-title mb-0">قائمة التاغات</h4>
                    <a href="{{ route('admin.tags.create') }}" class="btn btn-primary">
                        <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon>
                        إضافة تاغ جديد
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.tags.index') }}" class="row g-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" placeholder="ابحث عن تاغ..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">بحث</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tags List -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم التاغ</th>
                                <th>عدد المنتجات</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tags as $tag)
                                <tr>
                                    <td>{{ $tag->id }}</td>
                                    <td>
                                        <span class="badge bg-primary">{{ $tag->name }}</span>
                                    </td>
                                    <td>{{ $tag->products_count }}</td>
                                    <td>
                                        <a href="{{ route('admin.tags.edit', $tag) }}" class="btn btn-sm btn-primary">
                                            <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                            تعديل
                                        </a>
                                        <form action="{{ route('admin.tags.destroy', $tag) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التاغ؟')">
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
                                    <td colspan="4" class="text-center">لا توجد تاغات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $tags->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

