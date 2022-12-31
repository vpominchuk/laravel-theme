<?php

namespace VPominchuk\LaravelThemeSupport\Commands;

use Illuminate\Support\Str;
use VPominchuk\LaravelThemeSupport\Dto\ThemeDto;
use VPominchuk\LaravelThemeSupport\ThemeService;
use Illuminate\Console\Command;

class ThemeCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new theme';

    public array $namespaces = [];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ThemeService $themeService)
    {
        $dto = new ThemeDto();

        $systemName = $this->ask('Enter theme folder name (relative path)');

        if (!$systemName) {
            $this->error('Folder name is required.');
            return Command::FAILURE;
        }

        $normalizedSystemName = Str::slug($systemName, '_');

        if ($normalizedSystemName !== $systemName) {
            $systemName = $normalizedSystemName;

            $this->line('Folder Name: ' . $systemName);
        }

        if ($themeService->folderExists($systemName)) {
            $this->error(sprintf('Folder [%s] already exists.', $systemName));
            return Command::FAILURE;
        }

        $dto->setSystemName($systemName);

        $defaultName = ucfirst(Str::replace(['-', '_'], ' ', $systemName));
        $dto->setName($this->ask('Enter theme name (Human readable name)', $defaultName));

        $dto->setDescription($this->ask('Theme description', ''));
        $dto->setLicense($this->ask('Theme licanse', 'MIT'));
        $dto->setRepository($this->ask('Theme source code repository', ''));
        $dto->setAuthorName($this->ask('Author name', ''));
        $dto->setAuthorEmail($this->ask('Author email', ''));
        $dto->setAuthorWebsite($this->ask('Author website', ''));
        $dto->setViewsPath($this->ask('Set theme views path', 'views'));

        if ($this->confirm('Would you like to setup a folders for namespaced views?', true)) {
            $this->askForNamespaces();
        }

        $dto->setNamespaces($this->namespaces);

        $dto->setPublicPath($this->ask('Set theme public path (path to your css, js and other assets)', 'public'));

        $themePath = $themeService->create($dto);

        $this->info(sprintf('Theme [%s] successfully created.', $dto->getName()));
        $this->newLine();
        $this->line("You are almost ready to go. One more step.");
        $this->line(sprintf("You need to copy your views to newly created theme [%s/%s]", $themePath, $dto->getViewsPath()));

        if ($this->confirm('Can I do it for you?', true)) {
            if ($themeService->copyViews($dto)) {
                $this->line('Woohoo!! Well done!!');
                $this->newLine();
                $this->line('To activate a newly created theme, run: ');
                $this->info('php artisan theme:activate ' . $dto->getSystemName());
            } else {
                $this->error('Oops, something went wrong. I could not copy views. I\'m sorry.');
            }
        }

        $this->newLine();
        $this->line('To get list of useful commands to manage your themes, run:');
        $this->info('php artisan list theme');

        return Command::SUCCESS;
    }

    public function askForNamespaces()
    {
        $namespace = $this->ask('Enter namespace name', 'errors');
        $path = $this->ask('Enter folder name', 'views/' . $namespace . '/');

        $this->namespaces[$namespace] = $path;

        if ($this->confirm('Would you like to add one more namespace?', false)) {
            $this->askForNamespaces();
        }
    }
}
