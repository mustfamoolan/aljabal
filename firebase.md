# Firebase & Cloud Messaging - التوثيق الكامل

## معلومات المشروع

**Project ID:** sels-e407c  
**Sender ID:** 689946728760  
**App ID:** 1:689946728760:web:605322353c7706bb8409cd

---

## الملفات المُنشأة

### 1. `resources/js/firebase/config.js`
ملف إعدادات Firebase الأساسية يحتوي على:
- تهيئة Firebase App
- تهيئة Analytics
- تهيئة Cloud Messaging
- تصدير جميع الإعدادات للاستخدام في ملفات أخرى

**الميزات:**
- فحص دعم المتصفح قبل التهيئة
- معالجة الأخطاء عند عدم توفر Service Worker
- دعم بيئة المتصفح فقط (لا يعمل في Node.js)

---

### 2. `resources/js/firebase/messaging.js`
خدمة Cloud Messaging الرئيسية تحتوي على:

#### الدوال المتوفرة:

**`requestPermission()`**
- طلب إذن الإشعارات من المستخدم
- إرجاع `true` إذا تم منح الإذن، `false` خلاف ذلك

**`getToken(vapidKey)`**
- الحصول على FCM Registration Token
- يطلب الإذن تلقائياً إذا لم يكن موجوداً
- يمكن تمرير VAPID Key كمعامل اختياري

**`onMessage(callback)`**
- الاستماع للرسائل عند فتح التطبيق (Foreground)
- يستقبل callback function لمعالجة الرسائل

**`sendTokenToServer(token, endpoint)`**
- إرسال التوكن للخادم عبر API
- يدعم CSRF Token تلقائياً
- إرجاع `true` عند النجاح

**`init(options)`**
- تهيئة كاملة لخدمة Messaging
- يجمع جميع الوظائف في مكان واحد
- خيارات قابلة للتخصيص

---

### 3. `resources/js/firebase/init.js`
ملف تهيئة مع أمثلة استخدام:

**`initFirebaseMessaging(options)`**
- تسجيل Service Worker تلقائياً
- تهيئة Messaging مع الإعدادات المطلوبة
- إرجاع instance من firebaseMessaging

**`showCustomNotification(payload)`**
- مثال على عرض إشعار مخصص
- يمكن تخصيصه حسب الحاجة

---

### 4. `resources/js/firebase/index.js`
نقطة الدخول الرئيسية لتصدير جميع الخدمات:
```javascript
export { app, analytics, messaging, firebaseConfig } from './config.js';
export { default as firebaseMessaging } from './messaging.js';
```

---

### 5. `public/firebase-messaging-sw.js`
Service Worker للتعامل مع الإشعارات في الخلفية:

**الميزات:**
- معالجة الرسائل عند إغلاق التطبيق (Background)
- عرض الإشعارات تلقائياً
- التعامل مع النقر على الإشعارات
- فتح التطبيق عند النقر على الإشعار

**الوظائف:**
- `onBackgroundMessage`: معالجة الرسائل في الخلفية
- `notificationclick`: التعامل مع النقر على الإشعارات

---

## كيفية الاستخدام

### الطريقة الأولى: استخدام دالة التهيئة (موصى بها)

```javascript
import { initFirebaseMessaging } from './firebase/init.js';

// تهيئة Firebase Messaging
await initFirebaseMessaging({
    // VAPID Key (اختياري - احصل عليه من Firebase Console)
    vapidKey: 'YOUR_VAPID_KEY_HERE',
    
    // رابط API لإرسال التوكن
    tokenEndpoint: '/api/fcm/token',
    
    // دالة للتعامل مع الرسائل عند فتح التطبيق
    onMessageCallback: (payload) => {
        console.log('رسالة جديدة:', payload);
        // يمكنك عرض إشعار مخصص هنا
    },
    
    // طلب الإذن تلقائياً
    autoRequestPermission: true
});
```

### الطريقة الثانية: استخدام مباشر

```javascript
import firebaseMessaging from './firebase/messaging.js';

// 1. طلب الإذن
const hasPermission = await firebaseMessaging.requestPermission();

if (hasPermission) {
    // 2. الحصول على التوكن
    const token = await firebaseMessaging.getToken();
    
    if (token) {
        // 3. إرسال التوكن للخادم
        await firebaseMessaging.sendTokenToServer(token, '/api/fcm/token');
        
        // 4. الاستماع للرسائل
        firebaseMessaging.onMessage((payload) => {
            console.log('رسالة:', payload);
            // معالجة الرسالة هنا
        });
    }
}
```

### الطريقة الثالثة: استخدام في صفحة معينة

