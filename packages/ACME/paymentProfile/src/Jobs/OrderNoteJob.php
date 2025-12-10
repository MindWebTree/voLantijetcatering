<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\OrderNote;

class OrderNoteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $comment;
    public $order;
    /**
     * Create a new job instance.
     */
    public function __construct($comment,$order)
    {
        $this->comment = $comment;
        $this->order = $order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sandeep add code for mail send 
        try{
            if ($this->order->customer_email == null) {
                Mail::to($this->order->fbo_email_address)->send(new OrderNote($this->comment, $this->order));
            } else {
                Mail::to($this->order->customer_email)->send(new OrderNote($this->comment, $this->order));
            }
        }catch(\Exception $e){

        }
    }
}
