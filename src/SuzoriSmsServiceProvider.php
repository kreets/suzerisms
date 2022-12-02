<?php

namespace Kreets\SuzoriSms;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class SuzoriSmsServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind('suzorisms', SuzoriSms::class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        AliasLoader::getInstance()->alias('SuzoriSMS', 'Kreets\SuzoriSms\Facade\SuzoriSms');

        $config = __DIR__.'/Config/suzorisms.php';

        $this->publishes([
            $config => config_path('suzorisms.php'),
        ], 'config');

        $this->mergeConfigFrom( $config, 'suzorisms');

    }
}