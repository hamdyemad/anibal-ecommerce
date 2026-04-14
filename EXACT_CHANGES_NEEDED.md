# 📝 التغييرات المطلوبة في php.ini

## الملف:
```
C:\laragon\bin\php\php-8.3.16\php.ini
```

---

## التغييرات المطلوبة:

### 1️⃣ غيّر هذه القيم الموجودة:

```ini
; ❌ القيمة الحالية
opcache.interned_strings_buffer=8

; ✅ غيّرها إلى
opcache.interned_strings_buffer=16
```

```ini
; ❌ القيمة الحالية
opcache.max_accelerated_files=10000

; ✅ غيّرها إلى
opcache.max_accelerated_files=20000
```

### 2️⃣ أضف هذين السطرين في نهاية قسم opcache:

```ini
opcache.jit_buffer_size=128M
opcache.jit=1255
```

---

## الطريقة السريعة:

### افتح php.ini وابحث عن:
1. `opcache.interned_strings_buffer=8` → غيّرها إلى `16`
2. `opcache.max_accelerated_files=10000` → غيّرها إلى `20000`
3. بعد سطر `opcache.save_comments=1` أضف:
   ```ini
   opcache.jit_buffer_size=128M
   opcache.jit=1255
   ```

### احفظ وأعد تشغيل Laragon

---

## التحقق:

```bash
php -r "echo 'Strings Buffer: ' . ini_get('opcache.interned_strings_buffer') . PHP_EOL; echo 'Max Files: ' . ini_get('opcache.max_accelerated_files') . PHP_EOL; echo 'JIT Buffer: ' . ini_get('opcache.jit_buffer_size') . PHP_EOL;"
```

يجب أن ترى:
```
Strings Buffer: 16
Max Files: 20000
JIT Buffer: 134217728
```

---

## اختبار الأداء:

```bash
Measure-Command { Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/v1/test" -UseBasicParsing } | Select-Object TotalMilliseconds
```

**الهدف**: أقل من 200ms ✅
