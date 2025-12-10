<?php

namespace Webkul\MpAuthorizeNet\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\MpAuthorizeNet\Contracts\CustomerProfileLog as CustomerProfileLogContract;

class CustomerProfileLog extends Model implements CustomerProfileLogContract
{
    protected $table = 'customer_payment_profile';
    protected $fillable = ['profile_id', 'payment_id', 'payment_profile_id', 'customer_id', 'email', 'customer_token', 'order_id', 'airport', 'billing_address'];
}