@props([
    'containerId' => 'batchProgressContainer',
    'progressCheckUrl' => null,
    'checkInterval' => 2000,
    'storageKey' => 'batch_progress',
    'texts' => [
        'inProgress' => __('common.import_in_progress') ?? 'Import in Progress',
        'checking' => __('common.checking_progress') ?? 'Checking progress...',
        'completed' => __('common.import_completed') ?? 'Import Completed',
        'completedMessage' => __('common.import_completed_message') ?? 'Your import has been completed successfully',
        'failed' => __('common.import_failed') ?? 'Import Failed',
        'failedMessage' => __('common.import_failed_message') ?? 'Some errors occurred during import',
        'processing' => __('common.processing') ?? 'Processing',
        'jobsRemaining' => __('common.jobs_remaining') ?? 'jobs remaining',
    ]
])

{{-- Inline Batch Progress Container --}}
<div id="{{ $containerId }}" class="batch-progress-container" style="display: none;">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="d-flex align-items-center mb-3">
                <div class="spinner-border text-primary me-3" role="status" id="{{ $containerId }}_spinner">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-1" id="{{ $containerId }}_title">{{ $texts['inProgress'] }}</h5>
                    <p class="text-muted mb-0 small" id="{{ $containerId }}_subtext">{{ $texts['checking'] }}</p>
                </div>
                <button type="button" class="btn btn-sm btn-light" id="{{ $containerId }}_dismiss" style="display: none;">
                    <i class="uil uil-times"></i>
                </button>
            </div>
            
            <div class="progress mb-2" style="height: 25px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                     role="progressbar" 
                     id="{{ $containerId }}_bar"
                     style="width: 0%;" 
                     aria-valuenow="0" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <span class="fw-bold" id="{{ $containerId }}_text">0%</span>
                </div>
            </div>
            
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted" id="{{ $containerId }}_details"></small>
                <small class="text-muted" id="{{ $containerId }}_time"></small>
            </div>

            {{-- Results Summary --}}
            <div id="{{ $containerId }}_results" class="mt-3" style="display: none;">
                <div class="alert alert-info mb-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <strong id="{{ $containerId }}_results_summary"></strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary" id="{{ $containerId }}_view_details">
                            <i class="uil uil-eye"></i> {{ __('common.view_details') ?? 'View Details' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Results Modal --}}
