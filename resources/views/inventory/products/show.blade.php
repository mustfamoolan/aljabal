@extends('layouts.vertical', ['title' => 'Product Details'])

@section('content')

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                @php
                    $hasVideo = !empty($product->video_url);
                    $videoEmbedUrl = $hasVideo ? get_video_embed_url($product->video_url) : null;
                    $videoThumbnail = $hasVideo ? get_video_thumbnail($product->video_url) : null;
                    $totalItems = $product->images->count() + ($hasVideo ? 1 : 0);
                @endphp
                @if($product->images->isNotEmpty() || $hasVideo)
                    <!-- Crossfade Carousel -->
                    <div id="carouselExampleFade" class="carousel slide carousel-fade" data-bs-ride="false" data-bs-interval="false">
                        <div class="carousel-inner" role="listbox" style="min-height: 400px; display: flex; align-items: center;">
                            @if($hasVideo)
                                <div class="carousel-item active">
                                    <div class="ratio ratio-16x9">
                                        <iframe id="productVideo" src="{{ $videoEmbedUrl }}" class="rounded" allowfullscreen></iframe>
                                    </div>
                                </div>
                            @endif
                            @foreach($product->images as $index => $image)
                                <div class="carousel-item {{ !$hasVideo && $index === 0 ? 'active' : '' }}" style="display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ storage_url($image->image_path) }}" alt="{{ $product->name }}" class="img-fluid bg-light rounded" style="max-height: 400px; width: auto; object-fit: contain;">
                                </div>
                            @endforeach
                        </div>
                        @if($totalItems > 1)
                            <a class="carousel-control-prev rounded" href="#carouselExampleFade" role="button" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Previous</span>
                            </a>
                            <a class="carousel-control-next rounded" href="#carouselExampleFade" role="button" data-bs-slide="next">
                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                <span class="visually-hidden">Next</span>
                            </a>
                        @endif
                    </div>
                    <div class="carousel-indicators m-0 mt-2 d-lg-flex d-none position-static h-100">
                        @if($hasVideo)
                            <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="0"
                                    aria-label="Video"
                                    class="w-auto h-auto rounded bg-light active">
                                @if($videoThumbnail)
                                    <img src="{{ $videoThumbnail }}" class="d-block avatar-xl" alt="Video thumbnail">
                                @else
                                    <div class="d-block avatar-xl bg-primary d-flex align-items-center justify-content-center">
                                        <iconify-icon icon="solar:play-bold-duotone" class="text-white fs-20"></iconify-icon>
                                    </div>
                                @endif
                            </button>
                        @endif
                        @foreach($product->images as $index => $image)
                            <button type="button" data-bs-target="#carouselExampleFade" data-bs-slide-to="{{ $hasVideo ? $index + 1 : $index }}"
                                    aria-label="Slide {{ $index + 1 }}"
                                    class="w-auto h-auto rounded bg-light {{ !$hasVideo && $index === 0 ? 'active' : '' }}">
                                <img src="{{ storage_url($image->image_path) }}" class="d-block avatar-xl" alt="swiper-indicator-img">
                            </button>
                        @endforeach
                    </div>
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                        <iconify-icon icon="solar:box-bold-duotone" class="fs-64 text-muted"></iconify-icon>
                    </div>
                @endif
            </div>
            <div class="card-footer border-top">
                <div class="row g-2">
                    <div class="col-lg-5">
                        <a href="{{ route('inventory.products.edit', $product) }}" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 w-100">
                            <iconify-icon icon="solar:pen-2-bold-duotone" class="fs-18"></iconify-icon>
                            تعديل
                        </a>
                    </div>
                    <div class="col-lg-5">
                        <a href="{{ route('inventory.products.index') }}" class="btn btn-light d-flex align-items-center justify-content-center gap-2 w-100">
                            <iconify-icon icon="solar:arrow-left-bold-duotone" class="fs-18"></iconify-icon>
                            رجوع
                        </a>
                    </div>
                    <div class="col-lg-2">
                        <a href="{{ route('inventory.products.show', $product) }}" class="btn btn-soft-danger d-inline-flex align-items-center justify-content-center fs-20 rounded w-100">
                            <iconify-icon icon="solar:heart-broken"></iconify-icon>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                @if($product->product_type->value === 'original')
                    <h4 class="badge bg-success text-light fs-14 py-1 px-2">أصلي</h4>
                @endif
                <p class="mb-1">
                    <a href="{{ route('inventory.products.show', $product) }}" class="fs-24 text-dark fw-medium">{{ $product->name }}</a>
                </p>
                <div class="d-flex flex-wrap gap-2 mb-2">
                    @if($product->category)
                        <span class="text-muted">الفئة الرئيسية:</span>
                        <span class="badge bg-info-subtle text-info">{{ $product->category->name }}</span>
                    @endif
                    @if($product->subcategory)
                        <span class="text-muted">الفئة الفرعية:</span>
                        <span class="badge bg-secondary-subtle text-secondary">{{ $product->subcategory->name }}</span>
                    @endif
                </div>
                @if($product->tags->isNotEmpty())
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="text-muted">التاغات:</span>
                        @foreach($product->tags as $tag)
                            <a href="{{ route('inventory.products.index', ['tag_id' => $tag->id]) }}" class="badge bg-primary-subtle text-primary">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                @endif
                <h2 class="fw-medium my-3">
                    @if($product->retail_price)
                        {{ format_currency($product->retail_price) }}
                    @else
                        <span class="text-muted">-</span>
                    @endif
                    @if($product->purchase_price && $product->retail_price && auth()->user()->canViewPurchasePrice())
                        <span class="fs-16 text-muted">(شراء: {{ format_currency($product->purchase_price) }})</span>
                    @endif
                </h2>

                <div class="row align-items-center g-2 mt-3">
                    @if($product->color)
                    <div class="col-lg-3">
                        <div class="">
                            <h5 class="text-dark fw-medium">اللون > <span class="text-muted">{{ $product->color }}</span></h5>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="quantity mt-4">
                    <h4 class="text-dark fw-medium mt-3">الكمية :</h4>
                    <div class="d-flex align-items-center gap-3">
                        <span class="badge bg-{{ $product->quantity > 0 ? 'success' : 'danger' }}-subtle text-{{ $product->quantity > 0 ? 'success' : 'danger' }} py-2 px-3 fs-16">
                            {{ $product->quantity }} قطعة
                        </span>
                        @if($product->min_quantity !== null && $product->quantity <= $product->min_quantity)
                            <span class="badge bg-danger-subtle text-danger py-2 px-3">
                                <iconify-icon icon="solar:danger-triangle-bold-duotone"></iconify-icon>
                                منخفض المخزون
                            </span>
                        @endif
                    </div>
                </div>
                <ul class="d-flex flex-column gap-2 list-unstyled fs-15 my-3">
                    <li>
                        <i class='bx bx-check text-success'></i>
                        @if($product->quantity > 0)
                            متوفر في المخزون
                        @else
                            <span class="text-danger">نفد من المخزون</span>
                        @endif
                    </li>
                    @if($product->sku)
                    <li>
                        <i class='bx bx-check text-success'></i> كود المنتج: <span class="text-dark fw-medium">{{ $product->sku }}</span>
                    </li>
                    @endif
                    @if($product->supplier)
                    <li>
                        <i class='bx bx-check text-success'></i> المورد: <span class="text-dark fw-medium">{{ $product->supplier->name }}</span>
                    </li>
                    @endif
                </ul>
                @if($product->short_description)
                <h4 class="text-dark fw-medium">الوصف :</h4>
                <p class="text-muted">{{ $product->short_description }}</p>
                @endif
                @if($product->long_description)
                <p class="text-muted">{{ $product->long_description }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تفاصيل المنتج</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-6">
                        <ul class="d-flex flex-column gap-2 list-unstyled fs-14 text-muted mb-0">
                            @if($product->sku)
                            <li><span class="fw-medium text-dark">كود المنتج (SKU)</span><span class="mx-2">:</span>{{ $product->sku }}</li>
                            @endif
                            @if($product->author)
                            <li><span class="fw-medium text-dark">المؤلف</span><span class="mx-2">:</span>{{ $product->author }}</li>
                            @endif
                            @if($product->publisher)
                            <li><span class="fw-medium text-dark">دار النشر</span><span class="mx-2">:</span>{{ $product->publisher }}</li>
                            @endif
                            @if($product->category)
                            <li><span class="fw-medium text-dark">الفئة الرئيسية</span><span class="mx-2">:</span>{{ $product->category->name }}</li>
                            @endif
                            @if($product->subcategory)
                            <li><span class="fw-medium text-dark">الفئة الفرعية</span><span class="mx-2">:</span>{{ $product->subcategory->name }}</li>
                            @endif
                            @if($product->supplier)
                            <li><span class="fw-medium text-dark">المورد</span><span class="mx-2">:</span>{{ $product->supplier->name }}</li>
                            @endif
                    @if($product->purchase_price && auth()->user()->canViewPurchasePrice())
                    <li><span class="fw-medium text-dark">سعر الشراء</span><span class="mx-2">:</span>{{ format_currency($product->purchase_price) }}</li>
                    @endif
                            @if($product->retail_price)
                            <li><span class="fw-medium text-dark">سعر البيع مفرد</span><span class="mx-2">:</span>{{ format_currency($product->retail_price) }}</li>
                            @endif
                            @if($product->wholesale_price)
                            <li><span class="fw-medium text-dark">سعر البيع جملة</span><span class="mx-2">:</span>{{ format_currency($product->wholesale_price) }}</li>
                            @endif
                            <li><span class="fw-medium text-dark">الكمية</span><span class="mx-2">:</span>{{ $product->quantity }} قطعة</li>
                            @if($product->min_quantity)
                            <li><span class="fw-medium text-dark">الحد الأدنى</span><span class="mx-2">:</span>{{ $product->min_quantity }} قطعة</li>
                            @endif
                            @if($product->shelf || $product->compartment)
                            <li><span class="fw-medium text-dark">مكان التخزين</span><span class="mx-2">:</span>
                                @if($product->shelf && $product->compartment)
                                    الرف: {{ $product->shelf }} | الخانة: {{ $product->compartment }}
                                @elseif($product->shelf)
                                    الرف: {{ $product->shelf }}
                                @elseif($product->compartment)
                                    الخانة: {{ $product->compartment }}
                                @endif
                            </li>
                            @endif
                            @if($product->weight)
                            <li><span class="fw-medium text-dark">الوزن</span><span class="mx-2">:</span>{{ number_format($product->weight, 2) }} كيلوغرام</li>
                            @endif
                            @if($product->size)
                            <li><span class="fw-medium text-dark">الحجم</span><span class="mx-2">:</span>{{ $product->size }}</li>
                            @endif
                            @if($product->page_count)
                            <li><span class="fw-medium text-dark">عدد الصفحات</span><span class="mx-2">:</span>{{ $product->page_count }} صفحة</li>
                            @endif
                            @if($product->color)
                            <li><span class="fw-medium text-dark">اللون</span><span class="mx-2">:</span>{{ $product->color }}</li>
                            @endif
                            <li><span class="fw-medium text-dark">الحالة</span><span class="mx-2">:</span>
                                <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}-subtle text-{{ $product->is_active ? 'success' : 'danger' }}">
                                    {{ $product->is_active ? 'نشط' : 'غير نشط' }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($product->purchaseHistory->isNotEmpty())
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تاريخ المشتريات</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover table-centered">
                        <thead class="bg-light-subtle">
                            <tr>
                                <th>التاريخ</th>
                                <th>الكمية</th>
                                @if(auth()->user()->canViewPurchasePrice())
                                <th>سعر الشراء</th>
                                <th>الإجمالي</th>
                                @endif
                                <th>المورد</th>
                                <th>المسؤول</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($product->purchaseHistory as $purchase)
                            <tr>
                                <td>{{ $purchase->purchase_date->format('d M Y') }}</td>
                                <td>{{ $purchase->quantity }}</td>
                                @if(auth()->user()->canViewPurchasePrice())
                                <td>{{ format_currency($purchase->purchase_price) }}</td>
                                <td>{{ format_currency($purchase->total_amount) }}</td>
                                @endif
                                <td>{{ $purchase->supplier?->name ?? '-' }}</td>
                                <td>{{ $purchase->createdBy?->name ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">إضافة كمية</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.inventory.products.add-quantity', $product) }}">
                    @csrf
                    <div class="row">
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">الكمية <span class="text-danger">*</span></label>
                                <input type="number" id="quantity" name="quantity" class="form-control" placeholder="0" min="1" required>
                            </div>
                        </div>
                        @if(auth()->user()->canViewPurchasePrice())
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">سعر الشراء</label>
                                <input type="number" step="0.01" id="purchase_price" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>
                        @endif
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">المورد</label>
                                <select id="supplier_id" name="supplier_id" class="form-select">
                                    <option value="">اختر المورد</option>
                                    @foreach(\App\Models\Supplier::where('is_active', true)->get() as $supplier)
                                        <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="mb-3">
                                <label for="purchase_date" class="form-label">تاريخ الشراء</label>
                                <input type="date" id="purchase_date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="mb-3">
                                <label for="notes" class="form-label">ملاحظات</label>
                                <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="ملاحظات إضافية"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">إضافة الكمية</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script-bottom')
@vite(['resources/js/pages/ecommerce-product-details.js'])
@if($hasVideo)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.getElementById('carouselExampleFade');
        const videoIframe = document.getElementById('productVideo');
        
        if (carousel && videoIframe) {
            // Function to stop video by changing src
            function stopVideo() {
                const currentSrc = videoIframe.src;
                // Remove the src to stop the video
                videoIframe.src = '';
                // Restore it after a brief moment (this stops the video)
                setTimeout(() => {
                    videoIframe.src = currentSrc;
                }, 100);
            }
            
            // Listen for slide change events
            carousel.addEventListener('slide.bs.carousel', function (e) {
                // If we're leaving the video slide, stop the video
                if (e.from === 0) {
                    stopVideo();
                }
            });
            
            // Also stop video when clicking on indicators or controls
            carousel.addEventListener('slid.bs.carousel', function (e) {
                // If we're not on the video slide (index 0), make sure video is stopped
                if (e.to !== 0) {
                    stopVideo();
                }
            });
        }
    });
</script>
@endif
@endsection
