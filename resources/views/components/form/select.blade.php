@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'text' => null,
    'placeholder' => null,
    'ajax' => null,
    'dropdownParent' => null,
    'inline' => false,
    'style' => false,
    'class' => '',
])

@php
    $selectedValue = old($name, $value);
    $selectedText = $text ?? '';
@endphp

@if($inline)

    <div class="row mb-3 align-items-center {{ $class }}" {{ $style }}>
        @if($label)
            <label for="{{ $name }}" class="col-sm-3 col-form-label fw-semibold">
                {{ $label }}
            </label>
        @endif

        <div class="col-sm-9">
            <select
                name="{{ $name }}"
                id="{{ $name }}"
                data-ajax="{{ $ajax }}"
                data-placeholder="{{ $placeholder }}"
                data-value="{{ $selectedValue }}"
                data-selected="{{ $selectedText }}"
                @if($dropdownParent)
                    data-dropdown-parent="{{ $dropdownParent }}"
                @endif
                {{ $attributes->class(['form-select', 'select2']) }}
            >
                <option></option>

                @if($ajax)
                    @if($selectedValue && $selectedText)
                        <option value="{{ $selectedValue }}" selected>
                            {{ $selectedText }}
                        </option>
                    @endif
                @else
                    @foreach($options as $key => $option)
                        <option
                            value="{{ $key }}"
                            @selected($selectedValue == $key)
                        >
                            {{ $option }}
                        </option>
                    @endforeach
                @endif
            </select>
        </div>
    </div>

@else

    <div class="mb-3 {{ $class }}" {{ $style }}>
        @if($label)
            <label for="{{ $name }}" class="form-label fw-semibold">
                {{ $label }}
            </label>
        @endif

        <select
            name="{{ $name }}"
            id="{{ $name }}"
            data-ajax="{{ $ajax }}"
            data-placeholder="{{ $placeholder }}"
            data-value="{{ $selectedValue }}"
            data-selected="{{ $selectedText }}"
            @if($dropdownParent)
                data-dropdown-parent="{{ $dropdownParent }}"
            @endif
            {{ $attributes->class(['form-select', 'select2']) }}
        >
            <option></option>

            @if($ajax)
                @if($selectedValue && $selectedText)
                    <option value="{{ $selectedValue }}" selected>
                        {{ $selectedText }}
                    </option>
                @endif
            @else
                @foreach($options as $key => $option)
                    <option
                        value="{{ $key }}"
                        @selected($selectedValue == $key)
                    >
                        {{ $option }}
                    </option>
                @endforeach
            @endif
        </select>
    </div>

@endif
