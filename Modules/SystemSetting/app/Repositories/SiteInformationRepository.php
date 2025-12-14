<?php

namespace Modules\SystemSetting\app\Repositories;

use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Modules\SystemSetting\app\Models\SiteInformation;

class SiteInformationRepository
{
    public function getOrCreate()
    {
        return SiteInformation::first() ?? SiteInformation::create([]);
    }

    public function update($data)
    {
        return DB::transaction(function () use ($data) {
            $siteInfo = $this->getOrCreate();

            $siteInfo->update([
                'facebook_url' => $data['facebook_url'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
                'twitter_url' => $data['twitter_url'] ?? null,
                'instagram_url' => $data['instagram_url'] ?? null,
                'phone_1' => $data['phone_1'] ?? null,
                'phone_2' => $data['phone_2'] ?? null,
                'email' => $data['email'] ?? null,
                'google_maps_url' => $data['google_maps_url'] ?? null,
            ]);

            // Handle multilingual address from x-multilingual-input component
            if (isset($data['address']) && is_array($data['address'])) {
                $languages = Language::all()->keyBy('id');

                foreach ($data['address'] as $languageId => $translations) {
                    if (is_array($translations) && isset($languages[$languageId])) {
                        $languageCode = $languages[$languageId]->code;
                        // The component sends data as address[languageId][fieldName]
                        // where fieldName is 'address' in this case
                        $addressValue = $translations['address'] ?? '';
                        // Always set the translation, even if empty (to clear previous values)
                        $siteInfo->setTranslation('address', $languageCode, (string)$addressValue);
                    }
                }
            }

            return $siteInfo;
        });
    }

    public function getSiteInformation()
    {
        return $this->getOrCreate();
    }
}
