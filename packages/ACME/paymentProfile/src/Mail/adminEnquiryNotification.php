<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class AdminEnquiryNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Message Body
     *
     * @var array
     */
    public $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Enquiry Form Submission – ' . config('app.App_Name'))->view('mail.admin-enquiry-notify');
    }
}
