@extends('layouts.vertical', ['title' => 'الإعدادات العامة'])

@section('content')

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Withdrawal Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title d-flex align-items-center gap-2">
                    <iconify-icon icon="solar:wallet-money-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    إعدادات السحب
                </h4>
            </div>
            <div class="card-body">
                <!-- General Settings -->
                <div class="mb-4">
                    <h5 class="mb-3">الإعدادات العامة</h5>
                    <form action="{{ route('general.settings.withdrawal.update-general') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الحد الأدنى للسحب (دينار عراقي) <span class="text-danger">*</span></label>
                                    <input type="number" class="form-control" name="min_withdrawal_amount" 
                                           value="{{ $generalSetting->min_withdrawal_amount ?? 0 }}" 
                                           step="0.01" min="0" required>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary">حفظ الإعدادات العامة</button>
                            </div>
                        </div>
                    </form>
                </div>

                <hr>

                <!-- Exceptions -->
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0">الاستثناءات</h5>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addExceptionModal">
                            <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon>
                            إضافة استثناء
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المندوب</th>
                                    <th>الحد الأدنى</th>
                                    <th>تاريخ الإنشاء</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($exceptions as $exception)
                                    <tr>
                                        <td>{{ $exception->representative->name }}</td>
                                        <td>{{ format_currency($exception->min_withdrawal_amount) }}</td>
                                        <td>{{ $exception->created_at->format('Y-m-d') }}</td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editExceptionModal{{ $exception->id }}">
                                                <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                                تعديل
                                            </button>
                                            <form action="{{ route('general.settings.withdrawal.delete-exception', $exception) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا الاستثناء؟')">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                    حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Exception Modal -->
                                    <div class="modal fade" id="editExceptionModal{{ $exception->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('general.settings.withdrawal.update-exception', $exception) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تعديل استثناء</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label">المندوب</label>
                                                            <input type="text" class="form-control" value="{{ $exception->representative->name }}" disabled>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">الحد الأدنى (دينار عراقي) <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="min_withdrawal_amount" 
                                                                   value="{{ $exception->min_withdrawal_amount }}" 
                                                                   step="0.01" min="0" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد استثناءات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gift Settings -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title d-flex align-items-center gap-2 mb-0">
                    <iconify-icon icon="solar:gift-bold-duotone" class="text-primary fs-20"></iconify-icon>
                    إعدادات الهدايا
                </h4>
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addGiftModal">
                    <iconify-icon icon="solar:add-circle-bold-duotone"></iconify-icon>
                    إضافة هدية
                </button>
            </div>
            <div class="card-body">
                <!-- Gifts -->
                <div class="mb-4">
                    <h5 class="mb-3">الهدايا</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>السعر</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gifts as $gift)
                                    <tr>
                                        <td>{{ $gift->name }}</td>
                                        <td>{{ format_currency($gift->price) }}</td>
                                        <td>
                                            @if($gift->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editGiftModal{{ $gift->id }}">
                                                <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                                تعديل
                                            </button>
                                            <form action="{{ route('general.settings.gifts.delete', $gift) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذه الهدية؟')">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                    حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Gift Modal -->
                                    <div class="modal fade" id="editGiftModal{{ $gift->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('general.settings.gifts.update', $gift) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تعديل هدية</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="type" value="gift">
                                                        <div class="mb-3">
                                                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="name" value="{{ $gift->name }}" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">السعر (دينار عراقي) <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="price" value="{{ $gift->price }}" step="0.01" min="0" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_{{ $gift->id }}" {{ $gift->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="is_active_{{ $gift->id }}">
                                                                    نشط
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">لا توجد هدايا</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <hr>

                <!-- Gift Boxes -->
                <div>
                    <h5 class="mb-3">بوكسات الهدايا</h5>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>عدد الكتب (من - إلى)</th>
                                    <th>سعر البوكس</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($giftBoxes as $box)
                                    <tr>
                                        <td>{{ $box->name }}</td>
                                        <td>
                                            @if($box->min_books && $box->max_books)
                                                {{ $box->min_books }} - {{ $box->max_books }} كتاب
                                            @elseif($box->min_books)
                                                {{ $box->min_books }} كتاب فأكثر
                                            @elseif($box->max_books)
                                                حتى {{ $box->max_books }} كتاب
                                            @else
                                                جميع الأعداد
                                            @endif
                                        </td>
                                        <td>{{ format_currency($box->box_price) }}</td>
                                        <td>
                                            @if($box->is_active)
                                                <span class="badge bg-success">نشط</span>
                                            @else
                                                <span class="badge bg-secondary">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editGiftBoxModal{{ $box->id }}">
                                                <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                                تعديل
                                            </button>
                                            <form action="{{ route('general.settings.gifts.delete', $box) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" 
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا البوكس؟')">
                                                    <iconify-icon icon="solar:trash-bin-minimalistic-bold-duotone"></iconify-icon>
                                                    حذف
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                    <!-- Edit Gift Box Modal -->
                                    <div class="modal fade" id="editGiftBoxModal{{ $box->id }}" tabindex="-1">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('general.settings.gifts.update', $box) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">تعديل بوكس هدية</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" name="type" value="gift_box">
                                                        <div class="mb-3">
                                                            <label class="form-label">الاسم <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control" name="name" value="{{ $box->name }}" required>
                                                        </div>
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">الحد الأدنى لعدد الكتب</label>
                                                                    <input type="number" class="form-control" name="min_books" value="{{ $box->min_books }}" min="1">
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="mb-3">
                                                                    <label class="form-label">الحد الأقصى لعدد الكتب</label>
                                                                    <input type="number" class="form-control" name="max_books" value="{{ $box->max_books }}" min="1">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label">سعر البوكس (دينار عراقي) <span class="text-danger">*</span></label>
                                                            <input type="number" class="form-control" name="box_price" value="{{ $box->box_price }}" step="0.01" min="0" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active_box_{{ $box->id }}" {{ $box->is_active ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="is_active_box_{{ $box->id }}">
                                                                    نشط
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">لا توجد بوكسات هدايا</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Exception Modal -->
<div class="modal fade" id="addExceptionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('general.settings.withdrawal.create-exception') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة استثناء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المندوب <span class="text-danger">*</span></label>
                        <select class="form-select" name="representative_id" required>
                            <option value="">اختر المندوب</option>
                            @foreach(\App\Models\Representative::all() as $rep)
                                <option value="{{ $rep->id }}">{{ $rep->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الحد الأدنى (دينار عراقي) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" name="min_withdrawal_amount" 
                               step="0.01" min="0" required>
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

<!-- Add Gift Modal -->
<div class="modal fade" id="addGiftModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('general.settings.gifts.store') }}" method="POST" id="addGiftForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة هدية / بوكس هدية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">النوع <span class="text-danger">*</span></label>
                        <select class="form-select" name="type" id="gift_type" required>
                            <option value="">اختر النوع</option>
                            <option value="gift">هدية</option>
                            <option value="gift_box">بوكس هدية</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div id="gift_fields" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">السعر (دينار عراقي) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0">
                        </div>
                    </div>
                    <div id="gift_box_fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الحد الأدنى لعدد الكتب</label>
                                    <input type="number" class="form-control" name="min_books" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">الحد الأقصى لعدد الكتب</label>
                                    <input type="number" class="form-control" name="max_books" min="1">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">سعر البوكس (دينار عراقي) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" name="box_price" step="0.01" min="0">
                        </div>
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

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const giftTypeSelect = document.getElementById('gift_type');
    const giftFields = document.getElementById('gift_fields');
    const giftBoxFields = document.getElementById('gift_box_fields');
    const addGiftForm = document.getElementById('addGiftForm');

    if (giftTypeSelect) {
        giftTypeSelect.addEventListener('change', function() {
            if (this.value === 'gift') {
                giftFields.style.display = 'block';
                giftBoxFields.style.display = 'none';
                // Make price required
                giftFields.querySelector('input[name="price"]').required = true;
                // Remove required from gift box fields
                giftBoxFields.querySelectorAll('input').forEach(input => {
                    input.required = false;
                });
            } else if (this.value === 'gift_box') {
                giftFields.style.display = 'none';
                giftBoxFields.style.display = 'block';
                // Remove required from gift price
                giftFields.querySelector('input[name="price"]').required = false;
                // Make box_price required
                giftBoxFields.querySelector('input[name="box_price"]').required = true;
            } else {
                giftFields.style.display = 'none';
                giftBoxFields.style.display = 'none';
            }
        });
    }
});
</script>
@endsection
