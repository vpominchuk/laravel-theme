<?php

namespace VPominchuk\LaravelThemeSupport\Commands;

use VPominchuk\LaravelThemeSupport\Contracts\ThemeManager;
use VPominchuk\LaravelThemeSupport\Theme;
use Illuminate\Console\Command;

class ThemeActivate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:activate {theme_system_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set theme active';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThemeManager $themeManager)
    {
        $systemName = $this->argument('theme_system_name');

        $theme = new Theme($systemName);

        if (!$theme->valid()) {
            $this->error(sprintf('Theme [%s] is not a valid theme', $systemName));
            return Command::FAILURE;
        }

        if (!$themeManager->setActiveTheme($systemName)) {
            $this->error(sprintf('Failed to activate theme [%s]', $systemName));
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
