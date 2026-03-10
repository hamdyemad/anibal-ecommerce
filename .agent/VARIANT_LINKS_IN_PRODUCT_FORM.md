# Variant Configuration Links in Product Form - COMPLETE

## المشكلة
عند إنشاء منتج جديد في صفحة `http://127.0.0.1:8000/en/eg/admin/products/create`، عند اختيار variant (مثل "Red")، كان النظام يعرض فقط الـ children المباشرة (اللي عندهم `parent_id`)، لكن مش بيعرض الـ linked children (اللي مربوطة عن طريق جدول `variants_configurations_links`).

## الحل المطبق

### 1. تعديل Repository Method
**File**: `Modules/CatalogManagement/app/Repositories/VariantsConfigurationRepository.php`
**Method**: `getVariantsByKeyForApi()`

**التغييرات**:
- عند طلب الـ root variants (بدون parent_id):
  - يجلب الـ direct children و الـ linked children
  - يدمجهم ويزيل التكرار
  - يحسب `has_children` بناءً على كل الـ children (direct + linked)

- عند طلب children لـ parent معين:
  - يجلب الـ direct children (via `parent_id`)
  - يجلب الـ linked children (via `linkedChildren()` relationship)
  - يدمجهم ويزيل التكرار
  - لكل child، يتحقق إذا كان عنده children (direct or linked)

### الكود الجديد

```php
public function getVariantsByKeyForApi($keyId, $parentId = null)
{
    // If parent_id is provided, get all children (direct + linked) of that parent
    if ($parentId && $parentId !== 'root') {
        // Get the parent variant
        $parent = VariantsConfiguration::withoutGlobalScopes()->find($parentId);
        
        if (!$parent) {
            return [];
        }
        
        // Get direct children (via parent_id)
        $directChildren = VariantsConfiguration::withoutGlobalScopes()
            ->with(['translations', 'children.translations', 'linkedChildren.translations'])
            ->where('parent_id', $parentId)
            ->get();
        
        // Get linked children (via configuration_links table)
        $linkedChildren = $parent->linkedChildren()->with(['translations', 'children.translations', 'linkedChildren.translations'])->get();
        
        // Merge and remove duplicates
        $allChildren = $directChildren->merge($linkedChildren)->unique('id');
        
        return $allChildren->map(function ($variant) {
            // For each child, check if it has children (direct or linked)
            $childDirectChildren = $variant->children ?? collect();
            $childLinkedChildren = $variant->linkedChildren ?? collect();
            $childAllChildren = $childDirectChildren->merge($childLinkedChildren)->unique('id');
            
            return [
                'id' => $variant->id,
                'name' => $variant->getTranslation('name', app()->getLocale()) ?? $variant->value,
                'value' => $variant->value,
                'has_children' => $childAllChildren->count() > 0,
                'children_count' => $childAllChildren->count()
            ];
        })->toArray();
    }
    
    // If parent_id is null or 'root', get root variants (no parent)
    $query = VariantsConfiguration::withoutGlobalScopes()
        ->with(['translations', 'children.translations', 'linkedChildren.translations'])
        ->where('key_id', $keyId)
        ->whereNull('parent_id');

    $variants = $query->get();

    return $variants->map(function ($variant) {
        // Merge direct children and linked children
        $directChildren = $variant->children ?? collect();
        $linkedChildren = $variant->linkedChildren ?? collect();
        $allChildren = $directChildren->merge($linkedChildren)->unique('id');
        
        return [
            'id' => $variant->id,
            'name' => $variant->getTranslation('name', app()->getLocale()) ?? $variant->value,
            'value' => $variant->value,
            'has_children' => $allChildren->count() > 0,
            'children_count' => $allChildren->count()
        ];
    })->toArray();
}
```

## كيف يعمل النظام الآن

### مثال: Nike Air → Red → Sizes

1. **المستخدم يختار Variant Key**: "Color"
   - النظام يجلب root variants: Nike Air, Adidas Run, etc.

2. **المستخدم يختار "Nike Air"**
   - النظام يجلب children (direct + linked): Red, Blue, etc.

3. **المستخدم يختار "Red"**
   - النظام يجلب children (direct + linked): 40, 41, 42, etc.
   - هنا الـ sizes مربوطة بـ Red عن طريق `variants_configurations_links`
   - النظام يعرضهم كأنهم children عادية

4. **المستخدم يختار "40"**
   - إذا مفيش children تانية، النظام يعرض Pricing & Stock form

## الـ Relationships المستخدمة

### في Model: `VariantsConfiguration`

```php
// Direct children (via parent_id)
public function children()
{
    return $this->hasMany(self::class, 'parent_id');
}

// Linked children (via configuration_links table)
public function linkedChildren()
{
    return $this->belongsToMany(
        VariantsConfiguration::class,
        'variants_configurations_links',
        'parent_config_id',
        'child_config_id'
    )->withTimestamps();
}

// All children (merged)
public function allChildren()
{
    $directChildren = $this->children;
    $linkedChildren = $this->linkedChildren;
    
    return $directChildren->merge($linkedChildren)->unique('id');
}
```

## الفوائد

1. **مرونة أكبر**: يمكن ربط أي variant بأي variant آخر بدون تكرار
2. **تجنب التكرار**: "Red" موجود مرة واحدة فقط في الداتابيز
3. **سهولة الإدارة**: يمكن إضافة/إزالة الروابط بسهولة من صفحة الـ variant
4. **توافق كامل**: يعمل مع كل الـ product forms (create, edit, stock management, bank products)

## الملفات المعدلة

1. `Modules/CatalogManagement/app/Repositories/VariantsConfigurationRepository.php`
   - Method: `getVariantsByKeyForApi()`

## الملفات المتأثرة (تستخدم نفس الـ API)

- `Modules/CatalogManagement/resources/views/product/create.blade.php`
- `Modules/CatalogManagement/resources/views/product/edit.blade.php`
- `Modules/CatalogManagement/resources/views/product/stock-management.blade.php`
- `Modules/CatalogManagement/resources/views/product/partials/bank-stock-scripts.blade.php`

كل هذه الملفات تستخدم نفس الـ route: `admin.api.variants-by-key`، فالتعديل يؤثر عليهم جميعاً تلقائياً.

## Testing Checklist

- [x] عند اختيار variant، يظهر الـ linked children
- [x] عند اختيار linked child، يظهر children بتوعه (إذا موجودة)
- [x] الـ tree icon (🌳) يظهر بشكل صحيح للـ variants اللي عندها children
- [x] لا يوجد تكرار في الـ children المعروضة
- [x] يعمل مع كل الـ variant keys (Color, Size, Model, etc.)

## الحالات المدعومة

### 1. Direct Children Only
```
Nike Air (parent_id: null)
  └─ Red (parent_id: Nike Air ID)
      └─ 40 (parent_id: Red ID)
```

### 2. Linked Children Only
```
Red (parent_id: null)
  └─ 40 (linked via configuration_links)
  └─ 41 (linked via configuration_links)
```

### 3. Mixed (Direct + Linked)
```
Nike Air (parent_id: null)
  ├─ Red (parent_id: Nike Air ID) [direct]
  └─ Blue (linked via configuration_links) [linked]
```

## Status
✅ COMPLETE - Variant configuration links now work in product form
