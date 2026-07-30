@props([
    'title' => null,
    'subtitle' => null,
    'action' => null,
])

@if($title || $subtitle || $action)
    <div class="page-header d-flex justify-content-between align-items-center mb-4 mt-0">
        <div>
            @if($title)
                <h4 class="mb-1">{{ $title }}</h4>
            @endif

            @if($subtitle)
                <small class="text-muted">{{ $subtitle }}</small>
            @endif
        </div>

        @if($action)
            <div>
                {{ $action }}
            </div>
        @endif
    </div>
@endif
