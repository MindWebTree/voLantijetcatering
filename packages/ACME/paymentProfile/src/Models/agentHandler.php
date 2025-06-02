<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\agentHandler as agentHandlerContract;

class agentHandler extends Model implements agentHandlerContract
{
    protected $table='handling-agent';
    protected $fillable = ['order_id','Name','PPR_Permit','Handling_charges','Mobile'];

}