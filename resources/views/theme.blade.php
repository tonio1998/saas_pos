<style>
    :root{
        --theme-bg: {{ $themeVars['bg'] ?? '#059669' }};
        --theme-text: {{ $themeVars['text'] ?? '#ffffff' }};
        --theme-hover: {{ $themeVars['hover'] ?? 'rgba(5,150,105,0.08)' }};
        --theme-active: {{ $themeVars['active'] ?? 'rgba(5,150,105,0.15)' }};
        --theme-subtext: {{ $themeVars['subtext'] ?? '#64748B' }};
        --theme-border: {{ $themeVars['border'] ?? '#E2E8F0' }};
    }
</style>
