@props(['headers', 'data', 'emptyMessage' => 'No data found'])

<div class="table-responsive">
    <table class="table mb-0 table-bordered table-hover">
        <thead>
            <tr class="userDatatable-header">
                @foreach($headers as $header)
                    <th {!! isset($header['style']) ? 'style="' . $header['style'] . '"' : '' !!} 
                        {!! isset($header['class']) ? 'class="' . $header['class'] . '"' : '' !!}>
                        <span class="userDatatable-title">{{ $header['label'] }}</span>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @if(count($data) > 0)
                {{ $slot }}
            @else
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center text-muted py-4">
                        <div class="userDatatable-content">{{ $emptyMessage }}</div>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
