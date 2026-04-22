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
        // 1. Handle commands first
        if ($text === '/start') {
            $this->clearState($chatId);
            $this->telegram->sendMessage($chatId, "مرحباً بك في بوت المندوبين 👋\nيرجى إدخال رقم هاتفك للبدء:");
            $this->setState($chatId, 'awaiting_phone');
            return;
        }

        if ($text === '/info') {
            $rep = Representative::where('telegram_chat_id', $chatId)->first();
            if ($rep) {
                $this->sendRepresentativeInfo($chatId, $rep);
            } else {
                $this->telegram->sendMessage($chatId, "أنت غير مسجل. يرجى إرسال /start للبدء.");
            }
            return;
        }

        if ($text === '/logout') {
            $rep = Representative::where('telegram_chat_id', $chatId)->first();
            if ($rep) {
                $rep->telegram_chat_id = null;
                $rep->save();
            }
            $this->clearState($chatId);
            $this->telegram->sendMessage($chatId, "تم تسجيل الخروج بنجاح.");
            return;
        }

        // 2. Check if user is already logged in
        $rep = Representative::where('telegram_chat_id', $chatId)->first();
        if ($rep) {
            // Already logged in, maybe show menu or info
            $this->sendRepresentativeInfo($chatId, $rep);
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
        // Clean phone number if needed (remove spaces etc)
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
            // Success
            $rep->telegram_chat_id = $chatId;
            $rep->save();

            $this->clearState($chatId);
            $this->telegram->sendMessage($chatId, "✅ تم تسجيل الدخول بنجاح!");
            $this->sendRepresentativeInfo($chatId, $rep);
        } else {
            $this->telegram->sendMessage($chatId, "كلمة المرور غير صحيحة ❌\nالرجاء المحاولة مرة أخرى.");
        }
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
