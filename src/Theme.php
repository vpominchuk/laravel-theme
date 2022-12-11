<?php


namespace VPominchuk\LaravelThemeSupport;


use VPominchuk\LaravelThemeSupport\Dto\ThemeDto;
use \Illuminate\Filesystem\Filesystem;

class Theme
{
    private ?string $themeSystemName = null;
    private ?ThemeDto $themeInfo = null;
    private ?Filesystem $filesystem = null;

    public function __construct(string $themeSystemName, ?Filesystem $filesystem = null)
    {
        if ($filesystem) {
            $this->setFilesystem($filesystem);
        }

        $this->themeSystemName = $themeSystemName;
        $this->themeInfo = $this->load();
    }

    public function setFilesystem(Filesystem $filesystem): Theme
    {
        $this->filesystem = $filesystem;

        return $this;
    }

    public function getFilesystem(): Filesystem
    {
        if ($this->filesystem) {
            return $this->filesystem;
        }

        return app('files');
    }

    public function valid(): bool
    {
        return $this->themeInfo->getViewsPath() &&
            file_exists($this->getThemePath($this->themeInfo->getViewsPath()));
    }

    public function load(): ThemeDto
    {
        $fileSystem = $this->getFilesystem();

        $jsonFile = base_path(config('theme.path')) . DIRECTORY_SEPARATOR .
            $this->themeSystemName . DIRECTORY_SEPARATOR .
            "index.json";

        if (!$fileSystem->exists($jsonFile)) {
            return new ThemeDto();
        }

        $themeInfo = json_decode($fileSystem->get($jsonFile), true, 512, JSON_THROW_ON_ERROR);
        $themeInfo['system_name'] = basename(dirname($jsonFile));

        return new ThemeDto($themeInfo);
    }

    public function getInfo(): ?ThemeDto
    {
        return $this->themeInfo;
    }

    public function getThemePath(string $path = ''): string
    {
        return base_path(config('theme.path') . DIRECTORY_SEPARATOR .
            $this->themeSystemName . DIRECTORY_SEPARATOR . $path
        );
    }

    public function getViewPath(): string
    {
        return $this->getThemePath($this->themeInfo->getViewsPath() ?? 'views');
    }

    public function getNamespaces(): ?array
    {
        $nameSpaces = $this->themeInfo->getNamespaces();

        if (empty($nameSpaces)) {
            return [];
        }

        array_walk($nameSpaces, function(&$path, $nameSpace) {
            $path = $this->getThemePath($path);
        });

        return $nameSpaces;
    }
}
