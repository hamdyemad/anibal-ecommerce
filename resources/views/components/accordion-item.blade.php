@props([
    'id' => null,
    'title' => '',
    'icon' => null,
    'badge' => null,
    'badgeColor' => 'primary',
    'expanded' => false,
    'parentId' => 'accordion',
])

@php
    $uniqueId = $id ?? 'accordion-' . uniqid();
    $headingId = 'heading-' . $uniqueId;
    $collapseId = 'collapse-' . $uniqueId;
    $expandedClass = $expanded ? '' : 'collapsed';
    $showClass = $expanded ? 'show' : '';
@endphp

<div class="accordion-item">
    <h2 class="accordion-header" id="{{ $headingId }}">
        <button class="accordion-button {{ $expandedClass }}" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#{{ $collapseId }}" 
                aria-expanded="{{ $expanded ? 'true' : 'false' }}" 
                aria-controls="{{ $collapseId }}">
            @if($icon)
                <i class="{{ $icon }} me-2"></i>
            @endif
            @if($badge)
                @php
                    $badgeColorMap = [
                        'primary' => '#0d6efd',
                        'info' => '#0dcaf0',
                        'warning' => '#ffc107',
                        'success' => '#198754',
                        'danger' => '#dc3545',
                        'purple' => '#6f42c1',
                        'secondary' => '#6c757d',
                    ];
                    $badgeColor = $badgeColorMap[$badgeColor] ?? $badgeColorMap['primary'];
                @endphp
                <x-protected-badge 
                    :color="$badgeColor" 
                    :text="$badge" 
                    size="lg" 
                    style="margin-right: 12px; margin-left: 0px;" 
                />
            @endif
            <span class="ms-1">{{ $title }}</span>
        </button>
    </h2>
    <div id="{{ $collapseId }}" 
         class="accordion-collapse collapse {{ $showClass }}" 
         aria-labelledby="{{ $headingId }}" 
         data-bs-parent="#{{ $parentId }}">
        <div class="accordion-body">
            {{ $slot }}
        </div>
    </div>
</div>
