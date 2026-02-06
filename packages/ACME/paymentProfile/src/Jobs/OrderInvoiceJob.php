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
        $order = Order::where('orders.id', $this->orderId)
                    ->leftJoin(
                        'airport_fbo_details',
                        'airport_fbo_details.id',
                        '=',
                        'orders.airport_fbo_id'
                    )
                    ->leftJoin(
                        'cart',
                        'cart.id',
                        '=',
                        'orders.cart_id'
                    )
                    ->select(
                        'orders.*',
                        'airport_fbo_details.name as airport_fbo_name',
                        'airport_fbo_details.address as airport_fbo_address'
                    )
                    ->first();



        log::info('order invoice job details',['order'=>$order]);

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
