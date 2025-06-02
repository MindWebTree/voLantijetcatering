<?php

namespace App\Jobs;

use ACME\paymentProfile\Mail\adminOrderNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Webkul\Sales\Models\Order;
use Webkul\User\Models\Admin;
use ACME\paymentProfile\Mail\GuestNewOrderNotification;

class TestJob implements ShouldQueue
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
        try {
            Log::info('Sending queued email for guest order', [
                'email' => $this->fboDetails->email_address,
                'order_id' => $this->order['id'] ?? null
            ]);

            Mail::to($this->fboDetails->email_address)
                ->send(new GuestNewOrderNotification(
                    $this->order, 
                    $this->fboDetails->full_name
                ));

            Log::info('Queued email sent successfully');
        } catch (\Exception $e) {
            Log::error('Failed to send queued email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }
}
