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
                            <div class="position-relative d-inline-block mb-3">
                                @if($user->image && $user->image_url)
                                    <img src="{{ $user->image_url }}" alt="{{ $user->name }}" 
                                         class="avatar-lg rounded-circle border border-3 border-primary" 
                                         style="width: 120px; height: 120px; object-fit: cover;">
                                @else
                                    <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center border border-3 border-primary">
                                        <span class="text-primary fw-bold fs-48">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                                <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" 
                                        data-bs-toggle="modal" data-bs-target="#updateImageModal"
                                        style="width: 36px; height: 36px; padding: 0;">
                                    <iconify-icon icon="solar:camera-bold-duotone" class="fs-18"></iconify-icon>
                                </button>
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

    <!-- Update Image Modal -->
    <div class="modal fade" id="updateImageModal" tabindex="-1" aria-labelledby="updateImageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('admin.profile.update-image') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateImageModalLabel">تحديث صورة البروفايل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="image" class="form-label">اختر صورة جديدة <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/gif" required>
                            <small class="text-muted">الصيغ المدعومة: JPEG, PNG, JPG, GIF. الحد الأقصى: 2MB</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">معاينة الصورة</label>
                            <div id="imagePreview" class="text-center" style="display: none;">
                                <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px; max-height: 200px;">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                document.getElementById('imagePreview').style.display = 'none';
            }
        });
    </script>
    @endpush
@endsection

