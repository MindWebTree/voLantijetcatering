<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderNote extends Mailable
{
    use Queueable, SerializesModels;
    public $comment;
    public $order;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($comment,$order)
    {
        $this->comment = $comment;
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('your volantijetcatering order #'. $this->order->id .' updates')->view('paymentprofile::admin.sales.orders.mail.orderNotes');
    }
}