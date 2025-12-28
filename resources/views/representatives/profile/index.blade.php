@extends('layouts.representative', ['title' => 'الملف الشخصي'])

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">الملف الشخصي</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($representative->image)
                                <img src="{{ asset('storage/' . $representative->image) }}" alt="{{ $representative->name }}" 
                                     class="img-fluid rounded-circle mb-3" style="width: 150px; height: 150px; object-fit: cover;">
                            @else
                                <div class="avatar-lg mx-auto mb-3 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <span class="text-primary fw-bold fs-48">{{ substr($representative->name, 0, 1) }}</span>
                                </div>
                            @endif
                            <h4 class="mb-1">{{ $representative->name }}</h4>
                            <p class="text-muted mb-0">
                                @if($representative->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="30%">الاسم:</th>
                                            <td>{{ $representative->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>رقم الهاتف:</th>
                                            <td>{{ $representative->phone }}</td>
                                        </tr>
                                        @if($representative->email)
                                            <tr>
                                                <th>البريد الإلكتروني:</th>
                                                <td>{{ $representative->email }}</td>
                                            </tr>
                                        @endif
                                        @if($representative->address)
                                            <tr>
                                                <th>العنوان:</th>
                                                <td>{{ $representative->address }}</td>
                                            </tr>
                                        @endif
                                        @if($representative->area)
                                            <tr>
                                                <th>المنطقة:</th>
                                                <td>{{ $representative->area }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>تاريخ التسجيل:</th>
                                            <td>{{ $representative->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث:</th>
                                            <td>{{ $representative->updated_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

