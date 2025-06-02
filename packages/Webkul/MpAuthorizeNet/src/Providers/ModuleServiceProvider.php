<?php

namespace Webkul\MpAuthorizeNet\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\MpAuthorizeNet\Models\MpAuthorizeNetCart::class,
        \Webkul\MpAuthorizeNet\Models\MpAuthorizeNet::class,
    ];
}