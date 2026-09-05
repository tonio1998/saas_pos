@php
    $tenant = auth()->check() ? auth()->user()->tenant : null;
    if (!$tenant) {
        $tenant = \App\Models\POS\POSTenant::first();
    }
    $themeSettings = $tenant?->theme_settings ?? [];
    $primaryColor = $themeSettings['primary_color'] ?? '#059669';
    $topbarColor = $themeSettings['topbar_color'] ?? '#064E3B';
    $topbarTextColor = $themeSettings['topbar_text_color'] ?? '#FFFFFF';
    $sidebarColor = $themeSettings['sidebar_color'] ?? '#0F172A';
    $sidebarTextColor = $themeSettings['sidebar_text_color'] ?? '#CBD5E1';
    $sidebarActive = $themeSettings['sidebar_active_color'] ?? '#059669';
    $sidebarActiveTextColor = $themeSettings['sidebar_active_text_color'] ?? '#FFFFFF';
    $accentColor = $themeSettings['accent_color'] ?? '#10B981';
@endphp
<style id="likha-dynamic-theme-styles">
    :root {
        --theme-primary: {{ $primaryColor }};
        --theme-topbar: {{ $topbarColor }};
        --theme-topbar-text: {{ $topbarTextColor }};
        --theme-sidebar: {{ $sidebarColor }};
        --theme-sidebar-text: {{ $sidebarTextColor }};
        --theme-sidebar-active: {{ $sidebarActive }};
        --theme-sidebar-active-text: {{ $sidebarActiveTextColor }};
        --theme-accent: {{ $accentColor }};
        --theme-bg: {{ $primaryColor }};
        --theme-text: #ffffff;
        --theme-hover: rgba(5,150,105,0.08);
        --theme-active: rgba(5,150,105,0.15);
        --theme-subtext: #64748B;
        --theme-border: #E2E8F0;
    }

    /* ═══════════════════════════════════════════════════════════
       1. DYNAMIC TOPBAR (Header & Navigation)
       ═══════════════════════════════════════════════════════════ */
    header.crm-topbar,
    #crmTopbar,
    .crm-topbar,
    .likha-topbar,
    .lms-topbar {
        background-color: var(--theme-topbar) !important;
        background: var(--theme-topbar) !important;
        color: var(--theme-topbar-text) !important;
        border-bottom-color: rgba(255, 255, 255, 0.12) !important;
    }

    .crm-topbar .crm-title-text,
    .crm-topbar .crm-page-title span,
    .likha-topbar a,
    .likha-topbar span,
    .likha-topbar i,
    .likha-topbar .navbar-brand {
        color: var(--theme-topbar-text) !important;
    }

    .crm-topbar .crm-subtitle {
        color: var(--theme-topbar-text) !important;
        opacity: 0.75 !important;
    }

    .crm-topbar .crm-icon-btn {
        background: rgba(255, 255, 255, 0.15) !important;
        color: var(--theme-topbar-text) !important;
        border-color: rgba(255, 255, 255, 0.22) !important;
    }

    .crm-topbar .crm-icon-btn i {
        color: var(--theme-topbar-text) !important;
    }

    .crm-topbar .crm-store-pill {
        background: rgba(255, 255, 255, 0.12) !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }

    .crm-topbar .crm-store-name {
        color: var(--theme-topbar-text) !important;
    }

    .crm-topbar .crm-store-status {
        color: var(--theme-topbar-text) !important;
        opacity: 0.8 !important;
    }

    .crm-topbar .crm-pos-btn {
        background: var(--theme-primary) !important;
        border-color: var(--theme-primary) !important;
        color: #ffffff !important;
    }

    /* ═══════════════════════════════════════════════════════════
       2. DYNAMIC SIDEBAR (Drawer & Menu Items)
       ═══════════════════════════════════════════════════════════ */
    .likha-sidebar,
    .lms-sidebar,
    aside#mainSidebar,
    .sidebar-inner {
        background-color: var(--theme-sidebar) !important;
        background: var(--theme-sidebar) !important;
    }

    /* Section Headers (e.g. MAIN, SALES, SETTINGS) */
    .sidebar-section-title,
    .sidebar-heading {
        color: var(--theme-sidebar-text) !important;
        opacity: 0.75 !important;
    }

    /* Inactive Sidebar Links, Spans, Icons */
    .sidebar-link:not(.active),
    .sidebar-link:not(.active) *,
    .sidebar-link:not(.active) span,
    .sidebar-link:not(.active) i,
    .sidebar-link:not(.active) .sidebar-icon,
    .sidebar-link:not(.active) .dropdown-icon,
    .sidebar-item a:not(.active),
    .sidebar-item a:not(.active) span,
    .sidebar-item a:not(.active) i {
        color: var(--theme-sidebar-text) !important;
    }

    .sidebar-link:not(.active):hover {
        background-color: rgba(255, 255, 255, 0.08) !important;
        color: var(--theme-sidebar-text) !important;
    }

    /* Inactive Sublinks */
    .sidebar-sublink:not(.active),
    .sidebar-sublink:not(.active) *,
    .sidebar-sublink:not(.active) span,
    .sidebar-sublink:not(.active) i,
    .sidebar-sublink:not(.active) .sidebar-subicon {
        color: var(--theme-sidebar-text) !important;
        opacity: 0.85 !important;
    }

    /* Active Sidebar Parent / Main Menu Link */
    .sidebar-link.active,
    .sidebar-link.active *,
    .sidebar-link.active span,
    .sidebar-link.active i,
    .sidebar-link.active .sidebar-icon,
    .sidebar-link.active .dropdown-icon {
        background-color: var(--theme-sidebar-active) !important;
        background: var(--theme-sidebar-active) !important;
        color: var(--theme-sidebar-active-text) !important;
    }

    /* Active Sublink Highlight */
    .sidebar-sublink.active,
    .sidebar-sublink.active *,
    .sidebar-sublink.active span,
    .sidebar-sublink.active i,
    .sidebar-sublink.active .sidebar-subicon {
        color: var(--theme-primary) !important;
        font-weight: 800 !important;
        opacity: 1 !important;
    }

    /* Sidebar Footer Open POS Button */
    .sidebar-footer .btn-emerald,
    .sidebar-footer a {
        background: var(--theme-primary) !important;
        background-color: var(--theme-primary) !important;
        color: #ffffff !important;
    }

    /* ═══════════════════════════════════════════════════════════
       3. DYNAMIC PRIMARY BRAND BUTTONS
       ═══════════════════════════════════════════════════════════ */
    .btn-primary-theme,
    .btn-emerald {
        background-color: var(--theme-primary) !important;
        border-color: var(--theme-primary) !important;
    }
</style>
