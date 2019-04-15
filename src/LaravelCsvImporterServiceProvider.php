<?php

namespace Aaronbell1\LaravelCsvImporter;

use Illuminate\Support\ServiceProvider;

class LaravelCsvImporterServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function register()
    {
        $this->app->bind('Aaronbell1\LaravelCsvImporter\CsvLoader', function () {
            return new CsvLoader();
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
