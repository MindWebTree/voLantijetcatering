<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\CustomerInquery as CustomerInqueryContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerInquery extends Model implements CustomerInqueryContract
{
    use HasFactory;
    protected $table = 'customer_inquerys';
    protected $fillable = [
        'id',
        'fname',
        'lname',
        'email',
        'mobile_number',
        'message',
    ];
}