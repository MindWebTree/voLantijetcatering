<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderAccept extends Mailable
{
    use Queueable, SerializesModels;
    public $order;  // Define a public property to store the order data
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
 $increment_id = $this->order->increment_id;
        return $this->subject('Order Accepted'. ' #' . $increment_id)->view('paymentprofile::admin.sales.orders.mail.orderAccept');
    
    }
}
