<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\GuestNewOrderNotification;
use Illuminate\Support\Facades\Log;

class OrderConfirmationGuestEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Order details
     *
     * @var array
     */
    protected $order;

    /**
     * FBO details
     *
     * @var object
     */
    protected $fboDetails;

    /**
     * Create a new job instance.
     *
     * @param array $order
     * @param object $fboDetails
     */
    public function __construct($order, $fboDetails)
    {
        $this->order = $order;
        $this->fboDetails = $fboDetails;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sandeep || send guest user order confirmation mail
        $email = $this->order->customer_email ?? $this->fboDetails->email_address;
        log::info('customer_email',['email'=>$email]);
        try {
            log::info('mail send to guest user',['guest user'=>$this->fboDetails->email_address]);
            Mail::to($email)
                ->send(new GuestNewOrderNotification(
                    $this->order, 
                    $this->fboDetails
                ));
                Log::info('Email sent successfully to: ' . $this->fboDetails->email_address);
        } catch (\Exception $e) {
            log::info('faild to send mail');
            Log::error('Failed to send queued email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
