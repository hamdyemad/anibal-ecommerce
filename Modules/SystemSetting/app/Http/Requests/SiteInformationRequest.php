<?php

namespace Modules\SystemSetting\app\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SiteInformationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'facebook_url' => 'nullable|url',
            'linkedin_url' => 'nullable|url',
            'twitter_url' => 'nullable|url',
            'instagram_url' => 'nullable|url',
            'phone_1' => 'nullable|string|max:20',
            'phone_2' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'google_maps_url' => 'nullable|url',
            'address' => 'nullable|array',
            'address.*' => 'nullable|array',
            'address.*.*' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'facebook_url' => __('systemsetting::site-information.facebook_url'),
            'linkedin_url' => __('systemsetting::site-information.linkedin_url'),
            'twitter_url' => __('systemsetting::site-information.twitter_url'),
            'instagram_url' => __('systemsetting::site-information.instagram_url'),
            'phone_1' => __('systemsetting::site-information.phone_1'),
            'phone_2' => __('systemsetting::site-information.phone_2'),
            'email' => __('systemsetting::site-information.email'),
            'google_maps_url' => __('systemsetting::site-information.google_maps_url'),
            'address_en' => __('systemsetting::site-information.address_en'),
            'address_ar' => __('systemsetting::site-information.address_ar'),
        ];
    }

    public function messages(): array
    {
        return [
            'facebook_url.url' => __('systemsetting::site-information.facebook_url_invalid'),
            'linkedin_url.url' => __('systemsetting::site-information.linkedin_url_invalid'),
            'twitter_url.url' => __('systemsetting::site-information.twitter_url_invalid'),
            'instagram_url.url' => __('systemsetting::site-information.instagram_url_invalid'),
            'email.email' => __('systemsetting::site-information.email_invalid'),
            'google_maps_url.url' => __('systemsetting::site-information.google_maps_url_invalid'),
        ];
    }
}
