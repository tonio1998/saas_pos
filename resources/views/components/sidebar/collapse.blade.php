@props([
    'id',
    'title',
    'icon',
    'expanded' => false,
])

<li class="sidebar-item">

    <a
        class="sidebar-link d-flex justify-content-between align-items-center {{ $expanded ? '' : 'collapsed' }}"
        data-bs-toggle="collapse"
        href="#{{ $id }}"
        role="button"
        aria-expanded="{{ $expanded ? 'true' : 'false' }}"
    >

        <div class="d-flex align-items-center">
            <i class="{{ $icon }} sidebar-icon"></i>
            <span>{{ $title }}</span>
        </div>

        <i class="bi bi-chevron-down dropdown-icon"></i>

    </a>

    <div
        id="{{ $id }}"
        class="collapse sidebar-dropdown {{ $expanded ? 'show' : '' }}"
    >
        {{ $slot }}
    </div>

</li>
