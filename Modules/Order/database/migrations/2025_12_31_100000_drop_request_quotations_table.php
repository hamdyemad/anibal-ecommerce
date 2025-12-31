<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('request_quotations');
    }

    public function down(): void
    {
        // Cannot restore - use the create migration
    }
};
