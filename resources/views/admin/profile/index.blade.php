@extends('layouts.vertical', ['title' => 'الملف الشخصي'])

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
                            <div class="avatar-lg mx-auto mb-3 bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                <span class="text-primary fw-bold fs-48">{{ substr($user->name, 0, 1) }}</span>
                            </div>
                            <h4 class="mb-1">{{ $user->name }}</h4>
                            <p class="text-muted mb-0">
                                @if($user->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-danger">غير نشط</span>
                                @endif
                            </p>
                            <p class="text-muted mt-2">
                                <span class="badge bg-info">{{ $user->type->label() }}</span>
                            </p>
                        </div>
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-borderless">
                                    <tbody>
                                        <tr>
                                            <th width="30%">الاسم:</th>
                                            <td>{{ $user->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>البريد الإلكتروني:</th>
                                            <td>{{ $user->email }}</td>
                                        </tr>
                                        @if($user->phone)
                                            <tr>
                                                <th>رقم الهاتف:</th>
                                                <td>{{ $user->phone }}</td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>النوع:</th>
                                            <td>{{ $user->type->label() }}</td>
                                        </tr>
                                        @if($user->employeeType)
                                            <tr>
                                                <th>نوع الموظف:</th>
                                                <td>{{ $user->employeeType->name }}</td>
                                            </tr>
                                        @endif
                                        @if($user->roles->isNotEmpty())
                                            <tr>
                                                <th>الأدوار:</th>
                                                <td>
                                                    @foreach($user->roles as $role)
                                                        <span class="badge bg-primary me-1">{{ $role->name }}</span>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        @endif
                                        <tr>
                                            <th>تاريخ التسجيل:</th>
                                            <td>{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <th>آخر تحديث:</th>
                                            <td>{{ $user->updated_at->format('Y-m-d H:i') }}</td>
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

