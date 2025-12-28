@extends('layouts.auth', ['title' => 'Representative Login'])

@section('content')
    <div class="d-flex flex-column h-100 p-3">
        <div class="d-flex flex-column flex-grow-1">
            <div class="row h-100">
                <div class="col-xxl-7">
                    <div class="row justify-content-center h-100">
                        <div class="col-lg-6 py-lg-5">
                            <div class="d-flex flex-column h-100 justify-content-center">
                                <div class="auth-logo mb-4">
                                    <a href="{{ route('representative.dashboard') }}" class="logo-dark">
                                        <img src="/images/logo-dark.png" height="24" alt="logo dark">
                                    </a>

                                    <a href="{{ route('representative.dashboard') }}" class="logo-light">
                                        <img src="/images/logo-light.png" height="24" alt="logo light">
                                    </a>
                                </div>

                                <h2 class="fw-bold fs-24">تسجيل الدخول - المندوبين</h2>

                                <p class="text-muted mt-1 mb-4">أدخل رقم الهاتف أو البريد الإلكتروني وكلمة المرور للوصول إلى لوحة التحكم.</p>

                                <div class="mb-5">
                                    <form method="POST" action="{{ route('representative.login') }}" class="authentication-form">
                                        @csrf
                                        @if (sizeof($errors) > 0)
                                            @foreach ($errors->all() as $error)
                                                <p class="text-danger mb-3">{{ $error }}</p>
                                            @endforeach
                                        @endif

                                        <div class="mb-3">
                                            <label class="form-label" for="login">رقم الهاتف أو البريد الإلكتروني</label>
                                            <input type="text" id="login" name="login"
                                                   class="form-control bg-" placeholder="أدخل رقم الهاتف أو البريد الإلكتروني"
                                                   value="{{ old('login') }}" required autofocus>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label" for="password">كلمة المرور</label>
                                            <input type="password" id="password" name="password" class="form-control"
                                                   placeholder="أدخل كلمة المرور" required>
                                        </div>
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">تذكرني</label>
                                            </div>
                                        </div>

                                        <div class="mb-1 text-center d-grid">
                                            <button class="btn btn-soft-primary" type="submit">تسجيل الدخول</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xxl-5 d-none d-xxl-flex">
                    <div class="card h-100 mb-0 overflow-hidden">
                        <div class="d-flex flex-column h-100">
                            <img src="/images/small/img-10.jpg" alt="" class="w-100 h-100">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

