<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class adminContactUsNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Message Body
     *
     * @var array
     */
    public $messageData;

    /**
     * Message From Name
     *
     * @var array
     */
    public $name;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public $email;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($messageData, $name, $email)
    {
        $this->messageData = $messageData;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('New Contact Form Submission – ' . config('app.App_Name'))->view('mail.admin-contact-us-notify');
    }
}
