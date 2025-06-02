<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\invoiceImage as invoiceImageContract;

class invoiceImage extends Model implements invoiceImageContract
{
    protected $table='invoice-images';
    protected $fillable = [];
}