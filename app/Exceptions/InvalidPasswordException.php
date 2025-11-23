<?php

namespace App\Exceptions;

use Exception;

class InvalidPasswordException extends Exception
{
    public function __construct()
    {
        parent::__construct(config('responses.invalid_password')[app()->getLocale()] ?? 'Invalid password', 422);
    }
}
