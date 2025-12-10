<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Http\Controllers\QuickBookController;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Order;

class UpdateQuickbookPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $orderId;
    /**
     * Create a new job instance.
     */
    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // sandeep add code for update payment in quickbook 
        try {
            $orderDetail = Order::where('id', $this->orderId)->first();
            $invoiceId = $orderDetail->quickbook_invoice_id;
             if (!empty($invoiceId) && $invoiceId !== "0") {
              $updatePayemnt = app(QuickBookController::class);
              $updatePayemnt->updatePaymentInQuickBooks($this->orderId);
          }
        } catch (\Exception $e) {
            Log::error('Error processing QuickBooks invoice creation: ' . $e->getMessage());
        }
    }
}
