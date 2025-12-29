<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Webkul\Admin\Mail\NewOrderNotification;
use Illuminate\Support\Facades\Log;

class OrderConfirmationAuthEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

     protected $extraData;

 public $fboAdditionalNotes;

    /**
     * Create a new job instance.
     */
    public function __construct($order,$extraData,$fboAdditionalNotes)
    {
        $this->order = $order;
        $this->extraData = $extraData;
        $this->fboAdditionalNotes = $fboAdditionalNotes;    
    }
    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $email = $this->order->customer_email ?? $this->order->fbo_email_address;

        try {
            Mail::to($email)
                ->send(new NewOrderNotification(
                    $this->order,
                    $this->extraData,
                    $this->fboAdditionalNotes
                ));
                Log::info('Email sent successfully to: ' . $email);
        } catch (\Exception $e) {
            Log::error('Failed to send queued email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
    }
}
