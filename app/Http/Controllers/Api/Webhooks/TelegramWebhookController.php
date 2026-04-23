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
                [['text' => 'استعلام عن طلب 📦'], ['text' => 'بحث عن كتاب 🔍']],
                [['text' => 'معلومات حسابي 👤'], ['text' => 'تسجيل خروج 🚪']],
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
        $message .= "▪️ <b>رقم الطلب (النظام):</b> {$order->id}\n";
        $message .= "▪️ <b>رقم الطلب (الوسيط):</b> " . ($order->waseet_order_id ?? 'غير متوفر') . "\n";
        $message .= "▪️ <b>اسم الزبون:</b> {$order->customer_name}\n";
        $message .= "▪️ <b>المبلغ الإجمالي:</b> " . number_format($order->total_amount, 0) . " د.ع\n";
        $message .= "▪️ <b>حالة الطلب (النظام):</b> " . ($order->status ? $order->status->label() : 'غير معروف') . "\n";
        $message .= "▪️ <b>حالة الطلب (الوسيط):</b> " . ($order->waseet_status ?? 'غير معروف') . "\n\n";

        $logs = $order->statusLogs;
        if ($logs->isNotEmpty()) {
            $message .= "⏳ <b>سجل الحالات:</b>\n";
            foreach ($logs as $log) {
                $time = $log->created_at->format('Y-m-d h:i A');
                $message .= "- <b>{$log->status}</b> <i>({$time})</i>\n";
            }
        }

        $this->telegram->sendMessage($chatId, $message);
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
