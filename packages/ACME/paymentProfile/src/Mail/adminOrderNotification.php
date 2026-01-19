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

     public $fboAdditionalNotes;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order ,$fboDetails, $extraData = [],$fboAdditionalNotes)
    {
        $this->order = $order;
        $this->fboDetails = $fboDetails;
        $this->extraData = $extraData;
        $this->fboAdditionalNotes = $fboAdditionalNotes;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        log::info('Admin order email BUILD successful', [
    
        'notes'    => $this->fboAdditionalNotes
    ]);

        $increment_id = $this->order['increment_id'];
        log::info('admin page');
        return $this->subject('New Order Received #' . $increment_id)->view('mail.admin-order-notify');

    }
    
}