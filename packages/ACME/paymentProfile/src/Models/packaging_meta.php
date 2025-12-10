<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\packaging_meta as packaging_metaContract;

class packaging_meta extends Model implements packaging_metaContract
{


    protected $table = 'packaging_meta';
    protected $fillable = ['packaging_id','item_id','qty','product_id'];
}