<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Http\Requests\CustomerAddressForm;
use Webkul\Payment\Facades\Payment;
use Webkul\RestApi\Http\Resources\V1\Shop\Checkout\CartResource;
use Webkul\RestApi\Http\Resources\V1\Shop\Checkout\CartShippingRateResource;
use Webkul\RestApi\Http\Resources\V1\Shop\Sales\OrderResource;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shipping\Facades\Shipping;
use Illuminate\Support\Facades\DB;

// sandeep add
use Webkul\MpAuthorizeNet\Models\CustomerProfileLog;

class CheckoutController extends CustomerController
{
    /**
     * Save customer address.
     *
     * @param  \Webkul\Checkout\Http\Requests\CustomerAddressForm  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAddress(CustomerAddressForm $request)
    {
    
        // sandeep || add validation
        $fboDetail = DB::table('fbo_details')
        ->where('customer_id', $this->resolveShopUser($request)->id)
        ->first(['full_name', 'phone_number', 'email_address', 'tail_number']);

        if (!$fboDetail || in_array(null, (array) $fboDetail)) {
        return response()->json(['message' => 'Please fill in your FBO details first.'], 422);
        }
        
        // sandeep || add shipping bydefault
        $cart = Cart::getCart();
        
        if(!$cart){
            return response()->json([
                'message' => 'please add item first'
            ]);
        }

        
        DB::table('cart')
            ->where('id', $cart->id)
            ->update([
                'shipping_method' => 'free_free'
            ]);

        $data = $request->all();

            $airportAddress = DB::table('addresses') 
            ->where('id', $data['billing']['address_id'])
            ->first();

 
        // $data['billing']['address1'] = implode(PHP_EOL, array_filter($airportAddress->address1));
        // $data['shipping']['address1'] = implode(PHP_EOL, array_filter($airportAddress->address1));
        if ($airportAddress) {
            $data['billing']['address1'] = $airportAddress->address1;
            $data['shipping']['address1'] = $airportAddress->address1;
        }

        if (isset($data['billing']['id']) && str_contains($data['billing']['id'], 'address_')) {
            unset($data['billing']['id']);
            unset($data['billing']['address_id']);
        }

        if (isset($data['shipping']['id']) && Str::contains($data['shipping']['id'], 'address_')) {
            unset($data['shipping']['id']);
            unset($data['shipping']['address_id']);
        }

        if (Cart::hasError() || ! Cart::saveCustomerAddress($data) || ! Shipping::collectRates()) {
            return response()->json([
                'message' => 'Failed to process the request.',
            ], 422);
        
            
        }
        
        $rates = [];

        foreach (Shipping::getGroupedAllShippingRates() as $code => $shippingMethod) {
            $rates[] = [
                'carrier_title' => $shippingMethod['carrier_title'],
                'rates'         => CartShippingRateResource::collection(collect($shippingMethod['rates'])),
            ];
        }
        Cart::collectTotals();

        return response([
            'data'    => [
                'rates' => $rates,
                'cart'  => new CartResource(Cart::getCart()),
            ],
            'message' => 'Address saved successfully.',
        ],200);

    }

    /**
     * Save shipping method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function saveShipping(Request $request)
    {
        $shippingMethod = $request->get('shipping_method');

        if (Cart::hasError()
            || ! $shippingMethod
            || ! Cart::saveShippingMethod($shippingMethod)
        ) {
            // abort(400);
            // sandeep || add code
            return response([
                'message' => 'Unable to save the shipping method.',
            ], 422);
        }
       
        Cart::collectTotals();

        return response([
            'data'    => [
                'methods' => Payment::getPaymentMethods(),
                'cart'    => new CartResource(Cart::getCart()),
            ],
            'message' => 'Shipping method saved successfully.',
        ], 200);
    }

    /**
     * Save payment method.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function savePayment(Request $request)
    {
      
        $payment = $request->get('payment');
        if (Cart::hasError() || ! $payment || ! Cart::savePaymentMethod($payment)) {
            // abort(400);
            // sandeep || add response error message
            return response([
                'message' => "Unable to save the Payment method.",
            ], 422);
        }

        return response([
            'data'    => [
                'cart' => new CartResource(Cart::getCart()),
            ],
            'message' => 'Payment method saved successfully.',
        ], 200);
    }

    /**
     * Check for minimum order.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMinimumOrder()
    {
        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        $status = Cart::checkMinimumOrder();

        return response([
            'data'    => [
                'cart'   => new CartResource(Cart::getCart()),
                'status' => ! $status ? false : true,
            ],
            'message' => ! $status ? __('rest-api::app.checkout.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]) : 'Success',
        ]);
    }

    /**
     * Save order.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @return \Illuminate\Http\Response
     */
    public function saveOrder(OrderRepository $orderRepository)
    {
 
        if (Cart::hasError()) {
            abort(400);
        }
     
        Cart::collectTotals();
    
        $this->validateOrder();
     
        $cart = Cart::getCart();
        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            return response([
                'redirect_url' => $redirectUrl,
            ]);
        }

        $order = $orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();


        // sandeep || add success code        
        $orderId = $order->id;
        $customerId = $order->customer_id;
      
        $airport_name = '';

