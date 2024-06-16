<?php

namespace Weirdo\Helper;

/**
 * @license MIT
 * @package Weirdo\Entrust
 */
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__.'/../config/config.php' => app()->basePath() . '/config/helper.php',
        ]);
    }

    /**
     * Register the service provider.
     * @return void
     */
    public function register()
    {
        //
    }
}
