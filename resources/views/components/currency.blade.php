@props(['class' => ''])

@if(currencyUsesImage())
    <img src="{{ currencyImage() }}" alt="{{ currency() }}" class="currency-image {{ $class }}" style="height: 18px; width: auto; vertical-align: middle;">
@else
    {{ currency() }}
@endif
