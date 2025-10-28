<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\Translation;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use Translation, SoftDeletes;
    
    protected $guarded = [];
    
    /**
     * The attributes that are translatable.
     *
     * @var array
     */
    public $translatable = ['name'];

    /**
     * Get the role name from translations based on current locale.
     * This accessor allows accessing $role->name even though there's no name column.
     */
    public function getNameAttribute()
    {
        return $this->getTranslation('name', app()->getLocale()) 
               ?? $this->getTranslation('name', config('app.fallback_locale'))
               ?? 'Unnamed Role';
    }

    /**
     * Get the permissions for the role.
     */
    public function permessions()
    {
        return $this->belongsToMany(Permession::class, 'role_permession', 'role_id', 'permession_id')
                    ->withTimestamps();
    }
}
