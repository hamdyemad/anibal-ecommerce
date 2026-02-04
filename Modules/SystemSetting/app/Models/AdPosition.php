<?php

namespace Modules\SystemSetting\app\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;

class AdPosition extends Model
{
    use HumanDates;

    protected $table = 'ads_positions';
    protected $guarded = [];

    /**
     * Get the ads for this position
     */
    public function ads()
    {
        return $this->hasMany(Ad::class, 'ad_position_id');
    }

    /**
     * Get the reference image path for this ad position
     * Maps position names to their reference images in public/ads folder
     */
    public function getReferenceImageAttribute()
    {
        $imageMap = [
            'Homepage Left Upper Ad Card' => 'Homepage Left Upper Ad Card.png',
            'Homepage Left Lower Ad Card' => 'Homepage Left Lower Ad Card.png',
            'Homepage Main Right Banner' => 'Homepage Main Right Banner.png',
            'Homepage Mid-Content Banner' => 'Homepage Mid-Content Banner.png',
            'Middle Home Ad' => 'mobile-middle.jpg',
            'Sidebar Ad' => 'sidebarad.png',
        ];

        // Try exact match first (case-insensitive)
        foreach ($imageMap as $positionName => $imageName) {
            if (strcasecmp($this->name, $positionName) === 0 || 
                strcasecmp($this->position ?? '', $positionName) === 0) {
                $imagePath = 'ads/' . $imageName;
                if (file_exists(public_path($imagePath))) {
                    return asset($imagePath);
                }
            }
        }

        // Try partial match - but only if the position name STARTS with the key
        // This prevents "Middle Home Ad" from matching "Homepage Mid-Content Banner"
        foreach ($imageMap as $positionName => $imageName) {
            if (stripos($this->name, $positionName) === 0 || 
                stripos($this->position ?? '', $positionName) === 0) {
                $imagePath = 'ads/' . $imageName;
                if (file_exists(public_path($imagePath))) {
                    return asset($imagePath);
                }
            }
        }

        return null;
    }

    /**
     * Check if this position has a reference image
     */
    public function hasReferenceImage()
    {
        return !is_null($this->reference_image);
    }
}
