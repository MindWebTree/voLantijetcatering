<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\OrderAccept;

class OrderAcceptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $order;
    /**
     * Create a new job instance.
     */
    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sandeep send order confirmation mail
        try{
        if ($this->order->customer_email === null) {
                Mail::to($this->order->fbo_email_address)->send(new OrderAccept($this->order));
            } else {
                Mail::to($this->order->customer_email)->send(new OrderAccept($this->order));
            }
        }catch(\Exception $e){
      }
    }
}
