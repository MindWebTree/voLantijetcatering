<?php

namespace Webkul\MpAuthorizeNet\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Event::listen('bagisto.shop.checkout.payment-method.after', function($viewRenderEventManager){
            $viewRenderEventManager->addTemplate('mpauthorizenet::shop.checkout.card');
        });

        Event::listen('bagisto.shop.layout.head', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('mpauthorizenet::shop.style');
        });

        Event::listen('bagisto.shop.layout.body.after', function($viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('mpauthorizenet::shop.checkout.card-script');
        });
    }
}

