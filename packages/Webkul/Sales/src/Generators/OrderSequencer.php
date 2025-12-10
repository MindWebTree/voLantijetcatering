<?php

namespace Webkul\Sales\Generators;

use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderSequencer extends Sequencer
{
    /**
     * Create order sequencer instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setAllConfigs();
    }

    /**
     * Set all configs.
     *
     * @param  string  $configKey
     * @return void
     */
    public function setAllConfigs()
    {
        $this->prefix = core()->getConfigData('sales.orderSettings.order_number.order_number_prefix');

        $this->length = core()->getConfigData('sales.orderSettings.order_number.order_number_length');

        $this->suffix = core()->getConfigData('sales.orderSettings.order_number.order_number_suffix');

        $this->generatorClass = core()->getConfigData('sales.orderSettings.order_number.order_number_generator_class');

        $this->lastId = $this->getLastId();
    }

    /**
     * Get last id.
     *
     * @return int
     */
    public function getLastId()
    {
log::info('getlastId function');
        $lastOrder = Order::query()->orderBy('id', 'desc')->limit(1)->first();
       $number =  $lastOrder ? $lastOrder->id : 0;
log::info('number',['number'=>$number]);
return $number;
    }
}
