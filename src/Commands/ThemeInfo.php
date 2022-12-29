<?php

namespace VPominchuk\LaravelThemeSupport\Commands;

use VPominchuk\LaravelThemeSupport\ThemeService;
use Illuminate\Console\Command;

class ThemeInfo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:info {theme_system_name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show theme information';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThemeService $themeService)
    {
        $systemName = $this->argument('theme_system_name');

        $info = $themeService->getThemeInfoFlatten($systemName);

        if ($info === null) {
            $this->info('Theme [' . $systemName . '] does not found');
        } else {
            $this->table(
                ['Key', 'Value'],
                $info
            );
        }

        return Command::SUCCESS;
    }
}
