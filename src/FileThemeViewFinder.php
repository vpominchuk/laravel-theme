<?php

namespace VPominchuk\LaravelThemeSupport;

use Illuminate\View\FileViewFinder;

class FileThemeViewFinder extends FileViewFinder
{
    protected array $themeHints = [];

    public function setViewPath(string $path): FileThemeViewFinder
    {
        $this->prependLocation($path);

        return $this;
    }

    /**
     * @param string $namespace
     * @param array|string $paths
     * @return $this
     */
    public function setThemeNamespace(string $namespace, $paths): FileThemeViewFinder
    {
        $this->themeHints[$namespace] = $paths;

        return $this;
    }

    protected function findNamespacedView($name): string
    {
        $this->hints = array_merge_recursive($this->themeHints, $this->hints);

        return parent::findNamespacedView($name);
    }

    public function setTheme(Theme $theme): FileThemeViewFinder
    {
        $this->setViewPath($theme->getViewPath());

        $nameSpaces = $theme->getNamespaces();

        if ($nameSpaces) {
            foreach ($nameSpaces as $nameSpace => $path) {
                $this->setThemeNamespace($nameSpace, $path);
            }
        }

        return $this;
    }
}
