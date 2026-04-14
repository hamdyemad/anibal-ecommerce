<?php

namespace Modules\SystemSetting\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\Res;
use Modules\SystemSetting\app\Http\Resources\Api\AboutUsResource;
use Modules\SystemSetting\app\Http\Resources\Api\AboutUsMobileResource;
use Modules\SystemSetting\app\Services\AboutUsService;

class AboutUsApiController extends Controller
{
    use Res;

    protected $aboutUsService;

    public function __construct(AboutUsService $aboutUsService)
    {
        $this->aboutUsService = $aboutUsService;
    }

    /**
     * Get about us for website
     * GET /api/about-us/website
     */
    public function website()
    {
        // Cache about us website forever - will be cleared when updated
        $cacheKey = 'api_about_us_website_' . app()->getLocale();
        
        $aboutUs = \Cache::rememberForever($cacheKey, function() {
            return $this->aboutUsService->getByPlatform('website');
        });
        
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            new AboutUsResource($aboutUs)
        );
    }

    /**
     * Get about us for mobile
     * GET /api/about-us/mobile
     */
    public function mobile()
    {
        // Cache about us mobile forever - will be cleared when updated
        $cacheKey = 'api_about_us_mobile_' . app()->getLocale();
        
        $aboutUs = \Cache::rememberForever($cacheKey, function() {
            return $this->aboutUsService->getByPlatform('mobile');
        });
        
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            new AboutUsMobileResource($aboutUs)
        );
    }

    /**
     * Get about us by platform
     * GET /api/about-us/{platform}
     */
    public function show(string $platform)
    {
        if (!in_array($platform, ['website', 'mobile'])) {
            return $this->sendRes(
                config('responses.not_found')[app()->getLocale()],
                false,
                null,
                ['platform' => 'Invalid platform. Use "website" or "mobile".'],
                404
            );
        }

        // Cache about us by platform forever - will be cleared when updated
        $cacheKey = 'api_about_us_' . $platform . '_' . app()->getLocale();
        
        $aboutUs = \Cache::rememberForever($cacheKey, function() use ($platform) {
            return $this->aboutUsService->getByPlatform($platform);
        });
        
        $resource = $platform === 'mobile' 
            ? new AboutUsMobileResource($aboutUs) 
            : new AboutUsResource($aboutUs);
            
        return $this->sendRes(
            config('responses.success')[app()->getLocale()],
            true,
            $resource
        );
    }
}
