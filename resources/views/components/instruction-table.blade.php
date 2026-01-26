@props([
    'columns' => [],
])

<div class="table-responsive">
    <table class="table table-sm table-bordered">
        <thead class="table-light">
            <tr>
                <th style="width: 150px;">{{ __('catalogmanagement::product.column_name') }}</th>
                <th>{{ __('catalogmanagement::product.description') }}</th>
                <th style="width: 200px;">{{ __('catalogmanagement::product.where_to_get') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($columns as $column)
                <tr>
                    <td><code>{{ $column['name'] }}</code></td>
                    <td>{{ $column['description'] }}</td>
                    <td>
                        @if(isset($column['link']))
                            <a href="{{ $column['link'] }}" target="_blank">{{ $column['source'] }}</a>
                        @else
                            {{ $column['source'] }}
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
