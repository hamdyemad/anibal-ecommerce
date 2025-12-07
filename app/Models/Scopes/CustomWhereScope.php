<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CustomWhereScope implements Scope
{
    protected $column;
    protected $value;

    public function __construct($column, $value)
    {
        $this->column = $column;
        $this->value  = $value;
    }

    public function apply(Builder $builder, Model $model)
    {
        // Your global WHERE condition
        $builder->where($this->column, $this->value);
    }
}
