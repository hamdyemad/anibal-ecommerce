<?php

namespace Modules\SystemSetting\app\Models;

use Illuminate\Database\Eloquent\Model;

class AdPositionSetting extends Model
{
    protected $table = 'ad_position_settings';
    
    protected $fillable = [
        'position',
        'width',
        'height',
    ];

    protected $casts = [
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Get all positions with their dimensions
     */
    public static function getAllPositions()
    {
        $settings = self::all()->keyBy('position');
        $defaultPositions = Ad::getPositions();
        
        $result = [];
        foreach ($defaultPositions as $key => $name) {
            $setting = $settings->get($key);
            $result[$key] = [
                'name' => $name,
                'width' => $setting?->width ?? 0,
                'height' => $setting?->height ?? 0,
            ];
        }
        
        return $result;
    }

    /**
     * Get dimensions for a specific position
     */
    public static function getDimensions(string $position): array
    {
        $setting = self::where('position', $position)->first();
        
        return [
            'width' => $setting?->width ?? 0,
            'height' => $setting?->height ?? 0,
        ];
    }

    /**
     * Update or create position settings
     */
    public static function updatePosition(string $position, int $width, int $height): self
    {
        return self::updateOrCreate(
            ['position' => $position],
            ['width' => $width, 'height' => $height]
        );
    }
}
