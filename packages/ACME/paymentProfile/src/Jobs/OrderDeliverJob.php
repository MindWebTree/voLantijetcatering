<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\OrderDeliver;

class OrderDeliverJob implements ShouldQueue
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
        // sandeep add code for order delivery mail
        try{
             Mail::to($this->recipientEmail)->send(new OrderDeliver($this->order));
        }catch(\Exception $e){

        }
    }
}
