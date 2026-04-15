<?php

namespace Modules\Withdraw\app\Models;

use App\Models\Traits\HumanDates;
use App\Models\User;
use App\Models\Traits\CountryCheckIdTrait;
use App\Models\Traits\AutoStoreCountryId;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Vendor\app\Models\Vendor;

// use Modules\Withdraw\app\Database\Factories\WithdrawFactory;

class Withdraw extends Model
{
    use HasFactory, HumanDates, AutoStoreCountryId, CountryCheckIdTrait;

    protected $guarded = [];

    public function vendor(){
        return $this->belongsTo(Vendor::class, "reciever_id");
    }

    public function admin(){
        return $this->belongsTo(User::class, "sender_id");
    }

    public function setInvoiceAttribute($file)
    {
        if (!$file) return;

        // حفظ الصورة الجديدة في public/uploads
        if (is_object($file) && method_exists($file, 'move')) {
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/invoices'), $filename);
            $this->attributes['invoice'] = $filename;
        }
        // لو string
        else {
            $this->attributes['invoice'] = $file;
        }
    }

    /**
     * Accessor: جلب رابط الصورة
     */
    public function getInvoiceUrlAttribute()
    {
        if (empty($this->invoice)) return null;

        return asset('storage/invoices/' . $this->invoice);
    }
}
