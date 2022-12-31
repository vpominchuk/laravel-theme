![](https://banners.beyondco.de/Laravel%20Theme.png?theme=light&packageManager=composer+require&packageName=vpominchuk%2Flaravel-theme&pattern=texture&style=style_1&description=Add+multiple+theme+support+to+your+Laravel+application&md=1&showWatermark=0&fontSize=100px&images=https%3A%2F%2Flaravel.com%2Fimg%2Flogomark.min.svg)
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


## Available artisan commands
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
You can easily switch between your themes in two different ways:

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

Follow [this documentation](https://pominchuk.com/post/11-add-multiple-theme-support-for-laravel-application) to make your own `ThemeManager` implementation.

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