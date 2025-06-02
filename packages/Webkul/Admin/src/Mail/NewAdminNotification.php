<?php

namespace Webkul\Admin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Webkul\User\Models\Admin;
use Illuminate\Support\Facades\Log;

class NewAdminNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     * 
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return void
     */
    public function __construct(public $order)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        try {

        // $adminEmail = core()->getAdminEmailDetails()['email'];
        $adminEmail = core()->getAdminEmailDetails()['email'];
    
        if (empty($adminEmail)) {
            log::info("Admin email is missing in NewAdminNotification Mailable.");
            $admin = Admin::where('role_id', 1)->first();
            if ($admin && !empty($admin->email)) {
                $adminEmail = $admin->email;
                log::info($adminEmail);
            } else {
                throw new \Exception("No admin email found for role_id = 1.");
            }
        }

        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to($adminEmail)
            ->subject(trans('shop::app.mail.order.subject'))
            ->view('shop::emails.sales.new-admin-order');
        } catch (\Exception $e) {
            Log::error("Error in sending NewAdminNotification: " . $e->getMessage());
        }
    }

}