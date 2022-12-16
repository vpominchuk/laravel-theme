<?php


namespace VPominchuk\LaravelThemeSupport;

use VPominchuk\LaravelThemeSupport\Contracts\ThemeManager;

class StorageThemeManager implements ThemeManager
{
    private $configFile = 'theme.json';

    public function getActiveTheme(): ?string
    {
        $themeFile = storage_path('framework/' . $this->configFile);

        if (!file_exists($themeFile)) {
            return false;
        }

        $json = file_get_contents($themeFile);

        if (!$json) {
            return false;
        }

        $data = json_decode($json, true);

        if (empty($data) || empty($data['active_theme'])) {
            return null;
        }

        return $data['active_theme'];
    }

    public function setActiveTheme(string $systemName): bool
    {
        $themeFile = storage_path('framework/' . $this->configFile);

        throw_if(
            !is_writable(dirname($themeFile)),
            \RuntimeException::class,
            dirname($themeFile) . " is not writable"
        );

        return file_put_contents(
            $themeFile,
            json_encode(
                [
                    'active_theme' => $systemName
                ],
                JSON_PRETTY_PRINT
            )
        ) > 0;
    }
}
