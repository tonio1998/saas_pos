{{-- resources/views/components/lms/menu-item.blade.php --}}

@props([
    'title',
    'icon' => 'bi-circle',
    'route' => null,
    'active' => false,
    'badge' => null,
    'badgeColor' => 'secondary',
    'disabled' => false,
    'children' => null,
    'expanded' => false,
])

@php

    $hasChildren = filled($children);

    $href = $disabled
        ? '#'
        : ($route ?: '#');

    $collapseId = 'menu_' . md5($title);

@endphp

@if($hasChildren)

    <div class="sidebar-group">

        <a
            href="#{{ $collapseId }}"
            class="sidebar-link {{ $expanded ? 'active' : '' }}"
            data-bs-toggle="collapse"
            aria-expanded="{{ $expanded ? 'true' : 'false' }}"
        >

            <i class="bi {{ $icon }}"></i>

            <span class="flex-grow-1">
                {{ $title }}
            </span>

            @if($badge)

                <span class="badge bg-{{ $badgeColor }}-subtle text-{{ $badgeColor }} me-2">
                    {{ $badge }}
                </span>

            @endif

            <i class="bi bi-chevron-down small"></i>

        </a>

        <div
            id="{{ $collapseId }}"
            class="collapse {{ $expanded ? 'show' : '' }}"
        >

            <div class="sidebar-submenu">

                {{ $children }}

            </div>

        </div>

    </div>

@else

    <a
        href="{{ $href }}"
        class="sidebar-link
            {{ $active ? 'active' : '' }}
            {{ $disabled ? 'disabled' : '' }}"
    >

        <i class="bi {{ $icon }}"></i>

        <span class="flex-grow-1">

            {{ $title }}

        </span>

        @if($badge)

            <span
                class="badge
                bg-{{ $badgeColor }}-subtle
                text-{{ $badgeColor }}"
            >
                {{ $badge }}
            </span>

        @endif

    </a>

@endif

<style>

    .sidebar-group+.sidebar-group{

        margin-top:.15rem;

    }

    .sidebar-submenu{

        padding-left:1rem;

        margin-top:.25rem;

    }

    .sidebar-submenu .sidebar-link{

        font-size:.93rem;

        padding:.7rem 1rem;

    }

    .sidebar-link{

        transition:.2s;

    }

    .sidebar-link .badge{

        font-size:.68rem;

        font-weight:600;

        border-radius:999px;

        padding:.35rem .55rem;

    }

    .sidebar-link.disabled{

        opacity:.7;

        cursor:not-allowed;

        pointer-events:none;

    }

    .sidebar-link .bi-chevron-down{

        transition:.2s;

    }

    .sidebar-link[aria-expanded="true"] .bi-chevron-down{

        transform:rotate(180deg);

    }

</style>
