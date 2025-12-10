<?php

namespace Webkul\MpAuthorizeNet\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MpAuthorizeNet\Contracts\MpAuthorizeNetCart as MpAuthorizeNetCartContract;

class MpAuthorizeNetCart extends Model implements MpAuthorizeNetCartContract
{
    protected $table = 'mpauthorizenet_cart';

    protected $fillable = [
        'cart_id', 'mpauthorizenet_token'
    ];
}