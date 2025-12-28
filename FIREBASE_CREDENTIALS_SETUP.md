# إعداد Firebase Credentials للإشعارات

## المشكلة:
Laravel FCM package (`laravel-notification-channels/fcm`) يستخدم `kreait/laravel-firebase` library الذي يحتاج إلى **Firebase Service Account JSON file** لإرسال الإشعارات.

## الحل:

### الطريقة 1: استخدام Firebase Service Account JSON (موصى بها)

1. اذهب إلى [Firebase Console](https://console.firebase.google.com/)
2. اختر مشروعك: **sels-e407c**
3. اذهب إلى: **Project Settings** (⚙️) > **Service accounts**
4. اضغط **Generate new private key**
5. حمّل ملف JSON
6. احفظ الملف في `storage/app/firebase-credentials.json`
7. أضف إلى `.env`:
   ```env
   FIREBASE_CREDENTIALS=storage/app/firebase-credentials.json
   ```

### الطريقة 2: استخدام FCM_SERVER_KEY (بديل)

إذا لم تكن تريد استخدام Service Account، يمكنك استخدام FCM Server Key مباشرة. لكن package الحالي يحتاج إلى Service Account.

## التحقق من الإعداد:

بعد إضافة Firebase credentials، جرّب تعديل منتج مرة أخرى وتحقق من:
1. Logs في `storage/logs/laravel.log`
2. يجب أن ترى رسائل عن إرسال الإشعارات
3. إذا كان هناك خطأ، سترى تفاصيل الخطأ في logs

## ملاحظة مهمة:

- `FCM_SERVER_KEY` في `.env` هو **Private Key** وليس Server Key
- Server Key يجب الحصول عليه من Firebase Console > Cloud Messaging > Cloud Messaging API (Legacy)
- أو استخدم Service Account JSON file (أسهل وأكثر أماناً)
