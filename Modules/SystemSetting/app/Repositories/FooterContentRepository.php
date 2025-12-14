<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\FooterContent;
use Illuminate\Support\Facades\DB;

class FooterContentRepository
{
    /**
     * Get the footer content (only one record)
     */
    public function get()
    {
        return FooterContent::with('translations')->first();
    }

    /**
     * Create or update footer content
     */
    public function createOrUpdate(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Get existing footer content or create new
            $footerContent = FooterContent::first();

            if ($footerContent) {
                // Update existing
                $footerContent->update([
                    'google_play_link' => $data['google_play_link'] ?? null,
                    'apple_store_link' => $data['apple_store_link'] ?? null,
                    'active' => $data['active'] ?? 1,
                ]);

                // Delete old translations
                $footerContent->translations()->forceDelete();
            } else {
                // Create new
                $footerContent = FooterContent::create([
                    'google_play_link' => $data['google_play_link'] ?? null,
                    'apple_store_link' => $data['apple_store_link'] ?? null,
                    'active' => $data['active'] ?? 1,
                ]);
            }

            // Save translations
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['title'])) {
                        $footerContent->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'title',
                            'lang_value' => $translation['title'],
                        ]);
                    }
                    if (!empty($translation['description'])) {
                        $footerContent->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'description',
                            'lang_value' => $translation['description'],
                        ]);
                    }
                }
            }

            return $footerContent;
        });
    }
}
