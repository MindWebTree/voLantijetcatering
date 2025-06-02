<?php

namespace ACME\CateringPackage\Http\Controllers\Shop;

use ACME\paymentProfile\Mail\adminOrderNotification;
use ACME\paymentProfile\Mail\GuestNewOrderNotification;
use Illuminate\Support\Facades\Event;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Http\Requests\CustomerAddressForm;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\MpAuthorizeNet\Models\CustomerProfileLog;
use Webkul\Payment\Facades\Payment;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Shop\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\User\Models\Admin;
use Illuminate\Support\Facades\Log;
use App\Jobs\OrderConfirmationGuestEmailJob;
use App\Jobs\OrderConfirmationAuthEmailJob;
use App\Jobs\OrderConfirmationAdminEmailJob;
use Webkul\Sales\Models\Order;


class CheckoutController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @return void
     */

    public function __construct(
        protected OrderRepository $orderRepository,
        protected CustomerRepository $customerRepository
    ) {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */

    public function index()
    {
        Event::dispatch('checkout.load.index');
        
        if (
            !auth()->guard('customer')->check()
            && !core()->getConfigData('catalog.products.guest-checkout.allow-guest-checkout')
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        if (
            auth()->guard('customer')->check()
            && auth()->guard('customer')->user()->is_suspended
        ) {
            session()->flash('warning', trans('shop::app.checkout.cart.suspended-account-message'));

            return redirect()->route('shop.checkout.cart.index');
        }

        if (Cart::hasError()) {
            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        // dd($cart->applied_cart_rule_ids);
        if ($cart->applied_cart_rule_ids != '') {
            session()->flash('success', trans('shop::app.checkout.cart.rule-applied'));
        }

        if (
            (
                !auth()->guard('customer')->check()
                && $cart->hasDownloadableItems()
            )
            || (
                !auth()->guard('customer')->check()
                && !$cart->hasGuestCheckoutItems()
            )
        ) {
            return redirect()->route('shop.customer.session.index');
        }

        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?: 0;

        if (!$cart->checkMinimumOrder()) {
            session()->flash('warning', trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));

            return redirect()->back();
        }

        Cart::collectTotals();
        $token = session('token');
        $customerId = Auth::guard('customer')->id();


        // sandeep add check for login and guest user
        // if ($customerId) {
        //     $fboDetails = DB::table('fbo_details')
        //         ->where('customer_id', $customerId)
        //         ->orderBy('id', 'DESC')
        //         ->first();
        // } else {
        //     $fboDetails = DB::table('fbo_details')
        //         ->Where('customer_token', $token)
        //         ->orderBy('id', 'DESC')
        //         ->first();
        // }

        $fboDetails = DB::table('fbo_details')
            ->where($customerId ? 'customer_id' : 'customer_token', $customerId ?: $token)
            ->orderBy('id', 'DESC')
            ->first();

        /* order summary */
        $cart = Cart::getCart();

        //remove the session if exist(for test purpose)
        if (session()->has('ADMIN_PAYMENT') || session()->has('ADMIN_CARD')) {
            session()->forget(['ADMIN_PAYMENT', 'ADMIN_CARD']);
        }

        // /* GET CUSTOMER ADDRESS*/
        // if (auth()->check()) {
        //     $customer_address = DB::table('addresses')
        //         ->where('customer_id', auth()->user()->id)
        //         ->where('address_type', 'customer')
        //         ->whereNotNull('customer_token')
        //         ->orderBy('id', 'DESC')
        //         ->first();
        // } else {
        //     $customer_address = DB::table('addresses')
        //         ->where('customer_token', $token)
        //         ->where('address_type', 'customer')
        //         ->whereNotNull('customer_token')
        //         ->orderBy('id', 'DESC')
        //         ->first();
        // }

        // dd(auth()->check());
        /* GET CUSTOMER ADDRESS*/
        $customer_address = DB::table('addresses')
            ->where(auth()->check() ? 'customer_id' : 'customer_token', auth()->user()->id ?? $token)
            ->where('address_type', 'customer')
            ->whereNotNull('customer_token')
            ->orderBy('id', 'DESC')
            ->first();


        return view($this->_config['view'], compact('cart', 'fboDetails', 'customer_address'));
    }


    public function show_fbo_detail()
    {
        $token = session('token');
        // dd($token);
        $customerId = Auth::guard('customer')->id();

        if ($customerId) {
            $fboDetails = DB::table('fbo_details')
                ->where('customer_id', $customerId)
                ->orderBy('id', 'DESC')
                ->first();
        } else {
            $fboDetails = DB::table('fbo_details')
                ->Where('customer_token', $token)
                ->orderBy('id', 'DESC')
                ->first();
        }
        // dd($fboDetails);
        return view('shop::customers.account.address.index', compact('fboDetails'));
    }

    /**
     * Return order short summary.
     *
     * @return \Illuminate\Http\Response
     */
    public function summary()
    {

        log::info('summary');

        $cart = Cart::getCart();
        return response()->json([
            'html' => view('shop::checkout.total.summary', compact('cart'))->render(),
        ]);
    }

    /**
     * Saves customer address.
     *
     * @param  \Webkul\Checkout\Http\Requests\CustomerAddressForm  $request
     * @return \Illuminate\Http\Response
     */
    public function saveAddress(CustomerAddressForm $request)
    {

        log::info('saveAddress');
        $data = $request->all();

        if (
            !auth()->guard('customer')->check()
            && !Cart::getCart()->hasGuestCheckoutItems()
        ) {
            log::info('first');
            return response()->json(['redirect_url' => route('shop.customer.session.index')], 403);
        }

        $data['billing']['address1'] = implode(PHP_EOL, array_filter($data['billing']['address1']));
        $data['shipping']['address1'] = implode(PHP_EOL, array_filter($data['shipping']['address1']));
        if (isset($data['billing']['airport_name'])) {
            $data['billing']['airport_name'] = implode(PHP_EOL, array_filter($data['billing']['airport_name']));
            $airport_id = DB::table('delivery_location_airports')
                ->where('name', $data['billing']['airport_name'])
                ->value('id');
        }

        // sandeep add code for guest user add country and address1 data 
        if(!auth()->guard('customer')->check()){
          $addressData =   DB::table('addresses')
                            ->where('id',$data['billing']['address_id'])
                            ->select('address1','country')->first();

            $data['billing']['country'] = $addressData->country;
            $data['billing']['address1'] = $addressData->address1;

        }

        if (
            Cart::hasError()
            || !Cart::saveCustomerAddress($data)
        ) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }


        $cart = Cart::getCart();

        Cart::collectTotals();

        if ($cart->haveStockableItems()) {
            if (!$rates = Shipping::collectRates()) {
                return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
            }
            if (isset($airport_id)) {
                $rates['airport_id'] = $airport_id;
            }

            // dd($rates);
            return response()->json($rates);
        }

        return response()->json(Payment::getSupportedPaymentMethods());
    }

    /**
     * Saves shipping method.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveShipping()
    {
        $shippingMethod = request()->get('shipping_method');

        Cart::collectTotals();

        return response()->json(Payment::getSupportedPaymentMethods());
    }

    /**
     * Saves payment method.
     * 
     * @return \Illuminate\Http\Responsetrans('admin::app.sales.orders.order-status-shipped')
     */
    public function savePayment()
    {
        $payment = request()->get('payment');

        if (
            Cart::hasError()
            || !$payment
            || !Cart::savePaymentMethod($payment)
        ) {
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        return response()->json([
            'jump_to_section' => 'review',
            'html' => view('shop::checkout.onepage.review', compact('cart'))->render(),
        ]);
    }

    /**
     * Saves order.
     *
     * @return \Illuminate\Http\Response
     */
    public function saveOrder()
    {
        log::info('save order1');
        if (Cart::hasError()) {
            Log::info('cart errror');
            return response()->json(['redirect_url' => route('shop.checkout.cart.index')], 403);
        }

        log::info('save order2');

        Cart::collectTotals();

        $this->validateOrder();

        $cart = Cart::getCart();

        DB::table('cart')
            ->where('id', $cart->id)
            ->update([
                'shipping_method' => 'free_free'
            ]);

        log::info('save order3');

        if ($redirectUrl = Payment::getRedirectUrl($cart)) {
            log::info('redirectUrl', ['redirectUrl' => $redirectUrl]);
            Log::info('payment errror');
            return response()->json([
                'success' => true,
                'redirect_url' => $redirectUrl,
            ]);

          //   dd($redirectUrl);
        }

        log::info('saveOrderData');
        $order = $this->orderRepository->create(Cart::prepareDataForOrder());

        Cart::deActivateCart();

        Cart::activateCartIfSessionHasDeactivatedCartId();

        session()->flash('order', $order);
        Log::info('cart');

        return response()->json([
            'success' => true,
        ]);
    }

    /**
     * Order success page.
     *
     * @return \Illuminate\Http\Response
     */
    public function success()
    {
        log::info('success');
        $order = session('order');

        if (!$order = session('order')) {
            log::info('no data found');
            return redirect()->route('shop.checkout.cart.index');
        }

        $orderId = $order['id'];

        $token = session('token');

        $customerId = Auth::guard('customer')->id();

        $airport_name = '';

        if ($customerId != '') {
            $email = Auth::user()->email;

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
        } else {
            $airport_name = DB::table('addresses')
                ->select('airport_name', 'address1', 'city', 'delivery_date')
                ->where('address_type', 'customer')
                ->Where('customer_token', $token)
                ->first();

            if ($airport_name) {
                DB::table('addresses')
                    ->where('address_type', 'order_shipping')
                    ->where('order_id', $orderId)
                    ->update([
                        'airport_name' => $airport_name->airport_name,
                        'address1' => $airport_name->address1,
                        'city' => $airport_name->city,
                    ]);
            }
        }

        // sandeep add check 
        if ($customerId != '') {
            $fboDetails = DB::table('fbo_details')
                ->where('customer_id', $customerId)
                ->orderBy('id', 'DESC')
                ->first();
        } else {
            $fboDetails = DB::table('fbo_details')
                ->where('customer_token', $token)
                ->orderBy('id', 'DESC')
                ->first();

        }

    
        $billing_address = DB::table('addresses')
            ->select('airport_name', 'address1')
            ->where('address_type', 'order_shipping')
            ->where('order_id', $orderId)
            ->first();

        // DD($billing_address);
        if ($fboDetails) {
            DB::table('fbo_details')
                ->where('id', $fboDetails->id)
                ->update([
                    'delivery_time' => null,
                    'delivery_date' => null,
                ]);
        }

        // dd(auth()->user()->id);
        $customer = DB::table('customers')
            ->select('id')
            ->where('token', $token)
            ->first();



            // sandeep add code for add app notifications
            $appNotifications = DB::table('app_notifications')->insert([
                    'customer_id' => $customerId ?? $customer->id,
                    'order_id' => $orderId,
                    'message' => "Order Place",
                    'is_read' => 0
            ]);

            
            // sandeep add code for if guest customer then update customer id
            // if ($customerId == '') {
            // DB::table('cart')
            //     ->where('id', $order['cart_id'])
            //     ->update([
            //         'customer_id' => $customer->id
            //     ]);
            // }


        $airport_fbo_id = DB::table('addresses')
            ->select('airport_fbo_id')
            ->where('address_type', 'customer')
            ->where(auth()->check() ? 'customer_id' : 'customer_token', auth()->check() ? auth()->user()->id : $token)
            ->latest('created_at')
            ->first();

        //   dd($airport_fbo_id);
        //  sandeep update default address
        if ($customerId != '') {
            DB::table('addresses')
                ->where('customer_id', $customerId)
                ->update(['default_address' => '0']);

            // Set the selected address as default
            DB::table('addresses')
                ->where('airport_name', $airport_name->name)
                ->where('airport_fbo_id', $airport_fbo_id->airport_fbo_id)
                ->where('customer_id', $customerId)
                ->orderBy('id', 'desc')
                ->update([
                    'default_address' => '1',
                ]);
        } else {
            DB::table('addresses')
                ->where('customer_token', $token)
                ->update(['default_address' => '0']);

            // Set the selected address as default
            DB::table('addresses')
                ->where('airport_name', $airport_name->airport_name)
                ->where('airport_fbo_id', $airport_fbo_id->airport_fbo_id)
                ->where('customer_token', $token)
                ->orderBy('id', 'desc')
                ->update([
                    'default_address' => '1',
                ]);
        }

        if ($customerId != '') {
            CustomerProfileLog::where('customer_id', auth()->user()->id)
                ->orderBy('id', 'DESC')
                ->first()
                ->update([
                    'order_id' => $orderId,
                    'airport' => $billing_address->airport_name,
                    'billing_address' => $billing_address->address1,
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
                'email' => $email,
            ]);
        } else {
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'fbo_full_name' => $fboDetails->full_name,
                    'fbo_phone_number' => $fboDetails->phone_number,
                    'fbo_email_address' => $fboDetails->email_address,
                    'fbo_tail_number' => $fboDetails->tail_number,
                    'fbo_packaging' => $fboDetails->packaging_section,
                    'fbo_service_packaging' => $fboDetails->service_packaging,
                    'status' => 'pending',
                    'status_id' => 1,
                    'customer_id' => $customer->id,
                    'delivery_date' => $fboDetails->delivery_date,
                    'delivery_time' => $fboDetails->delivery_time,
                    'airport_fbo_id' => $airport_fbo_id->airport_fbo_id,
                ]);
            DB::table('airport_fbo_details')
                ->where('customer_token', $token)
                ->update([
                    'customer_id' => $customer->id,
                ]);
             
            DB::table('order_status_log')->insert([
                'order_id' => $orderId,
                'user_id' => $customer->id,
                'is_admin' => 0,
                'status_id' => 1,
                'email' => $fboDetails->email_address,
            ]);
         
            CustomerProfileLog::where('customer_id', $customer->id)
                ->orderBy('id', 'DESC')
                ->first()
                ->update([
                    'order_id' => $orderId,
                    'airport' => $billing_address->airport_name,
                    'billing_address' => $billing_address->address1,
                ]);
        }


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

            log::info($orderId);
            log::info($order['id']);

        // sandeep get shipping address data 
        $order = Order::find($orderId);

        $extraData = [
            'fbo_airport_name' => $orderDetails[0]->fbo_airport_name,
            'fbo_airport_address' => $orderDetails[0]->fbo_airport_address,
        ];


        // $order['fbo_airport_name'] = $orderDetails[0]->fbo_airport_name;
        // $order['fbo_airport_address'] = $orderDetails[0]->fbo_airport_name;
        

        // $order['shipping_address'] = $shippingAddress;

        // dd($orderDetails);
        $fullName = $fboDetails->full_name;
        $order['fbo_phone_number'] = $fboDetails->phone_number;
  
        // sandeep || send guest user order confirmation mail
        if (!Auth::check()) {
            log::info('16');
            try {
                Log::info('Preparing to queue mail for guest order', [
                    'email' => $fboDetails->email_address
                ]);
                log::info('17');
                // Dispatch the job to the queue
                OrderConfirmationGuestEmailJob::dispatch($order, $fboDetails, $extraData);
                log::info('18');
                Log::info('Mail queued successfully for guest order');
            } catch (\Exception $e) {
                log::info('19');
                Log::error('Error queuing mail', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }else{
            try {
                OrderConfirmationAuthEmailJob::dispatch($order);
                log::info('mail send to auth user');
            } catch (\Exception $e) {
                Log::error('Error queuing mail', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        }

        // sandeep ||send admin order confirmation mail
            try {
                log::info('21');
                OrderConfirmationAdminEmailJob::dispatch($order,$fboDetails, $extraData);
                log::info('22');
                Log::info('Email sent successfully to: ');
            } catch (\Exception $e) {
                log::info('23');
                Log::error('Failed to send email to: ' , [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }

        log::info('return data to success page');

        session()->forget('order');


        return view($this->_config['view'], compact('order', 'orderDetails','fboDetails'));

    }
    
    /**
     * Order success page.
     *
     * @return \Illuminate\Http\Response
     */

    // Update Success function remove redundant and repeated code to reduce slow

    // public function success()
    // {
    //     log::info('Checking session order data...', ['session_orderData' => session()->has('order')]);

    //     if (!$order = session('order')) {
    //         log::info('No order data found in session');
    //         return redirect()->route('shop.checkout.cart.index');
    //     }

    //     log::info('Processing order success flow');
    //     $orderId = $order['id'];
    //     $token = session('token');
    //     $customerId = Auth::guard('customer')->id();

    //     //retrieving guest customer id 
    //     $customer = DB::table('customers')
    //         ->select('id')
    //         ->where('token', $token)
    //         ->first();

    //     $email = $customerId ? Auth::user()->email : null;

    //     // Retrieve airport details
    //     $airport_name = $customerId
    //         ? DB::table('addresses')
    //             ->join('delivery_location_airports', 'addresses.address1', '=', 'delivery_location_airports.address')
    //             ->where('addresses.order_id', $orderId)
    //             ->where('addresses.address_type', 'order_billing')
    //             ->select('addresses.address1', 'delivery_location_airports.name', 'addresses.delivery_date')
    //             ->first()
    //         : DB::table('addresses')
    //             ->where('address_type', 'customer')
    //             ->where('customer_token', $token)
    //             ->select('airport_name', 'address1', 'city', 'delivery_date')
    //             ->first();

    //     if ($airport_name) {
    //         DB::table('addresses')
    //             ->where('address_type', 'order_shipping')
    //             ->where('order_id', $orderId)
    //             ->update([
    //                 'airport_name' => $customerId ? $airport_name->name : $airport_name->airport_name,
    //                 'address1' => $airport_name->address1 ?? null,
    //                 'city' => $airport_name->city ?? null,
    //             ]);
    //     }

    //     // Get FBO details
    //     $fboDetails = DB::table('fbo_details')
    //         ->where($customerId ? 'customer_id' : 'customer_token', $customerId ?: $token)
    //         ->orderBy('id', 'DESC')
    //         ->first();

    //     if ($fboDetails) {
    //         DB::table('fbo_details')->where('id', $fboDetails->id)->update([
    //             'delivery_time' => null,
    //             'delivery_date' => null,
    //         ]);
    //     }

    //     // Retrieve billing address
    //     $billing_address = DB::table('addresses')
    //         ->where('address_type', 'order_shipping')
    //         ->where('order_id', $orderId)
    //         ->select('airport_name', 'address1')
    //         ->first();

    //     // Handle default address logic
    //     DB::table('addresses')
    //         ->where($customerId ? 'customer_id' : 'customer_token', $customerId ?: $token)
    //         ->update(['default_address' => '0']);

    //     if ($airport_name) {
    //         DB::table('addresses')
    //             ->where('airport_name', $airport_name->name ?? $airport_name->airport_name)
    //             ->where('airport_fbo_id', $airport_name->airport_fbo_id ?? null)
    //             ->where($customerId ? 'customer_id' : 'customer_token', $customerId ?: $token)
    //             ->orderBy('id', 'desc')
    //             ->update(['default_address' => '1']);
    //     }

    //     // Update order details
    //     DB::table('orders')->where('id', $orderId)->update([
    //         'fbo_full_name' => $fboDetails->full_name ?? null,
    //         'fbo_phone_number' => $fboDetails->phone_number ?? null,
    //         'fbo_email_address' => $fboDetails->email_address ?? null,
    //         'fbo_tail_number' => $fboDetails->tail_number ?? null,
    //         'fbo_packaging' => $fboDetails->packaging_section ?? null,
    //         'fbo_service_packaging' => $fboDetails->service_packaging ?? null,
    //         'delivery_date' => $fboDetails->delivery_date ?? null,
    //         'delivery_time' => $fboDetails->delivery_time ?? null,
    //         'airport_fbo_id' => $airport_name->airport_fbo_id ?? null,
    //         'status' => 'pending',
    //         'status_id' => 1,
    //         'customer_id' => $customerId ?? $customer->id,
    //     ]);

    //     // Log order status
    //     DB::table('order_status_log')->insert([
    //         'order_id' => $orderId,
    //         'user_id' => $customerId ?? $customer->id,
    //         'is_admin' => 0,
    //         'status_id' => 1,
    //         'email' => $email ?? $fboDetails->email_address,
    //     ]);

    //     if ($customerId || $customer->id) {
    //         CustomerProfileLog::where('customer_id', $customerId ?? $customer->id)
    //             ->orderBy('id', 'DESC')
    //             ->first()
    //             ->update([
    //                 'order_id' => $orderId,
    //                 'airport' => $billing_address->airport_name,
    //                 'billing_address' => $billing_address->address1,
    //             ]);
    //     }

    //     // Clean up billing address
    //     DB::table('addresses')
    //         ->where('address_type', 'order_billing')
    //         ->where('order_id', $orderId)
    //         ->update([
    //             'postcode' => null,
    //             'state' => null,
    //             'address1' => null,
    //             'country' => null,
    //             'last_name' => null,
    //             'first_name' => null,
    //             'email' => null,
    //         ]);

    //     // Fetch order details for view
    //     $orderDetails = DB::table('order_items')
    //         ->join('orders', 'order_items.order_id', '=', 'orders.id')
    //         ->leftJoin('addresses', 'order_items.order_id', '=', 'addresses.order_id')
    //         ->leftJoin('airport_fbo_details', 'orders.airport_fbo_id', '=', 'airport_fbo_details.id')
    //         ->where('order_items.order_id', $orderId)
    //         ->where('addresses.address_type', 'order_shipping')
    //         ->whereNull('order_items.parent_id')
    //         ->select(
    //             'order_items.name',
    //             'order_items.parent_id',
    //             'order_items.additional',
    //             'order_items.qty_ordered',
    //             'addresses.airport_name',
    //             'addresses.address1',
    //             'addresses.address_type',
    //             'orders.fbo_full_name',
    //             'airport_fbo_details.name as fbo_airport_name',
    //             'airport_fbo_details.address as fbo_airport_address',
    //             'orders.fbo_phone_number',
    //             'orders.fbo_email_address',
    //             'orders.fbo_tail_number',
    //             'orders.fbo_packaging',
    //             'orders.fbo_service_packaging'
    //         )
    //         ->get();

    //     // Send email notifications
    //     // if (!Auth::check()) {
    //     //     try {
    //     //         Mail::to($fboDetails->email_address)->send(new GuestNewOrderNotification($order, $fboDetails->full_name));
    //     //     } catch (\Exception $e) {
    //     //         Log::error('Error sending email: ' . $e->getMessage());
    //     //     }
    //     // }

    //     if (!Auth::check()) {
    //         try {
    //             Log::info('Preparing to queue mail for guest order', [
    //                 'email' => $fboDetails->email_address
    //             ]);
        
    //             // Dispatch the job to the queue
    //             OrderConfirmationGuestEmailJob::dispatch($order, $fboDetails);
                  
    //             Log::info('Mail queued successfully for guest order');
    //         } catch (\Exception $e) {
    //             Log::error('Error queuing mail', [
    //                 'error' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString()
    //             ]);
    //         }
    //     }

    //     // Admin::select('name', 'email')->each(function ($admin) use ($order) {
    //     //     Mail::to($admin->email)->send(new AdminOrderNotification($order, $admin->name));
    //     // });

    //         try {
    //             OrderConfirmationAdminEmailJob::dispatch($order);
    //             Log::info('Email sent successfully to: ');
    //         } catch (\Exception $e) {
    //             Log::error('Failed to send email to: ' , [
    //                 'error' => $e->getMessage(),
    //                 'trace' => $e->getTraceAsString()
    //             ]);
    //         }

    //     // Cleanup session
    //     session()->forget('order');
    //     log::info('Order session data has been cleared.');

    //     return view($this->_config['view'], compact('order', 'orderDetails'));
    // }


    /**
     * Validate order before creation.
     *
     * @return void|\Exception
     */
    public function validateOrder()
    {
        $cart = Cart::getCart();

        $minimumOrderAmount = core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?: 0;

        if (
            auth()->guard('customer')->check()
            && auth()->guard('customer')->user()->is_suspended
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.suspended-account-message'));
        }

        if (
            auth()->guard('customer')->user()
            && !auth()->guard('customer')->user()->status
        ) {
            throw new \Exception(trans('shop::app.checkout.cart.inactive-account-message'));
        }

        if (!$cart->checkMinimumOrder()) {
            throw new \Exception(trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]));
        }

        if (!$cart->billing_address) {

            throw new \Exception(trans('shop::app.checkout.cart.check-billing-address'));
        }

        if (!$cart->payment) {
            throw new \Exception(trans('shop::app.checkout.cart.specify-payment-method'));
        }
    }

    /**
     * Check customer is exist or not.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkExistCustomer()
    {
        $customer = $this->customerRepository->findOneWhere([
            'email' => request()->email,
        ]);

        if (!is_null($customer)) {
            return 'true';
        }

        return 'false';
    }

    /**
     * Login for checkout.
     *
     * @return \Illuminate\Http\Response
     */
    public function loginForCheckout()
    {
        $this->validate(request(), [
            'email' => 'required|email',
        ]);

        if (!auth()->guard('customer')->attempt(request(['email', 'password']))) {
            return response()->json(['error' => trans('shop::app.customer.login-form.invalid-creds')]);
        }

        Cart::mergeCart();

        return response()->json(['success' => 'Login successfully']);
    }

    /**
     * To apply couponable rule requested.
     *
     * @return \Illuminate\Http\Response
     */
    public function applyCoupon()
    {
        $this->validate(request(), [
            'code' => 'string|required',
        ]);

        $code = request()->input('code');

        $result = $this->coupon->apply($code);

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('shop::app.checkout.total.coupon-applied'),
                'result' => $result,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => trans('shop::app.checkout.total.cannot-apply-coupon'),
            'result' => null,
        ], 422);
    }

    /**
     * Initiates the removal of couponable cart rule.
     *
     * @return array
     */
    public function removeCoupon()
    {
        $result = $this->coupon->remove();

        if ($result) {
            Cart::collectTotals();

            return response()->json([
                'success' => true,
                'message' => trans('admin::app.promotion.status.coupon-removed'),
                'data' => [
                    'grand_total' => core()->currency(Cart::getCart()->grand_total),
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => trans('admin::app.promotion.status.coupon-remove-failed'),
            'data' => null,
        ], 422);
    }

    /**
     * Check for minimum order.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkMinimumOrder()
    {
        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?: 0;

        $status = Cart::checkMinimumOrder();

        return response()->json([
            'status' => !$status ? false : true,
            'message' => !$status ? trans('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)]) : 'Success',
        ]);
    }
}
