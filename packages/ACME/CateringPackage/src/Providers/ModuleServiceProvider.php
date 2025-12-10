<?php

namespace ACME\CateringPackage\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    protected $models = [
        \ACME\CateringPackage\Models\Delivery_location_airport::class,
    ];
}