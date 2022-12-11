<?php


namespace VPominchuk\LaravelThemeSupport;


class ThemeManager implements ThemeManagerInterface
{
    public function getActiveTheme(): string
    {
        return config('theme.theme');
    }
}
