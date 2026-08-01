@props([
    'title',
])

<div class="px-3 mt-3 mb-2">

    <small class="text-uppercase text-muted fw-semibold">

        {{ $title }}

    </small>

</div>

<ul class="sidebar-menu">

    {{ $slot }}

</ul>
