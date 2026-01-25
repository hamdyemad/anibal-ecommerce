{{-- Bank Products Filters --}}
<div class="mb-25">
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                {{-- Search --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-search me-1"></i> {{ __('common.search') }}
                        </label>
                        <input type="text"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="search"
                            placeholder="{{ __('common.search') }}"
                            autocomplete="off">
                    </div>
                </div>

                {{-- Active Status Filter --}}
                @can('products.bank.change-activation')
                <div class="col-md-3">
                    <x-custom-select 
                        name="active" 
                        id="active"
                        :label="__('common.active_status')"
                        icon="uil-check-circle"
                        :options="$filterOptions['activeStatus']"
                        :selected="request('active')"
                        :placeholder="__('common.all')"
                    />
                </div>
                @endcan

                {{-- Date Filters --}}
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_from" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-calendar-alt me-1"></i>
                            {{ __('common.created_date_from') }}
                        </label>
                        <input type="date"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="created_date_from">
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        <label for="created_date_to" class="il-gray fs-14 fw-500 mb-10">
                            <i class="uil uil-calendar-alt me-1"></i>
                            {{ __('common.created_date_to') }}
                        </label>
                        <input type="date"
                            class="form-control ih-medium ip-gray radius-xs b-light px-15"
                            id="created_date_to">
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="col-md-12 d-flex align-items-center">
                    <button type="button" id="searchBtn"
                        class="btn btn-success btn-default btn-squared me-1"
                        title="{{ __('common.search') }}">
                        <i class="uil uil-search me-1"></i>
                        {{ __('common.search') }}
                    </button>
                    <button type="button" id="resetFilters"
                        class="btn btn-warning btn-default btn-squared"
                        title="{{ __('common.reset') }}">
                        <i class="uil uil-redo me-1"></i>
                        {{ __('common.reset_filters') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
