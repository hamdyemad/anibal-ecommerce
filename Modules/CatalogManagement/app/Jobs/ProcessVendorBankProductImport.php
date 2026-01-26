<?php

namespace Modules\CatalogManagement\app\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Modules\CatalogManagement\app\Imports\VendorBankProductsImport;

class ProcessVendorBankProductImport implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $filePath;
    protected int $vendorId;
    protected int $userId;

    public function __construct(string $filePath, int $vendorId, int $userId)
    {
        $this->filePath = $filePath;
        $this->vendorId = $vendorId;
        $this->userId = $userId;
    }

    public function handle()
    {
        if ($this->batch()->cancelled()) {
            return;
        }

        try {
            Log::info('Starting vendor bank product import', [
                'file' => $this->filePath,
                'vendor_id' => $this->vendorId,
                'user_id' => $this->userId
            ]);

            // Check if file exists
            if (!Storage::disk('local')->exists($this->filePath)) {
                Log::error('Import file not found', ['file' => $this->filePath]);
                throw new \Exception('Import file not found');
            }

            // Get full path
            $fullPath = Storage::disk('local')->path($this->filePath);

            // Import the Excel file
            $import = new VendorBankProductsImport($this->vendorId, $this->userId);
            Excel::import($import, $fullPath);

            // Store results in cache for retrieval
            $batchId = $this->batch()->id;
            $results = [
                'imported_count' => $import->getImportedCount(),
                'errors' => $import->getErrors(),
                'status' => 'completed',
            ];

            cache()->put("vendor_bank_import_results_{$batchId}", $results, now()->addHours(24));

            // Delete the file after import
            Storage::disk('local')->delete($this->filePath);

            Log::info('Vendor bank product import completed', [
                'vendor_id' => $this->vendorId,
                'imported_count' => $results['imported_count'],
                'errors_count' => count($import->getErrors())
            ]);

        } catch (\Exception $e) {
            Log::error('Vendor bank product import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Store error in cache
            $batchId = $this->batch()->id;
            cache()->put("vendor_bank_import_results_{$batchId}", [
                'status' => 'failed',
                'error' => $e->getMessage(),
            ], now()->addHours(24));

            // Clean up file
            if (Storage::disk('local')->exists($this->filePath)) {
                Storage::disk('local')->delete($this->filePath);
            }

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
            cache()->put("vendor_bank_import_results_{$batchId}", [
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
