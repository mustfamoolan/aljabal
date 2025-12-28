# إعداد Firebase Cloud Messaging (FCM)

## المتغيرات المطلوبة في ملف `.env`

يجب إضافة المتغيرات التالية إلى ملف `.env`:

```env
# Firebase Cloud Messaging Configuration
FCM_SERVER_KEY=your_server_key_here
FCM_SENDER_ID=689946728760
FCM_VAPID_KEY=your_vapid_key_here
```

## كيفية الحصول على هذه القيم:

### 1. FCM_SERVER_KEY (Server Key)
1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك: **sels-e407c**
3. اذهب إلى: **Project Settings** (⚙️) > **Cloud Messaging**
4. في قسم **Cloud Messaging API (Legacy)**:
   - انسخ **Server key**

### 2. FCM_SENDER_ID
- القيمة: `689946728760` (موجودة بالفعل في `firebase.md`)

### 3. FCM_VAPID_KEY (Web Push Key)
1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك: **sels-e407c**
3. اذهب إلى: **Project Settings** (⚙️) > **Cloud Messaging**
4. في قسم **Web Push certificates**:
   - إذا كان موجود: انسخ **Key pair**
   - إذا لم يكن موجود: اضغط **Generate key pair** ثم انسخ المفتاح

## معلومات إضافية من firebase.md:

**Private Key:** EDmNoiv2En2ImRopStMAFvT9Ne6kgT_P0vsjHuzgvAw  
**Key Pair:** BIDbKPxIA3hPX9GJxlxGLMNrwVTZax_T_dd-ttgpR08x5VJDMtAXmb_liIGaBGCEsTxV_Rg08irKVokgFiMb0vQ  
**Sender ID:** 689946728760

## بعد إضافة المتغيرات:

1. تأكد من إضافة جميع المتغيرات إلى `.env`
2. أعد تحميل التطبيق (أو أعد تشغيل `php artisan serve`)
3. افتح المتصفح وافتح Developer Console (F12)
4. يجب أن ترى رسالة "Firebase Messaging initialized successfully" بدلاً من "User not authenticated"

## اختبار الإشعارات:

1. تأكد من أن المستخدم لديه صلاحيات `inventory.products.create` أو `inventory.products.update`
2. أنشئ أو عدّل منتج بحيث تكون الكمية أقل من أو تساوي `min_quantity`
3. يجب أن تظهر إشعارات FCM للمستخدمين المصرح لهم

## استكشاف الأخطاء:

- إذا رأيت "User not authenticated": تأكد من تسجيل الدخول
- إذا رأيت "FCM_VAPID_KEY not found": أضف المتغير إلى `.env` وأعد تحميل الصفحة
- إذا لم تصل الإشعارات: تأكد من:
  - منح إذن الإشعارات في المتصفح
  - وجود Service Worker في `public/firebase-messaging-sw.js`
  - أن HTTPS مفعّل (أو localhost للتطوير)
