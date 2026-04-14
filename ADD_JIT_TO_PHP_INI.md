# 🚀 إضافة JIT إلى php.ini

## الوضع الحالي:
- ✅ OPcache مفعّل
- ✅ OPcache CLI مفعّل  
- ✅ Memory: 256MB
- ❌ JIT غير موجود في php.ini

---

## الحل:

### 1️⃣ افتح الملف:
```
C:\laragon\bin\php\php-8.3.16\php.ini
```

### 2️⃣ ابحث عن `[opcache]` (حوالي سطر 1800-1900)

### 3️⃣ أضف هذين السطرين في نهاية قسم [opcache]:

```ini
opcache.jit_buffer_size=128M
opcache.jit=1255
```

يجب أن يصبح القسم هكذا:

```ini
[opcache]
zend_extension=opcache
opcache.enable=1
opcache.enable_cli=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.validate_timestamps=1
opcache.revalidate_freq=2

; ⬇️ أضف هذين السطرين
opcache.jit_buffer_size=128M
opcache.jit=1255
```

### 4️⃣ احفظ الملف (Ctrl+S)

### 5️⃣ أعد تشغيل Laragon:
- Stop All
- Start All

### 6️⃣ تحقق من التفعيل:
```bash
php -r "echo 'JIT Buffer: ' . ini_get('opcache.jit_buffer_size') . PHP_EOL;"
```

يجب أن ترى: `JIT Buffer: 134217728` (وليس 0)

---

## اختبار الأداء:

```bash
Measure-Command { Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/v1/test" -UseBasicParsing } | Select-Object TotalMilliseconds
```

---

## النتيجة المتوقعة:
- **الآن**: 582ms
- **بعد JIT**: 150-250ms ✅
- **الهدف**: أقل من 200ms ✅

---

## ما هو JIT؟

JIT (Just-In-Time Compiler) هو ميزة في PHP 8.x تحوّل PHP bytecode إلى machine code مباشرة، مما يجعل التنفيذ أسرع بـ 20-40%.

### شرح القيم:
- `opcache.jit_buffer_size=128M` - حجم الذاكرة المخصصة للـ JIT
- `opcache.jit=1255` - وضع JIT (1255 هو الأفضل للـ web applications)

---

## ملاحظة مهمة:

إذا لم تجد قسم `[opcache]` في الملف، ابحث عن:
```
zend_extension=opcache
```

وأضف الإعدادات بعده مباشرة.
