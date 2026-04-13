<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class CustomChargesService
{
    /*
    |--------------------------------------------------------------------------
    | FBO FEE
    |--------------------------------------------------------------------------
    */
    public function getFboFee($order)
    {
        $defaultPercent = config('customChargesConfig.default_fbo_percent', 10);
        $subTotal = $order->sub_total ?? 0;
        $airportFboId = optional($order->shipping_address)->airport_fbo_id;

        $percent = $airportFboId
            ? DB::table('airport_fbo_details')
                ->where('id', $airportFboId)
                ->value('fbo_fee')
            : null;

        $percent = $percent ?? $defaultPercent;

        return round(($subTotal * $percent) / 100, 2);
    }

    /*
    |--------------------------------------------------------------------------
    | DELIVERY FEE
    |--------------------------------------------------------------------------
    */
    public function getDeliveryFee($order)
    {
        $defaultFee = config('customChargesConfig.default_delivery_fee', 0);

        $airportFboId = optional($order->shipping_address)->airport_fbo_id;
        $airportId    = optional($order->shipping_address)->airport_id;


        $fee = $airportFboId
            ?
            DB::table('airport_fbo_details')->where('id', $airportFboId)->value('delivery_fee')
            ??
            DB::table('delivery_location_airports')->where('id', $airportId)->value('delivery_fee')
            ??
            $defaultFee
            :
            $defaultFee;

        return (float) $fee;
    }
}