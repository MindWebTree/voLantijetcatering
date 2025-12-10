<?php

namespace ACME\paymentProfile\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class OrderInvoice extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $pdfPath;
    public $agent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($order,$agent,$pdfPath)
    {
        $this->order = $order;
        $this->pdfPath = $pdfPath;
        $this->agent = $agent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
public function build()
{
    $increment_id = $this->order->increment_id;
    $pdfUrl = url(Storage::url('invoice/' . basename($this->pdfPath)));
    
    return $this->subject('Invoice #' . $increment_id)
        ->view('paymentprofile::shop.volantijetcatering.invoices.mail.create')
        ->with([
            'pdfUrl' => $pdfUrl,
        ]);
}
}