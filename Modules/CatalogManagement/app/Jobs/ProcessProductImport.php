<?php

namespace Modules\CatalogManagement\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CatalogManagement\app\Imports\ProductsImport;

class ProcessProductImport implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $isAdmin;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct(string $filePath, bool $isAdmin, int $userId)
    {
        $this->filePath = $filePath;
        $this->isAdmin = $isAdmin;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            $import = new ProductsImport($this->isAdmin);
            Excel::import($import, Storage::disk('local')->path($this->filePath));

            // Store results in cache for retrieval
            $batchId = $this->batch()->id;
            $results = [
                'imported_count' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'status' => 'completed',
            ];

            cache()->put("import_results_{$batchId}", $results, now()->addHours(24));

            // Clean up the uploaded file
            Storage::disk('local')->delete($this->filePath);
        } catch (\Exception $e) {
            // Store error in cache
            $batchId = $this->batch()->id;
            cache()->put("import_results_{$batchId}", [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ], now()->addHours(24));

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        // Store failure information
        if ($this->batch()) {
            $batchId = $this->batch()->id;
            cache()->put("import_results_{$batchId}", [
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ], now()->addHours(24));
        }

        // Clean up the uploaded file
        if (Storage::disk('local')->exists($this->filePath)) {
            Storage::disk('local')->delete($this->filePath);
        }
    }
}
