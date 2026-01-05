<?php

namespace App\Exceptions;

use Exception;

class OrderException extends Exception
{
    protected $errorKey;
    protected $errorData;

    public function __construct(string $key, string $message = null, array $errorData = [])
    {
        $this->errorKey = $key;
        $this->errorData = $errorData;
        
        // Use provided message or fall back to translation key
        $finalMessage = $message ?? __($key);
        
        parent::__construct($finalMessage, 422);
    }

    public function getErrorKey()
    {
        return $this->errorKey;
    }

    public function getErrorData()
    {
        return $this->errorData;
    }
}
