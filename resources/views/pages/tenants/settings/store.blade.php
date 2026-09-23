@extends('layouts.app')

@section('title', 'Store Settings & POS Branding | LikhaPOS')

@section('content')
@php
    $themeSettings = $tenant->theme_settings ?? [];
    $crmSettings = $tenant->crm_settings ?? [];

    $currentPreset = $themeSettings['preset'] ?? 'emerald';
    $primaryColor = $themeSettings['primary_color'] ?? '#059669';
    $topbarColor = $themeSettings['topbar_color'] ?? '#064E3B';
    $topbarTextColor = $themeSettings['topbar_text_color'] ?? '#FFFFFF';
    $sidebarColor = $themeSettings['sidebar_color'] ?? '#0F172A';
    $sidebarTextColor = $themeSettings['sidebar_text_color'] ?? '#CBD5E1';
    $sidebarActive = $themeSettings['sidebar_active_color'] ?? '#059669';
    $sidebarActiveTextColor = $themeSettings['sidebar_active_text_color'] ?? '#FFFFFF';
    $accentColor = $themeSettings['accent_color'] ?? '#10B981';

    // Dynamic Logo Dimensions
    $logoSidebarH = $themeSettings['logo_sidebar_height'] ?? 38;
    $logoTopbarH = $themeSettings['logo_topbar_height'] ?? 32;
    $logoReceiptH = $themeSettings['logo_receipt_height'] ?? 52;

    $pointsPerPeso = (float)($crmSettings['points_per_peso'] ?? 0.01);
    if ($pointsPerPeso > 10) $pointsPerPeso = 0.01;
    $defaultCreditLimit = $crmSettings['default_credit_limit'] ?? 5000;
    $smsReceiptEnabled = isset($crmSettings['sms_receipt_enabled']) ? (bool)$crmSettings['sms_receipt_enabled'] : true;
    $smsUtangReminderEnabled = isset($crmSettings['sms_utang_reminder_enabled']) ? (bool)$crmSettings['sms_utang_reminder_enabled'] : true;

    // Horizontal Logo (Rectangle)
    $hasRectLogo = !empty($tenant->logo) && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo);
    $rectLogoUrl = $hasRectLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo) : asset('images/logo.png');

    // Square Logo (App Icon / Stamp)
    $hasSquareLogo = !empty($tenant->logo_square) && \Illuminate\Support\Facades\Storage::disk('public')->exists($tenant->logo_square);
    $squareLogoUrl = $hasSquareLogo ? \Illuminate\Support\Facades\Storage::url($tenant->logo_square) : asset('images/ic_launcher.png');

    // 57 Curated POS Industry Gradient Swatches with Zero Text Overflow
    $themePresets = [
        // 🎨 0. Canva Style & Signature Glow (Light Canvas with Vibrant Objects)
        ['id' => 'canva_vibrant',   'category' => 'canva',    'name' => 'Canva Signature (Light Canvas)',       'gradient' => 'linear-gradient(135deg, #7D2AE8 0%, #5E17EB 50%, #00C4CC 100%)', 'topbar' => '#FFFFFF', 'topbar_text' => '#0F172A', 'primary' => '#7D2AE8', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#475569', 'sidebar_active' => '#7D2AE8', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#00C4CC'],
        ['id' => 'canva_electric',  'category' => 'canva',    'name' => 'Canva Cyan Aurora (Light Canvas)',     'gradient' => 'linear-gradient(135deg, #00C4CC 0%, #7D2AE8 100%)',               'topbar' => '#FFFFFF', 'topbar_text' => '#0F172A', 'primary' => '#00C4CC', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#475569', 'sidebar_active' => '#00C4CC', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#7D2AE8'],
        ['id' => 'canva_pop',       'category' => 'canva',    'name' => 'Canva Magenta Pop (Light Studio)',      'gradient' => 'linear-gradient(135deg, #7D2AE8 0%, #FF007A 50%, #00C4CC 100%)', 'topbar' => '#FAFBFE', 'topbar_text' => '#0F172A', 'primary' => '#FF007A', 'sidebar' => '#FAFBFE', 'sidebar_text' => '#475569', 'sidebar_active' => '#7D2AE8', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#00C4CC'],
        ['id' => 'canva_sunset',    'category' => 'canva',    'name' => 'Canva Sunset Glow (Light Canvas)',      'gradient' => 'linear-gradient(135deg, #7D2AE8 0%, #FF6B4A 100%)',               'topbar' => '#FFFFFF', 'topbar_text' => '#0F172A', 'primary' => '#7D2AE8', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#475569', 'sidebar_active' => '#7D2AE8', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FF6B4A'],
        ['id' => 'canva_mint',      'category' => 'canva',    'name' => 'Canva Neon Mint (Light Canvas)',        'gradient' => 'linear-gradient(135deg, #6C5CE7 0%, #00D2D3 100%)',               'topbar' => '#FFFFFF', 'topbar_text' => '#0F172A', 'primary' => '#6C5CE7', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#475569', 'sidebar_active' => '#6C5CE7', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#00D2D3'],

        // 🌟 1. Featured & Modern SaaS (Gradients)
        ['id' => 'emerald',         'category' => 'featured', 'name' => 'Emerald Aurora (Fresh Market)',        'gradient' => 'linear-gradient(135deg, #064E3B 0%, #059669 50%, #10B981 100%)', 'topbar' => '#064E3B', 'topbar_text' => '#FFFFFF', 'primary' => '#059669', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#059669', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#10B981'],
        ['id' => 'cyber_neon',      'category' => 'featured', 'name' => 'Cyber Neon Wave (High Contrast)',      'gradient' => 'linear-gradient(135deg, #090D16 0%, #10B981 60%, #06B6D4 100%)', 'topbar' => '#090D16', 'topbar_text' => '#10B981', 'primary' => '#10B981', 'sidebar' => '#030712', 'sidebar_text' => '#6EE7B7', 'sidebar_active' => '#10B981', 'sidebar_active_text' => '#000000', 'accent' => '#06B6D4'],
        ['id' => 'sunset_coral',    'category' => 'featured', 'name' => 'Sunset Coral Fire (Vibrant)',          'gradient' => 'linear-gradient(135deg, #7C2D12 0%, #EA580C 50%, #F59E0B 100%)', 'topbar' => '#7C2D12', 'topbar_text' => '#FFFFFF', 'primary' => '#EA580C', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#EA580C', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F97316'],
        ['id' => 'indigo_cosmic',   'category' => 'featured', 'name' => 'Cosmic Indigo Galaxy',                 'gradient' => 'linear-gradient(135deg, #1E1B4B 0%, #4F46E5 50%, #818CF8 100%)', 'topbar' => '#1E1B4B', 'topbar_text' => '#FFFFFF', 'primary' => '#4F46E5', 'sidebar' => '#0F172A', 'sidebar_text' => '#C7D2FE', 'sidebar_active' => '#4F46E5', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#818CF8'],
        ['id' => 'rose_glam',       'category' => 'featured', 'name' => 'Rose Gold Deluxe (Boutique)',          'gradient' => 'linear-gradient(135deg, #4C0519 0%, #DB2777 50%, #F472B6 100%)', 'topbar' => '#4C0519', 'topbar_text' => '#FFFFFF', 'primary' => '#DB2777', 'sidebar' => '#18181B', 'sidebar_text' => '#E4E4E7', 'sidebar_active' => '#DB2777', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB7185'],
        ['id' => 'oceanic_surge',   'category' => 'featured', 'name' => 'Pacific Blue Wave',                    'gradient' => 'linear-gradient(135deg, #0C4A6E 0%, #0284C7 50%, #38BDF8 100%)', 'topbar' => '#0C4A6E', 'topbar_text' => '#FFFFFF', 'primary' => '#0284C7', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#0284C7', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#38BDF8'],
        ['id' => 'gold_obsidian',   'category' => 'featured', 'name' => 'Obsidian Royal Gold',                  'gradient' => 'linear-gradient(135deg, #1C1917 0%, #D97706 60%, #FDE047 100%)', 'topbar' => '#1C1917', 'topbar_text' => '#FACC15', 'primary' => '#D97706', 'sidebar' => '#0C0A09', 'sidebar_text' => '#FEF08A', 'sidebar_active' => '#D97706', 'sidebar_active_text' => '#000000', 'accent' => '#FACC15'],
        ['id' => 'amethyst_luxe',   'category' => 'featured', 'name' => 'Amethyst Velvet Purple',               'gradient' => 'linear-gradient(135deg, #3B0764 0%, #9333EA 50%, #C084FC 100%)', 'topbar' => '#3B0764', 'topbar_text' => '#FFFFFF', 'primary' => '#9333EA', 'sidebar' => '#1E1B4B', 'sidebar_text' => '#EDE9FE', 'sidebar_active' => '#9333EA', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#C084FC'],

        // 🛒 2. Retail, Supermarkets & Grocery
        ['id' => 'fresh_mint',      'category' => 'retail',   'name' => 'Fresh Supermarket Mint',               'gradient' => 'linear-gradient(135deg, #065F46 0%, #10B981 50%, #6EE7B7 100%)', 'topbar' => '#065F46', 'topbar_text' => '#FFFFFF', 'primary' => '#10B981', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#10B981', 'sidebar_active_text' => '#000000', 'accent' => '#34D399'],
        ['id' => 'mart_orange',     'category' => 'retail',   'name' => '7-Eleven Citrus Mart',                 'gradient' => 'linear-gradient(135deg, #9A3412 0%, #EA580C 50%, #FDBA74 100%)', 'topbar' => '#7C2D12', 'topbar_text' => '#FFFFFF', 'primary' => '#EA580C', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#EA580C', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB923C'],
        ['id' => 'ocean',           'category' => 'retail',   'name' => 'Sapphire Hypermarket',                 'gradient' => 'linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #60A5FA 100%)', 'topbar' => '#1E3A8A', 'topbar_text' => '#FFFFFF', 'primary' => '#2563EB', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#2563EB', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#60A5FA'],
        ['id' => 'robinsons_red',   'category' => 'retail',   'name' => 'Ruby Fresh Supercenter',               'gradient' => 'linear-gradient(135deg, #881337 0%, #DC2626 50%, #F87171 100%)', 'topbar' => '#881337', 'topbar_text' => '#FFFFFF', 'primary' => '#DC2626', 'sidebar' => '#111827', 'sidebar_text' => '#FCA5A5', 'sidebar_active' => '#DC2626', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F87171'],
        ['id' => 'nordic_teal',     'category' => 'retail',   'name' => 'Nordic Forest Teal',                   'gradient' => 'linear-gradient(135deg, #134E4A 0%, #0D9488 50%, #5EEAD4 100%)', 'topbar' => '#134E4A', 'topbar_text' => '#FFFFFF', 'primary' => '#0D9488', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#0D9488', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#2DD4BF'],
        ['id' => 'sun_gold',        'category' => 'retail',   'name' => 'Golden Horizon Superstore',            'gradient' => 'linear-gradient(135deg, #78350F 0%, #D97706 50%, #FCD34D 100%)', 'topbar' => '#78350F', 'topbar_text' => '#FFFFFF', 'primary' => '#D97706', 'sidebar' => '#0F172A', 'sidebar_text' => '#FEF3C7', 'sidebar_active' => '#D97706', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FBBF24'],
        ['id' => 'nordic_light',    'category' => 'retail',   'name' => 'Nordic Porcelain Ice',                 'gradient' => 'linear-gradient(135deg, #0369A1 0%, #0284C7 50%, #38BDF8 100%)', 'topbar' => '#F8FAFC', 'topbar_text' => '#0F172A', 'primary' => '#0284C7', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#1E293B', 'sidebar_active' => '#0284C7', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#38BDF8'],
        ['id' => 'tropic_mango',    'category' => 'retail',   'name' => 'Tropic Mango Mart',                    'gradient' => 'linear-gradient(135deg, #B45309 0%, #F59E0B 50%, #FEF08A 100%)', 'topbar' => '#78350F', 'topbar_text' => '#FFFFFF', 'primary' => '#F59E0B', 'sidebar' => '#18181B', 'sidebar_text' => '#FEF3C7', 'sidebar_active' => '#F59E0B', 'sidebar_active_text' => '#000000', 'accent' => '#FDE047'],
        ['id' => 'clean_white',     'category' => 'retail',   'name' => 'Minimalist Pure White',                'gradient' => 'linear-gradient(135deg, #334155 0%, #059669 60%, #10B981 100%)', 'topbar' => '#0F172A', 'topbar_text' => '#FFFFFF', 'primary' => '#059669', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#334155', 'sidebar_active' => '#059669', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#10B981'],

        // ☕ 3. Café, Bistro & Bakery
        ['id' => 'espresso',        'category' => 'cafe',     'name' => 'Espresso & Dark Chocolate',            'gradient' => 'linear-gradient(135deg, #271710 0%, #78350F 50%, #B45309 100%)', 'topbar' => '#3E2723', 'topbar_text' => '#FFFFFF', 'primary' => '#B45309', 'sidebar' => '#1C1917', 'sidebar_text' => '#E7E5E4', 'sidebar_active' => '#B45309', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F59E0B'],
        ['id' => 'caramel_toffee',  'category' => 'cafe',     'name' => 'Caramel Macchiato Toffee',             'gradient' => 'linear-gradient(135deg, #451A03 0%, #B45309 50%, #FBBF24 100%)', 'topbar' => '#451A03', 'topbar_text' => '#FFFFFF', 'primary' => '#D97706', 'sidebar' => '#1C1917', 'sidebar_text' => '#FEF3C7', 'sidebar_active' => '#D97706', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FBBF24'],
        ['id' => 'matcha',          'category' => 'cafe',     'name' => 'Kyoto Matcha & Sage',                  'gradient' => 'linear-gradient(135deg, #052E16 0%, #16A34A 50%, #86EFAC 100%)', 'topbar' => '#14532D', 'topbar_text' => '#FFFFFF', 'primary' => '#16A34A', 'sidebar' => '#052E16', 'sidebar_text' => '#DCFCE7', 'sidebar_active' => '#16A34A', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#4ADE80'],
        ['id' => 'bakery_rose',     'category' => 'cafe',     'name' => 'French Patisserie Strawberry',         'gradient' => 'linear-gradient(135deg, #701A75 0%, #DB2777 50%, #F9A8D4 100%)', 'topbar' => '#701A75', 'topbar_text' => '#FFFFFF', 'primary' => '#DB2777', 'sidebar' => '#18181B', 'sidebar_text' => '#E4E4E7', 'sidebar_active' => '#DB2777', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F472B6'],
        ['id' => 'wine_crimson',    'category' => 'cafe',     'name' => 'Royal Vintage Merlot',                 'gradient' => 'linear-gradient(135deg, #4C0519 0%, #9F1239 50%, #FDA4AF 100%)', 'topbar' => '#4C0519', 'topbar_text' => '#FFFFFF', 'primary' => '#BE123C', 'sidebar' => '#1C030B', 'sidebar_text' => '#FECDD3', 'sidebar_active' => '#BE123C', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB7185'],
        ['id' => 'tiramisu',        'category' => 'cafe',     'name' => 'Tiramisu Crema Mocha',                 'gradient' => 'linear-gradient(135deg, #382314 0%, #92400E 50%, #FDE68A 100%)', 'topbar' => '#451A03', 'topbar_text' => '#FFFFFF', 'primary' => '#B45309', 'sidebar' => '#1C1917', 'sidebar_text' => '#FEF3C7', 'sidebar_active' => '#B45309', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F59E0B'],
        ['id' => 'berry_smoothie',  'category' => 'cafe',     'name' => 'Wild Acai Berry Smoothie',             'gradient' => 'linear-gradient(135deg, #4A044E 0%, #A21CAF 50%, #E879F9 100%)', 'topbar' => '#4A044E', 'topbar_text' => '#FFFFFF', 'primary' => '#A21CAF', 'sidebar' => '#18181B', 'sidebar_text' => '#F5D0FE', 'sidebar_active' => '#A21CAF', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#E879F9'],
        ['id' => 'honey_cinnamon',  'category' => 'cafe',     'name' => 'Artisan Cinnamon Honey',               'gradient' => 'linear-gradient(135deg, #713F12 0%, #CA8A04 50%, #FEF08A 100%)', 'topbar' => '#713F12', 'topbar_text' => '#FFFFFF', 'primary' => '#CA8A04', 'sidebar' => '#1C1917', 'sidebar_text' => '#FEF08A', 'sidebar_active' => '#CA8A04', 'sidebar_active_text' => '#000000', 'accent' => '#FDE047'],

        // 💊 4. Pharmacy, Health & Beauty
        ['id' => 'pharma',          'category' => 'pharma',   'name' => 'MedCare Cyan Drugstore',               'gradient' => 'linear-gradient(135deg, #164E63 0%, #0891B2 50%, #67E8F9 100%)', 'topbar' => '#0E7490', 'topbar_text' => '#FFFFFF', 'primary' => '#0891B2', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#0891B2', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#22D3EE'],
        ['id' => 'clinical_aqua',   'category' => 'pharma',   'name' => 'Clinical Rx Pure Aqua',                'gradient' => 'linear-gradient(135deg, #115E59 0%, #0D9488 50%, #99F6E4 100%)', 'topbar' => '#115E59', 'topbar_text' => '#FFFFFF', 'primary' => '#0D9488', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#0D9488', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#2DD4BF'],
        ['id' => 'mercury',         'category' => 'pharma',   'name' => 'Mercury Blue Care',                    'gradient' => 'linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #93C5FD 100%)', 'topbar' => '#1E40AF', 'topbar_text' => '#FFFFFF', 'primary' => '#2563EB', 'sidebar' => '#0F172A', 'sidebar_text' => '#DBEAFE', 'sidebar_active' => '#2563EB', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#60A5FA'],
        ['id' => 'cosmetics',       'category' => 'pharma',   'name' => 'Beauty Velvet Orchid',                 'gradient' => 'linear-gradient(135deg, #4C1D95 0%, #7C3AED 50%, #C4B5FD 100%)', 'topbar' => '#581C87', 'topbar_text' => '#FFFFFF', 'primary' => '#9333EA', 'sidebar' => '#1E1B4B', 'sidebar_text' => '#EDE9FE', 'sidebar_active' => '#9333EA', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#C084FC'],
        ['id' => 'blush',           'category' => 'pharma',   'name' => 'Blush Velvet Salon',                   'gradient' => 'linear-gradient(135deg, #831843 0%, #EC4899 50%, #FBCFE8 100%)', 'topbar' => '#9D174D', 'topbar_text' => '#FFFFFF', 'primary' => '#EC4899', 'sidebar' => '#FFFFFF', 'sidebar_text' => '#374151', 'sidebar_active' => '#EC4899', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F472B6'],
        ['id' => 'herbal_green',    'category' => 'pharma',   'name' => 'Herbal Botanic Wellness',              'gradient' => 'linear-gradient(135deg, #064E3B 0%, #059669 50%, #A7F3D0 100%)', 'topbar' => '#065F46', 'topbar_text' => '#FFFFFF', 'primary' => '#10B981', 'sidebar' => '#064E3B', 'sidebar_text' => '#D1FAE5', 'sidebar_active' => '#10B981', 'sidebar_active_text' => '#000000', 'accent' => '#34D399'],
        ['id' => 'sakura_glow',     'category' => 'pharma',   'name' => 'Sakura Petal Glow',                    'gradient' => 'linear-gradient(135deg, #701A75 0%, #D946EF 50%, #F5D0FE 100%)', 'topbar' => '#701A75', 'topbar_text' => '#FFFFFF', 'primary' => '#D946EF', 'sidebar' => '#1E1B4B', 'sidebar_text' => '#F5D0FE', 'sidebar_active' => '#D946EF', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#E879F9'],
        ['id' => 'pure_dermatology','category' => 'pharma',   'name' => 'Pure Dermatology Clean',               'gradient' => 'linear-gradient(135deg, #075985 0%, #0284C7 50%, #BAE6FD 100%)', 'topbar' => '#075985', 'topbar_text' => '#FFFFFF', 'primary' => '#0284C7', 'sidebar' => '#F8FAFC', 'sidebar_text' => '#334155', 'sidebar_active' => '#0284C7', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#38BDF8'],

        // 🛠️ 5. Hardware, Automotive & Sports
        ['id' => 'hardware',        'category' => 'hardware', 'name' => 'Heavy Duty CAT Amber',                 'gradient' => 'linear-gradient(135deg, #1E293B 0%, #D97706 60%, #FCD34D 100%)', 'topbar' => '#1E293B', 'topbar_text' => '#FFFFFF', 'primary' => '#F59E0B', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#F59E0B', 'sidebar_active_text' => '#000000', 'accent' => '#FBBF24'],
        ['id' => 'dewalt_yellow',   'category' => 'hardware', 'name' => 'DeWalt High-Torque Gold',              'gradient' => 'linear-gradient(135deg, #09090B 0%, #CA8A04 60%, #FEF08A 100%)', 'topbar' => '#18181B', 'topbar_text' => '#FACC15', 'primary' => '#EAB308', 'sidebar' => '#09090B', 'sidebar_text' => '#FEF08A', 'sidebar_active' => '#EAB308', 'sidebar_active_text' => '#000000', 'accent' => '#FDE047'],
        ['id' => 'crimson',         'category' => 'hardware', 'name' => 'Apex Turbo Racing Red',                'gradient' => 'linear-gradient(135deg, #4C0519 0%, #E11D48 50%, #FDA4AF 100%)', 'topbar' => '#881337', 'topbar_text' => '#FFFFFF', 'primary' => '#E11D48', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#E11D48', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB7185'],
        ['id' => 'volt_lime',       'category' => 'hardware', 'name' => 'High-Volt Neon Energy',                'gradient' => 'linear-gradient(135deg, #020617 0%, #65A30D 60%, #BEF264 100%)', 'topbar' => '#0B0F19', 'topbar_text' => '#A3E635', 'primary' => '#84CC16', 'sidebar' => '#020617', 'sidebar_text' => '#D9F99D', 'sidebar_active' => '#84CC16', 'sidebar_active_text' => '#000000', 'accent' => '#BEF264'],
        ['id' => 'steel_slate',     'category' => 'hardware', 'name' => 'Titanium Slate Carbon',                'gradient' => 'linear-gradient(135deg, #0F172A 0%, #475569 50%, #94A3B8 100%)', 'topbar' => '#1E293B', 'topbar_text' => '#FFFFFF', 'primary' => '#475569', 'sidebar' => '#0F172A', 'sidebar_text' => '#94A3B8', 'sidebar_active' => '#475569', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#94A3B8'],
        ['id' => 'sonic_electric',  'category' => 'hardware', 'name' => 'Sonic Electric Blue',                  'gradient' => 'linear-gradient(135deg, #172554 0%, #1D4ED8 50%, #60A5FA 100%)', 'topbar' => '#172554', 'topbar_text' => '#FFFFFF', 'primary' => '#1D4ED8', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#1D4ED8', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#38BDF8'],
        ['id' => 'magma_torch',     'category' => 'hardware', 'name' => 'Magma Forge Copper',                   'gradient' => 'linear-gradient(135deg, #431407 0%, #C2410C 50%, #FDBA74 100%)', 'topbar' => '#431407', 'topbar_text' => '#FFFFFF', 'primary' => '#C2410C', 'sidebar' => '#0F172A', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#C2410C', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB923C'],
        ['id' => 'gunmetal_red',    'category' => 'hardware', 'name' => 'Gunmetal Machined Steel',              'gradient' => 'linear-gradient(135deg, #18181B 0%, #B91C1C 60%, #EF4444 100%)', 'topbar' => '#18181B', 'topbar_text' => '#FFFFFF', 'primary' => '#DC2626', 'sidebar' => '#09090B', 'sidebar_text' => '#CBD5E1', 'sidebar_active' => '#DC2626', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#F87171'],

        // 🌙 6. Midnight, Cyberpunk & OLED Dark
        ['id' => 'dark',            'category' => 'dark',     'name' => 'Stealth AMOLED Obsidian (OLED)',       'gradient' => 'linear-gradient(135deg, #000000 0%, #18181B 50%, #10B981 100%)', 'topbar' => '#000000', 'topbar_text' => '#FFFFFF', 'primary' => '#10B981', 'sidebar' => '#09090B', 'sidebar_text' => '#A1A1AA', 'sidebar_active' => '#10B981', 'sidebar_active_text' => '#000000', 'accent' => '#34D399'],
        ['id' => 'abyss',           'category' => 'dark',     'name' => 'Oceanic Abyss Cyan',                   'gradient' => 'linear-gradient(135deg, #020617 0%, #075985 50%, #38BDF8 100%)', 'topbar' => '#020617', 'topbar_text' => '#FFFFFF', 'primary' => '#38BDF8', 'sidebar' => '#0B1220', 'sidebar_text' => '#BAE6FD', 'sidebar_active' => '#38BDF8', 'sidebar_active_text' => '#000000', 'accent' => '#7DD3FC'],
        ['id' => 'tokyo_vapor',     'category' => 'dark',     'name' => 'Tokyo Vaporwave Magenta',              'gradient' => 'linear-gradient(135deg, #1E1B4B 0%, #A21CAF 50%, #06B6D4 100%)', 'topbar' => '#1E1B4B', 'topbar_text' => '#FFFFFF', 'primary' => '#C026D3', 'sidebar' => '#0F172A', 'sidebar_text' => '#F5D0FE', 'sidebar_active' => '#C026D3', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#22D3EE'],
        ['id' => 'matrix_terminal', 'category' => 'dark',     'name' => 'Matrix Terminal Glow',                 'gradient' => 'linear-gradient(135deg, #022C22 0%, #059669 50%, #34D399 100%)', 'topbar' => '#022C22', 'topbar_text' => '#34D399', 'primary' => '#10B981', 'sidebar' => '#02140D', 'sidebar_text' => '#6EE7B7', 'sidebar_active' => '#10B981', 'sidebar_active_text' => '#000000', 'accent' => '#6EE7B7'],
        ['id' => 'midnight_eclipse','category' => 'dark',     'name' => 'Midnight Eclipse Violet',              'gradient' => 'linear-gradient(135deg, #0F0E17 0%, #581C87 50%, #C084FC 100%)', 'topbar' => '#0F0E17', 'topbar_text' => '#C084FC', 'primary' => '#7E22CE', 'sidebar' => '#09070F', 'sidebar_text' => '#EDE9FE', 'sidebar_active' => '#7E22CE', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#C084FC'],
        ['id' => 'aurora_borealis', 'category' => 'dark',     'name' => 'Northern Lights Borealis',             'gradient' => 'linear-gradient(135deg, #042F2E 0%, #0D9488 40%, #06B6D4 70%, #10B981 100%)', 'topbar' => '#042F2E', 'topbar_text' => '#5EEAD4', 'primary' => '#0D9488', 'sidebar' => '#021A1A', 'sidebar_text' => '#99F6E4', 'sidebar_active' => '#0D9488', 'sidebar_active_text' => '#000000', 'accent' => '#2DD4BF'],
        ['id' => 'solar_flare',     'category' => 'dark',     'name' => 'Solar Flare Twilight',                 'gradient' => 'linear-gradient(135deg, #1C0A00 0%, #7C2D12 40%, #EA580C 70%, #FACC15 100%)', 'topbar' => '#1C0A00', 'topbar_text' => '#FED7AA', 'primary' => '#EA580C', 'sidebar' => '#0C0501', 'sidebar_text' => '#FFEDD5', 'sidebar_active' => '#EA580C', 'sidebar_active_text' => '#000000', 'accent' => '#FDE047'],
        ['id' => 'supernova_plasma','category' => 'dark',     'name' => 'Supernova Cosmic Plasma',              'gradient' => 'linear-gradient(135deg, #2E0854 0%, #7E22CE 50%, #F43F5E 100%)', 'topbar' => '#2E0854', 'topbar_text' => '#FBCFE8', 'primary' => '#9333EA', 'sidebar' => '#130324', 'sidebar_text' => '#F5D0FE', 'sidebar_active' => '#9333EA', 'sidebar_active_text' => '#FFFFFF', 'accent' => '#FB7185'],
    ];
@endphp

<div class="container-fluid px-3 px-md-4 py-3" style="max-width: 1400px; margin: 0 auto;">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-3 bg-success text-white" role="alert">
            <i class="bi bi-check-circle-fill fs-4 flex-shrink-0"></i>
            <div class="flex-grow-1 fw-semibold">
                {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-3 bg-danger text-white" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
            <div class="flex-grow-1 fw-semibold">
                {{ session('error') }}
            </div>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm p-3 mb-3" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1 text-danger fw-bold">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <span>Please check the following errors:</span>
            </div>
            <ul class="mb-0 ps-3 small text-danger">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Clean Executive Page Header --}}
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
        <div>
            <h4 class="fw-black text-dark mb-1 font-mono" style="letter-spacing:-0.5px;">Store Settings &amp; POS Branding</h4>
            <p class="text-muted extra-small mb-0">Customize store logos, dynamic logo sizing, live POS color swatches, and business profile</p>
        </div>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" id="btnPreviewReceipt" class="btn btn-outline-secondary bg-white rounded-3 px-3 py-2 fw-bold extra-small shadow-xs hover-lift d-flex align-items-center gap-1.5">
                <i class="bi bi-receipt-cutoff text-primary"></i>
                <span>Preview Thermal Receipt</span>
            </button>

            <button type="button" onclick="document.getElementById('storeSettingsForm').requestSubmit();" class="btn btn-success rounded-3 px-4 py-2 fw-bold text-white extra-small shadow-sm hover-lift d-flex align-items-center gap-2" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;">
                <i class="bi bi-check-circle-fill"></i>
                <span>Save All Settings</span>
            </button>
        </div>
    </div>

    {{-- Main Settings Form --}}
    <form id="storeSettingsForm" action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION 1: DUAL LOGOS & DYNAMIC LOGO SIZING (CSS)
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#EFF6FF;color:#2563EB;">
                        <i class="bi bi-images fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-black text-dark mb-0 font-mono">Store Brand Logos &amp; Dynamic Sizing (CSS)</h6>
                        <small class="text-muted extra-small">Upload your rectangle &amp; square logos, then fine-tune their display heights in real-time</small>
                    </div>
                </div>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1 font-mono fw-bold extra-small">
                    Responsive Logo CSS
                </span>
            </div>

            <div class="row g-4 mb-3">
                {{-- Slot 1: Horizontal / Rectangle Banner Logo --}}
                <div class="col-md-6">
                    <div class="p-3.5 rounded-4 border bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label extra-small fw-bold text-uppercase text-dark mb-0 font-mono">
                                    <i class="bi bi-aspect-ratio text-primary me-1"></i> 1. Horizontal Logo (Rectangle)
                                </label>
                                <span class="badge bg-white text-muted border extra-small font-mono">Topbar &amp; Sidebar</span>
                            </div>

                            {{-- Preview Box --}}
                            <div class="logo-preview-box rounded-3 border bg-white p-2 text-center position-relative mb-2.5 d-flex align-items-center justify-content-center" style="min-height: 85px;">
                                <img id="rectLogoPreview" src="{{ $rectLogoUrl }}" alt="Horizontal Logo" style="max-height: {{ $logoSidebarH }}px; max-width: 100%; width: auto; object-fit: contain; transition: max-height 0.2s ease;">
                                
                                <button type="button" id="btnRemoveRectLogo" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2 shadow-xs {{ $hasRectLogo ? '' : 'd-none' }}" title="Remove Horizontal Logo" style="width:22px;height:22px;padding:0;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-x-lg" style="font-size:0.6rem;"></i>
                                </button>
                            </div>
                            <input type="hidden" name="remove_logo" id="removeRectLogoInput" value="0">
                        </div>

                        <div>
                            <label for="rectLogoInput" class="btn btn-sm btn-white border rounded-3 w-100 py-1.5 extra-small fw-bold text-dark shadow-xs hover-lift cursor-pointer d-flex align-items-center justify-content-center gap-1.5 mb-1">
                                <i class="bi bi-cloud-arrow-up-fill text-primary"></i>
                                <span id="rectUploadBtnText">{{ $hasRectLogo ? 'Replace Horizontal Logo' : 'Upload Horizontal Logo' }}</span>
                            </label>
                            <input type="file" id="rectLogoInput" name="logo" class="d-none" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            <small class="text-muted extra-small d-block text-center font-mono">Recommended: 3:1 or 4:1 Ratio • Max 10MB</small>
                        </div>
                    </div>
                </div>

                {{-- Slot 2: Square App Icon / Receipt Stamp Logo --}}
                <div class="col-md-6">
                    <div class="p-3.5 rounded-4 border bg-light h-100 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="form-label extra-small fw-bold text-uppercase text-dark mb-0 font-mono">
                                    <i class="bi bi-app-indicator text-success me-1"></i> 2. Square Icon / Stamp (1:1)
                                </label>
                                <span class="badge bg-white text-muted border extra-small font-mono">Thermal Receipt &amp; App Icon</span>
                            </div>

                            {{-- Preview Box --}}
                            <div class="logo-preview-box rounded-3 border bg-white p-2 text-center position-relative mb-2.5 d-flex align-items-center justify-content-center" style="min-height: 85px;">
                                <img id="squareLogoPreview" src="{{ $squareLogoUrl }}" alt="Square Logo" style="width: {{ $logoTopbarH * 1.3 }}px; height: {{ $logoTopbarH * 1.3 }}px; object-fit: contain; transition: all 0.2s ease;">
                                
                                <button type="button" id="btnRemoveSquareLogo" class="btn btn-sm btn-danger rounded-circle position-absolute top-0 end-0 m-2 shadow-xs {{ $hasSquareLogo ? '' : 'd-none' }}" title="Remove Square Logo" style="width:22px;height:22px;padding:0;display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-x-lg" style="font-size:0.6rem;"></i>
                                </button>
                            </div>
                            <input type="hidden" name="remove_logo_square" id="removeSquareLogoInput" value="0">
                        </div>

                        <div>
                            <label for="squareLogoInput" class="btn btn-sm btn-white border rounded-3 w-100 py-1.5 extra-small fw-bold text-dark shadow-xs hover-lift cursor-pointer d-flex align-items-center justify-content-center gap-1.5 mb-1">
                                <i class="bi bi-cloud-arrow-up-fill text-success"></i>
                                <span id="squareUploadBtnText">{{ $hasSquareLogo ? 'Replace Square Icon' : 'Upload Square Icon' }}</span>
                            </label>
                            <input type="file" id="squareLogoInput" name="logo_square" class="d-none" accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml">
                            <small class="text-muted extra-small d-block text-center font-mono">Recommended: 1:1 Square (e.g. 512x512) • Max 10MB</small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Dynamic CSS Logo Size Sliders Strip --}}
            <div class="p-3 rounded-4 border bg-white">
                <div class="d-flex align-items-center gap-2 mb-2.5">
                    <i class="bi bi-sliders text-primary"></i>
                    <span class="fw-bold extra-small text-dark font-mono text-uppercase">Dynamic CSS Logo Sizing (Real-time Live Adjustment)</span>
                </div>
                <div class="row g-3">
                    {{-- 1. Sidebar Logo Height --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label extra-small text-muted mb-0 font-mono">Sidebar Logo Height</label>
                            <span class="badge bg-light text-dark border font-mono extra-small" id="sidebarHValBadge">{{ $logoSidebarH }}px</span>
                        </div>
                        <input type="range" class="form-range" id="logoSidebarHSlider" name="logo_sidebar_height" min="20" max="65" value="{{ $logoSidebarH }}" step="1">
                        <small class="text-muted extra-small font-mono" style="font-size:0.65rem;">Adjusts sidebar navigation brand banner</small>
                    </div>

                    {{-- 2. Topbar Store Icon Size --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label extra-small text-muted mb-0 font-mono">Topbar Icon Size</label>
                            <span class="badge bg-light text-dark border font-mono extra-small" id="topbarHValBadge">{{ $logoTopbarH }}px</span>
                        </div>
                        <input type="range" class="form-range" id="logoTopbarHSlider" name="logo_topbar_height" min="20" max="50" value="{{ $logoTopbarH }}" step="1">
                        <small class="text-muted extra-small font-mono" style="font-size:0.65rem;">Adjusts topbar store pill thumbnail</small>
                    </div>

                    {{-- 3. Thermal Receipt Logo Height --}}
                    <div class="col-md-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label extra-small text-muted mb-0 font-mono">Thermal Receipt Logo Height</label>
                            <span class="badge bg-light text-dark border font-mono extra-small" id="receiptHValBadge">{{ $logoReceiptH }}px</span>
                        </div>
                        <input type="range" class="form-range" id="logoReceiptHSlider" name="logo_receipt_height" min="25" max="85" value="{{ $logoReceiptH }}" step="1">
                        <small class="text-muted extra-small font-mono" style="font-size:0.65rem;">Adjusts printed 78mm receipt header logo</small>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION 2: POS COLOR THEMES (VIVID GRADIENT CAPSULES - ZERO OVERFLOW)
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#ECFDF5;color:#059669;">
                        <i class="bi bi-palette-fill fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-black text-dark mb-0 font-mono">POS Gradient Themes &amp; Live Terminal Palette</h6>
                        <small class="text-muted extra-small">Click any vibrant gradient capsule below to instantly transform Topbar, Sidebar, Accent &amp; POS</small>
                    </div>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1 font-mono fw-bold extra-small">
                    <i class="bi bi-broadcast me-1"></i> Live Real-time Sync
                </span>
            </div>

            <div class="row g-4">
                {{-- Pure Gradient Swatches Column (Left) --}}
                <div class="col-lg-7">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                        <label class="form-label extra-small fw-bold text-uppercase text-muted mb-0 font-mono">59 Curated Gradient Palettes (Zero Text Overflow)</label>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-mono extra-small">Pure Visual Gradients</span>
                    </div>

                    {{-- Category Filter Pill Tabs --}}
                    <div class="d-flex align-items-center gap-1.5 overflow-x-auto pb-1 mb-2.5 custom-scrollbar" id="swatchCategoryFilters" style="white-space: nowrap;">
                        <button type="button" class="btn btn-sm btn-category-filter active rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="all">All (59)</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="canva" style="background: linear-gradient(135deg, rgba(125,42,232,0.12), rgba(0,196,204,0.12)); color: #7d2ae8; border: 1px solid rgba(125,42,232,0.25);">🎨 Canva Style (5)</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="featured">🌟 Featured</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="retail">🛒 Retail &amp; Mart</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="cafe">☕ Café &amp; Bistro</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="pharma">💊 Pharma &amp; Clinic</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="hardware">🛠️ Hardware &amp; Auto</button>
                        <button type="button" class="btn btn-sm btn-category-filter rounded-pill px-2.5 py-0.5 extra-small fw-bold shadow-xs" data-filter="dark">🌙 Cyber &amp; Dark</button>
                    </div>

                    {{-- Swatches Grid (Eye-Candy Visual Gradient Capsules) --}}
                    <div class="swatches-grid-container p-2.5 rounded-4 border bg-light mb-3" style="max-height: 280px; overflow-y: auto;">
                        <div class="row g-2" id="colorSwatchesGrid">
                            @foreach($themePresets as $preset)
                                <div class="col-4 col-sm-3 col-md-3 col-lg-2 swatch-grid-col" data-category="{{ $preset['category'] }}">
                                    <button type="button" 
                                        class="btn btn-swatch w-100 p-1 rounded-3 {{ $currentPreset === $preset['id'] ? 'active-swatch' : '' }}" 
                                        data-category="{{ $preset['category'] }}"
                                        data-preset="{{ $preset['id'] }}" 
                                        data-name="{{ $preset['name'] }}"
                                        data-gradient="{{ $preset['gradient'] }}"
                                        data-topbar="{{ $preset['topbar'] }}" 
                                        data-topbar-text="{{ $preset['topbar_text'] }}" 
                                        data-primary="{{ $preset['primary'] }}" 
                                        data-sidebar="{{ $preset['sidebar'] }}" 
                                        data-sidebar-text="{{ $preset['sidebar_text'] }}" 
                                        data-sidebar-active="{{ $preset['sidebar_active'] }}" 
                                        data-sidebar-active-text="{{ $preset['sidebar_active_text'] }}" 
                                        data-accent="{{ $preset['accent'] }}"
                                        title="{{ $preset['name'] }}"
                                        data-bs-toggle="tooltip"
                                        data-bs-placement="top">
                                        
                                        {{-- Sleek Gradient Capsule Pill (Zero Text Inside) --}}
                                        <div class="swatch-gradient-capsule" style="background: {{ $preset['gradient'] }};">
                                            <div class="swatch-shine"></div>
                                            <div class="swatch-check-badge">
                                                <i class="bi bi-check2"></i>
                                            </div>
                                        </div>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="theme_preset" id="themePresetInput" value="{{ $currentPreset }}">
                    <input type="hidden" name="gradient" id="themeGradientInput" value="{{ $themeSettings['gradient'] ?? 'linear-gradient(135deg, #064E3B 0%, #059669 50%, #10B981 100%)' }}">

                    {{-- Fine-Tune Custom Color Pickers (Collapsible) --}}
                    <div class="border rounded-3 p-2.5 bg-light">
                        <div class="d-flex align-items-center justify-content-between cursor-pointer" data-bs-toggle="collapse" data-bs-target="#customColorsCollapse" aria-expanded="false">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-sliders2 text-primary"></i>
                                <span class="fw-bold extra-small text-dark font-mono">Custom Color Hex Pickers (Optional)</span>
                            </div>
                            <i class="bi bi-chevron-down extra-small text-muted"></i>
                        </div>

                        <div class="collapse mt-2.5" id="customColorsCollapse">
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Topbar</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="topbarColorPicker" name="topbar_color" value="{{ $topbarColor }}">
                                    <input type="hidden" id="topbarColorText" value="{{ $topbarColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Topbar Text</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="topbarTextColorPicker" name="topbar_text_color" value="{{ $topbarTextColor }}">
                                    <input type="hidden" id="topbarTextColorText" value="{{ $topbarTextColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Primary Btn</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="primaryColorPicker" name="primary_color" value="{{ $primaryColor }}">
                                    <input type="hidden" id="primaryColorText" value="{{ $primaryColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Accent Dot</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="accentColorPicker" name="accent_color" value="{{ $accentColor }}">
                                    <input type="hidden" id="accentColorText" value="{{ $accentColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Sidebar</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="sidebarColorPicker" name="sidebar_color" value="{{ $sidebarColor }}">
                                    <input type="hidden" id="sidebarColorText" value="{{ $sidebarColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Sidebar Text</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="sidebarTextColorPicker" name="sidebar_text_color" value="{{ $sidebarTextColor }}">
                                    <input type="hidden" id="sidebarTextColorText" value="{{ $sidebarTextColor }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Active Link</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="sidebarActivePicker" name="sidebar_active_color" value="{{ $sidebarActive }}">
                                    <input type="hidden" id="sidebarActiveText" value="{{ $sidebarActive }}">
                                </div>
                                <div class="col-6 col-md-3">
                                    <label class="form-label extra-small text-muted mb-1 font-mono">Active Text</label>
                                    <input type="color" class="form-control form-control-color w-100 p-1" id="sidebarActiveTextColorPicker" name="sidebar_active_text_color" value="{{ $sidebarActiveTextColor }}">
                                    <input type="hidden" id="sidebarActiveTextColorText" value="{{ $sidebarActiveTextColor }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Interactive Live UI Preview Mockup (Right) --}}
                <div class="col-lg-5">
                    <label class="form-label extra-small fw-bold text-uppercase text-muted mb-2 font-mono">Live Terminal Sandbox Mockup</label>
                    <div id="mockupContainer" class="rounded-4 border shadow-sm overflow-hidden bg-light" style="height: 310px; position:relative; font-family:'Plus Jakarta Sans',sans-serif;">
                        
                        {{-- Mockup Topbar --}}
                        <div id="mockTopbar" class="d-flex align-items-center justify-content-between px-3 py-2" style="background-color: {{ $topbarColor }}; color: {{ $topbarTextColor }}; transition: all 0.3s ease;">
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge rounded-pill bg-white text-dark font-mono extra-small px-2 py-0.5 fw-bold">{{ $tenant->business_code ?? 'STORE-01' }}</span>
                                <span class="fw-bold extra-small font-mono text-truncate" id="mockStoreTitle" style="max-width:130px;">{{ $tenant->business_name }}</span>
                            </div>
                            <div class="d-flex align-items-center gap-1.5">
                                <span class="badge bg-black-50 extra-small rounded-pill"><i class="bi bi-broadcast text-success me-1"></i>Live</span>
                                <button type="button" id="mockBtnPos" class="btn btn-sm rounded-pill text-white fw-bold px-2.5 py-0.5" style="background-color: {{ $primaryColor }}; font-size:0.65rem; border:none; transition: all 0.3s ease;">
                                    <i class="bi bi-cart-fill me-1"></i>POS
                                </button>
                            </div>
                        </div>

                        {{-- Mockup Body with Sidebar & Content --}}
                        <div class="d-flex" style="height:calc(100% - 37px);">
                            {{-- Mockup Sidebar --}}
                            <div id="mockSidebar" class="p-2 d-flex flex-column gap-1" style="width:115px; background-color: {{ $sidebarColor }}; color: {{ $sidebarTextColor }}; transition: all 0.3s ease;">
                                <small class="extra-small text-uppercase font-mono fw-bold px-1" style="font-size:0.55rem; opacity:0.75;">MAIN</small>
                                <div id="mockActiveLink" class="p-1.5 rounded-2 d-flex align-items-center gap-1.5 fw-bold" style="background-color: {{ $sidebarActive }}; color: {{ $sidebarActiveTextColor }}; font-size:0.65rem; transition: all 0.3s ease;">
                                    <i class="bi bi-speedometer2"></i>
                                    <span>Dashboard</span>
                                </div>
                                <div class="mock-inactive-item p-1.5 rounded-2 d-flex align-items-center gap-1.5" style="font-size:0.65rem; color: {{ $sidebarTextColor }}; opacity:0.85;">
                                    <i class="bi bi-box-seam"></i>
                                    <span>Products</span>
                                </div>
                                <div class="mock-inactive-item p-1.5 rounded-2 d-flex align-items-center gap-1.5" style="font-size:0.65rem; color: {{ $sidebarTextColor }}; opacity:0.85;">
                                    <i class="bi bi-people"></i>
                                    <span>Suki CRM</span>
                                </div>
                                <div class="mock-inactive-item p-1.5 rounded-2 d-flex align-items-center gap-1.5" style="font-size:0.65rem; color: {{ $sidebarTextColor }}; opacity:0.85;">
                                    <i class="bi bi-receipt"></i>
                                    <span>Receipts</span>
                                </div>
                            </div>

                            {{-- Mockup Content Area --}}
                            <div class="flex-grow-1 p-2.5 overflow-hidden bg-light d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="fw-bold extra-small text-dark font-mono">Today's Revenue</span>
                                        <span class="badge bg-success-subtle text-success extra-small">+18.4%</span>
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <div class="card p-2 border rounded-3 bg-white shadow-xs">
                                                <small class="text-muted" style="font-size:0.6rem;">Gross Sales</small>
                                                <span class="fw-black text-dark font-mono" style="font-size:0.85rem;">₱28,450.00</span>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="card p-2 border rounded-3 bg-white shadow-xs">
                                                <small class="text-muted" style="font-size:0.6rem;">Orders</small>
                                                <span class="fw-black text-success font-mono" style="font-size:0.85rem;">142</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center pt-2">
                                    <button type="button" id="mockActionButton" class="btn btn-sm text-white fw-bold rounded-3 px-3 py-1.5 shadow-xs w-100" style="background-color: {{ $primaryColor }}; font-size:0.72rem; border:none; transition: all 0.3s ease;">
                                        <i class="bi bi-calculator me-1"></i> Cashier Shift Active
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════════════════════════════
             SECTION 3: STORE PROFILE & RECEIPT CONFIGURATION
             ═══════════════════════════════════════════════════════════════════ --}}
        <div class="row g-4 mb-4">
            {{-- Business Profile (Left) --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#F0FDF4;color:#16A34A;">
                                <i class="bi bi-shop fs-5"></i>
                            </div>
                            <h6 class="fw-black text-dark mb-0 font-mono">Store Business Profile</h6>
                        </div>
                        <span class="badge bg-light border text-dark font-mono extra-small">
                            ID: <strong class="text-primary">{{ $tenant->business_code ?? 'STORE-001' }}</strong>
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Business Name <span class="text-danger">*</span></label>
                            <input type="text" name="business_name" id="businessNameInput" class="form-control font-mono fw-bold text-dark" value="{{ old('business_name', $tenant->business_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Owner / Manager Name <span class="text-danger">*</span></label>
                            <input type="text" name="owner_name" class="form-control font-mono text-dark" value="{{ old('owner_name', $tenant->owner_name) }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Contact Phone</label>
                            <input type="text" name="phone" class="form-control font-mono text-dark" value="{{ old('phone', $tenant->phone) }}" placeholder="e.g. 0917 123 4567">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Store Official Email</label>
                            <input type="email" name="email" class="form-control font-mono text-dark" value="{{ old('email', $tenant->email) }}" placeholder="e.g. store@example.com">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">BIR Tax TIN Number</label>
                            <input type="text" name="tin" class="form-control font-mono text-dark" value="{{ old('tin', $tenant->tin) }}" placeholder="e.g. 123-456-789-000">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Currency Symbol</label>
                            <input type="text" name="currency_symbol" class="form-control font-mono text-dark" value="{{ old('currency_symbol', $tenant->currency_symbol ?? '₱') }}" placeholder="₱" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Physical Store Address</label>
                            <textarea name="address" rows="2" class="form-control font-mono text-dark extra-small" placeholder="Complete address printed on receipts...">{{ old('address', $tenant->address) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Receipt & Loyalty Settings (Right) --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
                    <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <div class="rounded-3 d-flex align-items-center justify-content-center" style="width:34px;height:34px;background:#FAF5FF;color:#9333EA;">
                                <i class="bi bi-receipt-cutoff fs-5"></i>
                            </div>
                            <h6 class="fw-black text-dark mb-0 font-mono">Receipt &amp; Suki Customer Engine</h6>
                        </div>
                        <span class="badge bg-purple-subtle text-purple font-mono extra-small" style="background:#FAF5FF;color:#9333EA;">
                            Receipt &amp; CRM
                        </span>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Custom Receipt Header Subtitle</label>
                            <input type="text" name="header_text" class="form-control font-mono extra-small" value="{{ old('header_text', $tenant->header_text) }}" placeholder="e.g. Official Retailer • Open Daily 8AM - 10PM">
                        </div>

                        <div class="col-12">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Receipt Footer Thank-You Note</label>
                            <textarea name="receipt_footer" rows="2" class="form-control font-mono extra-small" placeholder="e.g. Maraming salamat sa inyong pagbili! Balik po kayo muli.">{{ old('receipt_footer', $tenant->footer_text) }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Points Per ₱100 Spent</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.1" name="points_per_peso" class="form-control font-mono text-dark" value="{{ old('points_per_peso', $pointsPerPeso * 100) }}">
                                <span class="input-group-text font-mono extra-small">pts / ₱100</span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label extra-small fw-bold text-uppercase text-muted">Suki Utang (Credit) Limit</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text font-mono extra-small">₱</span>
                                <input type="number" step="100" name="default_credit_limit" class="form-control font-mono text-dark" value="{{ old('default_credit_limit', $defaultCreditLimit) }}">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" name="sms_receipt_enabled" value="1" id="smsReceiptCheck" {{ $smsReceiptEnabled ? 'checked' : '' }}>
                                <label class="form-check-label extra-small fw-bold text-dark" for="smsReceiptCheck">
                                    Send SMS E-Receipts
                                </label>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" name="sms_utang_reminder_enabled" value="1" id="smsUtangCheck" {{ $smsUtangReminderEnabled ? 'checked' : '' }}>
                                <label class="form-check-label extra-small fw-bold text-dark" for="smsUtangCheck">
                                    SMS Utang Reminders
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sticky Bottom Action Bar --}}
        <div class="d-flex align-items-center justify-content-between p-3 rounded-4 bg-white border shadow-sm flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success font-mono extra-small fw-bold">
                    <i class="bi bi-shield-lock-fill me-1"></i> Auto Encrypted
                </span>
                <small class="text-muted extra-small">Changes apply instantly to Web App, Mobile Cashiers, and Receipts.</small>
            </div>

            <button type="submit" id="btnSaveStoreSettings" class="btn btn-success rounded-3 px-5 py-2.5 fw-bold d-flex align-items-center gap-2 shadow-sm hover-lift" style="background:linear-gradient(135deg, #059669 0%, #047857 100%);border:none;font-size:0.95rem;">
                <i class="bi bi-check-circle-fill fs-5"></i>
                <span>Save Store Profile, Logos &amp; POS Theme</span>
            </button>
        </div>
    </form>

</div>

{{-- Live Thermal Receipt Preview Modal --}}
<div class="modal fade" id="receiptPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom bg-light py-2 px-3">
                <h6 class="modal-title font-mono fw-bold extra-small text-dark mb-0">
                    <i class="bi bi-receipt-cutoff text-primary me-1"></i> Live 78mm Thermal Receipt Preview
                </h6>
                <button type="button" class="btn-close extra-small" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-2 text-center bg-dark">
                <iframe id="receiptPreviewFrame" style="width:100%;height:460px;border:none;background:#fff;" class="rounded-3 shadow-xs"></iframe>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-3 d-flex justify-content-between">
                <small class="extra-small text-muted font-mono">78mm Thermal Receipt Format</small>
                <button type="button" class="btn btn-secondary btn-sm rounded-3 extra-small px-3" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-swatch {
        background: transparent !important;
        border: 2.5px solid transparent !important;
        padding: 2px !important;
        border-radius: 12px !important;
        transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        cursor: pointer;
        display: block !important;
        width: 100% !important;
    }
    .btn-swatch:hover {
        transform: translateY(-3px) scale(1.02);
    }
    .btn-swatch:hover .swatch-gradient-capsule {
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.2);
    }
    .swatch-gradient-capsule {
        height: 38px;
        width: 100%;
        border-radius: 9px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.25);
        transition: all 0.22s ease;
    }
    .swatch-shine {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 48%;
        background: linear-gradient(180deg, rgba(255, 255, 255, 0.35) 0%, rgba(255, 255, 255, 0.02) 100%);
        pointer-events: none;
    }
    .swatch-check-badge {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #ffffff;
        font-size: 1.15rem;
        font-weight: 900;
        opacity: 0;
        transform: scale(0.5);
        transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
        text-shadow: 0 1px 4px rgba(0, 0, 0, 0.5);
        background: rgba(0, 0, 0, 0.18);
    }
    .active-swatch {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px rgba(5, 150, 105, 0.35), 0 6px 16px rgba(5, 150, 105, 0.2) !important;
    }
    .active-swatch .swatch-gradient-capsule {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.28);
    }
    .active-swatch .swatch-check-badge {
        opacity: 1;
        transform: scale(1);
    }
    .btn-category-filter {
        background: #ffffff;
        color: #475569;
        border: 1px solid #E2E8F0;
        transition: all 0.2s ease;
    }
    .btn-category-filter:hover {
        background: #F1F5F9;
        color: #0F172A;
        border-color: #CBD5E1;
    }
    .btn-category-filter.active {
        background: #0F172A !important;
        color: #ffffff !important;
        border-color: #0F172A !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.25);
    }
    .logo-preview-box {
        background-image: linear-gradient(45deg, #f8fafc 25%, transparent 25%), linear-gradient(-45deg, #f8fafc 25%, transparent 25%), linear-gradient(45deg, transparent 75%, #f8fafc 75%), linear-gradient(-45deg, transparent 75%, #f8fafc 75%);
        background-size: 16px 16px;
        background-position: 0 0, 0 8px, 8px -8px, -8px 0px;
    }
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 0. Initialize Bootstrap Tooltips for all Swatches
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // 1. Dual Logo Upload & Removal Logic
    // Slot 1: Horizontal / Rectangle Logo
    const rectLogoInput = document.getElementById('rectLogoInput');
    const rectLogoPreview = document.getElementById('rectLogoPreview');
    const btnRemoveRectLogo = document.getElementById('btnRemoveRectLogo');
    const removeRectLogoInput = document.getElementById('removeRectLogoInput');
    const rectUploadBtnText = document.getElementById('rectUploadBtnText');
    const defaultRectPlaceholder = "{{ asset('images/logo.png') }}";

    if (rectLogoInput && rectLogoPreview) {
        rectLogoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Selected image exceeds 10MB limit.');
                    rectLogoInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (evt) {
                    rectLogoPreview.src = evt.target.result;
                    if (btnRemoveRectLogo) {
                        btnRemoveRectLogo.classList.remove('d-none');
                        btnRemoveRectLogo.style.display = 'flex';
                    }
                    if (removeRectLogoInput) removeRectLogoInput.value = '0';
                    if (rectUploadBtnText) rectUploadBtnText.textContent = 'Replace Horizontal Logo';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnRemoveRectLogo) {
        btnRemoveRectLogo.addEventListener('click', function (e) {
            e.preventDefault();
            if (rectLogoInput) rectLogoInput.value = '';
            if (rectLogoPreview) rectLogoPreview.src = defaultRectPlaceholder;
            if (removeRectLogoInput) removeRectLogoInput.value = '1';
            btnRemoveRectLogo.classList.add('d-none');
            btnRemoveRectLogo.style.display = 'none';
            if (rectUploadBtnText) rectUploadBtnText.textContent = 'Upload Horizontal Logo';
        });
    }

    // Slot 2: Square Logo (App Icon / Stamp)
    const squareLogoInput = document.getElementById('squareLogoInput');
    const squareLogoPreview = document.getElementById('squareLogoPreview');
    const btnRemoveSquareLogo = document.getElementById('btnRemoveSquareLogo');
    const removeSquareLogoInput = document.getElementById('removeSquareLogoInput');
    const squareUploadBtnText = document.getElementById('squareUploadBtnText');
    const defaultSquarePlaceholder = "{{ asset('images/ic_launcher.png') }}";

    if (squareLogoInput && squareLogoPreview) {
        squareLogoInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                if (file.size > 10 * 1024 * 1024) {
                    alert('Selected image exceeds 10MB limit.');
                    squareLogoInput.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (evt) {
                    squareLogoPreview.src = evt.target.result;
                    if (btnRemoveSquareLogo) {
                        btnRemoveSquareLogo.classList.remove('d-none');
                        btnRemoveSquareLogo.style.display = 'flex';
                    }
                    if (removeSquareLogoInput) removeSquareLogoInput.value = '0';
                    if (squareUploadBtnText) squareUploadBtnText.textContent = 'Replace Square Icon';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (btnRemoveSquareLogo) {
        btnRemoveSquareLogo.addEventListener('click', function (e) {
            e.preventDefault();
            if (squareLogoInput) squareLogoInput.value = '';
            if (squareLogoPreview) squareLogoPreview.src = defaultSquarePlaceholder;
            if (removeSquareLogoInput) removeSquareLogoInput.value = '1';
            btnRemoveSquareLogo.classList.add('d-none');
            btnRemoveSquareLogo.style.display = 'none';
            if (squareUploadBtnText) squareUploadBtnText.textContent = 'Upload Square Icon';
        });
    }

    // 2. Dynamic CSS Logo Size Sliders
    const logoSidebarHSlider = document.getElementById('logoSidebarHSlider');
    const logoTopbarHSlider = document.getElementById('logoTopbarHSlider');
    const logoReceiptHSlider = document.getElementById('logoReceiptHSlider');
    const sidebarHValBadge = document.getElementById('sidebarHValBadge');
    const topbarHValBadge = document.getElementById('topbarHValBadge');
    const receiptHValBadge = document.getElementById('receiptHValBadge');

    if (logoSidebarHSlider) {
        logoSidebarHSlider.addEventListener('input', function() {
            const val = this.value;
            if (sidebarHValBadge) sidebarHValBadge.textContent = val + 'px';
            if (rectLogoPreview) rectLogoPreview.style.maxHeight = val + 'px';
            document.documentElement.style.setProperty('--theme-logo-sidebar-h', val + 'px');
            document.querySelectorAll('.sidebar-inner img, .likha-sidebar-brand img').forEach(img => {
                img.style.setProperty('max-height', val + 'px', 'important');
            });
        });
    }

    if (logoTopbarHSlider) {
        logoTopbarHSlider.addEventListener('input', function() {
            const val = this.value;
            if (topbarHValBadge) topbarHValBadge.textContent = val + 'px';
            if (squareLogoPreview) {
                squareLogoPreview.style.width = (val * 1.3) + 'px';
                squareLogoPreview.style.height = (val * 1.3) + 'px';
            }
            document.documentElement.style.setProperty('--theme-logo-topbar-h', val + 'px');
            document.querySelectorAll('.crm-topbar .crm-store-icon img').forEach(img => {
                img.style.setProperty('max-height', val + 'px', 'important');
                img.style.setProperty('max-width', val + 'px', 'important');
            });
        });
    }

    if (logoReceiptHSlider) {
        logoReceiptHSlider.addEventListener('input', function() {
            const val = this.value;
            if (receiptHValBadge) receiptHValBadge.textContent = val + 'px';
            document.documentElement.style.setProperty('--theme-logo-receipt-h', val + 'px');
        });
    }

    // 3. Category Filter Tabs
    document.querySelectorAll('.btn-category-filter').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.btn-category-filter').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const filter = this.dataset.filter;
            document.querySelectorAll('.swatch-grid-col').forEach(col => {
                if (filter === 'all' || col.dataset.category === filter) {
                    col.classList.remove('d-none');
                } else {
                    col.classList.add('d-none');
                }
            });
        });
    });

    // 4. Interactive Real-time Theme Customizer Elements
    const topbarPicker = document.getElementById('topbarColorPicker');
    const topbarText = document.getElementById('topbarColorText');
    const topbarTextColorPicker = document.getElementById('topbarTextColorPicker');
    const topbarTextColorText = document.getElementById('topbarTextColorText');

    const primaryPicker = document.getElementById('primaryColorPicker');
    const primaryText = document.getElementById('primaryColorText');
    const accentPicker = document.getElementById('accentColorPicker');
    const accentText = document.getElementById('accentColorText');

    const sidebarPicker = document.getElementById('sidebarColorPicker');
    const sidebarText = document.getElementById('sidebarColorText');
    const sidebarTextColorPicker = document.getElementById('sidebarTextColorPicker');
    const sidebarTextColorText = document.getElementById('sidebarTextColorText');

    const sidebarActivePicker = document.getElementById('sidebarActivePicker');
    const sidebarActiveText = document.getElementById('sidebarActiveText');
    const sidebarActiveTextColorPicker = document.getElementById('sidebarActiveTextColorPicker');
    const sidebarActiveTextColorText = document.getElementById('sidebarActiveTextColorText');

    const themePresetInput = document.getElementById('themePresetInput');
    const themeGradientInput = document.getElementById('themeGradientInput');

    // Mockup Elements
    const mockTopbar = document.getElementById('mockTopbar');
    const mockSidebar = document.getElementById('mockSidebar');
    const mockActiveLink = document.getElementById('mockActiveLink');
    const mockBtnPos = document.getElementById('mockBtnPos');
    const mockActionButton = document.getElementById('mockActionButton');
    const businessNameInput = document.getElementById('businessNameInput');
    const mockStoreTitle = document.getElementById('mockStoreTitle');

    if (businessNameInput && mockStoreTitle) {
        businessNameInput.addEventListener('input', function() {
            mockStoreTitle.textContent = this.value || 'Store';
        });
    }

    function isHexLight(hex) {
        if (!hex) return false;
        hex = hex.replace('#', '');
        if (hex.length === 3) hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        if (hex.length !== 6) return false;
        const r = parseInt(hex.substr(0, 2), 16);
        const g = parseInt(hex.substr(2, 2), 16);
        const b = parseInt(hex.substr(4, 2), 16);
        return ((0.299 * r + 0.587 * g + 0.114 * b) > 195);
    }

    function updateLivePreview(customGradient = null) {
        const topbarCol = topbarPicker ? topbarPicker.value : '#064E3B';
        const topbarTextCol = topbarTextColorPicker ? topbarTextColorPicker.value : '#FFFFFF';
        const primaryCol = primaryPicker ? primaryPicker.value : '#059669';
        const accentCol = accentPicker ? accentPicker.value : '#10B981';

        const sidebarCol = sidebarPicker ? sidebarPicker.value : '#0F172A';
        const sidebarTextCol = sidebarTextColorPicker ? sidebarTextColorPicker.value : '#CBD5E1';
        const sidebarActiveCol = sidebarActivePicker ? sidebarActivePicker.value : '#059669';
        const sidebarActiveTextCol = sidebarActiveTextColorPicker ? sidebarActiveTextColorPicker.value : '#FFFFFF';

        const activeGradient = customGradient || (themeGradientInput && themeGradientInput.value ? themeGradientInput.value : `linear-gradient(135deg, ${topbarCol} 0%, ${primaryCol} 100%)`);
        if (themeGradientInput) themeGradientInput.value = activeGradient;

        const isLightTb = isHexLight(topbarCol);
        const isLightSb = isHexLight(sidebarCol);

        // Update hidden text inputs
        if (topbarText) topbarText.value = topbarCol;
        if (topbarTextColorText) topbarTextColorText.value = topbarTextCol;
        if (primaryText) primaryText.value = primaryCol;
        if (accentText) accentText.value = accentCol;
        if (sidebarText) sidebarText.value = sidebarCol;
        if (sidebarTextColorText) sidebarTextColorText.value = sidebarTextCol;
        if (sidebarActiveText) sidebarActiveText.value = sidebarActiveCol;
        if (sidebarActiveTextColorText) sidebarActiveTextColorText.value = sidebarActiveTextCol;

        // 1. Update Preview Sandbox Mockup
        if (mockTopbar) {
            mockTopbar.style.backgroundColor = topbarCol;
            mockTopbar.style.color = isLightTb ? '#0F172A' : topbarTextCol;
            mockTopbar.style.borderBottom = isLightTb ? '1px solid #E2E8F0' : 'none';
        }
        if (mockSidebar) {
            mockSidebar.style.backgroundColor = sidebarCol;
            mockSidebar.style.color = isLightSb ? '#334155' : sidebarTextCol;
            mockSidebar.style.borderRight = isLightSb ? '1px solid #E2E8F0' : 'none';
        }
        if (mockActiveLink) {
            mockActiveLink.style.background = isLightSb ? activeGradient : sidebarActiveCol;
            mockActiveLink.style.color = sidebarActiveTextCol;
        }
        document.querySelectorAll('.mock-inactive-item').forEach(item => {
            item.style.color = isLightSb ? '#475569' : sidebarTextCol;
        });
        if (mockBtnPos) {
            mockBtnPos.style.background = isLightTb ? activeGradient : primaryCol;
        }
        if (mockActionButton) mockActionButton.style.backgroundColor = primaryCol;

        // 2. Real-time Live Update on the ACTUAL Application Layout
        document.documentElement.style.setProperty('--theme-topbar', topbarCol);
        document.documentElement.style.setProperty('--theme-topbar-text', topbarTextCol);
        document.documentElement.style.setProperty('--theme-sidebar', sidebarCol);
        document.documentElement.style.setProperty('--theme-sidebar-text', sidebarTextCol);
        document.documentElement.style.setProperty('--theme-sidebar-active', sidebarActiveCol);
        document.documentElement.style.setProperty('--theme-sidebar-active-text', sidebarActiveTextCol);
        document.documentElement.style.setProperty('--theme-primary', primaryCol);
        document.documentElement.style.setProperty('--theme-accent', accentCol);
        document.documentElement.style.setProperty('--theme-gradient', activeGradient);

        const realSidebar = document.querySelector('.likha-sidebar') || document.querySelector('#mainSidebar');
        if (realSidebar) {
            realSidebar.style.setProperty('background-color', sidebarCol, 'important');
            realSidebar.style.setProperty('background', sidebarCol, 'important');
            if (isLightSb) {
                realSidebar.classList.add('theme-light-sidebar');
            } else {
                realSidebar.classList.remove('theme-light-sidebar');
            }
        }

        const realTopbar = document.querySelector('.crm-topbar') || document.querySelector('#crmTopbar') || document.querySelector('.likha-topbar') || document.querySelector('header');
        if (realTopbar) {
            realTopbar.style.setProperty('background-color', topbarCol, 'important');
            realTopbar.style.setProperty('background', topbarCol, 'important');
            if (isLightTb) {
                realTopbar.classList.add('theme-light-topbar');
            } else {
                realTopbar.classList.remove('theme-light-topbar');
            }
        }

        if (!isLightSb) {
            document.querySelectorAll('.sidebar-link:not(.active), .sidebar-link:not(.active) span, .sidebar-link:not(.active) i, .sidebar-sublink:not(.active), .sidebar-sublink:not(.active) span, .sidebar-sublink:not(.active) i').forEach(el => {
                el.style.setProperty('color', sidebarTextCol, 'important');
            });
            document.querySelectorAll('.sidebar-section-title, .sidebar-heading').forEach(title => {
                title.style.setProperty('color', sidebarTextCol, 'important');
            });
            document.querySelectorAll('.sidebar-link.active, .sidebar-link.active span, .sidebar-link.active i').forEach(el => {
                el.style.setProperty('background-color', sidebarActiveCol, 'important');
                el.style.setProperty('background', sidebarActiveCol, 'important');
                el.style.setProperty('color', sidebarActiveTextCol, 'important');
            });
            document.querySelectorAll('.sidebar-sublink.active, .sidebar-sublink.active span, .sidebar-sublink.active i').forEach(el => {
                el.style.setProperty('color', primaryCol, 'important');
            });
        }
    }

    // Bind color pickers
    [
        topbarPicker, topbarTextColorPicker,
        primaryPicker, accentPicker,
        sidebarPicker, sidebarTextColorPicker,
        sidebarActivePicker, sidebarActiveTextColorPicker
    ].forEach(picker => {
        if (picker) {
            picker.addEventListener('input', () => {
                document.querySelectorAll('.btn-swatch').forEach(b => b.classList.remove('active-swatch'));
                if (themePresetInput) themePresetInput.value = 'custom';
                updateLivePreview();
            });
        }
    });

    // Preset Swatch Button Clicks (Gradient Capsule Click)
    document.querySelectorAll('.btn-swatch').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            const targetBtn = this.closest('.btn-swatch') || this;

            document.querySelectorAll('.btn-swatch').forEach(b => b.classList.remove('active-swatch'));
            targetBtn.classList.add('active-swatch');

            const preset = targetBtn.dataset.preset;
            const gradient = targetBtn.dataset.gradient;
            const topbar = targetBtn.dataset.topbar;
            const topbarText = targetBtn.dataset.topbarText || '#FFFFFF';
            const primary = targetBtn.dataset.primary;
            const accent = targetBtn.dataset.accent || primary;
            const sidebar = targetBtn.dataset.sidebar;
            const sidebarText = targetBtn.dataset.sidebarText || '#CBD5E1';
            const sidebarActive = targetBtn.dataset.sidebarActive;
            const sidebarActiveText = targetBtn.dataset.sidebarActiveText || '#FFFFFF';

            if (themePresetInput) themePresetInput.value = preset;
            if (themeGradientInput && gradient) themeGradientInput.value = gradient;
            if (topbarPicker && topbar) topbarPicker.value = topbar;
            if (topbarTextColorPicker && topbarText) topbarTextColorPicker.value = topbarText;
            if (primaryPicker && primary) primaryPicker.value = primary;
            if (accentPicker && accent) accentPicker.value = accent;
            if (sidebarPicker && sidebar) sidebarPicker.value = sidebar;
            if (sidebarTextColorPicker && sidebarText) sidebarTextColorPicker.value = sidebarText;
            if (sidebarActivePicker && sidebarActive) sidebarActivePicker.value = sidebarActive;
            if (sidebarActiveTextColorPicker && sidebarActiveText) sidebarActiveTextColorPicker.value = sidebarActiveText;

            updateLivePreview(gradient);
        });
    });

    // 5. Receipt Preview Modal
    const btnPreviewReceipt = document.getElementById('btnPreviewReceipt');
    const receiptModal = document.getElementById('receiptPreviewModal');
    const receiptFrame = document.getElementById('receiptPreviewFrame');

    if (btnPreviewReceipt && receiptModal && receiptFrame) {
        btnPreviewReceipt.addEventListener('click', function () {
            const previewUrl = "{{ route('settings.receipt.preview') }}";
            receiptFrame.src = previewUrl;
            const bsModal = new bootstrap.Modal(receiptModal);
            bsModal.show();
        });
    }
});
</script>
@endpush
@endsection