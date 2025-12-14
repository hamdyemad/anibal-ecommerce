<?php

namespace Modules\SystemSetting\app\Repositories;

use Modules\SystemSetting\app\Models\Faq;
use Illuminate\Support\Facades\DB;

class FaqRepository
{
    public function all()
    {
        return Faq::with('translations')->get();
    }

    public function find($id)
    {
        return Faq::with('translations')->findOrFail($id);
    }

    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $faq = Faq::create([
                'active' => 1,
            ]);
            $faq->translations()->forceDelete();

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['question'])) {
                        $faq->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'question',
                            'lang_value' => $translation['question'],
                        ]);
                    }
                    if (!empty($translation['answer'])) {
                        $faq->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'answer',
                            'lang_value' => $translation['answer'],
                        ]);
                    }
                }
            }

            return $faq;
        });
    }

    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $faq = Faq::findOrFail($id);
            $faq->translations()->forceDelete();

            if (isset($data['translations'])) {
                foreach ($data['translations'] as $langId => $translation) {
                    if (!empty($translation['question'])) {
                        $faq->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'question',
                            'lang_value' => $translation['question'],
                        ]);
                    }
                    if (!empty($translation['answer'])) {
                        $faq->translations()->create([
                            'lang_id' => $langId,
                            'lang_key' => 'answer',
                            'lang_value' => $translation['answer'],
                        ]);
                    }
                }
            }

            return $faq;
        });
    }

    public function delete($id)
    {
        return DB::transaction(function () use ($id) {
            $faq = Faq::findOrFail($id);
            $faq->translations()->forceDelete();
            return $faq->delete();
        });
    }

    public function filter($filters)
    {
        return Faq::filter($filters)->with('translations')->get();
    }
}
