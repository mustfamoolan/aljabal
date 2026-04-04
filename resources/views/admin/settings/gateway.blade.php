@extends('layouts.vertical', ['title' => 'إعدادات ربط الوسيط (Gateway)'])

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

<!-- Connection Settings -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">بيانات ربط شركة الوسيط</h4>
            </div>
            <div class="card-body">
                @if($setting && $setting->is_connected && $setting->api_key)
                    <div class="alert alert-info">
                        <strong>حالة الاتصال:</strong> <span class="badge bg-success">متصل بالبوابة</span><br>
                        <small class="text-muted">مفتاح API الخاص بمشروعك محفوظ بأمان ولن يظهر أبدًا لدواعي أمنية.</small>
                    </div>
                @else
                    <div class="alert alert-warning">
                        <strong>حالة الاتصال:</strong> <span class="badge bg-danger">غير متصل</span><br>
                        <small>أدخل بيانات "الوسيط" خاصتك ليتم إنشاء قناة اتصال ومفتاح سري آلياً لك في البوابة.</small>
                    </div>
                @endif

                <form action="{{ route('admin.settings.gateway.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">اسم المستخدم (Merchant Username)</label>
                        <input type="text" class="form-control" name="waseet_username" 
                               value="{{ $setting->waseet_username ?? '' }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">كلمة المرور (Merchant Password)</label>
                        <input type="password" class="form-control" name="waseet_password" 
                               placeholder="أدخل كلمة المرور الخاصة بحساب الشركة" required>
                    </div>
                    <button type="submit" class="btn btn-primary">حفظ وتسجيل الدخول</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Sync Locations -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">مزامنة المحافظات والمناطق</h4>
            </div>
            <div class="card-body text-center">
                <p>
                    هذه الأداة تقوم بسحب أحدث قوائم <strong>(المحافظات)</strong> و <strong>(المناطق)</strong> من سيرفرات شركة (الوسيط)
                    وتحديثها في قاعدة بيانات (الجبل) لتظهر في تطبيق المندوب فوراً وبلا أخطاء.
                </p>

                @if($setting && $setting->last_sync_at)
                    <p class="text-muted mb-4">
                        <i class="fas fa-clock"></i> <strong>تاريخ آخر مزامنة:</strong> 
                        <span dir="ltr">{{ $setting->last_sync_at->format('Y-m-d H:i') }}</span>
                    </p>
                @endif

                <form action="{{ route('admin.settings.gateway.sync') }}" method="POST">
                    @csrf
                    @if(!$setting || !$setting->is_connected)
                        <button type="button" class="btn btn-secondary w-100" disabled>
                            يجب تسجيل الدخولأولاً
                        </button>
                    @else
                        <button type="submit" class="btn btn-success w-100" onclick="this.innerHTML='جاري سحب آلاف المناطق... برجاء الانتظار قليلاً'; this.classList.add('disabled')">
                            <i class="fas fa-sync"></i> بدء المزامنة الآن
                        </button>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
