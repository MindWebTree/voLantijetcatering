<?php

namespace ACME\paymentProfile\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use Illuminate\Support\Facades\Log;

class ProcessQuickBooksInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $orderId;
    public $fromsendInvoiceButton;
    /**
     * Create a new job instance.
     */
    public function __construct($orderId, $fromsendInvoiceButton = false)
    {
        $this->orderId = $orderId;
        $this->fromsendInvoiceButton = $fromsendInvoiceButton;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        
        // sandeep add code for create invoice in quickbook
        try {
            $quickbookInvoice = app(InvoicesController::class);
            // Call the createInvoice function from your class
            $quickbookInvoice->createInvoice($this->orderId);

            if ($this->fromsendInvoiceButton) {
                $quickbookInvoice->createInvoicePdf($this->orderId);
            }

        } catch (\Exception $e) {
            Log::error('Error processing QuickBooks invoice creation: ' . $e->getMessage());
        }
    }
}
