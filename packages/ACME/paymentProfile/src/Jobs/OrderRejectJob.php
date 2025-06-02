<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\OrderReject;

class OrderRejectJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $order;
    public $recipientEmail;
    /**
     * Create a new job instance.
     */
    public function __construct($order, $recipientEmail)
    {
        $this->order = $order;
        $this->recipientEmail = $recipientEmail;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
    //    sandeep send reject order mail
        try{
            Mail::to($this->recipientEmail)->send(new OrderReject($this->order));
        }catch(\Exception $e){
            
        }
    }
}
