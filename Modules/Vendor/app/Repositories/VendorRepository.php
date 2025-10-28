<?php

namespace Modules\Vendor\app\Repositories;

use App\Models\Attachment;
use App\Models\User;
use App\Models\Translation;
use App\Models\UserType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\Vendor\app\Interfaces\VendorInterface;
use Modules\Vendor\app\Models\Vendor;

class VendorRepository implements VendorInterface
{
    public function getAllVendors(array $filters = [], int $perPage = 10)
    {
        $query = Vendor::with(['user', 'country', 'country.translations', 'activities', 'translations']);

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['country_id'])) {
            $query->where('country_id', $filters['country_id']);
        }

        if (!empty($filters['activity_id'])) {
            $query->where('activity_id', $filters['activity_id']);
        }

        return $query->latest()->paginate($perPage);
    }

    public function getVendorById(int $id)
    {
        return Vendor::with([
            'user', 
            'country', 
            'country.translations', 
            'activities', 
            'translations',
            'attachments',
            'attachments.translations',
            'commission'
        ])->findOrFail($id);
    }

    public function createVendor(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Create user account
            $user = User::create([
                'uuid' => \Str::uuid(),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'user_type_id' => UserType::VENDOR_TYPE, // Vendor type
            ]);

            // Create vendor
            $vendor = Vendor::create([
                'slug' => Str::uuid(),
                'user_id' => $user->id,
                'country_id' => $data['country_id'],
                'active' => $data['active'] ?? false,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);
            // Handle logo upload
            if (isset($data['logo'])) {
                $logoPath = $data['logo']->store("vendors/$vendor->id/logo", 'public');
                $vendor->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            // Handle banner upload
            if (isset($data['banner'])) {
                $bannerPath = $data['banner']->store("vendors/$vendor->id/banner", 'public');
                $vendor->attachments()->create([
                    'path' => $bannerPath,
                    'type' => 'banner',
                ]);
            }


            // Sync activities (many-to-many relationship)
            if (!empty($data['activity_ids'])) {
                $vendor->activities()->sync($data['activity_ids']);
            }

            // Store commission
            if (isset($data['commission'])) {
                $vendor->commission()->create([
                    'commission' => $data['commission'],
                ]);
            }

            // Store translations
            $this->storeTranslations($vendor, $data);

            // Handle documents
            if (!empty($data['documents'])) {
                $this->storeDocuments($vendor, $data['documents']);
            }

            return $vendor;
        });
    }

    public function updateVendor(int $id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $vendor = Vendor::findOrFail($id);

            // Handle logo upload
            if (isset($data['logo'])) {
                if ($vendor->logo) {
                    Storage::disk('public')->delete($vendor->logo->path);
                    $vendor->logo()->delete();
                }
                $logoPath = $data['logo']->store("vendors/$vendor->id/logo", 'public');
                $vendor->attachments()->create([
                    'path' => $logoPath,
                    'type' => 'logo',
                ]);
            }

            // Handle banner upload
            if (isset($data['banner'])) {
                if ($vendor->banner) {
                    Storage::disk('public')->delete($vendor->banner->path);
                    $vendor->banner()->delete();
                }
                $bannerPath = $data['banner']->store("vendors/$vendor->id/banner", 'public');
                $vendor->attachments()->create([
                    'path' => $bannerPath,
                    'type' => 'banner',
                ]);
            }

            // Update vendor
            $vendor->update([
                'country_id' => $data['country_id'],
                'active' => $data['active'] ?? $vendor->active,
                'meta_title' => $data['meta_title'] ?? null,
                'meta_description' => $data['meta_description'] ?? null,
                'meta_keywords' => $data['meta_keywords'] ?? null,
            ]);

            // Sync activities (many-to-many relationship)
            if (!empty($data['activity_ids'])) {
                $vendor->activities()->sync($data['activity_ids']);
            }

            // Update commission
            if (isset($data['commission'])) {
                $vendor->commission()->delete();
                $vendor->commission()->create([
                    'commission' => $data['commission'],
                ]);
            }

            // Update translations
            $this->storeTranslations($vendor, $data);

            // Handle documents
            if (!empty($data['documents'])) {
                $this->storeDocuments($vendor, $data['documents']);
            }

            return $vendor;
        });
    }

    public function deleteVendor(int $id)
    {
        return DB::transaction(function () use ($id) {
            $vendor = Vendor::with(['user', 'attachments'])->findOrFail($id);
            
            // Delete all attachments from storage
            foreach ($vendor->attachments as $attachment) {
                // Delete attachment translations
                $attachment->translations()->delete();
                
                // Delete attachment record
                $attachment->delete();
            }
            
            // Delete vendor translations
            $vendor->translations()->delete();
            
            // Detach activities (many-to-many)
            $vendor->activities()->detach();
            
            // Get user before deleting vendor
            $user = $vendor->user;
            
            // Delete vendor
            $vendor->delete();
            
            // Delete associated user account if exists
            if ($user) {
                $user->delete();
            }
            
            return true;
        });
    }

    /**
     * Store translations for vendor
     */
    protected function storeTranslations(Vendor $vendor, array $data)
    {
        // Delete existing translations
        $vendor->translations()->delete();

        // Handle translations array from form (translations[language_id][name/description])
        if (!empty($data['translations'])) {
            foreach ($data['translations'] as $languageId => $fields) {
                // Get language code from language ID
                $language = \App\Models\Language::find($languageId);
                if (!$language) {
                    continue;
                }

                // Store name translation
                if (!empty($fields['name'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }

                // Store description translation
                if (!empty($fields['description'])) {
                    $vendor->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'description',
                        'lang_value' => $fields['description'],
                    ]);
                }
            }
        }
    }

    /**
     * Store documents for vendor using Attachment model
     */
    protected function storeDocuments(Vendor $vendor, array $documents)
    {
        foreach ($documents as $documentData) {
            // Check if file exists
            if (empty($documentData['file'])) {
                continue;
            }
            
            $file = $documentData['file'];
            $filePath = $file->store("vendors/{$vendor->id}/documents", 'public');
            
            // Create the attachment
            $attachment = $vendor->attachments()->create([
                'type' => 'docs',
                'path' => $filePath,
            ]);
            
            // Store document name translations if they exist
            if (!empty($documentData['translations'])) {
                foreach ($documentData['translations'] as $languageId => $fields) {
                    // Get language code from language ID
                    $language = \App\Models\Language::find($languageId);
                    if (!$language || empty($fields['name'])) {
                        continue;
                    }
                    
                    // Store document name translation
                    $attachment->translations()->create([
                        'lang_id' => $language->id,
                        'lang_key' => 'name',
                        'lang_value' => $fields['name'],
                    ]);
                }
            }
        }
    }
}
