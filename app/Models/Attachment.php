<?php

namespace App\Models;

use App\Traits\Translation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use Translation, SoftDeletes;
    protected $guarded = [];

    public function attachmentable()
    {
        return $this->morphTo();
    }
}
