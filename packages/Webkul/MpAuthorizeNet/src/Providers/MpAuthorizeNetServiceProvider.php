<?php

namespace Webkul\MpAuthorizeNet\Providers;

use Illuminate\Support\ServiceProvider;

class MpAuthorizeNetServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        include __DIR__ . '/../Http/routes.php';

        include __DIR__ . '/../Http/front-routes.php';
        
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'mpauthorizenet');
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'mpauthorizenet');

        $this->publishes([
            dirname(__DIR__) . '/Resources/assets/js' => base_path('public/vendor/webkul/mpauthorizenet/assets/js')
        ]);

        $this->app->register(EventServiceProvider::class);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfig();
    }

    /**
     * Merge the stripe connect's configuration with the admin panel
     */
    public function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/front-menu.php', 'menu.customer'
        );

    }
}