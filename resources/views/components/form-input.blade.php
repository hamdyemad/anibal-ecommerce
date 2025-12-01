@props([
    'name',
    'label' => null,
    'placeholder' => null,
    'type' => 'text',
    'required' => false,
    'value' => null,
    'rows' => null,
    'id' => null,
    'class' => '',
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'step' => null,
    'accept' => null,
    'multiple' => false,
    'tags' => false,
    'options' => [],
    'selected' => null,
    'help' => null,
    'icon' => null,
    'dir' => null,
    'colSize' => 'col-md-6',
    'wrapperClass' => 'mb-25'
])

@php
    $inputId = $id ?? $name;
    $inputClass = 'form-control ' . ($type === 'textarea' ? 'ip-gray' : 'ih-medium ip-gray') . ' radius-xs b-light px-15';
    if ($class) {
        $inputClass .= ' ' . $class;
    }
    if ($errors->has($name)) {
        $inputClass .= ' is-invalid';
    }
@endphp

<div class="{{ $colSize }} {{ $wrapperClass }}">
    <div class="form-group">
        @if($label)
            <label for="{{ $inputId }}" class="il-gray fs-14 fw-500 mb-10 d-block" @if($dir) dir="{{ $dir }}" @endif>
                @if($icon)
                    <i class="{{ $icon }} me-1"></i>
                @endif
                {{ $label }}
                @if($required)
                    <span class="text-danger">*</span>
                @endif
            </label>
        @endif

        @if($tags)
            <div @if($dir) dir="{{ $dir }}" @endif>
                <x-tags-input
                    name="{{ $name }}"
                    :value="old($name, $value)"
                    placeholder="{{ $placeholder }}"
                    :rtl="$dir === 'rtl'"
                    @if($dir) dir="{{ $dir }}" @endif
                />
            </div>
        @elseif($type === 'select')
            <select
                class="{{ $inputClass }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($multiple) multiple @endif
                @if($dir) dir="{{ $dir }}" @endif>
                @if(!$multiple && !$required)
                    <option value="">{{ $placeholder ?? 'Select an option...' }}</option>
                @endif
                @foreach($options as $optionValue => $optionLabel)
                    <option value="{{ $optionValue }}"
                        @if(old($name, $selected) == $optionValue) selected @endif>
                        {{ $optionLabel }}
                    </option>
                @endforeach
            </select>
        @elseif($type === 'textarea')
            <textarea
                class="{{ $inputClass }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($rows) rows="{{ $rows }}" @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                @if($dir) dir="{{ $dir }}" @endif>{{ old($name, $value) }}</textarea>
        @elseif($type === 'checkbox')
            <div class="form-check form-switch form-switch-primary form-switch-md">
                <input type="hidden" name="{{ $name }}" value="0">
                <input
                    type="checkbox"
                    class="form-check-input"
                    id="{{ $inputId }}"
                    name="{{ $name }}"
                    value="1"
                    @if(old($name, $value) == 1) checked @endif
                    @if($disabled) disabled @endif>
                @if($label)
                    <label class="form-check-label" for="{{ $inputId }}">
                        {{ $placeholder ?? 'Enable' }}
                    </label>
                @endif
            </div>
        @else
            <input
                type="{{ $type }}"
                class="{{ $inputClass }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                value="{{ old($name, $value) }}"
                @if($placeholder) placeholder="{{ $placeholder }}" @endif
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
                @if($min !== null) min="{{ $min }}" @endif
                @if($max !== null) max="{{ $max }}" @endif
                @if($step !== null) step="{{ $step }}" @endif
                @if($accept) accept="{{ $accept }}" @endif
                @if($multiple) multiple @endif
                @if($dir) dir="{{ $dir }}" @endif>
        @endif

        @if($help)
            <small class="form-text text-muted mt-1">{{ $help }}</small>
        @endif

        @error($name)
            <div class="invalid-feedback d-block" style="display: block !important;">{{ $message }}</div>
        @enderror
    </div>
</div>
