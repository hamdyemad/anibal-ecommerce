<?php

namespace Modules\CatalogManagement\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantConfigurationLink extends Model
{
    use HasFactory;

    protected $table = 'variants_configurations_links';
    protected $guarded = [];

    /**
     * Get the parent variant configuration
     */
    public function parentConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'parent_config_id');
    }

    /**
     * Get the child variant configuration
     */
    public function childConfiguration()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'child_config_id');
    }

    /**
     * Get vendor product variants using this link
     */
    public function vendorProductVariants()
    {
        return $this->hasMany(VendorProductVariant::class, 'variant_link_id');
    }
}
