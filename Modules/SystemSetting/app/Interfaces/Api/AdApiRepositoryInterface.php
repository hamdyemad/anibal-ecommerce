<?php

namespace Modules\SystemSetting\app\Interfaces\Api;

interface AdApiRepositoryInterface
{
    public function all();

    public function find($id);
}
