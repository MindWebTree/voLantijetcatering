<?php

namespace App\Jobs;

use ACME\paymentProfile\Mail\adminContactUsNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Webkul\User\Models\Admin;

class SendContactUsMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Message Body
     *
     * @var array
     */
    protected $messageData;

    /**
     * Message From Name
     *
     * @var array
     */
    protected $name;

    /**
     * Message From Name
     *
     * @var array
     */
    protected $email;

    /**
     * Create a new job instance.
     *
     * @param array $order
     * @param object $fboDetails
     */
    public function __construct($messageData, $name , $email)
    {
        $this->messageData = $messageData;
        $this->name = $name;
        $this->email = $email;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sandeep || send guest user order confirmation mail
        // send admin order confirmation mail
        $admins = Admin::select('name', 'email')->where('role_id', '1')->where('status','1')->get();
        $messageData = $this->messageData;
        $name = $this->name;

        $emailList = $admins->pluck('email')->toArray();
        try {
            Mail::to($emailList)
                ->send(new adminContactUsNotification($messageData, $name, $this->email));
            Log::info('Email sent to: ' . implode(', ', $emailList));
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
