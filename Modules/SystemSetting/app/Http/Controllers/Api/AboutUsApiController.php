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
        $aboutUs = $this->aboutUsService->getByPlatform('website');
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
        $aboutUs = $this->aboutUsService->getByPlatform('mobile');
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

        $aboutUs = $this->aboutUsService->getByPlatform($platform);
        
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
