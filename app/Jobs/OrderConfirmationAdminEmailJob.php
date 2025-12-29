<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use ACME\paymentProfile\Mail\adminOrderNotification;
use Illuminate\Support\Facades\Log;
use Webkul\User\Models\Admin;

class OrderConfirmationAdminEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $fboDetails;
    protected $extraData;

    public $fboAdditionalNotes;

    /**
     * Create a new job instance.
     */
    public function __construct($order,$fboDetails, $extraData,$fboAdditionalNotes)
    {
        $this->order = $order;
        $this->fboDetails = $fboDetails;
        $this->extraData = $extraData;
        $this->fboAdditionalNotes = $fboAdditionalNotes;

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        // send admin order confirmation mail
        $admins = Admin::select('name', 'email')->where('role_id','1')->get();
        $order = $this->order;
        $fboDetails = $this->fboDetails;
        $extraData = $this->extraData;
        $fboAdditionalNotes = $this->fboAdditionalNotes;

        $emailList = $admins->pluck('email')->toArray();
        try {
            Mail::to($emailList)
                ->send(new AdminOrderNotification($order, $fboDetails,$extraData,  $fboAdditionalNotes));
        } catch (\Exception $e) {
            Log::error('Failed to send email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
