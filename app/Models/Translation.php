<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Translation extends Model
{
    use SoftDeletes;
    protected $guarded = [];

    public function translatable()
    {
        return $this->morphTo();
    }
}
