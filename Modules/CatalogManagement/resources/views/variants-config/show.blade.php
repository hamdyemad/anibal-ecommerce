@extends('layout.app')
@section('title')
    {{ trans('catalogmanagement::variantsconfig.view_variants_config') }} | Bnaia
@endsection

{{-- Include protected-badge component to load its styles and scripts --}}
<x-protected-badge color="#6366f1" text="" size="lg" id="protected-badge-init" style="display:none;" />

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => trans('catalogmanagement::variantsconfig.variants_configurations'), 'url' => route('admin.variants-configurations.index')],
                    ['title' => trans('catalogmanagement::variantsconfig.view_variants_config')]
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-bottom py-20 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-500">{{ trans('catalogmanagement::variantsconfig.variants_config_details') }}</h5>
                        <div class="d-flex gap-10">
                            <a href="{{ route('admin.variants-configurations.index') }}" class="btn btn-light btn-sm">
                                <i class="uil uil-arrow-left me-2"></i>{{ trans('common.back_to_list') }}
                            </a>
                            @can('variants-configurations.edit')
                            <a href="{{ route('admin.variants-configurations.edit', $variantsConfig->id) }}" class="btn btn-primary btn-sm">
                                <i class="uil uil-edit me-2"></i>{{ trans('common.edit') }}
                            </a>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card card-holder">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-info-circle me-1"></i>{{ trans('common.basic_information') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            {{-- Name Translations using component --}}
                                            <x-translation-display 
                                                :label="trans('catalogmanagement::variantsconfig.name')" 
                                                :model="$variantsConfig" 
                                                fieldName="name" 
                                                :languages="$languages" 
                                            />

                                            {{-- Type --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.type') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->type)
                                                            <span class="badge badge-{{ $variantsConfig->type == 'color' ? 'info' : 'secondary' }} badge-round badge-lg">
                                                                <i class="uil uil-{{ $variantsConfig->type == 'color' ? 'palette' : 'text' }}"></i>
                                                                {{ trans('catalogmanagement::variantsconfig.' . $variantsConfig->type) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Value --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.value') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->value)
                                                            @if($variantsConfig->type == 'color')
                                                                <span class="d-inline-flex align-items-center gap-2">
                                                                    <span class="color-preview" style="background-color: {{ $variantsConfig->value }}; width: 30px; height: 30px; border-radius: 6px; border: 2px solid #dee2e6; display: inline-block;"></span>
                                                                    <strong>{{ $variantsConfig->value }}</strong>
                                                                </span>
                                                            @else
                                                                <strong>{{ $variantsConfig->value }}</strong>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Key --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.key') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->key)
                                                            <span class="badge badge-primary badge-round badge-lg">
                                                                <i class="uil uil-key-skeleton-alt"></i>
                                                                {{ $variantsConfig->key->getTranslation('name', app()->getLocale()) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted">-</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Parent --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.parent') ?? 'Parent' }}</label>
                                                    <p class="fs-15 color-dark">
                                                        @if($variantsConfig->parent_data)
                                                            <a href="{{ route('admin.variants-configurations.show', $variantsConfig->parent_data->id) }}" class="badge badge-primary badge-round badge-lg">
                                                                {{ $variantsConfig->parent_data->getTranslation('name', app()->getLocale()) }}
                                                            </a>
                                                        @else
                                                            <span class="text-muted">{{ trans('catalogmanagement::variantsconfig.no_parent') ?? 'No Parent' }}</span>
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>

                                            {{-- Children Count --}}
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('catalogmanagement::variantsconfig.children_count') }}</label>
                                                    <p class="fs-15 color-dark">
                                                        <span class="badge badge-info badge-round badge-lg">{{ $variantsConfig->children->count() }}</span>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- All Children Section (Direct + Linked) --}}
                                @php
                                    $directChildren = $variantsConfig->children ?? collect();
                                    $linkedChildren = $variantsConfig->linkedChildren ?? collect();
                                    $allChildren = $directChildren->merge($linkedChildren)->unique('id');
                                @endphp
                                
                                @if($allChildren->count() > 0)
                                    <div class="card card-holder mt-3">
                                        <div class="card-header">
                                            <h3>
                                                <i class="uil uil-sitemap me-1"></i>{{ trans('catalogmanagement::variantsconfig.children') ?? 'Children' }}
                                            </h3>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="view-item">
                                                        <p class="fs-15 color-dark">
                                                            @foreach ($allChildren as $child)
                                                                <a href="{{ route('admin.variants-configurations.show', $child->id) }}" class="badge badge-primary badge-round badge-lg me-2 mb-2">
                                                                    {{ $child->getTranslation('name', app()->getLocale()) }}
                                                                    @if($child->value)
                                                                        ({{ $child->value }})
                                                                    @endif
                                                                    @if($linkedChildren->contains('id', $child->id))
                                                                        <i class="uil uil-link ms-1" title="{{ trans('catalogmanagement::variantsconfig.linked_child') }}"></i>
                                                                    @endif
                                                                </a>
                                                            @endforeach
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Linked Children Section --}}
                                <div class="card card-holder mt-3">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h3>
                                            <i class="uil uil-link me-1"></i>{{ trans('catalogmanagement::variantsconfig.linked_children') }}
                                        </h3>
                                        @can('variants-configurations.edit')
                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#manageLinkModal">
                                            <i class="uil uil-plus"></i> {{ trans('catalogmanagement::variantsconfig.manage_links') }}
                                        </button>
                                        @endcan
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="linkedChildrenContainer">
                                                    <div class="text-center py-3">
                                                        <div class="spinner-border text-primary" role="status">
                                                            <span class="visually-hidden">Loading...</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card card-holder mt-3">
                                    <div class="card-header">
                                        <h3>
                                            <i class="uil uil-clock me-1"></i>{{ trans('common.timestamps') }}
                                        </h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.created_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $variantsConfig->created_at }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="view-item">
                                                    <label class="il-gray fs-14 fw-500 mb-10">{{ trans('common.updated_at') }}</label>
                                                    <p class="fs-15 color-dark">{{ $variantsConfig->updated_at }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Manage Links Modal --}}
    <div class="modal fade" id="manageLinkModal" tabindex="-1" aria-labelledby="manageLinkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="manageLinkModalLabel">{{ trans('catalogmanagement::variantsconfig.manage_links') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <x-multi-select 
                            name="child_ids[]"
                            id="childrenMultiSelect"
                            :label="trans('catalogmanagement::variantsconfig.select_children_to_link')"
                            icon="uil uil-link"
                            :options="[]"
                            :selected="[]"
                            :placeholder="trans('common.loading') . '...'"
                        />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ trans('common.cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="saveLinkBtn">{{ trans('common.save') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        const variantId = {{ $variantsConfig->id }};
        const keyId = {{ $variantsConfig->key_id ?? 'null' }};
        
        // Load linked children on page load
        loadLinkedChildren();
        
        // Load available children when modal opens
        $('#manageLinkModal').on('show.bs.modal', function() {
            loadAvailableChildren();
        });
        
        // Save links
        $('#saveLinkBtn').on('click', function() {
            const selectedIds = window.MultiSelect.getValues('childrenMultiSelect');
            syncLinks(selectedIds);
        });
        
        function loadLinkedChildren() {
            $.ajax({
                url: '{{ route('admin.variants-configurations.linked-children', ['id' => ':id']) }}'.replace(':id', variantId),
                method: 'GET',
                success: function(response) {
                    console.log('Linked children response:', response);
                    if (response.success) {
                        displayLinkedChildren(response.data);
                    } else {
                        $('#linkedChildrenContainer').html(
                            '<p class="text-muted text-center">{{ trans('common.no_data_available') }}</p>'
                        );
                    }
                },
                error: function(xhr) {
                    console.error('Error loading linked children:', xhr);
                    $('#linkedChildrenContainer').html(
                        '<div class="alert alert-danger">' + 
                        (xhr.responseJSON?.message || '{{ trans('catalogmanagement::variantsconfig.error_fetching_linked_children') }}') + 
                        '</div>'
                    );
                }
            });
        }
        
        function displayLinkedChildren(children) {
            if (children.length === 0) {
                $('#linkedChildrenContainer').html(
                    '<p class="text-muted text-center">{{ trans('common.no_data_available') }}</p>'
                );
                return;
            }
            
            let html = '<div class="d-flex flex-wrap gap-2">';
            children.forEach(function(child) {
                const badgeText = child.name + (child.value ? ' (' + child.value + ')' : '');
                const uniqueId = 'protected-badge-' + child.id;
                
                html += `
                    <span class="protected-badge protected-badge-lg d-inline-flex align-items-center gap-2" 
                          style="background-color: #6366f1; color: white;"
                          data-protected="true"
                          data-original-value="${badgeText}"
                          id="${uniqueId}">
                        <span>${badgeText}</span>
                        @can('variants-configurations.edit')
                        <button type="button" class="btn-close btn-close-white btn-sm" 
                                onclick="unlinkChild(${child.id})" 
                                style="font-size: 0.7rem; padding: 0.1rem; margin-left: 4px;">
                        </button>
                        @endcan
                    </span>
                `;
            });
            html += '</div>';
            
            $('#linkedChildrenContainer').html(html);
        }
        
        function loadAvailableChildren() {
            // Load ALL available variants to allow flexible linking
            $.ajax({
                url: '{{ route('admin.variants-configurations.all-for-linking') }}',
                method: 'GET',
                success: function(response) {
                    console.log('All variants response:', response);
                    if (response.success && response.data && response.data.length > 0) {
                        populateMultiSelect(response.data);
                    } else {
                        toastr.warning('{{ trans('catalogmanagement::variantsconfig.no_variants_for_key') }}');
                    }
                },
                error: function(xhr) {
                    console.error('Error loading variants:', xhr);
                    toastr.error('{{ trans('common.error_loading_data') }}');
                }
            });
        }
        
        function populateMultiSelect(variants) {
            // Get currently linked children IDs
            $.ajax({
                url: '{{ route('admin.variants-configurations.linked-children', ['id' => ':id']) }}'.replace(':id', variantId),
                method: 'GET',
                async: false,
                success: function(response) {
                    console.log('Current linked children:', response);
                    if (response.success) {
                        const linkedIds = response.data.map(c => c.id.toString());
                        
                        // Prepare options for multi-select
                        const options = variants
                            .filter(v => v.id !== variantId) // Don't show self
                            .map(v => ({
                                id: v.id.toString(),
                                name: v.name + (v.value ? ' (' + v.value + ')' : '') + (v.key_name ? ' [' + v.key_name + ']' : '')
                            }));
                        
                        // Update the multi-select component
                        const container = document.getElementById('childrenMultiSelect');
                        if (!container) {
                            console.error('Multi-select container not found');
                            return;
                        }
                        
                        const dropdown = container.querySelector('.multi-select-dropdown');
                        const noResults = container.querySelector('.multi-select-no-results');
                        
                        // Clear existing options
                        dropdown.querySelectorAll('.multi-select-option').forEach(el => el.remove());
                        
                        // Add new options
                        options.forEach(function(option) {
                            const isSelected = linkedIds.includes(option.id);
                            const optDiv = document.createElement('div');
                            optDiv.className = 'multi-select-option' + (isSelected ? ' selected' : '');
                            optDiv.dataset.value = option.id;
                            optDiv.dataset.text = option.name;
                            optDiv.innerHTML = `
                                <span class="multi-select-checkbox">
                                    <i class="uil ${isSelected ? 'uil-check' : ''}"></i>
                                </span>
                                ${option.name}
                            `;
                            
                            // Add click handler
                            optDiv.addEventListener('click', function(e) {
                                e.stopPropagation();
                                const value = this.dataset.value;
                                const text = this.dataset.text;
                                const valuesContainer = container.querySelector('.multi-select-values');
                                const name = container.dataset.name;
                                
                                if (this.classList.contains('selected')) {
                                    // Deselect
                                    this.classList.remove('selected');
                                    this.querySelector('.multi-select-checkbox i').className = 'uil';
                                    
                                    const input = valuesContainer.querySelector('input[value="' + value + '"]');
                                    if (input) input.remove();
                                } else {
                                    // Select
                                    this.classList.add('selected');
                                    this.querySelector('.multi-select-checkbox i').className = 'uil uil-check';
                                    
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = name;
                                    input.value = value;
                                    valuesContainer.appendChild(input);
                                }
                                
                                window.MultiSelect.updateTagsDisplay('childrenMultiSelect');
                            });
                            
                            dropdown.insertBefore(optDiv, noResults);
                        });
                        
                        // Set initial selected values
                        window.MultiSelect.setValues('childrenMultiSelect', linkedIds);
                    }
                }
            });
        }
        
        function syncLinks(childIds) {
            $.ajax({
                url: '{{ route('admin.variants-configurations.sync-linked-children') }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    parent_id: variantId,
                    child_ids: childIds
                },
                success: function(response) {
                    console.log('Sync response:', response);
                    if (response.success) {
                        toastr.success(response.message);
                        $('#manageLinkModal').modal('hide');
                        loadLinkedChildren();
                    }
                },
                error: function(xhr) {
                    console.error('Sync error:', xhr);
                    toastr.error(xhr.responseJSON?.message || '{{ trans('catalogmanagement::variantsconfig.error_syncing_links') }}');
                }
            });
        }
        
        // Make unlinkChild available globally
        window.unlinkChild = function(childId) {
            Swal.fire({
                title: '{{ trans('common.are_you_sure') }}',
                text: '{{ trans('catalogmanagement::variantsconfig.unlink_confirmation') }}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '{{ trans('common.yes_unlink') }}',
                cancelButtonText: '{{ trans('common.cancel') }}'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '{{ route('admin.variants-configurations.unlink-child') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            parent_id: variantId,
                            child_id: childId
                        },
                        success: function(response) {
                            console.log('Unlink response:', response);
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: '{{ trans('common.success') }}',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                loadLinkedChildren();
                            }
                        },
                        error: function(xhr) {
                            console.error('Unlink error:', xhr);
                            Swal.fire({
                                icon: 'error',
                                title: '{{ trans('common.error') }}',
                                text: xhr.responseJSON?.message || '{{ trans('catalogmanagement::variantsconfig.error_removing_link') }}'
                            });
                        }
                    });
                }
            });
        };
    });
</script>
@endpush
