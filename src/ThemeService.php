<?php


namespace VPominchuk\LaravelThemeSupport;


use Exception;
use VPominchuk\LaravelThemeSupport\Dto\ThemeDto;

class ThemeService
{
    private \Illuminate\Filesystem\Filesystem $filesystem;
    private string $relativeThemesPath;

    public function __construct()
    {
        $this->filesystem = app('files');
        $this->relativeThemesPath = config('theme.path') ?? 'themes';
    }

    private function getThemesFolderPath($path = null): string
    {
        if ($path === null) {
            return base_path($this->relativeThemesPath);
        }

        return base_path($this->relativeThemesPath) . DIRECTORY_SEPARATOR . $path;
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
        $themeIndexes = glob($this->getThemesFolderPath() . DIRECTORY_SEPARATOR . "*");

        foreach ($themeIndexes as $jsonFile) {
            try {
                $themeDto = $this->getThemeInfo(basename($jsonFile));
                $themeInfo = $themeDto->toArray();
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

    public function getThemeInfo(string $systemName): ?ThemeDto
    {
        $theme = new Theme($systemName);

        return $theme->getInfo();
    }

    public function getThemeInfoFlatten(string $systemName): ?array
    {
        $themeDto = $this->getThemeInfo($systemName);

        if ($themeDto->getSystemName() === null) {
            return null;
        }

        $info = collect($this->arrayDot($themeDto->toArray()));

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
        $themesPath = $this->getThemesFolderPath();
        $themePath = $this->getThemesFolderPath($dto->getSystemName());

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

        throw_if(
            !$this->makeDirectory($themePath . DIRECTORY_SEPARATOR . $dto->getPublicPath()),
            \RuntimeException::class,
            "Could not create directory " . $themePath . DIRECTORY_SEPARATOR . $dto->getPublicPath()
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
        $source = resource_path('views');
        $destination = $this->getThemesFolderPath($dto->getSystemName()) . DIRECTORY_SEPARATOR . $dto->getViewsPath();

        return $this->filesystem->copyDirectory($source, $destination);
    }

    public function folderExists(string $systemName): bool
    {
        $path = $this->getThemesFolderPath($systemName);
        return $this->filesystem->exists($path);
    }

    private function getLinkRelativePath(string $path): string
    {
        return implode(
            DIRECTORY_SEPARATOR,
            array_fill(0, count(explode("/", $path)), '..')
        );
    }

    public function createPublicSymlink(string $systemName): bool
    {
        $public = base_path('public') . DIRECTORY_SEPARATOR . 'themes';

        if (!$this->makeDirectory($public)) {
            return false;
        }

        $symlinkDestination = $public . DIRECTORY_SEPARATOR . $systemName;
        $linkRelativePath = $this->getLinkRelativePath($this->relativeThemesPath . DIRECTORY_SEPARATOR . $systemName);

        $theme = new Theme($systemName);

        if ($this->filesystem->exists($symlinkDestination)) {
            $this->filesystem->delete($symlinkDestination);
        }

        $this->filesystem->link(
            $linkRelativePath . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . $systemName . DIRECTORY_SEPARATOR . $theme->getInfo()->getPublicPath(),
            $symlinkDestination
        );

        return $this->filesystem->exists($symlinkDestination);
    }
}
