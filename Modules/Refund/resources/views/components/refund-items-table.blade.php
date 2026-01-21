@props(['refundRequest'])

@php
use Modules\Refund\app\DataTables\RefundItemsDataTable;

$dataTable = new RefundItemsDataTable();
$itemsHeaders = $dataTable->getHeaders();
$itemsData = $dataTable->getData($refundRequest);
@endphp

<div class="table-responsive">
    <table class="table table-bordered table-hover mb-0" style="width:100%">
        <thead>
            <tr class="userDatatable-header">
                @foreach($itemsHeaders as $header)
                    <th class="{{ $header['class'] ?? '' }}">
                        <span class="userDatatable-title">{{ $header['label'] }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($itemsData as $item)
                <tr>
                    <td class="text-center fw-bold">{{ $item['index'] }}</td>
                    <td>{!! $item['product'] !!}</td>
                    <td class="text-center">{{ $item['price_before_tax'] }}</td>
                    <td class="text-center">{{ $item['tax'] }}</td>
                    <td class="text-center">{{ $item['price_with_tax'] }}</td>
                    <td class="text-center">{!! $item['quantity'] !!}</td>
                    <td class="text-center">{{ $item['shipping'] }}</td>
                    <td class="text-center">{!! $item['total'] !!}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($itemsHeaders) }}" class="text-center text-muted py-4">
                        {{ trans('common.no_records_found') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
