<?php

namespace ACME\paymentProfile\Models;

use Illuminate\Database\Eloquent\Model;
use ACME\paymentProfile\Contracts\OrderNotes as OrderNotesContract;

class OrderNotes extends Model implements OrderNotesContract
{
    protected $table = 'order_notes';
    protected $fillable = ['order_id','notes','user_id','is_admin','customer_notified'];
}