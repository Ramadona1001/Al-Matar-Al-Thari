<?php

namespace App\Services;

use App\Models\SiteSetting;

class ThemeService
{
    /**
     * Get theme colors as CSS variables
     */
    public static function getThemeCssVariables(): array
    {
        $settings = SiteSetting::getSettings();
        
        // Default colors based on logo (dark forest green and golden)
        $defaults = [
            'theme_primary_color' => '#1B4332',
            'theme_secondary_color' => '#D4AF37',
            'theme_accent_color' => '#D4AF37',
            'gradient_start_color' => '#1B4332',
            'gradient_end_color' => '#2D5016',
            'text_primary_color' => '#1B4332',
            'text_secondary_color' => '#6C757D',
            'text_on_primary_color' => '#FFFFFF',
            'bg_primary_color' => '#FFFFFF',
            'bg_secondary_color' => '#F8F9FA',
            'bg_dark_color' => '#1B4332',
        ];
        
        $colors = [];
        foreach ($defaults as $key => $default) {
            $value = $settings->$key ?? $default;
            $cssVarName = '--' . str_replace('_', '-', $key);
            $colors[$cssVarName] = $value;
        }
        
        return $colors;
    }
    
    /**
     * Generate CSS style tag with theme variables
     */
    public static function generateThemeStyles(): string
    {
        $variables = self::getThemeCssVariables();
        
        $css = ":root {\n";
        foreach ($variables as $var => $value) {
            $css .= "    {$var}: {$value};\n";
        }
        $css .= "}\n\n";
        
        // Generate gradient utility classes
        $gradientStart = $variables['--gradient-start-color'] ?? '#1B4332';
        $gradientEnd = $variables['--gradient-end-color'] ?? '#2D5016';
        
        $css .= "/* Gradient Utilities */\n";
        $css .= ".bg-gradient-theme {\n";
        $css .= "    background: linear-gradient(135deg, {$gradientStart} 0%, {$gradientEnd} 100%);\n";
        $css .= "}\n\n";
        
        $css .= ".bg-gradient-theme-horizontal {\n";
        $css .= "    background: linear-gradient(90deg, {$gradientStart} 0%, {$gradientEnd} 100%);\n";
        $css .= "}\n\n";
        
        $css .= ".bg-gradient-theme-vertical {\n";
        $css .= "    background: linear-gradient(180deg, {$gradientStart} 0%, {$gradientEnd} 100%);\n";
        $css .= "}\n\n";
        
        // Text color utilities
        $css .= "/* Text Color Utilities */\n";
        $css .= ".text-theme-primary { color: var(--theme-primary-color); }\n";
        $css .= ".text-theme-secondary { color: var(--theme-secondary-color); }\n";
        $css .= ".text-theme-accent { color: var(--theme-accent-color); }\n";
        $css .= ".text-on-primary { color: var(--text-on-primary-color); }\n\n";
        
        // Background color utilities
        $css .= "/* Background Color Utilities */\n";
        $css .= ".bg-theme-primary { background-color: var(--theme-primary-color); }\n";
        $css .= ".bg-theme-secondary { background-color: var(--theme-secondary-color); }\n";
        $css .= ".bg-theme-accent { background-color: var(--theme-accent-color); }\n";
        $css .= ".bg-theme-dark { background-color: var(--bg-dark-color); }\n\n";
        
        // Button styles
        $css .= "/* Button Styles */\n";
        $css .= ".btn-theme-primary {\n";
        $css .= "    background-color: var(--theme-primary-color);\n";
        $css .= "    border-color: var(--theme-primary-color);\n";
        $css .= "    color: var(--text-on-primary-color);\n";
        $css .= "}\n\n";
        
        $css .= ".btn-theme-primary:hover {\n";
        $css .= "    background-color: var(--gradient-end-color);\n";
        $css .= "    border-color: var(--gradient-end-color);\n";
        $css .= "    color: var(--text-on-primary-color);\n";
        $css .= "}\n\n";
        
        $css .= ".btn-theme-outline {\n";
        $css .= "    color: var(--theme-primary-color);\n";
        $css .= "    border-color: var(--theme-primary-color);\n";
        $css .= "    background-color: transparent;\n";
        $css .= "}\n\n";
        
        $css .= ".btn-theme-outline:hover {\n";
        $css .= "    background-color: var(--theme-primary-color);\n";
        $css .= "    border-color: var(--theme-primary-color);\n";
        $css .= "    color: var(--text-on-primary-color);\n";
        $css .= "}\n\n";
        
        // Link styles
        $css .= "/* Link Styles */\n";
        $css .= "a:not(.btn) {\n";
        $css .= "    color:var(--theme-secondary-color, #D4AF37);\n";
        $css .= "}\n\n";
        
        $css .= "a:not(.btn):hover {\n";
        $css .= "    color:var(--theme-secondary-color, #D4AF37);\n";
        $css .= "}\n\n";
        
        // Border styles
        $css .= "/* Border Styles */\n";
        $css .= ".border-theme-primary { border-color: var(--theme-primary-color) !important; }\n";
        $css .= ".border-theme-secondary { border-color: var(--theme-secondary-color) !important; }\n\n";
        
        return $css;
    }
    
    /**
     * Get a specific theme color
     */
    public static function getColor(string $key, ?string $default = null): string
    {
        $settings = SiteSetting::getSettings();
        return $settings->$key ?? $default ?? '#1B4332';
    }
}

