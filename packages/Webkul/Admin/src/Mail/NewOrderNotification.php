<?php

namespace Webkul\Admin\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\DB;

class NewOrderNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param  \Webkul\Sales\Contracts\Order  $order
     * @return void
     */
    public function __construct(
        public $order,
        public array $extraData = [],
        public  $fboAdditionalNotes 
    ) {}

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        log::info('auth order detail',['order'=>$this->order]);
        log::info('extraData',['extraData'=>$this->extraData]);
        log::info('fboAdditionalNotes',['fboAdditionalNotes'=>$this->fboAdditionalNotes]);


        $orderFboDetails = DB::table('orders')->where('id',$this->order->id)->select('fbo_full_name','fbo_email_address','fbo_phone_number','fbo_tail_number','fbo_packaging','fbo_service_packaging')->first();
        $increment_id = $this->order->increment_id;
        return $this->from(core()->getSenderEmailDetails()['email'], core()->getSenderEmailDetails()['name'])
            ->to($this->order->customer_email, $this->order->customer_full_name)
            ->subject('Volanti Jet Catering Order Request #' . $increment_id . ' Submitted')
            ->view('shop::emails.sales.new-order')
                ->with([
                
                'orderFboDetails'     => $orderFboDetails,
                'extraData'           => $this->extraData,
                'fboAdditionalNotes'  => $this->fboAdditionalNotes,
                ]);
            }
}
