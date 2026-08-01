@props([
    'name',
    'value' => null,
    'id' => null,
    'rows' => 4,
    'placeholder' => null
])

<textarea
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    rows="{{ $rows }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->class([
        'form-control',
        'is-invalid' => $errors->has($name)
    ]) }}
>{{ old($name, $value) }}</textarea>
