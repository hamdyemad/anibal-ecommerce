@props(['steps', 'currentStep' => 1])

<div class="checkout-progress-indicator content-center mb-40">
    <div class="checkout-progress">
        @foreach($steps as $index => $step)
            @if($index > 0)
                <div class="current">
                    <img src="{{ asset('assets/img/svg/checkout.svg') }}" alt="img" class="svg">
                </div>
            @endif
            
            <div class="step wizard-step-nav {{ $index + 1 == $currentStep ? 'current' : '' }}" data-step="{{ $index + 1 }}" style="cursor: pointer;">
                <span>{{ $index + 1 }}</span>
                <span>{{ $step }}</span>
            </div>
        @endforeach
    </div>
</div>