        if ($customerId) {
            $email = $order->customer_email;

            $airport_name = DB::table('addresses')
                    ->select('addresses.address1', 'delivery_location_airports.name', 'addresses.delivery_date')
                    ->where('addresses.order_id', $orderId)
                    ->where('addresses.address_type', 'order_billing')
                    ->join('delivery_location_airports', 'addresses.address1', '=', 'delivery_location_airports.address')
                    ->first();
                
            if ($airport_name) {
                DB::table('addresses')
                    ->where('address_type', 'order_shipping')
                    ->where('order_id', $orderId)
                    ->update([
                        'airport_name' => $airport_name->name,
                    ]);
            }
       

                $fboDetails = DB::table('fbo_details')
                    ->where('customer_id', $customerId)
                    ->orderBy('id', 'DESC')
                    ->first();

                $billing_address = DB::table('addresses')
                    ->select('airport_name', 'address1')
                    ->where('address_type', 'order_shipping')
                    ->where('order_id', $orderId)
                    ->first();
            
                if ($fboDetails) {
                    DB::table('fbo_details')
                        ->where('id', $fboDetails->id)
                        ->update([
                            'delivery_time' => null,
                            'delivery_date' => null,
                        ]);
                  }


                        $airport_fbo_id = DB::table('addresses')
                        ->select('airport_fbo_id')
                        ->where('address_type', 'customer')
                        ->where('customer_id',$customerId)
                        ->latest('created_at')
                        ->first();

                         //  sandeep
                        DB::table('addresses')
                        ->where('customer_id', $customerId)
                        ->update(['default_address' => '0']);

                        // Set the selected address as default

                        DB::table('addresses')
                        ->where('airport_name',$airport_name->name)
                        ->where('airport_fbo_id',$airport_fbo_id->airport_fbo_id)
                        ->where('customer_id',$customerId)
                        ->orderBy('id','desc')
                        ->update([
                            'default_address' => '1',
                        ]);
                    


            CustomerProfileLog::updateOrCreate(
                ['customer_id' => $customerId],
                [
                    'order_id' => $orderId,
                    'airport' => $billing_address->airport_name,
                    'billing_address' => $billing_address->address1,
                ]
            );

                DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'fbo_full_name' => $fboDetails->full_name,
                    'fbo_phone_number' => $fboDetails->phone_number,
                    'fbo_email_address' => $fboDetails->email_address,
                    'fbo_tail_number' => $fboDetails->tail_number,
                    'fbo_packaging' => $fboDetails->packaging_section,
                    'fbo_service_packaging' => $fboDetails->service_packaging,
                    'delivery_date' => $fboDetails->delivery_date,
                    'delivery_time' => $fboDetails->delivery_time,
                    'airport_fbo_id' => $airport_fbo_id->airport_fbo_id,
                    'status' => 'pending',
                    'status_id' => 1,
                ]);
            DB::table('order_status_log')->insert([
                'order_id' => $orderId,
                'user_id' => $customerId,
                'is_admin' => 0,
                'status_id' => 1,
                'email' => $email,
            ]);
        


            DB::table('addresses')
            ->where('address_type', 'order_billing')
            ->where('order_id', $orderId)
            ->update([
                'postcode' => null,
                'state' => null,
                'address1' => null,
                'country' => null,
                'last_name' => null,
                'first_name' => null,
                'email' => null,
            ]);
        }

       // get success page data 
        $orderDetails = DB::table('order_items')
        ->select(
            'order_items.name',
            'order_items.parent_id',
            'order_items.additional',
            'order_items.qty_ordered',
            'addresses.airport_name',
            'addresses.address1',
            'addresses.address_type',
            'orders.fbo_full_name',
            'airport_fbo_details.name as fbo_airport_name',
            'airport_fbo_details.address as fbo_airport_address',
            'orders.fbo_phone_number',
            'orders.fbo_email_address',
            'orders.fbo_tail_number',
            'orders.fbo_packaging',
            'orders.fbo_service_packaging'
        )
        ->join('orders', 'order_items.order_id', '=', 'orders.id')
        ->leftjoin('addresses', 'order_items.order_id', '=', 'addresses.order_id')
        ->leftjoin('airport_fbo_details', 'orders.airport_fbo_id', '=', 'airport_fbo_details.id')
        ->where('order_items.order_id', $orderId)
        ->where('addresses.address_type', 'order_shipping')
        ->whereNull('order_items.parent_id')
        ->get();

        return response([
            'data'    => [
                'order' => new OrderResource($order),
            ],
            'message' => 'Order saved successfully.',
        ], 200);
    

    }

    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    protected function validateOrder()
    {
       
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;

        if (! $cart->checkMinimumOrder()) {
            throw new \Exception(__('rest-api::app.checkout.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if ($cart->haveStockableItems() && ! $cart->shipping_address) {
            throw new \Exception(__('rest-api::app.checkout.check-shipping-address'));
        }

        if (! $cart->billing_address) {
            throw new \Exception(__('rest-api::app.checkout.check-billing-address'));
        }

        if ($cart->haveStockableItems() && ! $cart->selected_shipping_rate) {
            throw new \Exception(__('rest-api::app.checkout.specify-shipping-method'));
        }
       
        if (! $cart->payment) {
            throw new \Exception(__('rest-api::app.checkout.specify-payment-method'));
        }
      
    }
}
