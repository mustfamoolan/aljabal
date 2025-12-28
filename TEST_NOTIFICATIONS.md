# اختبار نظام الإشعارات

## خطوات الاختبار:

### 1. التحقق من الصلاحيات:
- تأكد من أن المستخدم الحالي لديه صلاحيات:
  - `inventory.products.create` أو
  - `inventory.products.update`

### 2. التحقق من FCM Token:

#### أ. فتح Developer Tools
- افتح المتصفح و Developer Tools (F12)
- اذهب إلى Console tab
- اذهب إلى Network tab

#### ب. إعادة تحميل الصفحة
- أعد تحميل الصفحة (F5)
- راقب Console logs - يجب أن ترى:
  - `[FCM] Initializing Firebase Messaging...`
  - `[FCM] getToken called`
  - `[FCM] ✅ FCM Registration token obtained`
  - `[FCM] sendTokenToServer called`
  - `[FCM] ✅ Token sent to server successfully`

#### ج. التحقق من Network Requests
- في Network tab، ابحث عن POST request إلى `/api/admin/fcm/token`
- تحقق من:
  - Status code: يجب أن يكون `200 OK`
  - Request payload: يجب أن يحتوي على `token`
  - Response: يجب أن يكون `{"success": true, "message": "Token saved successfully"}`

#### د. التحقق من Laravel Logs
- افتح `storage/logs/laravel.log`
- ابحث عن:
  - `[FCM] storeToken called`
  - `[FCM] User authenticated`
  - `[FCM] ✅ Token saved successfully`

#### هـ. استخدام Debug Endpoint
- افتح المتصفح واذهب إلى: `http://127.0.0.1:8000/api/admin/fcm/token/status`
- يجب أن ترى JSON response يحتوي على:
  ```json
  {
    "success": true,
    "data": {
      "user_id": 1,
      "user_name": "مدير النظام",
      "has_token": true,
      "token_preview": "fRe53Z4BIry6KAjfQVVSiw:APA91bE...",
      "token_length": 163
    }
  }
  ```

### 3. التحقق من Low Stock:
- عند تعديل منتج، تأكد من:
  - `quantity` ≤ `min_quantity`
  - `min_quantity` ليس `null`

### 4. التحقق من Logs:
بعد تعديل منتج، تحقق من `storage/logs/laravel.log`:
```bash
Get-Content storage\logs\laravel.log -Tail 100 | Select-String -Pattern "low stock|notification|FCM" -CaseSensitive:$false
```

يجب أن ترى:
- "Product updated, checking low stock"
- "Checking low stock notification for product"
- "Users with inventory permissions"
- "Sending FCM notification to user"
- "FCM notification sent successfully to user"

### 5. التحقق من Firebase Credentials:
- تأكد من أن `FCM_SERVER_KEY` موجود في `.env`
- Laravel FCM package يستخدم `kreait/laravel-firebase` الذي يحتاج إلى Firebase Service Account JSON file

### 6. اختبار يدوي:
1. افتح منتج
2. عدّل الكمية لتكون أقل من `min_quantity`
3. احفظ التعديل
4. تحقق من logs
5. تحقق من أن الإشعارات تظهر في صفحة `/notifications`
