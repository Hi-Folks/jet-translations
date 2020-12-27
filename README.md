# Jet Translations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hi-folks/jet-translations.svg?style=flat-square)](https://packagist.org/packages/hi-folks/jet-translations)
[![Total Downloads](https://img.shields.io/packagist/dt/hi-folks/jet-translations.svg?style=flat-square)](https://packagist.org/packages/hi-folks/jet-translations)

This package includes:
- translations strings (in json format) for Jetstream auth view and components
- command for extractig translation strings from view templates (Blade files)

## About translation strings
Json files are:
- resources/lang/vendor/jet-translations/auth
- resources/lang/vendor/jet-translations/components

### Translations available
Currently, just italian language is supported

### Do you want to contribute translating strings ?


## About Jetstream
[Laravel Jetstream](https://jetstream.laravel.com/1.x/introduction.html) includes login, registration, email verification, two-factor authentication, session management, API support via Laravel Sanctum, and optional team management.
With Livewire, Jetstream provides Blade templates with [Translation Strings](https://laravel.com/docs/8.x/localization#using-translation-strings-as-keys).
Blade templates provided by Jetstream are ready to be translated, they use the __() helper :
```php
__("Dashboard");
```


## Installation

You can install the package via composer:

```bash
composer require hi-folks/jet-translations
```

## Usage

``` php

```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Roberto Butti](https://github.com/hi-folks)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
