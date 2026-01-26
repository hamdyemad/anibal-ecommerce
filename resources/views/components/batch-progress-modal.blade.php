@props([
    'modalId' => 'batchProgressModal',
    'progressCheckUrl' => null,
    'onComplete' => 'window.location.reload()',
    'checkInterval' => 2000,
    'texts' => [
        'inProgress' => __('common.import_in_progress') ?? 'Import in Progress',
        'checking' => __('common.checking_progress') ?? 'Checking progress...',
        'completed' => __('common.import_completed') ?? 'Import Completed',
        'completedMessage' => __('common.import_completed_message') ?? 'Your import has been completed successfully',
        'failed' => __('common.import_failed') ?? 'Import Failed',
        'failedMessage' => __('common.import_failed_message') ?? 'Some errors occurred during import',
        'error' => __('common.error_checking_progress') ?? 'Error checking progress',
    ]
])

{{-- Batch Progress Modal --}}
<div class="modal fade" id="{{ $modalId }}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="mb-4">
                    <div class="spinner-border text-primary" style="width: 4rem; height: 4rem;" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <h5 class="mb-3" id="{{ $modalId }}_title">{{ $texts['inProgress'] }}</h5>
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" 
                         role="progressbar" 
                         id="{{ $modalId }}_bar"
                         style="width: 0%;" 
                         aria-valuenow="0" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                        <span class="fw-bold fs-16" id="{{ $modalId }}_text">0%</span>
                    </div>
                </div>
                <p class="text-muted mb-0" id="{{ $modalId }}_subtext">{{ $texts['checking'] }}</p>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        window.BatchProgressModal = window.BatchProgressModal || {};
        
        /**
         * Initialize and start batch progress tracking
         * @param {string} modalId - The modal ID
         * @param {string} batchId - The batch ID to track
         * @param {string} progressCheckUrl - URL template with :batchId placeholder
         * @param {object} options - Additional options
         */
        window.BatchProgressModal.start = function(modalId, batchId, progressCheckUrl, options = {}) {
            const defaults = {
                checkInterval: {{ $checkInterval }},
                onComplete: function() { {{ $onComplete }} },
                texts: @json($texts)
            };
            
            const config = { ...defaults, ...options };
            const url = progressCheckUrl.replace(':batchId', batchId);
            
            // Show modal
            $(`#${modalId}`).modal('show');
            
            // Reset progress
            $(`#${modalId}_bar`).css('width', '0%').attr('aria-valuenow', 0);
            $(`#${modalId}_text`).text('0%');
            $(`#${modalId}_title`).text(config.texts.inProgress);
            $(`#${modalId}_subtext`).text(config.texts.checking);
            $(`#${modalId}_bar`).removeClass('bg-danger').addClass('bg-primary progress-bar-animated');
            
            // Start checking progress
            const interval = setInterval(function() {
                $.ajax({
                    url: url,
                    type: 'GET',
                    success: function(response) {
                        if (response.success || response.progress !== undefined) {
                            const progress = Math.round(response.progress || 0);
                            $(`#${modalId}_bar`).css('width', progress + '%').attr('aria-valuenow', progress);
                            $(`#${modalId}_text`).text(progress + '%');
                            
                            if (response.finished) {
                                clearInterval(interval);
                                
                                if (response.failed) {
                                    $(`#${modalId}_title`).text(config.texts.failed);
                                    $(`#${modalId}_subtext`).text(config.texts.failedMessage);
                                    $(`#${modalId}_bar`).removeClass('bg-primary').addClass('bg-danger');
                                } else {
                                    $(`#${modalId}_title`).text(config.texts.completed);
                                    $(`#${modalId}_subtext`).text(config.texts.completedMessage);
                                    $(`#${modalId}_bar`).removeClass('progress-bar-animated');
                                }
                                
                                // Execute completion callback after 2 seconds
                                setTimeout(function() {
                                    if (typeof config.onComplete === 'function') {
                                        config.onComplete(response);
                                    }
                                }, 2000);
                            }
                        }
                    },
                    error: function(xhr) {
                        clearInterval(interval);
                        alert(config.texts.error);
                        if (typeof config.onComplete === 'function') {
                            config.onComplete({ error: true });
                        }
                    }
                });
            }, config.checkInterval);
            
            // Store interval ID for potential cleanup
            $(`#${modalId}`).data('progressInterval', interval);
        };
        
        /**
         * Stop tracking progress
         * @param {string} modalId - The modal ID
         */
        window.BatchProgressModal.stop = function(modalId) {
            const interval = $(`#${modalId}`).data('progressInterval');
            if (interval) {
                clearInterval(interval);
            }
            $(`#${modalId}`).modal('hide');
        };
    </script>
    @endpush
@endonce
