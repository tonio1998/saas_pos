@php
    $tenantId = auth()->check() && auth()->user()->tenant_id ? auth()->user()->tenant_id : session('tenant_id');
    $currentTenant = $tenantId ? \App\Models\POS\POSTenant::find($tenantId) : null;
    if (!$currentTenant) {
        $currentTenant = \App\Models\POS\POSTenant::first();
    }
    $hasLogo = $currentTenant && $currentTenant->logo && \Illuminate\Support\Facades\Storage::disk('public')->exists($currentTenant->logo);
    $sidebarLogo = $hasLogo ? \Illuminate\Support\Facades\Storage::url($currentTenant->logo) : null;
    $storeName = session('tenant_name', $currentTenant?->business_name ?? 'LikhaPOS Store');
    $mainBranch = $currentTenant?->branches()->where('is_main_branch', true)->first();
    $branchName = $mainBranch?->branch_name ?? 'Main Branch';
@endphp

<div class="sidebar-inner d-flex flex-column h-100">
    
    <!-- Sidebar Header Logo & Store Identity -->
    <div class="sidebar-brand-wrapper position-relative">
        <a href="{{ route('dashboard.index') }}" class="sidebar-brand-link text-decoration-none">
            @if($sidebarLogo)
                <div class="sidebar-logo-card">
                    <img src="{{ $sidebarLogo }}" alt="{{ $storeName }}" class="sidebar-brand-img">
                </div>
            @else
                <div class="sidebar-brand-fallback">
                    <div class="sidebar-brand-icon">
                        <i class="bi bi-stars"></i>
                    </div>
                    <div class="sidebar-brand-text">
                        <div class="sidebar-brand-title">Likha<span class="sidebar-brand-accent">POS</span></div>
                        <div class="sidebar-brand-sub text-truncate">{{ $storeName }}</div>
                    </div>
                </div>
            @endif
        </a>

        <!-- Mobile Close Button -->
        <button type="button" id="sidebarCloseBtn" class="sidebar-close-btn d-lg-none" aria-label="Close Sidebar">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Active Branch Badge -->
    <div class="sidebar-branch-strip">
        <div class="sidebar-branch-card d-flex align-items-center justify-content-between gap-2">
            <div class="d-flex align-items-center gap-1.5 min-w-0 flex-grow-1" title="{{ $branchName }}">
                <i class="bi bi-geo-alt-fill sidebar-branch-icon flex-shrink-0"></i>
                <span class="sidebar-branch-name text-truncate">{{ $branchName }}</span>
            </div>
            <span class="sidebar-status-pill flex-shrink-0">
                <span class="sidebar-status-dot"></span> Cloud
            </span>
        </div>
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-scroll flex-grow-1 overflow-auto">
        @foreach(config('sidebar') as $section)
            <div class="sidebar-section-title">
                {{ $section['title'] }}
            </div>

            <ul class="sidebar-menu list-unstyled mb-0">
                @foreach($section['items'] as $item)
                    @if($item['type'] === 'link')
                        <li class="sidebar-item">
                            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                <span class="sidebar-icon-wrap">
                                    <i class="{{ $item['icon'] }} sidebar-icon"></i>
                                </span>
                                <span class="sidebar-label">{{ $item['label'] }}</span>
                            </a>
                        </li>
                    @elseif($item['type'] === 'collapse')
                        @php
                            $expanded = false;
                            foreach ($item['active'] as $route) {
                                if (request()->routeIs($route)) {
                                    $expanded = true;
                                    break;
                                }
                            }
                        @endphp
                        <li class="sidebar-item">
                            <a class="sidebar-link {{ $expanded ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapse-{{ $item['id'] }}" role="button" aria-expanded="{{ $expanded ? 'true' : 'false' }}">
                                <span class="sidebar-icon-wrap">
                                    <i class="{{ $item['icon'] }} sidebar-icon"></i>
                                </span>
                                <span class="sidebar-label">{{ $item['label'] }}</span>
                                <i class="bi bi-chevron-down dropdown-icon ms-auto"></i>
                            </a>
                            <div class="collapse {{ $expanded ? 'show' : '' }} sidebar-dropdown" id="collapse-{{ $item['id'] }}">
                                @foreach($item['children'] as $child)
                                    <a href="{{ route($child['route']) }}" class="sidebar-sublink {{ request()->routeIs($child['active']) ? 'active' : '' }}">
                                        <i class="{{ $child['icon'] }} sidebar-subicon"></i>
                                        <span>{{ $child['label'] }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endforeach
    </div>

    <!-- Sidebar Footer Quick POS Launcher -->
    <div class="sidebar-footer mt-auto">
        <a href="{{ route('terminal.index') }}" class="sidebar-pos-launch-btn">
            <i class="bi bi-calculator-fill"></i>
            <span>Open POS Terminal</span>
        </a>
    </div>

</div>

{{-- ── Sidebar Styles (Dynamic Theme Sensitive) ───────────── --}}
<style>
.sidebar-inner {
    padding: 16px 14px 18px;
    background: var(--theme-sidebar, #0f172a);
    color: var(--theme-sidebar-text, #cbd5e1);
}

/* Header & Brand */
.sidebar-brand-wrapper {
    padding: 4px 6px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.sidebar-brand-link {
    width: 100%;
    display: flex;
    align-items: center;
}
.sidebar-logo-card {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 14px;
    padding: 8px 14px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    max-width: 100%;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.12);
}
.sidebar-brand-img {
    max-height: 38px;
    width: auto;
    object-fit: contain;
}

.sidebar-brand-fallback {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}
.sidebar-brand-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    background: var(--theme-primary, #059669);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.22);
}
.sidebar-brand-text {
    line-height: 1.2;
    min-width: 0;
}
.sidebar-brand-title {
    font-family: 'Outfit', sans-serif;
    font-size: 1.2rem;
    font-weight: 800;
    color: var(--theme-sidebar-text, #ffffff);
    letter-spacing: -0.01em;
}
.sidebar-brand-accent {
    color: var(--theme-accent, #34d399);
}
.sidebar-brand-sub {
    font-size: 0.72rem;
    color: var(--theme-sidebar-text, #cbd5e1);
    opacity: 0.75;
    font-weight: 600;
    margin-top: 1px;
}

.sidebar-close-btn {
    position: absolute;
    right: 4px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    border: none;
    border-radius: 10px;
    color: var(--theme-sidebar-text, #ffffff);
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background 0.15s ease;
}
.sidebar-close-btn:hover {
    background: rgba(255, 255, 255, 0.2);
}

/* Branch & Status Strip */
.sidebar-branch-strip {
    padding: 10px 4px 12px;
    margin-bottom: 8px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.sidebar-branch-card {
    background: rgba(255, 255, 255, 0.07);
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 12px;
    padding: 7px 11px;
    min-width: 0;
}
.sidebar-branch-name {
    font-size: 0.77rem;
    font-weight: 700;
    color: var(--theme-sidebar-text, #cbd5e1);
    letter-spacing: 0.15px;
    line-height: 1.2;
}
.sidebar-branch-icon {
    font-size: 0.85rem;
    color: var(--theme-primary, #059669);
}
.sidebar-status-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #4ade80;
    background: rgba(34, 197, 94, 0.12);
    border: 1px solid rgba(34, 197, 94, 0.25);
    border-radius: 50px;
    padding: 3px 9px;
}
.sidebar-status-dot {
    width: 6px;
    height: 6px;
    background: #22c55e;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 6px #22c55e;
}

/* Scroll Area */
.sidebar-scroll {
    padding-right: 4px;
}
.sidebar-scroll::-webkit-scrollbar {
    width: 4px;
}
.sidebar-scroll::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.15);
    border-radius: 10px;
}

/* Section Title */
.sidebar-section-title {
    font-size: 0.69rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.9px;
    color: var(--theme-sidebar-text, #cbd5e1);
    opacity: 0.55;
    padding: 16px 12px 6px;
}

/* Sidebar Menu Item */
.sidebar-item {
    margin-bottom: 5px;
}
.sidebar-link {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    border-radius: 14px;
    color: var(--theme-sidebar-text, #cbd5e1);
    font-size: 0.88rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    min-height: 44px;
    gap: 12px;
}
.sidebar-link:hover {
    background: rgba(255, 255, 255, 0.08);
    color: var(--theme-sidebar-text, #ffffff);
    transform: translateX(4px);
}
.sidebar-link.active {
    background: var(--theme-sidebar-active, #059669) !important;
    color: var(--theme-sidebar-active-text, #ffffff) !important;
    font-weight: 700;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.sidebar-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.07);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.02rem;
    transition: all 0.2s ease;
}
.sidebar-link.active .sidebar-icon-wrap {
    background: rgba(255, 255, 255, 0.22);
    color: var(--theme-sidebar-active-text, #ffffff) !important;
}

.sidebar-label {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-size: 0.88rem;
}

.dropdown-icon {
    font-size: 0.72rem;
    transition: transform 0.2s ease;
    opacity: 0.75;
}
.sidebar-link[aria-expanded="true"] .dropdown-icon {
    transform: rotate(180deg);
}

/* Accordion Dropdown Submenu */
.sidebar-dropdown {
    padding-left: 10px;
    margin: 4px 0 8px 14px;
    border-left: 2px solid rgba(255, 255, 255, 0.15);
}
.sidebar-sublink {
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 8px 10px;
    min-height: 40px;
    font-size: 0.86rem;
    font-weight: 600;
    color: var(--theme-sidebar-text, #cbd5e1);
    opacity: 0.92;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
    margin-bottom: 3px;
    white-space: nowrap;
}
.sidebar-sublink span {
    white-space: nowrap;
}
.sidebar-sublink:hover {
    background: rgba(255, 255, 255, 0.08);
    color: var(--theme-sidebar-text, #ffffff);
    opacity: 1;
    transform: translateX(3px);
}
.sidebar-sublink.active {
    background: rgba(255, 255, 255, 0.15);
    color: var(--theme-accent, #34d399) !important;
    font-weight: 700;
    opacity: 1;
}
.sidebar-subicon {
    font-size: 1.05rem;
    width: 20px;
    height: 20px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    opacity: 0.95;
    flex-shrink: 0;
    transition: transform 0.2s ease;
}
.sidebar-sublink:hover .sidebar-subicon {
    transform: scale(1.15);
}

/* Footer & POS Button */
.sidebar-footer {
    padding-top: 16px;
    border-top: 1px solid rgba(255, 255, 255, 0.08);
}
.sidebar-pos-launch-btn {
    width: 100%;
    padding: 12px 16px;
    min-height: 46px;
    background: var(--theme-primary, #059669);
    color: #ffffff !important;
    border: none;
    border-radius: 14px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.9rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 9px;
    text-decoration: none;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1);
}
.sidebar-pos-launch-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
    filter: brightness(1.08);
}
</style>
