@extends('layouts.vertical', ['title' => 'العروض والبنرات'])

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="header-title">العروض والبنرات</h4>
                <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">إضافة عرض جديد</a>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>الصورة</th>
                                <th>العنوان</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>الترتيب</th>
                                <th>عدد الكتب</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($offers as $offer)
                            <tr>
                                <td>
                                    @if($offer->image_path)
                                        <img src="{{ Storage::url($offer->image_path) }}" alt="Offer Image" height="50">
                                    @endif
                                </td>
                                <td>{{ $offer->title }}</td>
                                <td>{{ $offer->price ? $offer->price . ' د.ع' : 'بدون سعر' }}</td>
                                <td>
                                    @if($offer->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td>{{ $offer->order }}</td>
                                <td>{{ $offer->products_count }}</td>
                                <td>
                                    <a href="{{ route('admin.offers.edit', $offer->id) }}" class="btn btn-sm btn-info">تعديل</a>
                                    <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">حذف</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد عروض مضافة حالياً.</td>
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
