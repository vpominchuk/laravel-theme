<?php

namespace VPominchuk\LaravelThemeSupport\Providers;

use Illuminate\Support\ServiceProvider;
use VPominchuk\LaravelThemeSupport\Commands\ThemeCreate;
use VPominchuk\LaravelThemeSupport\Commands\ThemeInfo;
use VPominchuk\LaravelThemeSupport\Commands\ThemeList;
use VPominchuk\LaravelThemeSupport\FileThemeViewFinder;
use VPominchuk\LaravelThemeSupport\Exceptions\ThemeNotFoundException;
use VPominchuk\LaravelThemeSupport\Theme;
use VPominchuk\LaravelThemeSupport\ThemeManager;

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
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                ThemeInfo::class,
                ThemeList::class,
                ThemeCreate::class,
            ]);
        }
    }

    private function registerThemeViewFinder(): void
    {
        $this->app->bind('view.finder', function ($app) {
            $finder = new FileThemeViewFinder($app['files'], $app['config']['view.paths']);
            $themeManager = app('theme.manager');

            $theme = new Theme($themeManager->getActiveTheme(), $finder->getFilesystem());

            if ($theme->valid()) {
                $finder->setTheme($theme);
            }

            return $finder;
        });
    }

    private function registerThemeManager(): void
    {
        $this->app->bind('theme.manager', function ($app) {
            return new ThemeManager();
        });
    }
}
