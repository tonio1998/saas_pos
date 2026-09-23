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
    $logoSidebarH = $themeSettings['logo_sidebar_height'] ?? 38;
    $logoTopbarH = $themeSettings['logo_topbar_height'] ?? 32;
    $logoReceiptH = $themeSettings['logo_receipt_height'] ?? 52;
    $themeGradient = $themeSettings['gradient'] ?? "linear-gradient(135deg, {$topbarColor} 0%, {$primaryColor} 100%)";

    // Luminance helper to detect light background vs dark background
    $isLightColor = function ($hex) {
        if (!$hex) return false;
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        if (strlen($hex) !== 6) return false;
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        return ((0.299 * $r + 0.587 * $g + 0.114 * $b) > 195);
    };

    $isLightTopbar = $isLightColor($topbarColor);
    $isLightSidebar = $isLightColor($sidebarColor);
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
        --theme-gradient: {{ $themeGradient }};
        --theme-bg: {{ $primaryColor }};
        --theme-text: #ffffff;
        --theme-hover: rgba(5,150,105,0.08);
        --theme-active: rgba(5,150,105,0.15);
        --theme-subtext: #64748B;
        --theme-border: #E2E8F0;
        --theme-logo-sidebar-h: {{ $logoSidebarH }}px;
        --theme-logo-topbar-h: {{ $logoTopbarH }}px;
        --theme-logo-receipt-h: {{ $logoReceiptH }}px;
    }

    /* ═══════════════════════════════════════════════════════════
       0. DYNAMIC LOGO SIZING
       ═══════════════════════════════════════════════════════════ */
    .sidebar-brand-img,
    .sidebar-inner img,
    .likha-sidebar-brand img {
        max-height: var(--theme-logo-sidebar-h) !important;
        width: auto !important;
        object-fit: contain !important;
    }

    .crm-topbar .crm-store-icon img,
    .crm-store-icon-img {
        max-height: var(--theme-logo-topbar-h) !important;
        max-width: var(--theme-logo-topbar-h) !important;
        width: auto !important;
        object-fit: contain !important;
    }

    .thermal-receipt-logo,
    .receipt-header img.store-logo {
        max-height: var(--theme-logo-receipt-h) !important;
        width: auto !important;
        object-fit: contain !important;
    }

    /* ═══════════════════════════════════════════════════════════
       1. BASE TOPBAR (Header & Navigation)
       ═══════════════════════════════════════════════════════════ */
    header.crm-topbar,
    #crmTopbar,
    .crm-topbar,
    .likha-topbar,
    .lms-topbar {
        background-color: var(--theme-topbar) !important;
        background: var(--theme-topbar) !important;
        color: var(--theme-topbar-text) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
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
       1-B. LIGHT TOPBAR (Canva Style / Light Canvas with Vibrant Objects)
       ═══════════════════════════════════════════════════════════ */
    @if($isLightTopbar)
    header.crm-topbar,
    #crmTopbar,
    .crm-topbar,
    .likha-topbar,
    .lms-topbar,
    @endif
    .theme-light-topbar {
        background-color: var(--theme-topbar, #FFFFFF) !important;
        background: var(--theme-topbar, #FFFFFF) !important;
        color: #0F172A !important;
        border-bottom: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.03) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-title-text,
    .crm-topbar .crm-page-title span,
    @endif
    .theme-light-topbar .crm-title-text,
    .theme-light-topbar .crm-page-title span {
        color: #0F172A !important;
        font-weight: 800 !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-subtitle,
    @endif
    .theme-light-topbar .crm-subtitle {
        color: #64748B !important;
        opacity: 1 !important;
    }

    /* Light Topbar: Objects take the colors */
    @if($isLightTopbar)
    .crm-topbar .crm-icon-btn,
    @endif
    .theme-light-topbar .crm-icon-btn {
        background: #FFFFFF !important;
        color: #475569 !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
        transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-icon-btn i,
    @endif
    .theme-light-topbar .crm-icon-btn i {
        color: #475569 !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-icon-btn:hover,
    @endif
    .theme-light-topbar .crm-icon-btn:hover {
        background: rgba(125, 42, 232, 0.08) !important;
        color: var(--theme-primary) !important;
        border-color: var(--theme-primary) !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(125, 42, 232, 0.18) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-icon-btn:hover i,
    @endif
    .theme-light-topbar .crm-icon-btn:hover i {
        color: var(--theme-primary) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-notif-badge,
    @endif
    .theme-light-topbar .crm-notif-badge {
        background: linear-gradient(135deg, #FF007A 0%, var(--theme-primary) 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(255, 0, 122, 0.4) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-store-pill,
    @endif
    .theme-light-topbar .crm-store-pill {
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-store-icon,
    @endif
    .theme-light-topbar .crm-store-icon {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 8px rgba(125, 42, 232, 0.25) !important;
        border-radius: 9px !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-store-icon i,
    @endif
    .theme-light-topbar .crm-store-icon i {
        color: #FFFFFF !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-store-name,
    @endif
    .theme-light-topbar .crm-store-name {
        color: #0F172A !important;
        font-weight: 700 !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-store-status,
    @endif
    .theme-light-topbar .crm-store-status {
        color: #64748B !important;
    }

    /* POS Button in Topbar: Signature Vibrant Gradient Object */
    @if($isLightTopbar)
    .crm-topbar .crm-pos-btn,
    @endif
    .theme-light-topbar .crm-pos-btn {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        border: none !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(125, 42, 232, 0.35) !important;
        font-weight: 700 !important;
        transition: all 0.2s cubic-bezier(0.2, 0.8, 0.2, 1) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-pos-btn:hover,
    @endif
    .theme-light-topbar .crm-pos-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(125, 42, 232, 0.45) !important;
        filter: brightness(1.08);
    }

    @if($isLightTopbar)
    .crm-topbar .crm-user-btn,
    @endif
    .theme-light-topbar .crm-user-btn {
        background: #FFFFFF !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04) !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-user-name,
    @endif
    .theme-light-topbar .crm-user-name {
        color: #0F172A !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-user-role,
    .crm-topbar .crm-chevron,
    @endif
    .theme-light-topbar .crm-user-role,
    .theme-light-topbar .crm-chevron {
        color: #64748B !important;
    }

    @if($isLightTopbar)
    .crm-topbar .crm-avatar,
    @endif
    .theme-light-topbar .crm-avatar {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        box-shadow: 0 2px 8px rgba(125, 42, 232, 0.25) !important;
        font-weight: 800 !important;
    }

    /* ═══════════════════════════════════════════════════════════
       2. BASE SIDEBAR (Drawer & Menu Items)
       ═══════════════════════════════════════════════════════════ */
    .likha-sidebar,
    .lms-sidebar,
    aside#mainSidebar,
    .sidebar-inner {
        background-color: var(--theme-sidebar) !important;
        background: var(--theme-sidebar) !important;
    }

    .likha-sidebar .border-bottom,
    .likha-sidebar .border-top,
    .sidebar-inner .border-bottom,
    .sidebar-inner .border-top,
    .sidebar-footer,
    .sidebar-dropdown {
        border-top: none !important;
        border-bottom: none !important;
        border-left: none !important;
    }

    /* Section Headers */
    .sidebar-section-title,
    .sidebar-heading {
        color: var(--theme-sidebar-text) !important;
        opacity: 0.75 !important;
    }

    /* Inactive Sidebar Links */
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

    /* Active Sidebar Parent Link */
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

    /* Sidebar Brand & Fallback Icon */
    .sidebar-brand-icon {
        background: var(--theme-primary) !important;
        color: #ffffff !important;
    }
    .sidebar-brand-accent {
        color: var(--theme-accent) !important;
    }
    .sidebar-brand-title {
        color: var(--theme-sidebar-text) !important;
    }
    .sidebar-pos-launch-btn {
        background: var(--theme-primary) !important;
        color: #ffffff !important;
    }
    .sidebar-link.active .sidebar-icon-wrap {
        background: rgba(255, 255, 255, 0.22) !important;
        color: var(--theme-sidebar-active-text) !important;
    }

    /* ═══════════════════════════════════════════════════════════
       2-B. LIGHT SIDEBAR (Canva Style / Light Canvas with Vibrant Objects)
       ═══════════════════════════════════════════════════════════ */
    @if($isLightSidebar)
    .likha-sidebar,
    .lms-sidebar,
    aside#mainSidebar,
    .sidebar-inner,
    @endif
    .theme-light-sidebar,
    aside#mainSidebar.theme-light-sidebar,
    .likha-sidebar.theme-light-sidebar,
    .theme-light-sidebar .sidebar-inner {
        background-color: var(--theme-sidebar, #FFFFFF) !important;
        background: var(--theme-sidebar, #FFFFFF) !important;
        border-right: 1px solid #E2E8F0 !important;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.03) !important;
    }

    @if($isLightSidebar)
    .sidebar-brand-wrapper,
    @endif
    .theme-light-sidebar .sidebar-brand-wrapper {
        border-bottom: 1px solid #E2E8F0 !important;
    }

    @if($isLightSidebar)
    .sidebar-brand-title,
    @endif
    .theme-light-sidebar .sidebar-brand-title {
        color: #0F172A !important;
    }

    @if($isLightSidebar)
    .sidebar-brand-accent,
    @endif
    .theme-light-sidebar .sidebar-brand-accent {
        color: var(--theme-primary) !important;
    }

    @if($isLightSidebar)
    .sidebar-brand-sub,
    @endif
    .theme-light-sidebar .sidebar-brand-sub {
        color: #64748B !important;
        opacity: 1 !important;
    }

    @if($isLightSidebar)
    .sidebar-brand-icon,
    @endif
    .theme-light-sidebar .sidebar-brand-icon {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 14px rgba(125, 42, 232, 0.3) !important;
    }

    @if($isLightSidebar)
    .sidebar-branch-strip,
    @endif
    .theme-light-sidebar .sidebar-branch-strip {
        border-bottom: 1px solid #F1F5F9 !important;
    }

    @if($isLightSidebar)
    .sidebar-branch-card,
    @endif
    .theme-light-sidebar .sidebar-branch-card {
        background: #F8FAFC !important;
        border: 1px solid #E2E8F0 !important;
    }

    @if($isLightSidebar)
    .sidebar-branch-name,
    @endif
    .theme-light-sidebar .sidebar-branch-name {
        color: #1E293B !important;
    }

    @if($isLightSidebar)
    .sidebar-section-title,
    @endif
    .theme-light-sidebar .sidebar-section-title {
        color: #94A3B8 !important;
        opacity: 1 !important;
        font-weight: 800 !important;
        font-size: 0.68rem !important;
        letter-spacing: 0.9px !important;
    }

    /* Inactive items on light sidebar */
    @if($isLightSidebar)
    .sidebar-link:not(.active),
    .sidebar-link:not(.active) span,
    .sidebar-link:not(.active) .sidebar-label,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active),
    .theme-light-sidebar .sidebar-link:not(.active) span,
    .theme-light-sidebar .sidebar-link:not(.active) .sidebar-label {
        color: #334155 !important;
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active) .dropdown-icon,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active) .dropdown-icon {
        color: #94A3B8 !important;
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active) .sidebar-icon-wrap,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active) .sidebar-icon-wrap {
        background: #F1F5F9 !important;
        color: #64748B !important;
        border: 1px solid #E2E8F0 !important;
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active) .sidebar-icon,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active) .sidebar-icon {
        color: #64748B !important;
    }

    /* Hover: "sa colors tayo babawi sa mga object not as bg" */
    @if($isLightSidebar)
    .sidebar-link:not(.active):hover,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active):hover {
        background: rgba(125, 42, 232, 0.05) !important;
        color: var(--theme-primary) !important;
        transform: translateX(4px);
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active):hover span,
    .sidebar-link:not(.active):hover .sidebar-label,
    .sidebar-link:not(.active):hover .dropdown-icon,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active):hover span,
    .theme-light-sidebar .sidebar-link:not(.active):hover .sidebar-label,
    .theme-light-sidebar .sidebar-link:not(.active):hover .dropdown-icon {
        color: var(--theme-primary) !important;
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active):hover .sidebar-icon-wrap,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active):hover .sidebar-icon-wrap {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        border-color: transparent !important;
        box-shadow: 0 4px 12px rgba(125, 42, 232, 0.3) !important;
    }

    @if($isLightSidebar)
    .sidebar-link:not(.active):hover .sidebar-icon,
    @endif
    .theme-light-sidebar .sidebar-link:not(.active):hover .sidebar-icon {
        color: #FFFFFF !important;
    }

    /* Active: Vibrant Signature Gradient Object */
    @if($isLightSidebar)
    .sidebar-link.active,
    @endif
    .theme-light-sidebar .sidebar-link.active {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        font-weight: 700 !important;
        box-shadow: 0 4px 18px rgba(125, 42, 232, 0.35) !important;
    }

    @if($isLightSidebar)
    .sidebar-link.active *,
    .sidebar-link.active span,
    .sidebar-link.active .sidebar-label,
    .sidebar-link.active .sidebar-icon,
    .sidebar-link.active .dropdown-icon,
    @endif
    .theme-light-sidebar .sidebar-link.active *,
    .theme-light-sidebar .sidebar-link.active span,
    .theme-light-sidebar .sidebar-link.active .sidebar-label,
    .theme-light-sidebar .sidebar-link.active .sidebar-icon,
    .theme-light-sidebar .sidebar-link.active .dropdown-icon {
        color: #FFFFFF !important;
    }

    @if($isLightSidebar)
    .sidebar-link.active .sidebar-icon-wrap,
    @endif
    .theme-light-sidebar .sidebar-link.active .sidebar-icon-wrap {
        background: rgba(255, 255, 255, 0.24) !important;
        color: #FFFFFF !important;
        border-color: transparent !important;
    }

    /* Submenus on light sidebar */
    @if($isLightSidebar)
    .sidebar-dropdown,
    @endif
    .theme-light-sidebar .sidebar-dropdown {
        border-left: 2px solid #E2E8F0 !important;
        padding-left: 10px !important;
        margin: 4px 0 8px 14px !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink,
    @endif
    .theme-light-sidebar .sidebar-sublink {
        min-height: 40px !important;
        padding: 8px 10px !important;
        font-size: 0.86rem !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        margin-bottom: 3px !important;
        gap: 9px !important;
        white-space: nowrap !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink:not(.active),
    .sidebar-sublink:not(.active) span,
    @endif
    .theme-light-sidebar .sidebar-sublink:not(.active),
    .theme-light-sidebar .sidebar-sublink:not(.active) span {
        color: #334155 !important;
        font-weight: 600 !important;
        opacity: 1 !important;
        white-space: nowrap !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink:not(.active) .sidebar-subicon,
    @endif
    .theme-light-sidebar .sidebar-sublink:not(.active) .sidebar-subicon {
        color: #64748B !important;
        font-size: 1.05rem !important;
        width: 20px !important;
        height: 20px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 0.95 !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink:not(.active):hover,
    @endif
    .theme-light-sidebar .sidebar-sublink:not(.active):hover {
        background: rgba(125, 42, 232, 0.08) !important;
        color: var(--theme-primary) !important;
        transform: translateX(4px) !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink:not(.active):hover *,
    .sidebar-sublink:not(.active):hover span,
    .sidebar-sublink:not(.active):hover .sidebar-subicon,
    @endif
    .theme-light-sidebar .sidebar-sublink:not(.active):hover *,
    .theme-light-sidebar .sidebar-sublink:not(.active):hover span,
    .theme-light-sidebar .sidebar-sublink:not(.active):hover .sidebar-subicon {
        color: var(--theme-primary) !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink.active,
    @endif
    .theme-light-sidebar .sidebar-sublink.active {
        background: linear-gradient(135deg, rgba(125, 42, 232, 0.14), rgba(0, 196, 204, 0.14)) !important;
        color: var(--theme-primary) !important;
        font-weight: 700 !important;
        border: 1px solid rgba(125, 42, 232, 0.25) !important;
    }

    @if($isLightSidebar)
    .sidebar-sublink.active *,
    .sidebar-sublink.active span,
    .sidebar-sublink.active .sidebar-subicon,
    @endif
    .theme-light-sidebar .sidebar-sublink.active *,
    .theme-light-sidebar .sidebar-sublink.active span,
    .theme-light-sidebar .sidebar-sublink.active .sidebar-subicon {
        color: var(--theme-primary) !important;
    }
    .theme-light-sidebar .sidebar-sublink.active * {
        color: var(--theme-primary) !important;
    }

    /* Footer POS Button */
    @if($isLightSidebar)
    .sidebar-footer,
    @endif
    .theme-light-sidebar .sidebar-footer {
        border-top: 1px solid #E2E8F0 !important;
    }

    @if($isLightSidebar)
    .sidebar-pos-launch-btn,
    @endif
    .theme-light-sidebar .sidebar-pos-launch-btn {
        background: linear-gradient(135deg, var(--theme-primary), var(--theme-accent)) !important;
        color: #FFFFFF !important;
        box-shadow: 0 4px 16px rgba(125, 42, 232, 0.35) !important;
    }

    @if($isLightSidebar)
    .sidebar-pos-launch-btn:hover,
    @endif
    .theme-light-sidebar .sidebar-pos-launch-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 22px rgba(125, 42, 232, 0.45) !important;
        filter: brightness(1.08);
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
