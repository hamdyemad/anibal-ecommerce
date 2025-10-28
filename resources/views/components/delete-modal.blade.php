@props([
    'modalId' => 'modal-delete',
    'title' => '',
    'message' => '',
    'itemNameId' => 'delete-item-name',
    'confirmBtnId' => 'confirmDeleteBtn',
    'deleteRoute' => '',
    'cancelText' => '',
    'deleteText' => ''
])

<!-- Delete Confirmation Modal -->
<div class="modal-info-delete modal fade" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-info" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-info-body d-flex">
                    <div class="modal-info-icon warning">
                        <img src="{{ asset('assets/img/svg/alert-circle.svg') }}" alt="alert-circle" class="svg">
                    </div>
                    <div class="modal-info-text">
                        <h6>{{ $title }}</h6>
                        <p id="{{ $itemNameId }}" class="fw-500"></p>
                        <p class="text-muted fs-13">{{ $message }}</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light btn-outlined btn-sm" data-bs-dismiss="modal">
                    <i class="uil uil-times"></i> {{ $cancelText }}
                </button>
                <button type="button" class="btn btn-danger btn-sm" id="{{ $confirmBtnId }}">
                    <i class="uil uil-trash-alt"></i> {{ $deleteText }}
                </button>
            </div>
        </div>
    </div>
</div>

@once
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modalId = '{{ $modalId }}';
            const deleteModal = document.getElementById(modalId);
            const confirmDeleteBtn = document.getElementById('{{ $confirmBtnId }}');
            const itemNameElement = document.getElementById('{{ $itemNameId }}');
            let currentItemId = null;
            let deleteRouteBase = '{{ $deleteRoute }}';

            if (deleteModal && confirmDeleteBtn) {
                // When modal is triggered, store item data
                deleteModal.addEventListener('show.bs.modal', function(event) {
                    const button = event.relatedTarget;
                    currentItemId = button.getAttribute('data-item-id') || button.getAttribute('data-role-id');
                    const itemName = button.getAttribute('data-item-name') || button.getAttribute('data-role-name');
                    
                    // Update modal content
                    if (itemNameElement && itemName) {
                        itemNameElement.textContent = itemName;
                    }
                });

                // Handle delete confirmation
                confirmDeleteBtn.addEventListener('click', function() {
                    if (currentItemId && deleteRouteBase) {
                        // Close the modal first
                        const modalInstance = bootstrap.Modal.getInstance(deleteModal);
                        if (modalInstance) {
                            modalInstance.hide();
                        }
                        
                        // Show loading overlay with progress animation
                        if (window.LoadingOverlay) {
                            window.LoadingOverlay.show();
                            // Animate progress bar
                            window.LoadingOverlay.progressSequence([30, 60, 90], [200, 300, 400]);
                        }
                        
                        // Make AJAX request
                        fetch(`${deleteRouteBase}/${currentItemId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Complete progress bar
                                if (window.LoadingOverlay) {
                                    window.LoadingOverlay.animateProgressBar(100, 200).then(() => {
                                        // Show success state
                                        window.LoadingOverlay.showSuccess(
                                            data.message || 'Deleted successfully!',
                                            'Redirecting...'
                                        );
                                        
                                        // Redirect after showing success
                                        setTimeout(() => {
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        }, 1000);
                                    });
                                } else {
                                    // Fallback if no LoadingOverlay
                                    if (data.redirect) {
                                        window.location.href = data.redirect;
                                    } else {
                                        window.location.reload();
                                    }
                                }
                            } else {
                                // Hide loading overlay on error
                                if (window.LoadingOverlay) {
                                    window.LoadingOverlay.hide();
                                }
                                
                                // Show error message
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: data.message || 'An error occurred while deleting.'
                                    });
                                } else {
                                    alert(data.message || 'An error occurred while deleting.');
                                }
                            }
                        })
                        .catch(error => {
                            // Hide loading overlay
                            if (window.LoadingOverlay) {
                                window.LoadingOverlay.hide();
                            }
                            
                            console.error('Delete error:', error);
                            
                            // Show error message
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'An error occurred while deleting.'
                                });
                            } else {
                                alert('An error occurred while deleting.');
                            }
                        });
                    }
                });
            }
        });
    </script>
    @endpush
@endonce
