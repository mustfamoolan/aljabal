<?php

namespace App\Http\Controllers\Api\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Services\TelegramBotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    protected TelegramBotService $telegram;

    public function __construct(TelegramBotService $telegram)
    {
        $this->telegram = $telegram;
    }

    public function handle(Request $request)
    {
        $update = $request->all();

        // Handle simple text messages
        if (isset($update['message']['text'])) {
            $chatId = $update['message']['chat']['id'];
            $text = trim($update['message']['text']);

            $this->processMessage($chatId, $text);
        }

        return response()->json(['status' => 'ok']);
    }

    protected function processMessage($chatId, $text)
    {
        // 1. Check if user is already logged in
        $rep = Representative::where('telegram_chat_id', $chatId)->first();

        if ($text === '/start') {
            $this->clearState($chatId);
            if ($rep) {
                $this->sendMainMenu($chatId, "مرحباً مجدداً {$rep->name}! أنت مسجل دخول بالفعل. اختر من القائمة أدناه:");
            } else {
                $this->telegram->sendMessage($chatId, "مرحباً بك في بوت المندوبين 👋\nيرجى إدخال رقم هاتفك للبدء:");
                $this->setState($chatId, 'awaiting_phone');
            }
            return;
        }

        if ($text === '/info') {
            if ($rep) {
                $this->sendRepresentativeInfo($chatId, $rep);
            } else {
                $this->telegram->sendMessage($chatId, "أنت غير مسجل. يرجى إرسال /start للبدء.");
            }
            return;
        }

        if ($text === '/logout' || $text === 'تسجيل خروج 🚪') {
            if ($rep) {
                $rep->telegram_chat_id = null;
                $rep->save();
            }
            $this->clearState($chatId);
            $this->telegram->sendMessage($chatId, "تم تسجيل الخروج بنجاح.");
            return;
        }

        if ($rep) {
            $state = $this->getState($chatId);

            if ($text === 'استعلام عن طلب 📦') {
                $this->setState($chatId, 'awaiting_order_search');
                $this->telegram->sendMessage($chatId, "يرجى إرسال كود الوسيط أو رقم الطلب للاستعلام عنه:");
                return;
            }

            if ($text === 'بحث عن كتاب 🔍') {
                $this->setState($chatId, 'awaiting_book_search');
                $this->telegram->sendMessage($chatId, "يرجى كتابة اسم الكتاب أو المؤلف للبحث عنه:");
                return;
            }

            if ($text === 'معلومات حسابي 👤') {
                $this->sendRepresentativeInfo($chatId, $rep);
                return;
            }

            if ($text === '📝 إنشاء طلب جديد') {
                $template = "🛒 إنشاء طلب جديد:\n(انسخ هذه الرسالة، املأ الفراغات وأرسلها مجدداً)\n====================\n👤 الزبون: \n📞 الهاتف 1: \n📞 الهاتف 2: \n📱 صفحة الزبون: \n📍 المحافظة: \n🏘 المنطقة: \n🏠 العنوان: \n📝 ملاحظات: \n\n📚 الكتب:\n- اسم الكتاب الأول | الكمية: 1 | السعر: 15000\n- اسم الكتاب الثاني | الكمية: 2 | السعر: 10000\n";
                $this->telegram->sendMessage($chatId, $template);
                return;
            }

            if ($state === 'awaiting_order_search') {
                $this->handleOrderSearch($chatId, $rep->id, $text);
                $this->clearState($chatId);
                return;
            }

            if ($state === 'awaiting_book_search') {
                $this->handleBookSearch($chatId, $text);
                $this->clearState($chatId);
                return;
            }

            if (!$state && !str_starts_with($text, '/')) {
                // Check if text is a new order creation template
                if (str_starts_with(trim($text), '🛒 إنشاء طلب جديد:')) {
                    $this->handleOrderCreation($chatId, $rep, $text);
                    return;
                }

                // Check if text is a forwarded order template
                $phoneMatches = [];
                $accountMatches = [];
                $hasPhone = preg_match('/الرقم\s*:\s*([^\n\r]+)/ui', $text, $phoneMatches);
                $hasAccount = preg_match('/اسم الحساب\s*:?\s*([^\n\r]+)/ui', $text, $accountMatches);

                if ($hasPhone || $hasAccount) {
                    $phone = $hasPhone ? trim($phoneMatches[1]) : null;
                    $account = $hasAccount ? trim($accountMatches[1]) : null;
                    $this->handleTemplateOrderSearch($chatId, $rep->id, $phone, $account);
                    return;
                }

                // Default search
                $this->handleBookSearch($chatId, $text);
                return;
            }

            $this->sendMainMenu($chatId, "الرجاء اختيار إحدى الخدمات من القائمة:");
            return;
        }

        // 3. Process state-based inputs
        $state = $this->getState($chatId);

        if ($state === 'awaiting_phone') {
            $this->handlePhoneInput($chatId, $text);
        } elseif ($state === 'awaiting_password') {
            $this->handlePasswordInput($chatId, $text);
        } else {
            $this->telegram->sendMessage($chatId, "الرجاء إرسال /start للبدء.");
        }
    }

    protected function handlePhoneInput($chatId, $phone)
    {
        $phone = preg_replace('/\s+/', '', $phone);
        $rep = Representative::where('phone', $phone)->first();

        if ($rep) {
            if (!$rep->isActive()) {
                $this->telegram->sendMessage($chatId, "حسابك غير مفعل. يرجى التواصل مع الإدارة.");
                $this->clearState($chatId);
                return;
            }

            Cache::put("telegram_{$chatId}_phone", $phone, now()->addMinutes(10));
            $this->setState($chatId, 'awaiting_password');
            $this->telegram->sendMessage($chatId, "تم العثور على حسابك. الرجاء إدخال كلمة المرور:");
        } else {
            $this->telegram->sendMessage($chatId, "رقم الهاتف غير موجود ❌\nالرجاء التحقق وإرسال الرقم الصحيح، أو أرسل /start لإعادة المحاولة.");
        }
    }

    protected function handlePasswordInput($chatId, $password)
    {
        $phone = Cache::get("telegram_{$chatId}_phone");

        if (!$phone) {
            $this->telegram->sendMessage($chatId, "انتهت جلسة تسجيل الدخول. الرجاء إرسال /start من جديد.");
            $this->clearState($chatId);
            return;
        }

        $rep = Representative::where('phone', $phone)->first();

        if ($rep && Hash::check($password, $rep->password)) {
            $rep->telegram_chat_id = $chatId;
            $rep->save();

            $this->clearState($chatId);
            $this->telegram->sendMessage($chatId, "✅ تم تسجيل الدخول بنجاح!");
            $this->sendMainMenu($chatId, "أهلاً بك {$rep->name} 👋\nماذا ترغب أن تفعل الآن؟");
        } else {
            $this->telegram->sendMessage($chatId, "كلمة المرور غير صحيحة ❌\nالرجاء المحاولة مرة أخرى.");
        }
    }

    protected function sendMainMenu($chatId, $text)
    {
        $keyboard = [
            'keyboard' => [
                [['text' => '📝 إنشاء طلب جديد'], ['text' => 'استعلام عن طلب 📦']],
                [['text' => 'بحث عن كتاب 🔍'], ['text' => 'معلومات حسابي 👤']],
                [['text' => 'تسجيل خروج 🚪']],
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => false,
        ];
        $this->telegram->sendMessage($chatId, $text, $keyboard);
    }

    protected function sendRepresentativeInfo($chatId, Representative $rep)
    {
        $totalOrders = $rep->orders()->count();
        $completedOrders = $rep->orders()->where('status', 'completed')->count();
        
        $message = "👤 <b>معلومات المندوب</b>\n\n";
        $message .= "▪️ <b>الاسم:</b> {$rep->name}\n";
        $message .= "▪️ <b>الهاتف:</b> {$rep->phone}\n";
        $message .= "▪️ <b>إجمالي الطلبات:</b> {$totalOrders}\n";
        $message .= "▪️ <b>الطلبات المكتملة:</b> {$completedOrders}\n";
        $message .= "▪️ <b>الرصيد المتاح:</b> " . number_format($rep->available_balance, 0) . " د.ع\n";
        
        $this->telegram->sendMessage($chatId, $message);
    }

    protected function handleOrderSearch($chatId, $representativeId, $query)
    {
        $order = \App\Models\Order::where('representative_id', $representativeId)
            ->where(function ($q) use ($query) {
                $q->where('id', $query)
                  ->orWhere('waseet_order_id', $query)
                  ->orWhere('waseet_tracking_url', 'like', "%{$query}%");
            })
            ->first();

        if (!$order) {
            $this->telegram->sendMessage($chatId, "لم يتم العثور على طلب بهذا الرقم، أو أنه لا يخصك. ❌");
            return;
        }

        $message = "📦 <b>تفاصيل الطلب:</b>\n\n";
        $message .= "🔖 <b>رقم الطلب (النظام):</b> <code>{$order->id}</code>\n";
        $message .= "🚚 <b>رقم الطلب (الوسيط):</b> <code>" . ($order->waseet_order_id ?? 'لا يوجد') . "</code>\n";
        $message .= "👤 <b>اسم الزبون:</b> <code>{$order->customer_name}</code>\n";
        $message .= "💰 <b>المبلغ الإجمالي:</b> <code>" . number_format($order->total_amount, 0) . " د.ع</code>\n";
        $message .= "📌 <b>حالة النظام:</b> " . ($order->status ? $order->status->label() : 'غير معروف') . "\n";
        $message .= "📍 <b>حالة التوصيل:</b> " . ($order->waseet_status ?? 'غير متوفر') . "\n\n";

        $logs = $order->statusLogs;
        if ($logs->isNotEmpty()) {
            $message .= "⏳ <b>سجل الحالات الزمني:</b>\n";
            $sortedLogs = $logs->sortBy('created_at');
            foreach ($sortedLogs as $log) {
                $time = $log->created_at->format('Y-m-d h:i A');
                
                $statusText = $log->status;
                $enumCase = \App\Enums\OrderStatus::tryFrom($statusText);
                if ($enumCase) {
                    $statusText = $enumCase->label();
                }

                $message .= "🔸 {$statusText} <i>({$time})</i>\n";
            }
        }

        $this->telegram->sendMessage($chatId, $message);
    }

    protected function handleTemplateOrderSearch($chatId, $representativeId, $phone, $account)
    {
        $query = \App\Models\Order::where('representative_id', $representativeId);

        if ($phone && $account) {
            $query->where(function($q) use ($phone, $account) {
                $q->where('customer_phone', 'like', "%{$phone}%")
                  ->orWhere('customer_phone_2', 'like', "%{$phone}%")
                  ->orWhere('customer_social_media', 'like', "%{$account}%");
            });
        } elseif ($phone) {
            $query->where(function($q) use ($phone) {
                $q->where('customer_phone', 'like', "%{$phone}%")
                  ->orWhere('customer_phone_2', 'like', "%{$phone}%");
            });
        } elseif ($account) {
            $query->where('customer_social_media', 'like', "%{$account}%");
        }

        // Get the latest order matching
        $order = $query->orderBy('created_at', 'desc')->first();

        if (!$order) {
            $this->telegram->sendMessage($chatId, "لم يتم العثور على طلب مطابق لهذا الهاتف أو الحساب. ❌");
            return;
        }

        // Reuse the order details formatter
        $this->handleOrderSearch($chatId, $representativeId, $order->id);
    }

    protected function handleOrderCreation($chatId, Representative $rep, $text)
    {
        try {
            $customerName = preg_match('/👤 الزبون:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $phone1 = preg_match('/📞 الهاتف 1:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $phone2 = preg_match('/📞 الهاتف 2:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $social = preg_match('/📱 صفحة الزبون:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $govName = preg_match('/📍 المحافظة:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $districtName = preg_match('/🏘 المنطقة:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $address = preg_match('/🏠 العنوان:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;
            $notes = preg_match('/📝 ملاحظات:\s*([^\n]+)/u', $text, $matches) ? trim($matches[1]) : null;

            if (empty($customerName) || empty($phone1) || empty($govName) || empty($districtName) || empty($address)) {
                $this->telegram->sendMessage($chatId, "❌ خطأ: يرجى التأكد من ملء جميع الحقول الأساسية (الاسم، الهاتف 1، المحافظة، المنطقة، العنوان).");
                return;
            }

            $gov = \App\Models\Governorate::where('name', 'like', "%{$govName}%")->first();
            if (!$gov) {
                $this->telegram->sendMessage($chatId, "❌ خطأ: لم يتم التعرف على المحافظة ( {$govName} ). يرجى كتابة اسم المحافظة بشكل صحيح.");
                return;
            }

            $district = \App\Models\District::where('name', 'like', "%{$districtName}%")->where('governorate_id', $gov->id)->first();
            if (!$district) {
                $this->telegram->sendMessage($chatId, "❌ خطأ: لم يتم التعرف على المنطقة ( {$districtName} ) ضمن محافظة {$govName}. يرجى التحقق من اسم المنطقة.");
                return;
            }

            $booksText = explode('📚 الكتب:', $text);
            $booksLines = isset($booksText[1]) ? explode("\n", trim($booksText[1])) : [];
            $orderItemsData = [];

            foreach ($booksLines as $line) {
                if (trim($line) === '' || str_starts_with(trim($line), 'اسم الكتاب')) continue;

                if (preg_match('/-\s*(.*?)\s*\|\s*الكمية:\s*(\d+)\s*\|\s*السعر:\s*(\d+)/u', $line, $matches)) {
                    $bookName = trim($matches[1]);
                    $qty = (int)$matches[2];
                    $price = (float)$matches[3];

                    $product = \App\Models\Product::where('name', 'like', "%{$bookName}%")->where('is_active', true)->first();

                    if (!$product) {
                        $this->telegram->sendMessage($chatId, "❌ خطأ: لم يتم العثور على كتاب باسم ( {$bookName} ). يرجى البحث عنه باستخدام زر 'بحث عن كتاب' للتأكد من الاسم الصحيح.");
                        return;
                    }

                    if ($product->available_quantity < $qty) {
                        $this->telegram->sendMessage($chatId, "❌ خطأ: الكمية المطلوبة من كتاب ( {$bookName} ) غير متوفرة. المتوفر: {$product->available_quantity}");
                        return;
                    }

                    $orderItemsData[] = [
                        'product_id' => $product->id,
                        'product' => $product,
                        'quantity' => $qty,
                        'customer_price' => $price,
                    ];
                }
            }

            if (empty($orderItemsData)) {
                $this->telegram->sendMessage($chatId, "❌ خطأ: لم يتم إدخال أي كتب في الطلب، أو التنسيق غير صحيح. يرجى التأكد من الإبقاء على الفواصل ( | ) بين الاسم والكمية والسعر.");
                return;
            }

            // Create Order
            $orderService = app(\App\Services\Orders\OrderService::class);
            $order = $orderService->createOrder([
                'customer_name' => $customerName,
                'customer_phone' => $phone1,
                'customer_phone_2' => $phone2,
                'customer_social_media' => $social,
                'customer_address' => $address,
                'customer_notes' => $notes,
                'governorate_id' => $gov->id,
                'district_id' => $district->id,
            ], $rep);

            foreach ($orderItemsData as $item) {
                $orderService->addItemToOrder($order, $item['product'], $item['quantity'], $item['customer_price']);
            }

            $this->telegram->sendMessage($chatId, "✅ تم إنشاء الطلب بنجاح! يتم الآن عرض التفاصيل:");
            $this->handleOrderSearch($chatId, $rep->id, $order->id);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Order creation via Telegram failed: ' . $e->getMessage());
            $this->telegram->sendMessage($chatId, "❌ حدث خطأ غير متوقع أثناء إنشاء الطلب. يرجى التأكد من صحة التنسيق أو التواصل مع الإدارة.");
        }
    }

    protected function handleBookSearch($chatId, $query)
    {
        $products = \App\Models\Product::where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                  ->orWhere('author', 'like', "%{$query}%");
            })
            ->limit(3)
            ->get();

        if ($products->isEmpty()) {
            $this->telegram->sendMessage($chatId, "لم يتم العثور على نتائج مطابقة لـ \"{$query}\". ❌");
            return;
        }

        $this->telegram->sendMessage($chatId, "🔍 <b>نتائج البحث عن:</b> \"{$query}\"");

        foreach ($products as $product) {
            $message = "📖 <b>اسم الكتاب:</b> {$product->name}\n";
            $message .= "📦 <b>الكمية المتوفرة:</b> {$product->available_quantity}\n";
            $message .= "💰 <b>سعر مفرد:</b> " . number_format($product->retail_price, 0) . " د.ع\n";
            $message .= "💼 <b>سعر جملة:</b> " . number_format($product->wholesale_price, 0) . " د.ع\n";
            
            if ($product->author) {
                $message .= "✍️ <b>المؤلف:</b> {$product->author}\n";
            }
            if ($product->publisher) {
                $message .= "🏢 <b>دار النشر:</b> {$product->publisher}\n";
            }
            
            $desc = $product->long_description ?? $product->short_description;
            if ($desc) {
                $desc = \Illuminate\Support\Str::limit(strip_tags($desc), 150);
                $message .= "📝 <b>الوصف:</b>\n{$desc}\n";
            }

            $imageUrl = $product->image_url;

            if ($imageUrl) {
                $this->telegram->sendPhoto($chatId, $imageUrl, $message);
            } else {
                $this->telegram->sendMessage($chatId, $message);
            }
        }
    }

    // State Helpers
    protected function getState($chatId)
    {
        return Cache::get("telegram_state_{$chatId}");
    }

    protected function setState($chatId, $state)
    {
        Cache::put("telegram_state_{$chatId}", $state, now()->addMinutes(10));
    }

    protected function clearState($chatId)
    {
        Cache::forget("telegram_state_{$chatId}");
        Cache::forget("telegram_{$chatId}_phone");
    }
}
