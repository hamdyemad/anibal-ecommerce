<?php

namespace Modules\Accounting\app\Models;

use App\Models\BaseModel;
use App\Models\Attachment;
use App\Models\Traits\HumanDates;
use App\Models\Traits\AutoStoreCountryId;
use App\Models\Traits\CountryCheckIdTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends BaseModel
{
    use HasFactory, SoftDeletes, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $fillable = [
        'expense_item_id',
        'amount',
        'description',
        'expense_date',
        'country_id'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expense_date' => 'date'
    ];

    public function expenseItem()
    {
        return $this->belongsTo(\Modules\Accounting\app\Models\ExpenseItem::class)->withTrashed();
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function getReceiptAttribute()
    {
        $receiptAttachment = $this->attachments()->where('type', 'receipt')->first();
        return $receiptAttachment ? $receiptAttachment->path : null;
    }

    public function scopeFilter($query, array $filters)
    {
        if (!empty($filters['search'])) {
            $this->applyCustomSearch($query, $filters['search']);
            // Remove search from filters to prevent parent from processing it
            unset($filters['search']);
        }
        
        parent::scopeFilter($query, $filters);
        
        // Custom expense date filter
        if (!empty($filters['date_from'])) {
            $query->whereDate('expense_date', '>=', $filters['date_from']);
        }
        
        if (!empty($filters['date_to'])) {
            $query->whereDate('expense_date', '<=', $filters['date_to']);
        }
        
        if (!empty($filters['expense_item_id'])) {
            $query->where('expense_item_id', $filters['expense_item_id']);
        }
        
        return $query;
    }

    protected function applyCustomSearch(\Illuminate\Database\Eloquent\Builder $query, string $search): \Illuminate\Database\Eloquent\Builder
    {
        $query->where('description', 'like', "%{$search}%")
              ->orWhereHas('expenseItem', function($subQ) use ($search) {
                  $subQ->whereHas('translations', function($transQ) use ($search) {
                      $transQ->where('lang_value', 'like', "%{$search}%")
                             ->where('lang_key', 'name');
                  });
              });
        
        return $query;
    }
}
