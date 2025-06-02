<?php

namespace Webkul\MpAuthorizeNet\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\MpAuthorizeNet\Contracts\MpAuthorizeNet as MpAuthorizeNetContract;

class MpAuthorizeNet extends Model implements MpAuthorizeNetContract
{
    protected $table = 'mpauthorizenet_cards';

    protected $fillable = ['token', 'customers_id','last_four', 'misc','is_default'];
}