# Firebase Cloud Messaging - دليل الاستخدام

## الملفات المُنشأة

1. **config.js** - إعدادات Firebase الأساسية
2. **messaging.js** - خدمة Cloud Messaging
3. **init.js** - ملف تهيئة مع أمثلة
4. **index.js** - نقطة الدخول الرئيسية
5. **public/firebase-messaging-sw.js** - Service Worker للرسائل في الخلفية

## كيفية الاستخدام

### 1. في أي صفحة JavaScript:

```javascript
import { initFirebaseMessaging } from './firebase/init.js';

// تهيئة Firebase Messaging
initFirebaseMessaging({
    tokenEndpoint: '/api/fcm/token', // رابط API لإرسال التوكن
    autoRequestPermission: true,     // طلب الإذن تلقائياً
    onMessageCallback: (payload) => {
        // التعامل مع الرسائل عند فتح التطبيق
        console.log('رسالة جديدة:', payload);
    }
});
```

### 2. استخدام مباشر:

```javascript
import firebaseMessaging from './firebase/messaging.js';

// طلب الإذن
await firebaseMessaging.requestPermission();

// الحصول على التوكن
const token = await firebaseMessaging.getToken();

// إرسال التوكن للخادم
await firebaseMessaging.sendTokenToServer(token, '/api/fcm/token');

// الاستماع للرسائل
firebaseMessaging.onMessage((payload) => {
    console.log('رسالة:', payload);
});
```

## ملاحظات مهمة

1. **VAPID Key**: ستحتاج للحصول على VAPID Key من Firebase Console:
   - اذهب إلى: Project Settings > Cloud Messaging > Web Push certificates
   - انسخ المفتاح وأضفه في `initFirebaseMessaging({ vapidKey: 'YOUR_KEY' })`

2. **Service Worker**: يجب أن يكون الملف `firebase-messaging-sw.js` في مجلد `public`

3. **HTTPS**: Cloud Messaging يتطلب HTTPS (أو localhost للتطوير)

4. **API Endpoint**: ستحتاج لإنشاء endpoint في Laravel لاستقبال وحفظ التوكنات

## مثال على API Endpoint في Laravel

```php
// routes/api.php
Route::post('/fcm/token', [FCMController::class, 'storeToken']);

// app/Http/Controllers/FCMController.php
public function storeToken(Request $request) {
    $user = auth()->user();
    $user->fcm_token = $request->token;
    $user->save();
    return response()->json(['success' => true]);
}
```
