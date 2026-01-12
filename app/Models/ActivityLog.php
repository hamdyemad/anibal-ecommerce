<?php

namespace App\Models;

use App\Models\Traits\HumanDates;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HumanDates;
    protected $fillable = [
        'user_id',
        'action',
        'model',
        'model_id',
        'description_key',
        'description_params',
        'properties',
        'ip_address',
        'user_agent',
        'country_id',
    ];

    protected $casts = [
        'description_params' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'translated_description',
        'translated_action',
        'model_name',
        'user_name',
    ];

    /**
     * Default ordering by latest first
     */
    protected static function boot()
    {
        parent::boot();
        
        static::addGlobalScope('latest', function ($query) {
            $query->orderBy('created_at', 'desc');
        });
    }

    /**
     * Get the user who performed the action
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject model (polymorphic)
     */
    public function subject()
    {
        if (!$this->model || !$this->model_id) {
            return null;
        }

        if (!class_exists($this->model)) {
            return null;
        }

        return $this->model::find($this->model_id);
    }

    /**
     * Get translated description
     */
    public function getTranslatedDescriptionAttribute(): string
    {
        if (!$this->description_key) {
            return '';
        }

        $params = $this->description_params ?? [];
        
        // Translate the model name if present
        if (isset($params['model'])) {
            $modelName = $params['model'];
            
            // Check if it contains translation key pattern (case-insensitive)
            if (stripos($modelName, 'activity_log.models.') !== false) {
                // Extract just the model name from the key (handle both cases)
                $modelName = preg_replace('/activity_log\.models\./i', '', $modelName);
            }
            
            // Now translate the clean model name
            $translatedModel = __("activity_log.models.{$modelName}");
            
            // If translation not found (returns the key), use the original model name
            if (str_contains($translatedModel, 'activity_log.models.')) {
                $params['model'] = $modelName;
            } else {
                $params['model'] = $translatedModel;
            }
        }
        
        // If we have model information, try to get the translated identifier
        if ($this->model && isset($params["identifier"])) {
            $modelClass = $this->model;
            $modelId = $this->model_id;
            
            // Get the model instance
            if (class_exists($modelClass)) {
                try {
                    // Build query
                    $query = $modelClass::query();
                    
                    // Check if model uses SoftDeletes trait and handle it
                    if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelClass) ?: [])) {
                        $query = $query->withTrashed();
                    }
                    
                    $modelInstance = $query->find($modelId);
                    
                    if ($modelInstance) {
                        // Check if model has getTranslation method from Translation trait
                        if (method_exists($modelInstance, 'getTranslation')) {
                            // Get current locale
                            $locale = app()->getLocale() ?? 'en';
                            
                            // Get translated name
                            $translatedName = $modelInstance->getTranslation('name', $locale);
                            
                            // If translation found, use it; otherwise keep the ID
                            if ($translatedName) {
                                $params['identifier'] = $translatedName;
                            }
                        }
                        // Special handling for Order model - use order_number
                        elseif ($modelClass === 'Modules\Order\app\Models\Order' || class_basename($modelClass) === 'Order') {
                            if (isset($modelInstance->order_number)) {
                                $params['identifier'] = $modelInstance->order_number;
                            }
                        }
                        // Special handling for User model - use name or email
                        elseif (class_basename($modelClass) === 'User') {
                            $params['identifier'] = $modelInstance->name ?? $modelInstance->email ?? $params['identifier'];
                        }
                    }
                } catch (\Exception $e) {
                    // Silent fail - use the original identifier (ID)
                }
            }
        }
        
        // For updated action, try to show what changed (especially for stage changes)
        if ($this->action === 'updated' && !empty($this->properties['new'])) {
            $changes = $this->properties['new'];
            
            // Check if stage_id was changed
            if (isset($changes['stage_id'])) {
                try {
                    $stageClass = 'Modules\Order\app\Models\OrderStage';
                    if (class_exists($stageClass)) {
                        $stage = $stageClass::withoutGlobalScopes()->find($changes['stage_id']);
                        if ($stage) {
                            $locale = app()->getLocale() ?? 'en';
                            $stageName = method_exists($stage, 'getTranslation') 
                                ? $stage->getTranslation('name', $locale) 
                                : ($stage->name ?? $changes['stage_id']);
                            
                            // Update identifier to show stage name
                            $params['identifier'] = ($params['identifier'] ?? $this->model_id) . ' → ' . $stageName;
                        }
                    }
                } catch (\Exception $e) {
                    // Silent fail
                }
            }
        }

        // Use Laravel's translation with parameters
        return __($this->description_key, $params);
    }

    /**
     * Get translated action
     */
    public function getTranslatedActionAttribute(): string
    {
        return __("activity_log.actions.{$this->action}");
    }

    /**
     * Get formatted model name
     */
    public function getModelNameAttribute(): string
    {
        if (!$this->model) {
            return 'N/A';
        }

        $modelBaseName = class_basename($this->model);
        return __("activity_log.models.{$modelBaseName}");
    }

    /**
     * Get user name or 'System' if no user
     */
    public function getUserNameAttribute(): string
    {
        return $this->user ? $this->user->email : __('System');
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by model
     */
    public function scopeByModel($query, $model)
    {
        return $query->where('model', $model);
    }
}
