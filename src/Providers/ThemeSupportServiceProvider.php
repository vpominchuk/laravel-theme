<?php

namespace VPominchuk\LaravelThemeSupport\Providers;

use Illuminate\Support\ServiceProvider;
use VPominchuk\LaravelThemeSupport\Commands\ThemeActivate;
use VPominchuk\LaravelThemeSupport\Commands\ThemeCreate;
use VPominchuk\LaravelThemeSupport\Commands\ThemeInfo;
use VPominchuk\LaravelThemeSupport\Commands\ThemeList;
use VPominchuk\LaravelThemeSupport\FileThemeViewFinder;
use VPominchuk\LaravelThemeSupport\Theme;
use VPominchuk\LaravelThemeSupport\StorageThemeManager;
use VPominchuk\LaravelThemeSupport\Contracts\ThemeManager;

class ThemeSupportServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerThemeManager();
        $this->registerThemeViewFinder();
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/theme.php' => config_path('theme.php')
        ], 'theme-config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeInfo::class,
                ThemeList::class,
                ThemeCreate::class,
                ThemeActivate::class,
            ]);
        }
    }

    private function registerThemeViewFinder(): void
    {
        $this->app->bind('view.finder', function ($app) {
            $finder = new FileThemeViewFinder($app['files'], $app['config']['view.paths']);
            $themeManager = app(ThemeManager::class);

            $theme = new Theme($themeManager->getActiveTheme(), $finder->getFilesystem());

            if ($theme->valid()) {
                $finder->setTheme($theme);
            }

            return $finder;
        });
    }

    private function registerThemeManager(): void
    {
        $this->app->bind(ThemeManager::class, StorageThemeManager::class);
    }
}
