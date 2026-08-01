@props([
    'route',
    'icon',
    'active' => false,
])

<li class="sidebar-item">

    <a
        href="{{ route($route) }}"
        class="sidebar-link justify-content-start {{ $active ? 'active' : '' }}"
    >

        <i class="{{ $icon }} sidebar-icon"></i>

        <span>

            {{ $slot }}

        </span>

    </a>

</li>
