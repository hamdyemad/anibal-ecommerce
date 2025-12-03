<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Interfaces\CurrencyRepositoryInterface;
use Modules\SystemSetting\app\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CurrencyRepository implements CurrencyRepositoryInterface
{
    /**
     * Get all currencies with filters and pagination
     */
    public function getAllCurrencies(array $filters = [], ?int $perPage = 15)
    {
        $query = Currency::with('translations','attachments')->filter($filters);

        // Order by latest
        $query->orderBy('created_at', 'desc');

        // Return paginated or all records
        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    /**
     * Get currencies query for DataTables
     */
    public function getCurrenciesQuery(array $filters = [], $orderBy = null, $orderDirection = 'asc')
    {
        return Currency::with('translations','attachments')->filter($filters);
    }

    /**
     * Get currency by ID
     */
    public function getCurrencyById(int $id)
    {
        return Currency::with('translations','attachments')->findOrFail($id);
    }

    /**
     * Create a new currency
     */
    public function createCurrency(array $data)
    {
        return DB::transaction(function () use ($data) {
            $currency = Currency::create([
                'code' => $data['code'],
                'symbol' => $data['symbol'],
                'use_image' => $data['use_image'] ?? 0,
                'active' => $data['active'] ?? 0,
            ]);

            // Set translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $currency->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'name',
                            'lang_value' => $translation['name'],
                        ]);
                    }
                }
            }

            // Handle image upload
            if (isset($data['image']) && $data['image']) {
                $this->handleImageUpload($currency, $data['image']);
            }

            return $currency;
        });
    }

    /**
     * Update currency
     */
    public function updateCurrency(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $currency = Currency::findOrFail($id);

            $currency->update([
                'code' => $data['code'],
                'symbol' => $data['symbol'],
                'use_image' => $data['use_image'] ?? 0,
                'active' => $data['active'] ?? 0,
            ]);

            // Update translations from nested array
            if (isset($data['translations']) && is_array($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (isset($translation['name'])) {
                        $currency->translations()->updateOrCreate(
                            [
                                'lang_id' => $langId,
                                'lang_key' => 'name',
                            ],
                            [
                                'lang_value' => $translation['name'],
                            ]
                        );
                    }
                }
            }

            // Handle image removal
            if (isset($data['remove_image']) && $data['remove_image']) {
                $this->removeImage($currency);
            }
            // Handle image upload
            elseif (isset($data['image']) && $data['image']) {
                $this->handleImageUpload($currency, $data['image']);
            }

            $currency->refresh();
            $currency->load('translations');

            return $currency;
        });
    }

    /**
     * Delete currency
     */
    public function deleteCurrency(int $id)
    {
        $currency = Currency::findOrFail($id);

        // Check if currency is used by any countries
        $countriesCount = $currency->countries()->count();
        if ($countriesCount > 0) {
            throw new \Exception(
                __('systemsetting::currency.cannot_delete_currency_with_countries', [
                    'count' => $countriesCount
                ])
            );
        }

        $this->removeImage($currency);
        $currency->translations()->delete();
        return $currency->delete();
    }

    /**
     * Get active currencies
     */
    public function getActiveCurrencies()
    {
        return Currency::with('translations','attachments')->where('active', 1)->get();
    }

    /**
     * Handle image upload for currency
     */
    protected function handleImageUpload(Currency $currency, $image): void
    {
        if (!$image) {
            return;
        }

        // Remove old image if exists
        $this->removeImage($currency);

        // Store new image
        $path = $image->store('currencies', 'public');

        // Create attachment record
        $currency->attachments()->create([
            'path' => $path,
            'type' => 'image',
        ]);
    }

    /**
     * Remove currency image
     */
    protected function removeImage(Currency $currency): void
    {
        $attachment = $currency->attachments()->where('type', 'image')->first();
        if ($attachment) {
            Storage::disk('public')->delete($attachment->path);
            $attachment->delete();
        }
    }
}
