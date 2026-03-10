<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantsConfigurationLink extends Model
{
    protected $table = 'variants_configurations_links';
    
    protected $fillable = [
        'parent_config_id',
        'child_config_id',
    ];

    /**
     * Get the parent configuration
     */
    public function parentConfig(): BelongsTo
    {
        return $this->belongsTo(VariantsConfiguration::class, 'parent_config_id');
    }

    /**
     * Get the child configuration
     */
    public function childConfig(): BelongsTo
    {
        return $this->belongsTo(VariantsConfiguration::class, 'child_config_id');
    }
}
