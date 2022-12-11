<?php


namespace VPominchuk\LaravelThemeSupport;


use Exception;
use VPominchuk\LaravelThemeSupport\Dto\ThemeDto;

class ThemeService
{
    private \Illuminate\Filesystem\Filesystem $filesystem;

    public function __construct()
    {
        /** @var FileThemeViewFinder $fileThemeViewFinder */
        $fileThemeViewFinder = app('view.finder');
        $this->filesystem = $fileThemeViewFinder->getFilesystem();
    }

    private function arrayDot($array, $prepend = '')
    {
        $results = array();
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $results = array_merge($results, $this->arrayDot($value, $prepend . $key . '.'));
            } else {
                $results[$prepend . $key] = $value;
            }
        }
        return $results;
    }

    public function getThemes(): array
    {
        $result = [];
        $themeIndexes = glob(base_path(config('theme.path')) . DIRECTORY_SEPARATOR . "*");

        foreach ($themeIndexes as $jsonFile) {
            try {
                $themeInfo = $this->getThemeInfo(basename($jsonFile));
            } catch (Exception $e) {
                $themeInfo = [
                    'name' => 'ERROR',
                    'description' => 'Incorrect ' . $jsonFile . ' file'
                ];
            }

            $result[] = $themeInfo;
        }

        return $result;
    }

    public function getThemeInfo(string $systemName): ?array
    {
        $theme = new Theme($systemName);

        return optional($theme->getInfo())->toArray();
    }

    public function getThemeInfoFlatten(string $systemName): ?array
    {
        $themeInfo = $this->getThemeInfo($systemName);

        if ($themeInfo === null) {
            return null;
        }

        $info = collect($this->arrayDot($themeInfo));

        return $info->map(function($value, $key) {
            return [ $key, $value ];
        })->values()->toArray();
    }

    private function makeDirectory(string $path): bool
    {
        if (!$this->filesystem->exists($path)) {
            return $this->filesystem->makeDirectory($path, 0755, true);
        }

        return true;
    }

    public function create(ThemeDto $dto): string
    {
        $themesPath = base_path(config('theme.path'));
        $themePath = $themesPath . DIRECTORY_SEPARATOR . $dto->getSystemName();

        throw_if(
            !$this->makeDirectory($themesPath),
            \RuntimeException::class,
            "Could not create directory " . $themesPath
        );

        if (!$this->filesystem->isWritable($themesPath)) {
            throw new \RuntimeException("Themes directory is not writable. [" . $themesPath ."]");
        }

        throw_if(
            !$this->makeDirectory($themePath),
            \RuntimeException::class,
            "Could not create directory " . $themePath
        );

        $this->filesystem->put(
            $themePath . DIRECTORY_SEPARATOR . 'index.json',
            json_encode($dto->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );

        throw_if(
            !$this->makeDirectory($themePath . DIRECTORY_SEPARATOR . $dto->getViewsPath()),
            \RuntimeException::class,
            "Could not create directory " . $themePath . DIRECTORY_SEPARATOR . $dto->getViewsPath()
        );

        $namespaces = $dto->getNamespaces();

        foreach ($namespaces as $namespace) {
            throw_if(
                !$this->makeDirectory($themePath . DIRECTORY_SEPARATOR . $namespace),
                \RuntimeException::class,
                "Could not create directory " . $themePath . DIRECTORY_SEPARATOR . $namespace
            );
        }

        return $themePath;
    }

    public function copyViews(ThemeDto $dto): bool
    {
        $themesPath = base_path(config('theme.path'));

        $source = resource_path('views');
        $destination = $themesPath . DIRECTORY_SEPARATOR .
            $dto->getSystemName() . DIRECTORY_SEPARATOR .
            $dto->getViewsPath();

        return $this->filesystem->copyDirectory($source, $destination);
    }
}
