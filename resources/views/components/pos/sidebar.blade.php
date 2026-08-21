<div class="sidebar-inner d-flex flex-column h-100 p-2.5">
    
    <!-- Sidebar Header Logo & Mobile Close Button -->
    <div class="d-flex align-items-center justify-content-between pb-2 mb-2 border-bottom">
        <a href="{{ route('dashboard.index') }}" class="d-flex align-items-center gap-2 text-decoration-none">
            <div class="rounded-3 d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" style="width:32px; height:32px; background: linear-gradient(135deg, #059669, #047857); font-size: 1rem;">
                <i class="bi bi-shop"></i>
            </div>
            <div>
                <h6 class="fw-extrabold text-dark mb-0 lh-1" style="font-size: 0.95rem;">Barya<span style="color:#059669;">POS</span></h6>
                <small class="text-muted" style="font-size: 0.65rem; letter-spacing: 0.5px;">MINIMART & CRM</small>
            </div>
        </a>

        <button type="button" id="sidebarCloseBtn" class="btn-close d-lg-none shadow-none" aria-label="Close Sidebar" style="font-size: 0.75rem;"></button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-scroll flex-grow-1 overflow-auto pe-1">
        @foreach(config('sidebar') as $section)
            <div class="sidebar-section-title text-uppercase font-mono text-muted mb-1 mt-2">
                {{ $section['title'] }}
            </div>

            <ul class="sidebar-menu list-unstyled mb-0">
                @foreach($section['items'] as $item)
                    @if($item['type'] === 'link')
                        <li class="sidebar-item mb-1">
                            <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['active']) ? 'active' : '' }}">
                                <i class="{{ $item['icon'] }} sidebar-icon"></i>
                                <span>{{ $item['label'] }}</span>
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
                        <li class="sidebar-item mb-1">
                            <a class="sidebar-link {{ $expanded ? 'active' : '' }}" data-bs-toggle="collapse" href="#collapse-{{ $item['id'] }}" role="button" aria-expanded="{{ $expanded ? 'true' : 'false' }}">
                                <i class="{{ $item['icon'] }} sidebar-icon"></i>
                                <span>{{ $item['label'] }}</span>
                                <i class="bi bi-chevron-down dropdown-icon ms-auto"></i>
                            </a>
                            <div class="collapse {{ $expanded ? 'show' : '' }} sidebar-dropdown my-1" id="collapse-{{ $item['id'] }}">
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

    <!-- Sidebar Footer Quick POS Link -->
    <div class="sidebar-footer pt-2 mt-auto border-top">
        <a href="{{ route('terminal.index') }}" class="btn btn-emerald btn-sm w-100 py-1.5 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2 text-white shadow-sm" style="background:#059669; border:none; font-size: 0.825rem;">
            <i class="bi bi-calculator-fill"></i>
            <span>Open POS Terminal</span>
        </a>
    </div>

</div>
