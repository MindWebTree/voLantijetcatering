<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class adminOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $fboDetails;
    public $extraData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order ,$fboDetails, $extraData = [])
    {
        $this->order = $order;
        $this->fboDetails = $fboDetails;
        $this->extraData = $extraData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $increment_id = $this->order['increment_id'];
        // Log::info('admin page');
        return $this->subject(trans('shop::app.mail.order.subject'). ' #' . $increment_id)->view('mail.admin-order-notify');

    }
    
}