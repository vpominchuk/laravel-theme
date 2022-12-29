<?php


namespace VPominchuk\LaravelThemeSupport\Contracts;


interface ThemeManager
{
    public function getActiveTheme(): ?string;
    public function setActiveTheme(string $systemName): bool;
}
