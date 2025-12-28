# إعداد Gemini API لتوليد الوصف التلقائي

## الخطوات المطلوبة

### 1. تثبيت المكتبة

قم بتشغيل الأمر التالي في Terminal:

```bash
composer require google-gemini-php/laravel
```

### 2. نشر ملف الإعدادات

بعد التثبيت، قم بتشغيل:

```bash
php artisan gemini:install
```

هذا الأمر سيقوم بـ:
- إنشاء ملف `config/gemini.php`
- إضافة `GEMINI_API_KEY` إلى ملف `.env` (إذا لم يكن موجوداً)

### 3. إضافة API Key

افتح ملف `.env` وأضف مفتاح الـ API الخاص بك:

```env
GEMINI_API_KEY=AIzaSyCIKPKya-SMCngyoOSgV6w_IPQSRmT72qM
```

### 4. مسح Cache (اختياري)

بعد إضافة المتغيرات، يمكنك مسح cache الإعدادات:

```bash
php artisan config:clear
php artisan cache:clear
```

### 5. اختبار الميزة

1. افتح صفحة إنشاء منتج جديد: `/admin/inventory/products/create`
2. أدخل اسم المنتج (مطلوب)
3. أدخل أي معلومات إضافية (مؤلف، ناشر، فئة، إلخ)
4. اضغط زر "توليد نص تلقائي" بجانب حقل الوصف القصير أو الطويل
5. انتظر حتى يتم توليد النص (عادة 2-5 ثواني)
6. سيتم ملء الحقل تلقائياً بالنص المُولد
7. اضغط "حفظ" لحفظ المنتج

## ملاحظات

- **المكتبة المستخدمة**: `google-gemini-php/laravel` - مكتبة رسمية ومحفوظة جيداً
- **النموذج المستخدم**: `gemini-1.5-flash` (مجاني وسريع)
- **API Key**: المفتاح المقدم مجاني ويستخدم Google AI Studio (Gemini API)
- **الحدود**: الخطة المجانية توفر عدداً كبيراً من الطلبات يومياً
- **السرعة**: Gemini 1.5 Flash سريع جداً (عادة أقل من 5 ثواني)
- **الدعم**: يدعم اللغة العربية ببراعة

## استخدام المكتبة

المكتبة تستخدم Facade بسيط:

```php
use Gemini\Laravel\Facades\Gemini;

// توليد نص باستخدام gemini-1.5-flash
$response = Gemini::generativeModel(model: 'gemini-1.5-flash')->generateContent('اكتب وصفاً للمنتج');
$text = $response->text();
```

## استكشاف الأخطاء

إذا واجهت مشاكل:

1. **تحقق من API Key**: تأكد من إضافة `GEMINI_API_KEY` في `.env`
2. **تحقق من Logs**: راجع `storage/logs/laravel.log` للأخطاء
3. **تحقق من Console**: افتح Developer Tools في المتصفح وابحث عن أخطاء JavaScript
4. **تحقق من Network**: تأكد من أن API request يصل للخادم بنجاح
5. **تحقق من Config**: تأكد من أن `config/gemini.php` موجود و `GEMINI_API_KEY` مضبوط

## الملفات المعنية

- `app/Services/AI/GeminiService.php` - Service class للتواصل مع Gemini API (يستخدم المكتبة)
- `app/Http/Controllers/Api/AI/ProductDescriptionController.php` - API Controller
- `resources/js/pages/product-ai-generator.js` - JavaScript frontend
- `config/gemini.php` - إعدادات Gemini API (يتم إنشاؤه بواسطة `gemini:install`)

## الوثائق الرسمية

- [Google Gemini PHP Laravel Package](https://github.com/google-gemini-php/laravel)
- [Google Gemini PHP Client](https://github.com/google-gemini-php/client)
