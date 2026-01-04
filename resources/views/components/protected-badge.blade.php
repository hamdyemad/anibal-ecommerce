@props([
    'color' => '#6c757d',
    'text' => '',
    'size' => 'md', // sm, md, lg
    'id' => null,
])

@php
    $uniqueId = $id ?? 'protected-badge-' . uniqid();
@endphp

<span 
    class="protected-badge protected-badge-{{ $size }}"
    style="background-color: {{ $color }}; color: white;"
    data-protected="true"
    data-original-value="{{ $text }}"
    id="{{ $uniqueId }}">
    {{ $text }}
</span>

@once
    @push('styles')
        <style>
            .protected-badge {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 50px;
                font-weight: 500;
                white-space: nowrap;
                transition: all 0.2s ease;
                line-height: 1.2;
            }
            
            .protected-badge-sm {
                font-size: 8pt !important;
                padding: 2pt 5pt !important;
            }
            
            .protected-badge-md {
                font-size: 9pt !important;
                padding: 3pt 6pt !important;
            }
            
            .protected-badge-lg {
                font-size: 10pt !important;
                padding: 4pt 8pt !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            (function() {
                // Enhanced protection specifically for protected-badge components
                function protectBadges() {
                    $('.protected-badge').each(function() {
                        var $badge = $(this);
                        var originalValue = $badge.data('original-value');
                        var currentValue = $badge.text().trim();
                        
                        // If badge was cleared or changed to 0, restore it
                        if ((!currentValue || currentValue === '0' || currentValue === '0.00') && originalValue) {
                            $badge.text(originalValue);
                        }
                    });
                }
                
                $(document).ready(function() {
                    // Protect on button clicks
                    $(document).on('click', 'button, .btn', function() {
                        setTimeout(protectBadges, 50);
                    });
                    
                    // Protect after AJAX
                    $(document).ajaxComplete(function() {
                        setTimeout(protectBadges, 100);
                    });
                    
                    // Periodic protection
                    setInterval(protectBadges, 2000);
                    
                    // MutationObserver for real-time protection
                    if (window.MutationObserver) {
                        var observer = new MutationObserver(function(mutations) {
                            protectBadges();
                        });
                        
                        $('.protected-badge').each(function() {
                            observer.observe(this, {
                                childList: true,
                                characterData: true,
                                subtree: true
                            });
                        });
                    }
                });
            })();
        </script>
    @endpush
@endonce
