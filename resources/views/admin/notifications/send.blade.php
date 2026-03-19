@extends('layouts.vertical', ['title' => 'إرسال إشعار'])

@section('content')

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">إرسال إشعار مخصص</h4>
                <iconify-icon icon="solar:bell-bing-bold-duotone" class="fs-2 text-primary"></iconify-icon>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.notifications.store') }}">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="title" class="form-label">عنوان الإشعار <span class="text-danger">*</span></label>
                        <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror"
                               placeholder="مثلاً: عرض خاص جديد" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="body" class="form-label">محتوى الإشعار <span class="text-danger">*</span></label>
                        <textarea id="body" name="body" class="form-control @error('body') is-invalid @enderror" 
                                  rows="4" placeholder="اكتب تفاصيل الإشعار هنا..." required>{{ old('body') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="image_url" class="form-label">رابط صورة (اختياري)</label>
                        <input type="url" id="image_url" name="image_url" class="form-control @error('image_url') is-invalid @enderror"
                               placeholder="https://example.com/image.png" value="{{ old('image_url') }}">
                        @error('image_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="mb-4">
                        <label class="form-label d-block mb-2">تحديد المستهدفين <span class="text-danger">*</span></label>
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="target_type" id="target_all" value="all" {{ old('target_type', 'all') == 'all' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="target_all">
                                        <span class="fs-16 text-dark d-block">الكل</span>
                                        <span class="text-muted">إرسال للجميع (مدراء ومندوبين)</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="target_type" id="target_admins" value="admins" {{ old('target_type') == 'admins' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="target_admins">
                                        <span class="fs-16 text-dark d-block">المدراء</span>
                                        <span class="text-muted">إرسال لجميع موظفي الإدارة فقط</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="target_type" id="target_representatives" value="representatives" {{ old('target_type') == 'representatives' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="target_representatives">
                                        <span class="fs-16 text-dark d-block">المناديب</span>
                                        <span class="text-muted">إرسال لجميع المندوبين المشتركين</span>
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check card-radio">
                                    <input class="form-check-input" type="radio" name="target_type" id="target_specific" value="specific_user" {{ old('target_type') == 'specific_user' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="target_specific">
                                        <span class="fs-16 text-dark d-block">مستلم محدد</span>
                                        <span class="text-muted">اختر شخصاً واحداً لإرسال الإشعار له</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="recipient_search_wrapper" style="display: none;">
                        <div class="mb-3">
                            <label for="recipient_query" class="form-label">ابحث عن الاسم أو الهاتف</label>
                            <div class="input-group">
                                <span class="input-group-text"><iconify-icon icon="solar:magnifer-bold-duotone"></iconify-icon></span>
                                <input type="text" id="recipient_query" class="form-control" placeholder="اكتب للبحث...">
                            </div>
                            <div id="search_results" class="list-group mt-2 shadow-sm border rounded-3" style="max-height: 200px; overflow-y: auto; display: none;">
                                <!-- Results will be injected here -->
                            </div>
                            <input type="hidden" name="target_id" id="target_id" value="{{ old('target_id') }}">
                            <div id="selected_recipient_badge" class="mt-2" style="display: none;">
                                <span class="badge bg-soft-primary text-primary fs-14 p-2">
                                    <iconify-icon icon="solar:user-check-bold" class="me-1"></iconify-icon>
                                    المستلم المحدد: <span id="selected_name">---</span>
                                    <button type="button" class="btn-close ms-2 fs-10" id="clear_recipient"></button>
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 pt-2 text-center text-lg-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <iconify-icon icon="solar:plain-bold-duotone" class="me-1"></iconify-icon>
                            إرسال الإشعار الآن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const targetRadios = document.querySelectorAll('input[name="target_type"]');
        const searchWrapper = document.getElementById('recipient_search_wrapper');
        const searchQuery = document.getElementById('recipient_query');
        const searchResults = document.getElementById('search_results');
        const targetIdInput = document.getElementById('target_id');
        const selectedBadge = document.getElementById('selected_recipient_badge');
        const selectedName = document.getElementById('selected_name');
        const clearBtn = document.getElementById('clear_recipient');

        function toggleSearch() {
            const selectedType = document.querySelector('input[name="target_type"]:checked').value;
            if (selectedType === 'specific_user' || selectedType === 'specific_representative') {
                searchWrapper.style.display = 'block';
            } else {
                searchWrapper.style.display = 'none';
                clearRecipient();
            }
        }

        targetRadios.forEach(radio => radio.addEventListener('change', toggleSearch));
        toggleSearch(); // Initial check

        let timeout = null;
        searchQuery.addEventListener('input', function() {
            clearTimeout(timeout);
            const query = this.value;
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }

            timeout = setTimeout(() => {
                fetch(`{{ route('admin.notifications.search-recipients') }}?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        searchResults.innerHTML = '';
                        if (data.data && data.data.length > 0) {
                            data.data.forEach(item => {
                                const btn = document.createElement('button');
                                btn.type = 'button';
                                btn.className = 'list-group-item list-group-item-action py-2';
                                btn.innerHTML = `
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0 text-dark font-15">${item.name}</h6>
                                            <p class="mb-0 text-muted font-13">${item.phone || '---'}</p>
                                        </div>
                                        <iconify-icon icon="solar:add-circle-bold-duotone" class="fs-20 text-primary"></iconify-icon>
                                    </div>
                                `;
                                btn.onclick = () => selectRecipient(item.id, item.name);
                                searchResults.appendChild(btn);
                            });
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="p-3 text-center text-muted">لا توجد نتائج</div>';
                            searchResults.style.display = 'block';
                        }
                    });
            }, 500);
        });

        function selectRecipient(id, name) {
            targetIdInput.value = id;
            selectedName.innerText = name;
            selectedBadge.style.display = 'block';
            searchResults.style.display = 'none';
            searchQuery.value = '';
        }

        function clearRecipient() {
            targetIdInput.value = '';
            selectedName.innerText = '---';
            selectedBadge.style.display = 'none';
        }

        clearBtn.onclick = clearRecipient;

        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchWrapper.contains(e.target)) {
                searchResults.style.display = 'none';
            }
        });
    });
</script>

<style>
    .card-radio {
        padding: 0;
    }
    .card-radio .form-check-input {
        display: none;
    }
    .card-radio .form-check-label {
        background-color: #fff;
        border: 1.5px solid #e9ebec;
        border-radius: 12px;
        padding: 15px;
        width: 100%;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }
    .card-radio .form-check-label:hover {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.02);
    }
    .card-radio .form-check-input:checked + .form-check-label {
        border-color: var(--bs-primary);
        background-color: rgba(var(--bs-primary-rgb), 0.05);
    }
    .card-radio .form-check-input:checked + .form-check-label:after {
        content: "\ec3e";
        font-family: 'remixicon' !important;
        position: absolute;
        top: 10px;
        left: 10px;
        color: var(--bs-primary);
        font-size: 18px;
    }
    .bg-soft-primary {
        background-color: rgba(var(--bs-primary-rgb), 0.1) !important;
    }
</style>
@endsection
