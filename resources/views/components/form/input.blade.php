@props([
    'type' => 'text',
    'name',
    'value' => null,
    'id' => null,
    'placeholder' => null
])

<input
    type="{{ $type }}"
    name="{{ $name }}"
    id="{{ $id ?? $name }}"
    value="{{ old($name,$value) }}"
    placeholder="{{ $placeholder }}"
    {{ $attributes->class([
        'form-control',
        'form-control-lg',
        'is-invalid' => $errors->has($name)
    ]) }}
>
