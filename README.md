# Laravel Multiple Theme
A package that allows developers to implement 
multiple theme support to their Laravel applications quickly and 
easily.

**Laravel Theme** package allows you to convert your `resources/views/` to independent theme.

**Laravel Theme** allows you to easily add multiple themes to your Laravel application. It provides the ability to create themes.
It also offers a theme helper commands to allow for easy switch between themes, list available themes and more... 

**Laravel Theme** is a great way to quickly add theme support to your Laravel application, allowing you to customize the look and feel of your app.

## Installation
```shell
$ composer require vpominchuk/laravel-theme
$ php artisan vendor:publish --tag=config
```

## Converting your views to a theme
As long as your views located in `resources/views/` you can convert them to a theme, just run:
```shell
$ php artisan theme:create
```
and answer some general question.


## Available Artisan commands
To get list of available artisan commands try:
```shell
$ php artisan theme list
```

| Command                       | Description           |
|-------------------------------|-----------------------|
| `theme:list`                  | Show available themes |
| `theme:info {theme_name}`     | Get theme information |
| `theme:create`                | Create a new theme    |
| `theme:activate {theme_name}` | Activate theme        |

## Switching between themes
You can easily switch between youe themes in two different ways:

1. Using artisan command
```shell
$ php artisan theme:activate {theme_name}
```

2. Programmatically
```php
use VPominchuk\LaravelThemeSupport\Contracts\ThemeManager;

/** @var ThemeManager $themeManager */
$themeManager = app(ThemeManager::class);
$themeManager->setActiveTheme($systemName); 
```

## Customizing `ThemeManager`

By default, `ThemeManager` class stores information about active theme in 
`framework/theme.json` file.
```json
{
    "active_theme": "default"
}
```
In real application you might want to use your own mechanism to store information about active theme.

To do it, you can create a new class, for instance:

```php
namespace App\Services\Theme;


use App\Facades\Settings;
use VPominchuk\LaravelThemeSupport\Contracts\ThemeManager as ThemeManagerInterface;
use VPominchuk\LaravelThemeSupport\ThemeService;

class ThemeManager implements ThemeManagerInterface
{
    public function __construct(private ThemeService $themeService)
    { }

    public function getActiveTheme(): ?string
    {
        return Settings::get('ACTIVE_THEME');
    }

    public function setActiveTheme(string $systemName): bool
    {
        Settings::set('ACTIVE_THEME', $systemName);

        return $this->themeService->createPublicSymlink($systemName);
    }
}
```

Where `Settings` class is your Facade/Service/Model... which saves settings for your application.
Replace it with your own class/method.

## Security

If you discover any security related issues, please use the issue tracker.

## Credits

- [Vasyl Pominchuk](https://pominchuk.com/)

## Contributing
Feel free to make any suggestions on the issues or create a pull request. 
I'll be very happy. 

See [CONTRIBUTING.md](CONTRIBUTING.md) for more information about how to contribute.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.