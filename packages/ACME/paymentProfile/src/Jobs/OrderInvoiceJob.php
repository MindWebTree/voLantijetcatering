<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\OrderInvoice;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Order;

class OrderInvoiceJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    public $pdfPath;
    public $agent;
    /**
     * Create a new job instance.
     */
    public function __construct($orderId, $agent, $pdfPath)
    {
        $this->orderId = $orderId;
        $this->pdfPath = $pdfPath;
        $this->agent = $agent;
    }


    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order  = Order::where('id', $this->orderId)->first();
        // sandeep add code for send invoice mail
        if ($order->customer_email === null) {
            $email = $order->fbo_email_address;
        } else {
            $email = $order->customer_email;
        }

        try {
            Mail::to($email)->send(new OrderInvoice($order, $this->agent, $this->pdfPath));
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
