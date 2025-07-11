<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\OrderFailedMail;
use Illuminate\Support\Facades\Mail;

class SendOrderFailedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  public $orderData;

    /**
     * Create a new job instance.
     */
    public function __construct($orderData)
    {
         $this->orderData = $orderData;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
         Mail::to('info@mindwebtree.com')->send(new OrderFailedMail($this->orderData));
    }
}
