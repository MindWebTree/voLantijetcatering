<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;

class GuestNewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Order details
     *
     * @var array
     */
    public $order;

    /**
     * Customer full name
     *
     * @var string
     */
    public $fboDetails;
    public $extraData;

    /**
     * Create a new message instance.
     *
     * @param array $order
     * @param string $fullName
     */
    public function __construct($order, $fboDetails, $extraData = [])
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
        
        log::info('mail succesfully send');
        $fullName = $this->fboDetails->full_name;
        $increment_id = $this->order['increment_id'];
        // Ensure view path is correct and exists
        return $this->from(
            core()->getSenderEmailDetails()['email'],
            core()->getSenderEmailDetails()['name']
        )
            ->to($this->order['customer_email'], $fullName)
            ->subject(trans('shop::app.mail.order.subject'). ' #' . $increment_id)
            ->view('mail.guest-new-order')
            ->with([
                'order' => $this->order,
                'fboDetails' => $this->fboDetails,
                'extraData' => $this->extraData,
            ]);
    }
}