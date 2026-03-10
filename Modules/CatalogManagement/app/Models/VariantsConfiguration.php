<?php

namespace Modules\CatalogManagement\app\Models;

use App\Models\Traits\HumanDates;
use App\Traits\Translation;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class VariantsConfiguration extends Model
{
    use HasFactory, Translation, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $table = 'variants_configurations';
    protected $guarded = [];


    // scope
    public function scopeParent($query)
    {
        return $query->whereNull("parent_id");
    }

    public function getNameAttribute() {
        return $this->getTranslation('name', app()->getLocale());
    }

    // relationships
    public function key()
    {
        return $this->belongsTo(VariantConfigurationKey::class, 'key_id');
    }

    public function parent_data()
    {
        return $this->belongsTo(VariantsConfiguration::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive', 'key');
    }

    public function getColorAttribute() {
        return $this->value;
    }

    /**
     * Get linked children through configuration_links table
     */
    public function linkedChildren()
    {
        return $this->belongsToMany(
            VariantsConfiguration::class,
            'variants_configurations_links',
            'parent_config_id',
            'child_config_id'
        )->withTimestamps();
    }

    /**
     * Get linked parents through configuration_links table
     */
    public function linkedParents()
    {
        return $this->belongsToMany(
            VariantsConfiguration::class,
            'variants_configurations_links',
            'child_config_id',
            'parent_config_id'
        )->withTimestamps();
    }

    /**
     * Get all children (both direct and linked)
     */
    public function allChildren()
    {
        // Merge direct children and linked children
        $directChildren = $this->children;
        $linkedChildren = $this->linkedChildren;
        
        return $directChildren->merge($linkedChildren)->unique('id');
    }

    /**
     * Get the link ID between this variant (parent) and a child variant
     * 
     * @param int $childConfigId The child variant configuration ID
     * @return int|null The link ID or null if no link exists
     */
    public function getLinkIdToChild($childConfigId)
    {
        $link = DB::table('variants_configurations_links')
            ->where('parent_config_id', $this->id)
            ->where('child_config_id', $childConfigId)
            ->first();
        
        return $link ? $link->id : null;
    }

    /**
     * Get the link ID between a parent variant and this variant (child)
     * 
     * @param int $parentConfigId The parent variant configuration ID
     * @return int|null The link ID or null if no link exists
     */
    public function getLinkIdFromParent($parentConfigId)
    {
        $link = DB::table('variants_configurations_links')
            ->where('parent_config_id', $parentConfigId)
            ->where('child_config_id', $this->id)
            ->first();
        
        return $link ? $link->id : null;
    }

    /**
     * Get link details with parent and child configurations
     */
    public function links()
    {
        return $this->hasMany(VariantConfigurationLink::class, 'parent_config_id');
    }


    /**
     * Get all of the ProductVariantConfiguration for the VariantsConfiguration
     * TODO: Uncomment when ProductVariantConfiguration model is created
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function productVariantConfiguration(): HasMany
    // {
    //     return $this->hasMany(ProductVariantConfiguration::class, 'variants_configuration_id');
    // }

    /**
     * Get all of the comments for the VariantsConfiguration
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    // public function comments(): HasMany
    // {
    //     return $this->hasMany(Comment::class, 'foreign_key', 'local_key');
    // }
}
