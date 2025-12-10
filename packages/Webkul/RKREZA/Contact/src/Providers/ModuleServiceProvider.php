<?php

namespace Webkul\RKREZA\Contact\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \Webkul\RKREZA\Contact\Models\Contact::class
    ];
}