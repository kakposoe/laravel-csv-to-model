<?php

namespace Kakposoe\CsvToModel;

use Illuminate\Support\ServiceProvider;

class CsvToModelServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'kakposoe');
        // $this->loadViewsFrom(__DIR__.'/../resources/views', 'kakposoe');
        // $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        // $this->loadRoutesFrom(__DIR__.'/routes.php');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/csvtomodel.php', 'csvtomodel');

        // Register the service the package provides.
        $this->app->singleton('csvtomodel', function ($app) {
            return new CsvToModel;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['csvtomodel'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/csvtomodel.php' => config_path('csvtomodel.php'),
        ], 'csvtomodel.config');

        // Publishing the views.
        /*$this->publishes([
            __DIR__.'/../resources/views' => base_path('resources/views/vendor/kakposoe'),
        ], 'csvtomodel.views');*/

        // Publishing assets.
        /*$this->publishes([
            __DIR__.'/../resources/assets' => public_path('vendor/kakposoe'),
        ], 'csvtomodel.views');*/

        // Publishing the translation files.
        /*$this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/kakposoe'),
        ], 'csvtomodel.views');*/

        // Registering package commands.
        // $this->commands([]);
    }
}