<div class="modal fade" id="{{ $containerId }}_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('common.import_results') ?? 'Import Results' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="{{ $containerId }}_modal_content"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('common.close') ?? 'Close' }}</button>
                <button type="button" class="btn btn-success" id="{{ $containerId }}_download_results">
                    <i class="uil uil-download-alt"></i> {{ __('common.download_errors') ?? 'Download Errors' }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        // Ensure BatchProgressInline is initialized only once
        if (typeof window.BatchProgressInline === 'undefined') {
            window.BatchProgressInline = {
                activeIntervals: {}, // Track all active intervals by containerId
                
                /**
                 * Initialize and start batch progress tracking (inline)
                 * Progress persists across page navigation using localStorage
                 */
                start: function(containerId, batchId, progressCheckUrl, options = {}) {
                    const defaults = {
                        checkInterval: {{ $checkInterval }},
                        storageKey: '{{ $storageKey }}',
                        texts: @json($texts),
                        onComplete: null,
                        onUpdate: null,
                    };
                    
                    const config = { ...defaults, ...options };
                    const url = progressCheckUrl.replace(':batchId', batchId);
                    
                    // Stop any existing tracking for this container first
                    this.stop(containerId, config.storageKey);
                    
                    // Store batch info in localStorage for persistence
                    const batchInfo = {
                        containerId: containerId,
                        batchId: batchId,
                        progressCheckUrl: progressCheckUrl,
                        startTime: Date.now(),
                        config: config
                    };
                    localStorage.setItem(config.storageKey, JSON.stringify(batchInfo));
                    
                    // Show container
                    $(`#${containerId}`).slideDown();
                    
                    // Reset progress
                    this._updateUI(containerId, {
                        progress: 0,
                        title: config.texts.inProgress,
                        subtext: config.texts.checking,
                        details: '',
                        finished: false,
                        failed: false
                    }, config);
                    
                    // Start checking progress
                    this._startChecking(containerId, batchId, url, config);
                },
                
                /**
                 * Resume tracking from localStorage (call on page load)
                 */
                resume: function(containerId, progressCheckUrl, options = {}) {
                    const defaults = {
                        checkInterval: {{ $checkInterval }},
                        storageKey: '{{ $storageKey }}',
                        texts: @json($texts),
                    };
                    
                    const config = { ...defaults, ...options };
                    const stored = localStorage.getItem(config.storageKey);
                    
                    if (!stored) return false;
                    
                    try {
                        const batchInfo = JSON.parse(stored);
                        
                        // Check if batch is still relevant (not older than 24 hours)
                        const age = Date.now() - batchInfo.startTime;
                        if (age > 24 * 60 * 60 * 1000) {
                            localStorage.removeItem(config.storageKey);
                            return false;
                        }
                        
                        // Stop any existing tracking first
                        this.stop(containerId, config.storageKey);
                        
                        // First, verify the batch still exists on the server
                        const self = this;
                        const url = progressCheckUrl.replace(':batchId', batchInfo.batchId);
                        
                        $.ajax({
                            url: url,
                            type: 'GET',
                            async: false, // Make it synchronous to check before resuming
                            success: function(response) {
                                // If batch not found, clear storage and don't resume
                                if (response.status === 'not_found' || response.error === 'Batch not found') {
                                    console.log('Batch no longer exists, clearing storage');
                                    localStorage.removeItem(config.storageKey);
                                    return false;
                                }
                                
                                // If batch is already finished, clear storage and don't resume
                                if (response.finished) {
                                    console.log('Batch already finished, clearing storage');
                                    localStorage.removeItem(config.storageKey);
                                    return false;
                                }
                                
                                // Batch exists and is still running, resume tracking
                                $(`#${containerId}`).slideDown();
                                self._startChecking(containerId, batchInfo.batchId, url, { ...config, ...batchInfo.config });
                            },
                            error: function(xhr) {
                                // If 404 or any error, clear storage and don't resume
                                console.log('Error checking batch, clearing storage');
                                localStorage.removeItem(config.storageKey);
                                return false;
                            }
                        });
                        
                        return true;
                    } catch (e) {
                        console.error('Error resuming batch progress:', e);
                        localStorage.removeItem(config.storageKey);
                        return false;
                    }
                },
                
                /**
                 * Stop tracking and clear storage
                 */
                stop: function(containerId, storageKey = '{{ $storageKey }}') {
                    // Clear the specific interval for this container
                    if (this.activeIntervals[containerId]) {
                        clearInterval(this.activeIntervals[containerId]);
                        delete this.activeIntervals[containerId];
                    }
                    
                    // Clear localStorage
                    localStorage.removeItem(storageKey);
                    
                    // Hide container
                    $(`#${containerId}`).slideUp();
                },
                
                /**
                 * Internal: Start checking progress
                 */
                _startChecking: function(containerId, batchId, url, config) {
                    const self = this;
                    
                    // Clear any existing interval for this container
                    if (this.activeIntervals[containerId]) {
                        clearInterval(this.activeIntervals[containerId]);
                        delete this.activeIntervals[containerId];
                    }
                    
                    // Start new interval and store it
                    this.activeIntervals[containerId] = setInterval(function() {
                        $.ajax({
                            url: url,
                            type: 'GET',
                            success: function(response) {
                                // Check if batch was not found or deleted
                                if (response.status === 'not_found' || response.error === 'Batch not found') {
                                    console.log('Batch not found, stopping progress tracking');
                                    self.stop(containerId, config.storageKey);
                                    return;
                                }
                                
                                if (response.success || response.progress_percentage !== undefined || response.progress !== undefined) {
                                    const progress = Math.round(response.progress_percentage || response.progress || 0);
                                    const totalJobs = response.total_jobs || 0;
                                    const pendingJobs = response.pending_jobs || 0;
                                    const processedJobs = response.processed_jobs || 0;
                                    
                                    // Calculate elapsed time
                                    const stored = localStorage.getItem(config.storageKey);
                                    let elapsedTime = '';
                                    if (stored) {
                                        const batchInfo = JSON.parse(stored);
                                        const elapsed = Math.floor((Date.now() - batchInfo.startTime) / 1000);
                                        const minutes = Math.floor(elapsed / 60);
                                        const seconds = elapsed % 60;
                                        elapsedTime = minutes > 0 ? `${minutes}m ${seconds}s` : `${seconds}s`;
                                    }
                                    
                                    // Update UI
                                    self._updateUI(containerId, {
                                        progress: progress,
                                        title: config.texts.inProgress,
                                        subtext: `${config.texts.processing} - ${processedJobs}/${totalJobs} ${config.texts.jobsRemaining}`,
                                        details: pendingJobs > 0 ? `${pendingJobs} ${config.texts.jobsRemaining}` : '',
                                        time: elapsedTime,
                                        finished: response.finished,
                                        failed: response.failed
                                    }, config);
                                    
                                    // Call update callback
                                    if (typeof config.onUpdate === 'function') {
                                        config.onUpdate(response);
                                    }
                                    
                                    // Handle completion
                                    if (response.finished) {
                                        // Clear interval
                                        if (self.activeIntervals[containerId]) {
                                            clearInterval(self.activeIntervals[containerId]);
                                            delete self.activeIntervals[containerId];
                                        }
                                        
                                        if (response.failed) {
                                            self._updateUI(containerId, {
                                                progress: 100,
                                                title: config.texts.failed,
                                                subtext: config.texts.failedMessage,
                                                details: '',
                                                time: elapsedTime,
                                                finished: true,
                                                failed: true
                                            }, config);
                                        } else {
                                            self._updateUI(containerId, {
                                                progress: 100,
                                                title: config.texts.completed,
                                                subtext: config.texts.completedMessage,
                                                details: '',
                                                time: elapsedTime,
                                                finished: true,
                                                failed: false
                                            }, config);
                                        }
                                        
                                        // Show dismiss button
                                        $(`#${containerId}_dismiss`).show();
                                        
                                        // Display results if available
                                        if (response.results) {
                                            self._displayResults(containerId, response.results, config);
                                        }
                                        
                                        // Clear storage
                                        localStorage.removeItem(config.storageKey);
                                        
                                        // Call completion callback
                                        if (typeof config.onComplete === 'function') {
                                            config.onComplete(response);
                                        }
                                    }
                                }
                            },
                            error: function(xhr) {
                                console.error('Error checking batch progress:', xhr);
                                
                                // If batch not found (404), stop tracking
                                if (xhr.status === 404) {
                                    console.log('Batch not found (404), stopping progress tracking');
                                    self.stop(containerId, config.storageKey);
                                }
                                // Don't stop on other errors, keep trying
                            }
                        });
                    }, config.checkInterval);
                },
                
                /**
                 * Display import results
                 */
                _displayResults: function(containerId, results, config) {
                    const successCount = results.imported_count || 0;
                    const errorCount = results.errors ? results.errors.length : 0;
                    const totalRows = successCount + errorCount;
                    
                    // Show results summary
                    $(`#${containerId}_results`).show();
                    $(`#${containerId}_results_summary`).html(
                        `<i class="uil uil-check-circle text-success"></i> ${successCount} succeeded, ` +
                        `<i class="uil uil-times-circle text-danger"></i> ${errorCount} failed out of ${totalRows} total rows`
                    );
                    
                    // Store results for modal
                    window[`${containerId}_resultsData`] = results;
                },
                
                /**
                 * Internal: Update UI elements
                 */
                _updateUI: function(containerId, data, config) {
                    $(`#${containerId}_bar`).css('width', data.progress + '%').attr('aria-valuenow', data.progress);
                    $(`#${containerId}_text`).text(data.progress + '%');
                    $(`#${containerId}_title`).text(data.title);
                    $(`#${containerId}_subtext`).text(data.subtext);
                    $(`#${containerId}_details`).text(data.details || '');
                    $(`#${containerId}_time`).text(data.time || '');
                    
                    if (data.finished) {
                        $(`#${containerId}_spinner`).hide();
                        $(`#${containerId}_bar`).removeClass('progress-bar-animated');
                        
                        if (data.failed) {
                            $(`#${containerId}_bar`).removeClass('bg-primary').addClass('bg-danger');
                        } else {
                            $(`#${containerId}_bar`).removeClass('bg-primary').addClass('bg-success');
                        }
                    }
                }
            };
            
            // Setup dismiss button handler
            $(document).on('click', '[id$="_dismiss"]', function() {
                const containerId = $(this).attr('id').replace('_dismiss', '');
                window.BatchProgressInline.stop(containerId);
            });
            
            // Setup view details button handler
            $(document).on('click', '[id$="_view_details"]', function() {
                const containerId = $(this).attr('id').replace('_view_details', '');
                const results = window[`${containerId}_resultsData`];
                
                if (!results) return;
                
                // Build results table
                let html = '';
                
                // Success summary
                html += `<div class="alert alert-success">
                    <h6 class="mb-2"><i class="uil uil-check-circle"></i> Successfully Imported: ${results.imported_count || 0} rows</h6>
                </div>`;
                
                // Errors table
                if (results.errors && results.errors.length > 0) {
                    html += `<div class="alert alert-danger">
                        <h6 class="mb-2"><i class="uil uil-times-circle"></i> Failed: ${results.errors.length} rows</h6>
                    </div>`;
                    
                    html += `<div class="table-responsive" style="max-height: 500px;">
                        <table class="table table-hover table-bordered table-sm">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th style="width: 120px;">Sheet</th>
                                    <th style="width: 80px;">Row</th>
                                    <th style="width: 180px;">SKU/ID</th>
                                    <th>Error</th>
                                </tr>
                            </thead>
                            <tbody>`;
                    
                    results.errors.forEach(function(error) {
                        const sheetBadgeClass = {
                            'products': 'bg-primary',
                            'variants': 'bg-info',
                            'variant_stock': 'bg-warning text-dark',
                            'images': 'bg-success',
                            'occasions': 'bg-purple',
                            'occasion_products': 'bg-danger'
                        }[error.sheet] || 'bg-secondary';
                        
                        const identifier = error.sku || error.id || error.variant_id || error.product_id || error.occasion_id || '-';
                        const errors = Array.isArray(error.errors) ? error.errors.join('<br>') : error.errors;
                        
                        html += `<tr>
                            <td><span class="badge ${sheetBadgeClass}">${error.sheet}</span></td>
                            <td class="text-center"><span class="badge bg-light text-dark border">${error.row}</span></td>
                            <td><code class="text-danger">${identifier}</code></td>
                            <td>${errors}</td>
                        </tr>`;
                    });
                    
                    html += `</tbody></table></div>`;
                }
                
                // Display in modal
                $(`#${containerId}_modal_content`).html(html);
                $(`#${containerId}_modal`).modal('show');
            });
            
            // Setup download results button handler
            $(document).on('click', '[id$="_download_results"]', function() {
                const containerId = $(this).attr('id').replace('_download_results', '');
                const results = window[`${containerId}_resultsData`];
                
                if (!results || !results.errors || results.errors.length === 0) {
                    alert('No errors to download');
                    return;
                }
                
                // Create CSV content
                let csv = 'Sheet,Row,SKU/ID,Error\n';
                results.errors.forEach(function(error) {
                    const identifier = error.sku || error.id || error.variant_id || error.product_id || error.occasion_id || '-';
                    const errors = Array.isArray(error.errors) ? error.errors.join('; ') : error.errors;
                    csv += `"${error.sheet}","${error.row}","${identifier}","${errors.replace(/"/g, '""')}"\n`;
                });
                
                // Download CSV
                const blob = new Blob([csv], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `import_errors_${new Date().getTime()}.csv`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            });
        }
    </script>
    @endpush
@endonce
