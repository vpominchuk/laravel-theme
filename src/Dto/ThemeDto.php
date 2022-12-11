<?php

namespace VPominchuk\LaravelThemeSupport\Dto;

class ThemeDto
{
    private array $data = [
        'name' => '',
        'description' => '',
        'license' => '',
        'repository' => '',
        'author' => [
            'name' => '',
            'email' => '',
            'website' => []
        ],
        'paths' => [
            'views' => 'views',
            'namespaces' => [
                'errors' => 'views/errors/'
            ],
            'public' => 'public'
        ],
    ];
    private ?string $systemName = null;

    public function __construct(array $data = [])
    {
        if (!empty($data)) {
            $this->data = $data;
            $this->setSystemName($data['system_name']);
            unset($data['system_name']);
        }
    }

    public function setName(string $name): ThemeDto
    {
        $this->data['name'] = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->data['name'];
    }

    public function setDescription(string $description): ThemeDto
    {
        $this->data['description'] = $description;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->data['description'];
    }

    public function setLicense(string $license): ThemeDto
    {
        $this->data['license'] = $license;
        return $this;
    }

    public function getLicense(): string
    {
        return $this->data['license'];
    }

    public function setRepository(string $repository): ThemeDto
    {
        $this->data['repository'] = $repository;
        return $this;
    }

    public function getRepository(): string
    {
        return $this->data['repository'];
    }

    public function setAuthorName(string $name): ThemeDto
    {
        $this->data['author']['name'] = $name;
        return $this;
    }

    public function getAuthorName(): string
    {
        return $this->data['author']['name'];
    }

    public function setAuthorEmail(string $email): ThemeDto
    {
        $this->data['author']['email'] = $email;
        return $this;
    }

    public function getAuthorEmail(): string
    {
        return $this->data['author']['email'];
    }

    public function setAuthorWebsite(string $url): ThemeDto
    {
        $this->data['author']['website'] = $url;
        return $this;
    }

    public function getAuthorWebsite(): string
    {
        return $this->data['author']['website'];
    }

    public function setViewsPath(string $path): ThemeDto
    {
        $this->data['paths']['views'] = $path;
        return $this;
    }

    public function getViewsPath(): string
    {
        return $this->data['paths']['views'];
    }

    public function setPublicPath(string $path): ThemeDto
    {
        $this->data['paths']['public'] = $path;
        return $this;
    }

    public function getPublicPath(): string
    {
        return $this->data['paths']['public'];
    }

    public function setNamespaces(array $namespaces): ThemeDto
    {
        $this->data['paths']['namespaces'] = $namespaces;
        return $this;
    }

    public function getNamespaces(): array
    {
        return $this->data['paths']['namespaces'];
    }

    public function getSystemName(): ?string
    {
        return $this->systemName;
    }

    public function setSystemName(?string $systemName): ThemeDto
    {
        $this->systemName = $systemName;
        return $this;
    }

    public function toArray(): array
    {
        return $this->data;
    }



}
