@extends('layout.app')
@section('title')
    {{ __('catalogmanagement::tax.taxes_management') }} | Bnaia
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <x-breadcrumb :items="[
                    ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                    ['title' => __('catalogmanagement::tax.taxes_management')],
                ]" />
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="userDatatable global-shadow border-light-0 p-30 bg-white radius-xl w-100 mb-30">
                    <div class="d-flex justify-content-between align-items-center mb-25">
                        <h4 class="mb-0 fw-500">{{ __('catalogmanagement::tax.taxes_management') }}</h4>
                        @can('taxes.create')
                            <a href="{{ route('admin.taxes.create') }}" class="btn btn-primary btn-default btn-squared text-capitalize">
                                <i class="uil uil-plus"></i> {{ __('catalogmanagement::tax.add_tax') }}
                            </a>
                        @endcan
                    </div>

                    <div class="table-responsive">
                        <table id="taxesDataTable" class="table mb-0 table-bordered table-hover" style="width:100%">
                            <thead>
                                <tr class="userDatatable-header">
                                    <th class="text-center"><span class="userDatatable-title">#</span></th>
                                    @foreach($languages as $language)
                                        <th><span class="userDatatable-title">{{ __('catalogmanagement::tax.name') }} ({{ strtoupper($language->code) }})</span></th>
                                    @endforeach
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::tax.percentage') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('catalogmanagement::tax.status') }}</span></th>
                                    <th><span class="userDatatable-title">{{ __('common.actions') }}</span></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-delete-modal modalId="modal-delete-tax" :title="__('catalogmanagement::tax.confirm_delete')" :message="__('catalogmanagement::tax.delete_confirmation')" itemNameId="delete-tax-name" confirmBtnId="confirmDeleteTaxBtn" :deleteRoute="route('admin.taxes.index')" :cancelText="__('common.cancel')" :deleteText="__('catalogmanagement::tax.delete_tax')" />
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let languages = @json($languages);
    
    let columns = [
        {
            data: 'index',
            name: 'index',
            orderable: false,
            render: function(data) {
                return '<div class="userDatatable-content">' + data + '</div>';
            }
        }
    ];

    // Add name columns for each language
    languages.forEach(function(language) {
        columns.push({
            data: 'names',
            name: 'name_' + language.id,
            orderable: false,
            render: function(data, type, row) {
                let name = data[language.id] ? data[language.id].value : '-';
                let rtl = data[language.id] ? data[language.id].rtl : false;
                return '<div class="userDatatable-content" ' + (rtl ? 'dir="rtl"' : '') + '>' + name + '</div>';
            }
        });
    });

    columns.push(
        {
            data: 'percentage',
            name: 'percentage',
            render: function(data) {
                return '<div class="userDatatable-content"><span class="badge badge-info badge-lg badge-round">' + data + '%</span></div>';
            }
        },
        {
            data: 'is_active',
            name: 'is_active',
            orderable: false,
            render: function(data, type, row) {
                let checked = data ? 'checked' : '';
                return `
                    <div class="userDatatable-content d-flex justify-content-center">
                        <div class="form-check form-switch form-switch-primary form-switch-md">
                            <input type="checkbox" class="form-check-input toggle-status" 
                                data-id="${row.id}" 
                                ${checked}
                                @cannot('taxes.edit') disabled @endcannot>
                        </div>
                    </div>
                `;
            }
        },
        {
            data: null,
            name: 'actions',
            orderable: false,
            searchable: false,
            render: function(data, type, row) {
                let viewUrl = "{{ route('admin.taxes.show', ':id') }}".replace(':id', row.id);
                let editUrl = "{{ route('admin.taxes.edit', ':id') }}".replace(':id', row.id);
                return `
                    <div class="orderDatatable_actions d-inline-flex gap-1">
                        @can('taxes.show')
                        <a href="${viewUrl}" class="view btn btn-primary table_action_father" title="{{ trans('common.view') }}">
                            <i class="uil uil-eye table_action_icon"></i>
                        </a>
                        @endcan
                        @can('taxes.edit')
                        <a href="${editUrl}" class="edit btn btn-warning table_action_father" title="{{ trans('common.edit') }}">
                            <i class="uil uil-edit table_action_icon"></i>
                        </a>
                        @endcan
                        @can('taxes.delete')
                        <a href="javascript:void(0);" class="remove delete-tax btn btn-danger table_action_father" data-bs-toggle="modal" data-bs-target="#modal-delete-tax" data-item-id="${row.id}" data-item-name="${row.display_name}" title="{{ trans('common.delete') }}">
                            <i class="uil uil-trash-alt table_action_icon"></i>
                        </a>
                        @endcan
                    </div>
                `;
            }
        }
    );

    $('#taxesDataTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.taxes.datatable") }}',
        columns: columns,
        order: [[0, 'desc']],
        language: {
            zeroRecords: "{{ __('catalogmanagement::tax.no_taxes_found') }}",
            emptyTable: "{{ __('catalogmanagement::tax.no_taxes_found') }}"
        }
    });

    // Handle toggle status
    $(document).on('change', '.toggle-status', function() {
        let checkbox = $(this);
        let taxId = checkbox.data('id');
        let isActive = checkbox.is(':checked') ? 1 : 0;

        $.ajax({
            url: "{{ route('admin.taxes.toggle-status', ':id') }}".replace(':id', taxId),
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                is_active: isActive
            },
            success: function(response) {
                if (response.success) {
                    toastr.success(response.message);
                } else {
                    toastr.error(response.message);
                    checkbox.prop('checked', !isActive);
                }
            },
            error: function(xhr) {
                toastr.error('{{ __("common.error_occurred") }}');
                checkbox.prop('checked', !isActive);
            }
        });
    });
});
</script>
@endpush
