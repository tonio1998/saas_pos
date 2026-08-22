@php
    $tenantLogo = auth()->check() && auth()->user()->tenant_id
        ? \App\Models\POS\POSTenant::where('id', auth()->user()->tenant_id)->value('logo')
        : null;
    $sidebarLogo = $tenantLogo ? asset('storage/' . $tenantLogo) : asset('images/logo.png');
@endphp

<div class="border-bottom px-3 pb-2 mb-2 text-center" style="border-color: #1e293b !important;">
    <a href="{{ route('dashboard.index') }}" class="d-inline-flex align-items-center justify-content-center text-decoration-none">
        <div class="px-3 py-1.5 rounded-3 bg-white d-inline-flex align-items-center justify-content-center shadow-xs">
            <img
                src="{{ $sidebarLogo }}"
                alt="LikhaPOS Logo"
                style="max-height:34px; width:auto; object-fit:contain;"
            >
        </div>
    </a>
</div>
