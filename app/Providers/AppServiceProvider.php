<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Mail;
use App\Mail\GraphTransport;
use App\Services\MicrosoftGraphMailService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
Mail::extend('graph', function () {
    return new \App\Mail\GraphTransport(
        app(\App\Services\MicrosoftGraphMailService::class)
    );
});
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {    //umesh add only this line
        $this->app->register(\L5Swagger\L5SwaggerServiceProvider::class);
    }
}