```javascript
// في resources/js/pages/notifications.js مثلاً
import { initFirebaseMessaging } from '../firebase/init.js';

document.addEventListener('DOMContentLoaded', async () => {
    await initFirebaseMessaging({
        tokenEndpoint: '/api/fcm/token',
        autoRequestPermission: true,
        onMessageCallback: (payload) => {
            // عرض إشعار باستخدام SweetAlert2 أو أي مكتبة أخرى
            Swal.fire({
                title: payload.notification?.title,
                text: payload.notification?.body,
                icon: 'info'
            });
        }
    });
});
```

---

## إعداد Laravel Backend

### 1. إنشاء Migration لإضافة عمود FCM Token

```php
// database/migrations/xxxx_add_fcm_token_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->text('fcm_token')->nullable()->after('email');
});
```

### 2. إنشاء Controller

```php
// app/Http/Controllers/Api/FCMController.php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FCMController extends Controller
{
    public function storeToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string'
        ]);

        $user = auth()->user();
        $user->fcm_token = $request->token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Token saved successfully'
        ]);
    }

    public function removeToken(Request $request)
    {
        $user = auth()->user();
        $user->fcm_token = null;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Token removed successfully'
        ]);
    }
}
```

### 3. إضافة Route

```php
// routes/api.php
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/fcm/token', [App\Http\Controllers\Api\FCMController::class, 'storeToken']);
    Route::delete('/fcm/token', [App\Http\Controllers\Api\FCMController::class, 'removeToken']);
});
```

---

## إرسال إشعارات من Laravel

### استخدام Laravel FCM Package (موصى به)

```bash
composer require laravel-notification-channels/fcm
```

### مثال على إرسال إشعار:

```php
use Illuminate\Notifications\Notification;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Fcm\Resources\Notification as FcmNotification;

class OrderNotification extends Notification
{
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setData(['order_id' => $this->order->id])
            ->setNotification(
                FcmNotification::create()
                    ->setTitle('طلب جديد')
                    ->setBody('لديك طلب جديد #' . $this->order->id)
            );
    }
}

// الاستخدام:
$user->notify(new OrderNotification($order));
```

---

## الحصول على VAPID Key

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك: **sels-e407c**
3. اذهب إلى: **Project Settings** (⚙️) > **Cloud Messaging**
4. في قسم **Web Push certificates**:
   - إذا كان موجود: انسخ **Key pair**
   - إذا لم يكن موجود: اضغط **Generate key pair** ثم انسخ المفتاح

---

## متطلبات التشغيل

1. ✅ Firebase SDK مثبت (`npm install firebase`)
2. ✅ HTTPS (أو localhost للتطوير)
3. ✅ Service Worker مدعوم في المتصفح
4. ✅ إذن الإشعارات من المستخدم
5. ⚠️ VAPID Key (اختياري لكن موصى به)

---

## هيكل الملفات

```
resources/js/firebase/
├── config.js          # إعدادات Firebase
├── messaging.js       # خدمة Cloud Messaging
├── init.js            # ملف التهيئة
├── index.js           # نقطة الدخول
└── README.md          # دليل الاستخدام

public/
└── firebase-messaging-sw.js  # Service Worker
```

---

## معلومات إضافية

**Private Key:** EDmNoiv2En2ImRopStMAFvT9Ne6kgT_P0vsjHuzgvAw  
**Key Pair:** BIDbKPxIA3hPX9GJxlxGLMNrwVTZax_T_dd-ttgpR08x5VJDMtAXmb_liIGaBGCEsTxV_Rg08irKVokgFiMb0vQ  
**Sender ID:** 689946728760

---

## ملاحظات مهمة

1. **Service Worker** يجب أن يكون في مجلد `public` وليس `resources`
2. **VAPID Key** ضروري لإرسال الإشعارات من الخادم
3. **CSRF Token** يتم إرساله تلقائياً في `sendTokenToServer`
4. **HTTPS** مطلوب في الإنتاج (localhost يعمل للتطوير)
5. جميع الملفات جاهزة للاستخدام - فقط قم بالربط عند الحاجة

---

## الخطوات التالية

1. ✅ Firebase مثبت ومهيأ
2. ✅ Cloud Messaging جاهز
3. ⏭️ احصل على VAPID Key من Firebase Console
4. ⏭️ أنشئ API endpoint في Laravel لحفظ التوكنات
5. ⏭️ اربط `initFirebaseMessaging` في الصفحات المطلوبة
6. ⏭️ ابدأ بإرسال الإشعارات!

---

**تاريخ الإعداد:** 2025  
**الإصدار:** Firebase SDK v12.7.0
