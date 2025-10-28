@extends('layout.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="breadcrumb-main">
                    <div class="breadcrumb-action justify-content-center flex-wrap">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}"><i
                                            class="uil uil-estate"></i>{{ trans('dashboard.title') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ trans('menu.admin managment.roles managment') }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ trans('roles.roles_management') }}</h4>
                        <div class="d-flex gap-2">
                            <button class="btn btn-light btn-default btn-squared" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                <i class="uil uil-filter"></i> {{ __('common.filter') }}
                            </button>
                            <a href="{{ route('admin.admin-management.roles.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ trans('roles.create_role') }}
                            </a>
                        </div>
                    </div>

                    {{-- Search and Filter Form --}}
                    <div class="collapse {{ ($search || $dateFrom || $dateTo) ? 'show' : '' }}" id="filterCollapse">
                        <form method="GET" action="{{ route('admin.admin-management.roles.index') }}" class="mb-25">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="search" class="il-gray fs-14 fw-500 mb-10">{{ __('common.search') }}</label>
                                                <input type="text" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="search" 
                                                       name="search" 
                                                       value="{{ $search ?? '' }}"
                                                       placeholder="{{ __('roles.search_by_name') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_from" class="il-gray fs-14 fw-500 mb-10">{{ __('common.date_from') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="date_from" 
                                                       name="date_from" 
                                                       value="{{ $dateFrom ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="date_to" class="il-gray fs-14 fw-500 mb-10">{{ __('common.date_to') }}</label>
                                                <input type="date" 
                                                       class="form-control ih-medium ip-gray radius-xs b-light px-15" 
                                                       id="date_to" 
                                                       name="date_to" 
                                                       value="{{ $dateTo ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label class="il-gray fs-14 fw-500 mb-10">&nbsp;</label>
                                                <div class="d-flex gap-2">
                                                    <button type="submit" class="btn btn-primary btn-default btn-squared w-100">
                                                        <i class="uil uil-search"></i> {{ __('common.search') }}
                                                    </button>
                                                    @if($search || $dateFrom || $dateTo)
                                                        <a href="{{ route('admin.admin-management.roles.index') }}" class="btn btn-light btn-default btn-squared">
                                                            <i class="uil uil-redo"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="table-responsive">
                        <table class="table mb-0 table-bordered">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th>
                                        <span class="userDatatable-title">#</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('roles.name') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('roles.permissions') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('roles.created_at') }}</span>
                                    </th>
                                    <th>
                                        <span class="userDatatable-title">{{ trans('common.actions') }}</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($roles as $index => $role)
                                    <tr>
                                        <td>
                                            <div class="userDatatable-content">
                                                {{ $index + 1 }}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="userDatatable-content">
                                                <strong>{{ $role->getTranslation('name', app()->getLocale()) ?? $role->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="userDatatable-content" data-permissions-count="{{ $role->permessions->count() }}">
                                                @if($role->permessions->count() > 0)
                                                    <span class="badge badge-primary badge-lg" style="border-radius: 6px; padding: 6px 12px;" data-original-count="{{ $role->permessions->count() }}">
                                                        <i class="uil uil-shield-check me-1"></i>
                                                        <span class="permissions-count-text me-1">{{ $role->permessions->count() }}</span> {{ trans('roles.permissions') }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-light-warning" style="border-radius: 6px; padding: 6px 12px;">
                                                        <i class="uil uil-exclamation-triangle me-1"></i>
                                                        {{ __('roles.no_permissions') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="userDatatable-content">
                                                {{ $role->created_at->format('Y-m-d H:i') }}
                                            </div>
                                        </td>
                                        <td>
                                            <ul class="orderDatatable_actions mb-0 d-flex flex-wrap justify-content-start">
                                                <li>
                                                    <a href="{{ route('admin.admin-management.roles.show', $role->id) }}" class="view" title="{{ trans('common.view') }}">
                                                        <i class="uil uil-eye"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="{{ route('admin.admin-management.roles.edit', $role->id) }}" class="edit" title="{{ trans('common.edit') }}">
                                                        <i class="uil uil-edit"></i>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:void(0);" 
                                                       class="remove" 
                                                       title="{{ trans('common.delete') }}"
                                                       data-bs-toggle="modal" 
                                                       data-bs-target="#modal-delete-role"
                                                       data-role-id="{{ $role->id }}"
                                                       data-role-name="{{ $role->getTranslation('name', app()->getLocale()) ?? $role->name }}">
                                                        <i class="uil uil-trash-alt"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="uil uil-user-check" style="font-size: 48px; color: #ccc;"></i>
                                                <p class="mt-3 text-muted">{{ trans('roles.no_roles_found') }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Delete Confirmation Modal Component --}}
    <x-delete-modal 
        modalId="modal-delete-role"
        :title="trans('roles.confirm_delete')"
        :message="trans('roles.delete_warning')"
        itemNameId="delete-role-name"
        confirmBtnId="confirmDeleteBtn"
        :deleteRoute="route('admin.admin-management.roles.index')"
        :cancelText="trans('roles.cancel')"
        :deleteText="trans('roles.delete_role')"
    />
@endsection

@push('scripts')
<script>
    // Protect permissions count badges from being cleared
    document.addEventListener('DOMContentLoaded', function() {
        // Store original badge HTML
        const badgeContainers = document.querySelectorAll('[data-permissions-count]');
        const originalHTML = new Map();
        
        badgeContainers.forEach(container => {
            originalHTML.set(container, container.innerHTML);
        });
        
        // Prevent badge clearing on any click
        document.addEventListener('click', function(e) {
            // Immediately restore badges if they get cleared
            setTimeout(function() {
                badgeContainers.forEach(container => {
                    const currentHTML = container.innerHTML;
                    const original = originalHTML.get(container);
                    
                    // If badge content was cleared or changed to 0, restore it
                    if (currentHTML !== original && (currentHTML.includes('>0<') || currentHTML.trim() === '')) {
                        console.log('Restoring badge content');
                        container.innerHTML = original;
                    }
                });
            }, 0);
        }, true);
        
        // Also use MutationObserver for real-time protection
        const observer = new MutationObserver(function(mutations) {
            badgeContainers.forEach(container => {
                const currentHTML = container.innerHTML;
                const original = originalHTML.get(container);
                
                if (currentHTML !== original && (currentHTML.includes('>0<') || currentHTML.trim() === '')) {
                    container.innerHTML = original;
                }
            });
        });
        
        badgeContainers.forEach(container => {
            observer.observe(container, {
                childList: true,
                subtree: true,
                characterData: true
            });
        });
    });
</script>
@endpush

{{-- Include Loading Overlay Component outside content section --}}
@push('after-body')
    <x-loading-overlay 
        :loadingText="trans('loading.processing')" 
        :loadingSubtext="trans('loading.please_wait')" 
    />
@endpush
