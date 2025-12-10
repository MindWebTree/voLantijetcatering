<?php

namespace ACME\CateringPackage\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use ACME\CateringPackage\Contracts\Delivery_location_airport as Delivery_location_airportContract;

class Delivery_location_airport extends Model implements Delivery_location_airportContract
{   
    // use SoftDeletes;
    // protected $dates = ['deleted_at'];
    
    protected $table="delivery_location_airports";
    protected $fillable = [
        'name',
        'address',
        'zipcode',
        'state',
        'country',
        'latitude',
        'longitude', 
        'display_order',
        'active',
    ];

}