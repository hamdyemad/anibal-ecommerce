<?php

namespace App\Interfaces;
use Illuminate\Database\Eloquent\Collection;

interface LanguageRepositoryInterface
{
    /**
     * Get all languages
     */
    public function getAll(): Collection;

}
