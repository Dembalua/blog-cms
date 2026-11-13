<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;

class ThemeService
{
    public function generateVariables(): void
    {
        $light = [
            'primary' => Setting::get('theme.light_primary') ?? '#7C3AED',
            'secondary' => Setting::get('theme.light_secondary') ?? '#8B5CF6',
            'background' => Setting::get('theme.light_background') ?? '#FAF5FF',
            'text' => Setting::get('theme.light_text') ?? '#0F172A',
            'surface' => Setting::get('theme.light_surface') ?? '#FAF5FF',
            'text_muted' => Setting::get('theme.light_text_muted') ?? '#374151',
            'text_subtle' => Setting::get('theme.light_text_subtle') ?? '#6B7280',
            'border' => Setting::get('theme.light_border') ?? '#EFE7FC',
        ];

        $dark = [
            'primary' => Setting::get('theme.dark_primary') ?? '#A78BFA',
            'secondary' => Setting::get('theme.dark_secondary') ?? '#C4B5FD',
            'background' => Setting::get('theme.dark_background') ?? '#100D1A',
            'text' => Setting::get('theme.dark_text') ?? '#EDE9F6',
            'surface' => Setting::get('theme.dark_surface') ?? '#1A1625',
            'text_muted' => Setting::get('theme.dark_text_muted') ?? '#B8B0CC',
            'text_subtle' => Setting::get('theme.dark_text_subtle') ?? '#8B80A0',
            'border' => Setting::get('theme.dark_border') ?? '#2D2640',
        ];

        $css = ":root {\n";
        $css .= "    --color-primary: {$light['primary']};\n";
        $css .= "    --color-secondary: {$light['secondary']};\n";
        $css .= "    --color-background: {$light['background']};\n";
        $css .= "    --color-text: {$light['text']};\n";
        $css .= "    --color-surface: {$light['surface']};\n";
        $css .= "    --color-text-muted: {$light['text_muted']};\n";
        $css .= "    --color-text-subtle: {$light['text_subtle']};\n";
        $css .= "    --color-border: {$light['border']};\n";
        $css .= "}\n\n";

        $css .= ".dark {\n";
        $css .= "    --color-primary: {$dark['primary']};\n";
        $css .= "    --color-secondary: {$dark['secondary']};\n";
        $css .= "    --color-background: {$dark['background']};\n";
        $css .= "    --color-text: {$dark['text']};\n";
        $css .= "    --color-surface: {$dark['surface']};\n";
        $css .= "    --color-text-muted: {$dark['text_muted']};\n";
        $css .= "    --color-text-subtle: {$dark['text_subtle']};\n";
        $css .= "    --color-border: {$dark['border']};\n";
        $css .= "}\n";

        File::ensureDirectoryExists(public_path('css'));
        File::put(public_path('css/variables.css'), $css);
    }
}
