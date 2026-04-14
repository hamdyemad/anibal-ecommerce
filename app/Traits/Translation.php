<?php

namespace App\Traits;

use App\Models\Language;
use App\Models\Translation as TranslationModel;
use Illuminate\Support\Facades\Cache;

trait Translation
{
    protected static array $localLangCache = [];

    protected function getLanguageByCode(string $code)
    {
        if (!array_key_exists($code, self::$localLangCache)) {
            self::$localLangCache[$code] = \Illuminate\Support\Facades\Cache::rememberForever('lang_code_' . $code, function () use ($code) {
                return Language::where('code', $code)->first();
            });
        }
        return self::$localLangCache[$code];
    }

    public function translations()
    {
        return $this->morphMany(TranslationModel::class, 'translatable')->withTrashed();
    }

    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale());
    }

    public function getTranslation(string $key, string $locale = 'en')
    {
        $lang = $this->getLanguageByCode($locale);

        if (!$lang) {
            return null;
        }

        $langId = $lang->id;

        // If translations are already eager loaded, use them
        if ($this->relationLoaded('translations')) {
            $translation = $this->translations
                ->where('lang_id', $langId)
                ->where('lang_key', $key)
                ->first();
            return $translation ? $translation->lang_value : null;
        }

        // Fallback to query if not eager loaded
        $translation = $this->translations()
            ->where('lang_id', $langId)
            ->where('lang_key', $key)
            ->first();
        return $translation ? $translation->lang_value : null;
    }

    public function setTranslation(string $key, string $locale, string $value)
    {
        $lang = $this->getLanguageByCode($locale);
        if ($lang) {
            $translation = $this->translations()
                ->firstOrNew(['lang_id' => $lang->id, 'lang_key' => $key]);
            $translation->lang_value = $value;
            $translation->save();
        }
    }
}
