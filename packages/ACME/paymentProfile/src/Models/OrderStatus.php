<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\OrderStatus as OrderStatusContract;

class OrderStatus extends Model implements OrderStatusContract
{
    protected $fillable = [];
}