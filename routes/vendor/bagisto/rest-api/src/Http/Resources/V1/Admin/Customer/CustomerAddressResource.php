<?php

namespace Webkul\RestApi\Http\Resources\V1\Admin\Customer;

use Illuminate\Http\Resources\Json\JsonResource;

class CustomerAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */

    //  sandeep add delivery date,time, airport fbo id and airport name 
    public function toArray($request)
    {
            return [
            'id'             => $this->id,
            'first_name'     => $this->first_name,
            'last_name'      => $this->last_name,
            'company_name'   => $this->company_name,
            // 'delivery_date'  => $this->delivery_date,
            // 'delivery_time'  => $this->delivery_time,
            'vat_id'         => $this->vat_id,
            'airport_name'   => $this->airport_name,
            'address1'       => explode(PHP_EOL, $this->address1),
            'country'        => $this->country,
            'airport_fbo_id' => $this->airport_fbo_id,
            'country_name'   => core()->country_name($this->country),
            'state'          => $this->state,
            'city'           => $this->city,
            'customer_id'    => $this->customer_id,
            'postcode'       => $this->postcode,
            'phone'          => $this->phone,
            'is_default'     => $this->default_address,
            'created_at'     => $this->created_at,
            'updated_at'     => $this->updated_at,
        ];
    }
}
