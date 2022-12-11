<?php

namespace VPominchuk\LaravelThemeSupport\Commands;

use VPominchuk\LaravelThemeSupport\ThemeService;
use Illuminate\Console\Command;

class ThemeList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show installed themes list';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThemeService $themeService)
    {
        $this->line('Themes folder:' . base_path(config('theme.path')));

        $themes = collect($themeService->getThemes())->map(function($theme) {
            return [
                'system_name' =>
                    ($theme['system_name'] === config('theme.theme') ? '* ' : '  ') . $theme['system_name'],
                'name' => $theme['name'] ?? '',
                'description' => $theme['description'] ?? '',
                'author' => $theme['author']['name'] ?? '',
            ];
        });

        if ($themes->isNotEmpty()) {
            $this->table(
                ['System Name', 'Name', 'Description', 'Author'],
                $themes
            );
        } else {
            $this->info('No themes found');
        }

        $this->line('* - active theme');

        return Command::SUCCESS;
    }
}
