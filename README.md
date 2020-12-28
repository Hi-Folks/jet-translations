# Jet Translations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hi-folks/jet-translations.svg?style=flat-square)](https://packagist.org/packages/hi-folks/jet-translations)
[![Total Downloads](https://img.shields.io/packagist/dt/hi-folks/jet-translations.svg?style=flat-square)](https://packagist.org/packages/hi-folks/jet-translations)

This package includes:
- translation strings (in json format) for Jetstream auth view and components
- translation template file  (in json format) for Jetstream auth view and components (useful for who wants starting to translate strings in a new language)
- command for extracting translation strings from view templates (Blade files)

## Installation

You can install the package via composer:

```bash
composer require hi-folks/jet-translations
```

### Publish the languages
```bash
php artisan vendor:publish --provider="HiFolks\JetTranslations\JetTranslationsServiceProvider" --tag="lang" --force
```

### Usage
Install this package in your Laravel Jetstream application and enable a language in your config/app.php via locale configuration:
```
// config/app.php file
'locale' => 'it',
```

## About translation strings
Json files is located in this package in the directory:
- resources/lang/_lang_.json

### Translations available
Currently, just italian language is supported.


## About translations template file
If you don't find translations in your native language, you could contribute to Jetstream, translating strings in your native language.
### Do you want to contribute translating strings ?
To help you I created a "template" json file to start the translation process.
You can find _resources/lang/template-lang.json_ with all untranslated strings. My suggestion is to copy this file in _resource/lang/xy.lang, where xy is the code of your language (de, en, fr, etc...).

## About Command line usage
The JetTranslations package is shipped with an artisan command:
```bash
php artisan jet-trans:extract
```

This command:
- it parses all blade files in vendor/laravel/jetstream/stubs/livewire/resources/views
- it extracts strings defined in __("");
- it checks if there is some missing keys in ./resources/lang/vendor/jet-translations/it.json (ot the json of the specified language via --language option)
  
This command could save the json file using --save-json options.
For example using:
```shell
php artisan jet-trans:extract --language=de --save-json
```
It saves _de.json_ file in resources/lang/vendor/jet-translations/ directory of your Laravel app.




## About Jetstream
[Laravel Jetstream](https://jetstream.laravel.com/1.x/introduction.html) includes login, registration, email verification, two-factor authentication, session management, API support via Laravel Sanctum, and optional team management.
With Livewire, Jetstream provides Blade templates with [Translation Strings](https://laravel.com/docs/8.x/localization#using-translation-strings-as-keys).
Blade templates provided by Jetstream are ready to be translated, they use the __() helper :
```php
__("Dashboard");
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
