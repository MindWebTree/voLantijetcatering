<?php

namespace Webkul\Shop\src\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiRequestLog extends Model
{
    use HasFactory;
    
    protected $table="api_request_log";
    protected $fillable = [
        'Id',
        'Device_id',
        'Device_name',
        'Os',
        'Customer_id',
        'Url',
        'created_at', 
        'updated_at',
    ];


}
