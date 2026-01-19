@extends('layout.app')

@section('title', trans('refund::refund.titles.vendor_refund_settings'))

@section('content')
<div class="container-fluid mb-3">
    {{-- Breadcrumb Component --}}
    <div class="row">
        <div class="col-lg-12">
            <x-breadcrumb :items="[
                ['title' => trans('dashboard.title'), 'url' => route('admin.dashboard'), 'icon' => 'uil uil-estate'],
                ['title' => trans('refund::refund.titles.vendor_refund_settings')]
            ]" />
        </div>
    </div>

    {{-- Form Card Handler Component --}}
    <x-form-card-handler
        formId="vendorRefundSettingsForm"
        :formAction="route('admin.refunds.settings.update')"
        formMethod="PUT"
        :title="trans('refund::refund.titles.vendor_refund_settings')"
        icon="uil uil-setting"
        :successMessage="trans('refund::refund.messages.settings_updated')"
        :showSuccessAlert="true">

        {{-- Customer Pays Return Shipping - Using Form Switcher Component --}}
        <div class="col-md-6">
            <x-form-switcher
                name="customer_pays_return_shipping"
                :label="trans('refund::refund.fields.customer_pays_return_shipping')"
                :checked="$vendorSettings->customer_pays_return_shipping ?? 0"
                switchColor="primary"
                :helpText="trans('refund::refund.help.customer_pays_return_shipping')"
            />
        </div>

        {{-- Vendor Refund Days - Using Form Input Component --}}
        <div class="col-md-6">
            <x-form-input-field
                type="number"
                name="refund_processing_days"
                :label="trans('refund::refund.vendor_settings.vendor_refund_days')"
                :value="$vendorSettings->refund_processing_days ?? 7"
                placeholder="7"
                :min="1"
                :max="365"
                :required="true"
                :helpText="trans('refund::refund.vendor_settings.vendor_refund_days_help')"
            />
        </div>
    </x-form-card-handler>
</div>
@endsection
