<?php

namespace HiFolks\JetTranslations;

use HiFolks\JetTranslations\Console\JetTranslationsExtractor;
use Illuminate\Support\ServiceProvider;

class JetTranslationsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        //$this->loadJsonTranslationsFrom(__DIR__.'/../resources/lang' );
        $this->loadJsonTranslationsFrom(resource_path('lang/vendor/jet-translations'));
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'jet-translations');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('jet-translations.php'),
            ], 'config');

            // Publishing the views.
            /*$this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/jet-translations'),
            ], 'views');*/

            // Publishing assets.
            /*$this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/jet-translations'),
            ], 'assets');*/

            // Publishing the translation files.
            $this->publishes([
                __DIR__ . '/../resources/lang' => resource_path('lang/vendor/jet-translations'),
            ], 'lang');

            // Registering package commands.
            $this->commands([
                    JetTranslationsExtractor::class
            ]);
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'jet-translations');

        // Register the main class to use with the facade
        $this->app->singleton('jet-translations', function () {
            return new JetTranslations();
        });
    }
}
