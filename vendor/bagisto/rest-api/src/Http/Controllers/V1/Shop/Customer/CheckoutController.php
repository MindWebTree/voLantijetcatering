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
use Illuminate\Support\Facades\Log;
use App\Jobs\OrderConfirmationGuestEmailJob;
use App\Jobs\OrderConfirmationAdminEmailJob;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;


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
        return response()->json(['message' => 'Please fill your FBO details first.'], 422);
        }
        
        // sandeep || add shipping bydefault
        $cart = Cart::getCart();
        
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
    public function saveOrder(OrderRepository $orderRepository, Request $request)
    {

        $validator = Validator::make(
            request()->all(),
            [
            'card_number'    => 'required',
            'expiration_Date'    => 'required',
            'cvv' => 'required'
        ]);
        
        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors(),
                ],
                422,
            );
        }
        

        if (Cart::hasError()) { 
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Please add items to the cart before proceeding.',
            ], 400);
        }

        $customer = $this->resolveShopUser($request);

        if($customer->id){

        $fboDetails = DB::table('fbo_details')
        ->where('customer_id', $customer->id)
        ->orderBy('id', 'DESC')
        ->first();
        
        // sandeep add code for create customer profile and customer payment profile
        $cart = Cart::getCart();
        $billingAddress = $cart->billing_address ?? null;

        if (!$billingAddress) {
            return response()->json([
                'success' => false,
                'message' => 'Billing address is required to proceed with the order.',
            ], 400);
        }
        
        $customerCreateEmail = $customer->email ?? $fboDetails->email_address;

        $customerProfile = $this->createCustomerProfile($request->all(), $customerCreateEmail);

        // when create customer succesfully then create customer payment profile
        if($customerProfile['messages']['resultCode'] == "Ok"){
            $customerPaymentProfile = $this->createPaymentProfile($request->all(),$billingAddress, $customerProfile['customerProfileId']);
            if($customerPaymentProfile['messages']['resultCode'] == "Error"){
                return response()->json([
                'error' => $customerPaymentProfile['messages']['message']['0']['text'],
                ]);
            }

            if($customerPaymentProfile['messages']['resultCode'] == "Ok"){
                CustomerProfileLog::create([
                    'profile_id' => $customerPaymentProfile['customerProfileId'],
                    'payment_profile_id' => $customerPaymentProfile['customerPaymentProfileId'],
                    'email' => $customerCreateEmail,
                    'customer_id' => $customer->id,
                    'airport' => $billingAddress->airport_name,
                    'billing_address' => $billingAddress->address1,
                ]);
        }
    }

        // When errors occur in customer creation or customer payment profile, send erorr to response
        if($customerProfile['messages']['resultCode'] == "Error"){
            return response()->json([
            'error' => $customerProfile['messages']['message']['0']['text'],
            ]);
        }
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

        $orderId = $order->id;
        $customerId = $order->customer_id;


        Cart::deActivateCart();


        // sandeep || add success code        

      
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

                // $billing_address = DB::table('addresses')
                //     ->select('airport_name', 'address1')
                //     ->where('address_type', 'order_shipping')
                //     ->where('order_id', $orderId)
                //     ->first();
            
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

                        // sandeep
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
                    


                  CustomerProfileLog::where('customer_id', $customerId)
                    ->orderBy('id', 'DESC')
                    ->first()
                    ->update([
                        'order_id' => $orderId,
                   ]);

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
                'email' => $email ?? $fboDetails->email_address,
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




           // sandeep get shipping address data 
           $shippingAddress = Order::find($order['id'])->shipping_address()->first();

           $order['shipping_address'] = $shippingAddress;
   
           // dd($orderDetails);
           $fullName = $fboDetails->full_name;
           $order['fbo_phone_number'] = $fboDetails->phone_number;
           log::info("fullname",['fullname'=>$fullName]);
           log::info("email_address",['email_address'=>$fboDetails->email_address]);
           log::info("order",['order'=>$order]);

   
           // sandeep ||send user order confirmation mail
               try {
                   // Dispatch the job to the queue
                   OrderConfirmationGuestEmailJob::dispatch($order, $fboDetails);
               } catch (\Exception $e) {
                   Log::error('Error queuing mail', [
                       'error' => $e->getMessage(),
                       'trace' => $e->getTraceAsString()
                   ]);
               }

           // sandeep ||send admin order confirmation mail
               try {
                   OrderConfirmationAdminEmailJob::dispatch($order);
               } catch (\Exception $e) {
                   Log::error('Failed to send email to: ' , [
                       'error' => $e->getMessage(),
                       'trace' => $e->getTraceAsString()
                   ]);
               }

           log::info('return data to success page');

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











    // sandeep create customer profile
    public function createCustomerProfile($cardData, $customerEmail)
    {
        $url = "https://apitest.authorize.net/xml/v1/request.api";
        $apiRequest = [
            "createCustomerProfileRequest" => [
                "merchantAuthentication" => [
                "name" => "65LmnYT3F",
                "transactionKey" => "6CKt6NQ23u9M3pBZ"
                ],
                "profile" => [
                    "merchantCustomerId" => "M_" . time(), 
                    "email" => $customerEmail,
                    "paymentProfiles" => [
                        "customerType" => "individual",
                        "payment" => [
                            "creditCard" => [
                                "cardNumber" => $cardData['card_number'],
                                "expirationDate" => $cardData['expiration_Date'],
                                "cardCode" => $cardData['cvv'],
                            ]
                        ]
                    ]
                ],
                "validationMode" => "testMode"
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $apiRequest);
        

        $rawResponse = $response->body();
        $cleanResponse = preg_replace('/^\x{FEFF}/u', '', $rawResponse);
        
        if ($response->successful()) {
            try {
                $decodedResponse = json_decode($cleanResponse, true);

                return $decodedResponse;
            } catch (\Exception $e) {
                return [
                    'error' => true,
                    'message' => 'Failed to decode JSON response.',
                    'raw_response' => $cleanResponse
                ];
            }
        }

        }



    // sandeep create customer payment profile
    public function createPaymentProfile($cardData, $billingAddress, $customerProfileId){
        $url = "https://apitest.authorize.net/xml/v1/request.api";
        $apiRequest = [
            "createCustomerPaymentProfileRequest" => [
                "merchantAuthentication" => [
                "name" => "65LmnYT3F",
                    "transactionKey" => "6CKt6NQ23u9M3pBZ"
                ],
                "customerProfileId" => $customerProfileId,
                "paymentProfile" => [
                    "billTo" => [
                        "firstName" => $billingAddress->first_name,
                        "lastName" => $billingAddress->last_name,
                        "address" => $billingAddress->address1,
                        "city" => $billingAddress->city,
                        "state" => $billingAddress->state,
                        "zip" => $billingAddress->postcode,
                        "country" => $billingAddress->country,
                        "phoneNumber" => $billingAddress->phone
                    ],
                "payment" => [
                        "creditCard" => [
                            "cardNumber" => $cardData['card_number'],
                            "expirationDate" => $cardData['expiration_Date'],
                            "cardCode" => $cardData['cvv'],
                        ]
                ]
            ],
                "validationMode" => "liveMode"
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json'
        ])->post($url, $apiRequest);
        
        $rawResponse = $response->body();
        $cleanResponse = preg_replace('/^\x{FEFF}/u', '', $rawResponse);
        
        if ($response->successful()) {
            try {
                $decodedResponse = json_decode($cleanResponse, true);

                return $decodedResponse;
            } catch (\Exception $e) {
                return [
                    'error' => true,
                    'message' => 'Failed to decode JSON response.',
                    'raw_response' => $cleanResponse
                ];
            }
        }
    }

}
