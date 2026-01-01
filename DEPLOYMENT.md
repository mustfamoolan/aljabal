# دليل النشر (Deployment Guide)

## إعداد الرابط الرمزي للتخزين (Storage Symbolic Link)

بعد كل تحديث من GitHub، يجب التأكد من أن الرابط الرمزي `public/storage` موجود ويعمل بشكل صحيح.

### الطريقة الأولى: استخدام Script التلقائي (موصى به)

1. بعد `git pull`، قم بتشغيل:
```bash
chmod +x deploy.sh
./deploy.sh
```

أو يمكنك إضافة الأمر إلى workflow الخاص بك:
```bash
git pull origin main && ./deploy.sh
```

### الطريقة الثانية: يدوياً عبر SSH

إذا كان `public/storage` مجلداً وليس رابطاً رمزياً:

```bash
# حذف المجلد القديم
rm -rf public/storage

# إنشاء الرابط الرمزي
ln -s ../storage/app/public public/storage

# التحقق من أن الرابط تم إنشاؤه بشكل صحيح
ls -la public/storage
```

يجب أن ترى:
```
lrwxrwxrwx ... public/storage -> ../storage/app/public
```

### الطريقة الثالثة: استخدام Laravel Artisan (إذا كان exec() متاح)

```bash
php artisan storage:link
```

**ملاحظة:** على بعض السيرفرات المشتركة، قد تكون دالة `exec()` معطلة، لذلك يجب استخدام الطريقة اليدوية.

## استكشاف الأخطاء

### المشكلة: الصور لا تظهر

1. تحقق من أن الرابط الرمزي موجود:
   ```bash
   ls -la public/storage
   ```

2. تحقق من أن الملفات موجودة في `storage/app/public`:
   ```bash
   ls -la storage/app/public/products/
   ```

3. تحقق من الصلاحيات:
   ```bash
   chmod -R 755 storage/app/public
   ```

### المشكلة: الرابط الرمزي لا يعمل

إذا كان `public/storage` مجلداً وليس رابطاً رمزياً:
```bash
rm -rf public/storage
ln -s ../storage/app/public public/storage
```

## إعداد تلقائي (اختياري)

يمكنك إضافة الأمر إلى `.bashrc` أو `.bash_profile`:

```bash
alias deploy='cd ~/public_html && git pull origin main && ./deploy.sh'
```

ثم يمكنك استخدام:
```bash
deploy
```

