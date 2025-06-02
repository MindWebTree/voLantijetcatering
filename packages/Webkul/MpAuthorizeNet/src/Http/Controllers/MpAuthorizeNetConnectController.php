<?php

namespace Webkul\MpAuthorizeNet\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Webkul\MpAuthorizeNet\Http\Controllers\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\MpAuthorizeNet\Models\CustomerProfileLog;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository;
use Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetCartRepository;
use Webkul\MpAuthorizeNet\Helpers\Helper;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendOrderFailedEmail;


/**
 * MpAuthorizeNetConnectController Controller
 *
 * @author  shaiv roy <shaiv.roy361@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */
class MpAuthorizeNetConnectController extends Controller
{
    /**
     * Cart object
     *
     * @var array
     */
    protected $cart;

    /**
     * Order object
     *
     * @var array
     */
    protected $order;

    /**
     * Helper object
     *
     * @var array
     */
    protected $helper;

    /**
     * mpauthorizenetRepository object
     *
     * @var array
     */
    protected $mpauthorizenetRepository;

    /**
     * mpauthorizenetcartRepository object
     *
     * @var array
     */
    protected $mpauthorizenetcartRepository;

    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;



    /**
     * Create a new controller instance.
     *
     * @param  Webkul\Attribute\Repositories\OrderRepository  $orderRepository
     * 
     * @return void
     */
    public function __construct(
        OrderRepository $orderRepository,
        MpAuthorizeNetRepository $mpauthorizenetRepository,
        MpAuthorizeNetCartRepository $mpauthorizenetcartRepository,
        Helper $helper
    ) {

        $this->orderRepository = $orderRepository;

        $this->mpauthorizenetRepository = $mpauthorizenetRepository;

        $this->mpauthorizenetcartRepository = $mpauthorizenetcartRepository;

        $this->helper = $helper;

        $this->cart = Cart::getCart();

    }

    public function collectToken()
    {


        log::info('collectToken');
        try {
            // sandeep add code 
            $orderId = request()->input('order_id');
            
            if(isset($orderId) && $orderId){
                DB::table('mpauthorizenet_cart')
                ->where('cart_id', $orderId)
                ->delete();
            }else{
                DB::table('mpauthorizenet_cart')
                ->where('cart_id', Cart::getCart()->id)
                ->delete();
            }
 
            log::info('savedCardSelectedId',['savedCardSelectedId',request()->input('savedCardSelectedId')]);
            if (request()->input('savedCardSelectedId')) {

                log::info('1');

                if (isset($orderId) && $orderId) {
                log::info('2');

                    session()->put('ADMIN_PAYMENT', true);
                    session()->put('ADMIN_CARD', true);

                    $misc = $this->mpauthorizenetRepository->findOneWhere([
                        'id' => request()->input('savedCardSelectedId'),
                        'customers_id' => request()->input('customerId'),
                    ])->misc;

                    DB::statement('SET FOREIGN_KEY_CHECKS=0');
                    $result = $this->mpauthorizenetcartRepository->create([
                        'cart_id' => request()->input('order_id'),
                        'mpauthorizenet_token' => $misc,
                    ]);
                    DB::statement('SET FOREIGN_KEY_CHECKS=1');

                } else {
                log::info('3a');

                    session()->forget('ADMIN_PAYMENT');
                    session()->forget('ADMIN_CARD');
                    $misc = $this->mpauthorizenetRepository->findOneWhere([
                        'id' => request()->input('savedCardSelectedId'),
                        'customers_id' => auth()->guard('customer')->user()->id,
                    ])->misc;

                    $result = $this->mpauthorizenetcartRepository->create([
                        'cart_id' => Cart::getCart()->id,
                        'mpauthorizenet_token' => $misc,
                    ]);
                }


                if ($result) {
                     // sandeep add code
                     session()->put('card', request()->input('result'));
                    return response()->json(['success' => 'true']);
                } else {
                    return response()->json(['success' => 'false'], 400);
                }

            } else {
                log::info('4a');
                $misc = request()->input('response');
                log::info('misc',['misc'=>$misc]);
                log::info('result',['result',request()->input('result')]);
                if (auth()->guard('customer')->check() && request()->input('result') == 'true') {
                log::info('5a');

                    log::info('misc',['misc'=>$misc]);
                    $last4 = $misc['encryptedCardData']['cardNumber'];
                    log::info('last4',['last4',$last4]);

                    $cardExist = $this->mpauthorizenetRepository->findOneWhere([
                        'last_four' => $last4,
                        'customers_id' => auth()->guard('customer')->user()->id,
                    ]);

                    if ($cardExist) {
                        $result = $cardExist->update([
                            'token' => $misc['opaqueData']['dataValue'],
                            'misc' => json_encode($misc),
                        ]);
                        
                    } else {
                        $result = $this->mpauthorizenetRepository->create([
                            'customers_id' => auth()->guard('customer')->user()->id,
                            'token' => $misc['opaqueData']['dataValue'],
                            'last_four' => $last4,
                            'misc' => json_encode($misc),
                        ]);
                    }

                    $this->mpauthorizenetcartRepository->create([
                        'cart_id' => Cart::getCart()->id,
                        'mpauthorizenet_token' => json_encode($misc),
                    ]);

                    if ($result) {
                        // sandeep add code
                        session()->put('card', request()->input('result'));
                        return response()->json(['success' => 'true']);
                    } else {
                        return response()->json(['success' => 'false'], 400);
                    }
                } else {
                    log::info('second');

                    //payment from admin or invoice view and card is not save
                    if (request()->input('order_id')) {
                        session()->put('ADMIN_PAYMENT', true);
                        session()->forget('ADMIN_CARD');

                        // $result = $this->mpauthorizenetcartRepository->create([
                        //     'cart_id' => request()->input('order_id'),
                        //     'mpauthorizenet_token' => json_encode($misc),
                        // ]);
                        try {
                            // Disable foreign key checks
                            DB::statement('SET FOREIGN_KEY_CHECKS=0');

                            $result = $this->mpauthorizenetcartRepository->create([
                                'cart_id' => request()->input('order_id'),
                                'mpauthorizenet_token' => json_encode($misc),
                            ]);

                            // Re-enable foreign key checks
                            DB::statement('SET FOREIGN_KEY_CHECKS=1');

                            if ($result) {
                                return response()->json(['success' => 'true']);
                            } else {
                                return response()->json(['success' => 'false'], 400);
                            }
                        } catch (\Exception $e) {

                            DB::statement('SET FOREIGN_KEY_CHECKS=1');
                            return response()->json(['success' => 'false', 'message' => $e->getMessage()], 500);
                            // dd($e->getMessage());
                        }

                    } else {
                        log::info('set card velue');
                        session()->forget('ADMIN_PAYMENT');
                        session()->forget('ADMIN_CARD');

                        session()->put('card', request()->input('result'));
                        $result = $this->mpauthorizenetcartRepository->create([
                            'cart_id' => Cart::getCart()->id,
                            'mpauthorizenet_token' => json_encode($misc),
                        ]);
                    }
                    if ($result) {
                        return response()->json(['success' => 'true']);
                    } else {
                        return response()->json(['success' => 'false'], 400);
                    }

                }
            }
        } catch (\Exception $e) {
            session()->flash('error', __('mpauthorizenet::app.error.something-went-wrong'));
            // return redirect()->route('shop.checkout.cart.index');
            return redirect()->back();
        }

    }


    public function createCharge(Request $request)
    {
        log::info('1');
        log::info('session_data',['session_data',session()->all()]); 
        try {

            $cardBoolean = session()->get('card');
            $orderId = request()->input('order_id');

            log::info('2');
            log::info('cardBoolean',['cardBoolean'=>$cardBoolean]);

            // dd($orderId);
            //customer is login and customer has saved card or if session has ADMIN_CARD and order id
            if ((auth()->guard('customer')->check() && $cardBoolean != 'false'  && !isset($orderId)) || session()->has('ADMIN_CARD') && isset($orderId)) {
                log::info('3');

                if (session()->has('ADMIN_PAYMENT') && $orderId) {
                    log::info('4');
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => $orderId
                    ])->mpauthorizenet_token;

                } else {
                    log::info('5');
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => Cart::getCart()->id
                    ])->mpauthorizenet_token;
                }
        
                $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);

                if (isset($MpauthorizeNetCardDecode->customerResponse)) {
                    log::info('6');
                    if (session()->has('ADMIN_PAYMENT') && $orderId) {
                        log::info('7');
                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($MpauthorizeNetCardDecode);

                        $this->mpauthorizenetcartRepository->deleteWhere([
                            'cart_id' => $orderId
                        ]);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);
                        // dd($customerProfileResponse);
                        if ($customerProfileResponse == 'true') {
                            log::info('8');
                            session()->forget('ADMIN_PAYMENT');
                            session()->forget('ADMIN_CARD');
                            return $customerProfileResponse;
                        }
                    } else {
                        log::info('9');
                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($MpauthorizeNetCardDecode);

                        $this->mpauthorizenetcartRepository->deleteWhere([
                            'cart_id' => Cart::getCart()->id
                        ]);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);

                        if ($customerProfileResponse == 'true') {
                            $cart = Cart::getCart();
                            log::info('10');
                            CustomerProfileLog::create([
                                'profile_id' => $MpauthorizeNetCardDecode->customerResponse->customerProfileId,
                                'payment_profile_id' => $MpauthorizeNetCardDecode->customerResponse->paymentProfielId,
                                'email' => Auth::user()->email,
                                'customer_id' => Auth::user()->id,
                            ]);
                    
                            return redirect()->route('shop.checkout.success');

                        } else {
                            log::info('11');
                            session()->flash('warning', $customerProfileResponse);
                            return redirect()->route('shop.checkout.cart.index');
                        }
                    }

                } else {

                    $customerEmail = Cart::getCart()->customer_email;
                    $cutomerProfileResponse = $this->helper->createCustomerProfile($customerEmail, $MpauthorizeNetCardDecode);

                    log::info('cutomerProfileResponse',['cutomerProfileResponse'=>$cutomerProfileResponse]);

                    if (($cutomerProfileResponse != null) && ($cutomerProfileResponse->getMessages()->getResultCode() == "Ok")) {
                        $paymentProfiles = $cutomerProfileResponse->getCustomerPaymentProfileIdList();

                        log::info('paymentProfiles',['paymentProfiles'=>$paymentProfiles]);

                        $customerResponse = [
                            'customerProfileId' => $cutomerProfileResponse->getCustomerProfileId(),
                            'paymentProfielId' => $paymentProfiles[0],
                        ];

                        $cardToken = $this->mpauthorizenetRepository->findOneWhere([
                            'token' => $MpauthorizeNetCardDecode->opaqueData->dataValue,
                        ])->misc;

                        log::info('cardToken',['cardToken'=>$cardToken]);

                        $cardTokenDecode = json_decode($cardToken);

                        log::info('cardTokenDecode',['cardTokenDecode',$cardTokenDecode]);

                        $updateRespone = [
                            'cardResponse' => $cardTokenDecode,
                            'customerResponse' => $customerResponse,
                        ];

                        $this->mpauthorizenetRepository->findOneWhere([
                            'token' => $MpauthorizeNetCardDecode->opaqueData->dataValue,
                        ])->update([
                                    'misc' => json_encode($updateRespone),
                               ]);

                        $UpdatedToken = $this->mpauthorizenetRepository->findOneWhere([
                            'token' => $MpauthorizeNetCardDecode->opaqueData->dataValue,
                        ])->misc;

                        $decodeUpdatedToken = json_decode($UpdatedToken);
                        log::info('decodeUpdatedToken',['decodeUpdatedToken',$decodeUpdatedToken]);

                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($decodeUpdatedToken);

                        log::info('savedCardPaymentResponse',['savedCardPaymentResponse',$savedCardPaymentResponse]);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);

                        log::info('customerProfileResponse',['customerProfileResponse'=>$customerProfileResponse]);

                        if ($customerProfileResponse == 'true') {
                            log::info('14');
                            CustomerProfileLog::create([
                                'profile_id' => $customerResponse['customerProfileId'],
                                'payment_profile_id' => $customerResponse['paymentProfielId'],
                                'email' => Auth::user()->email,
                                'customer_id' => Auth::user()->id,
                            ]);
                            return redirect()->route('shop.checkout.success');
                        } else {
                            log::info('15');
                            session()->flash('warning', $customerProfileResponse);

                            return redirect()->route('shop.checkout.cart.index');
                        }

                    } else {
                        log::info('16');
                        $this->helper->deleteCart();

                        $errorMessages = $cutomerProfileResponse->getMessages()->getMessage();

                        session()->flash('warning', $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText());

                        return redirect()->route('shop.checkout.cart.index');
                    }
                }
            } else {
                log::info('17');
                if (session()->has('ADMIN_PAYMENT')) {
                    log::info('18');
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => request()->input('order_id')
                    ])->mpauthorizenet_token;

                    $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);
                    $token = session('token');

                    $guestResponse = $this->helper->createAnAcceptPaymentTransaction($MpauthorizeNetCardDecode);
                    // dd($guestResponse);

                    $this->mpauthorizenetcartRepository->deleteWhere([
                        'cart_id' => request()->input('order_id')
                    ]);

                    $paymentResponse = $this->helper->paymentResponse($guestResponse);



                    if ($paymentResponse == 'true') {
                        
                        log::info('19');
                        session()->forget('ADMIN_PAYMENT');
                        return $paymentResponse;

                    } else {
                        log::info('20');
                        $this->helper->deleteCart();

                        return redirect()->back();
                    }
                } else {

                    log::info('21');                
                    log::info(Cart::getCart()->id);
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => Cart::getCart()->id
                    ])->mpauthorizenet_token;
                    

                    log::info('card_detail',['cart_detail'=>$MpauthorizeNetCard]);

                    $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);
                    log::info('MpauthorizeNetCardDecode3',['MpauthorizeNetCardDecode2'=>$MpauthorizeNetCardDecode]);

                    $token = session('token');
                    log::info('token',['token'=>$token]);

                    // sandeep add code
                    if(auth()->guard('customer')->check()){
                            $fboDetails = DB::table('fbo_details')
                            ->where('customer_id', Auth::user()->id)
                            ->orderBy('id', 'DESC')
                            ->first();

                            $customerEmail = Cart::getCart()->customer_email;
                            $customerName = Cart::getCart()->customer_first_name . ' ' . Cart::getCart()->customer_last_name ?? "";
                    }else{
                            $fboDetails = DB::table('fbo_details')
                            ->where('customer_token', $token)
                            ->whereNotNull('customer_token')
                            ->orderBy('id', 'DESC')
                            ->first();
                            $customerEmail = $fboDetails->email_address;
                            $customerName = $fboDetails->full_name ?? "";
                    }

                log::info('cartcustomerEmail',['customerEmail'=>$customerEmail]);
                
                log::info('guest_email',['guest_email'=>$customerEmail]);

                    $guestPaymentprofile = $this->helper->createCustomerProfile($customerEmail, $MpauthorizeNetCardDecode);
                    log::info('guestPaymentprofile',['guestPaymentprofile'=>$guestPaymentprofile]);

                    $guestResponse = $this->helper->createAnAcceptPaymentTransaction($MpauthorizeNetCardDecode);

                    $this->mpauthorizenetcartRepository->deleteWhere([
                        'cart_id' => Cart::getCart()->id
                    ]);

                    $paymentResponse = $this->helper->paymentResponse($guestResponse);
                    log::info('paymentResponse',['paymentResponse'=>$paymentResponse]);
                    log::info('session_id',['session_id'=>session()->has('customer_id')]);
                    log::info('session_orderData',['session_orderData'=>session()->has('order')]);
                    if ($paymentResponse == 'true') {
                        log::info('22');
                        // sandeep add auth check
                        if(!auth()->guard('customer')->check()){
                        if (!session()->has('customer_id')) {
                            //creating guest as customer for customer ID if doesn't exist
                            $customer_id = DB::table('customers')->insertGetId([
                                'first_name' => '',
                                'last_name' => '',
                                'password' => '',
                                'token' => $token,
                            ]);
                            session(['customer_id' => $customer_id]);
                            log::info('guest customer id',['customer_id'=>$customer_id]);

                        } else {
                            log::info('23');
                            // Customer found, use the existing ID                   
                            $customer_id = session('customer_id');
                            log::info('login customer id',['customer_id'=>$customer_id]);
                        }

                    }else{
                        $customer_id = Auth::user()->id;
                    }

                    log::info('profile_id',['profile_id'=>$guestPaymentprofile->getCustomerProfileId()]);
                    log::info('payment_profile_id',['payment_profile_id'=>$guestPaymentprofile->getCustomerPaymentProfileIdList()[0]]);
                    log::info('profile_id',['profile_id'=>$customer_id]);
                    log::info('email',['email'=>$fboDetails->email_address]);


                        CustomerProfileLog::create([
                            'profile_id' => $guestPaymentprofile->getCustomerProfileId(),
                            'payment_profile_id' => $guestPaymentprofile->getCustomerPaymentProfileIdList()[0],
                            'customer_id' => $customer_id,
                            'email' => $customerEmail
                        ]);

                        log::info('session_orderData1',['session_orderData1'=>session()->has('order')]);

                        return redirect()->route('shop.checkout.success');

                    } else {
                        log::info('24');
                        $this->helper->deleteCart();

                        session()->flash('warning', $guestResponse);
                        return redirect()->route('shop.checkout.cart.index');
                    }
                }
            }

        } catch (\Exception $e) {
            session()->flash('error', __('mpauthorizenet::app.error.something-went-wrong'));
            log::info('25');
            $order = session('order');
            log::info('order',['order'=>$order]);
            log::error($e->getMessage());
            if(isset($order)){
            $orderData = [
                'order_id' => $order['increment_id'],
                'status' => $order['status'],
                'Name' => $customerName ?? '',
                'Email' => $customerEmail ?? '',
                'Mail_Heading' => "Order Processing Failure",
                'Mail_Subject' => "Order Failed Notification",
                'error_message' => $e->getMessage(),
            ];
            // Dispatch the job to send email
            SendOrderFailedEmail::dispatch($orderData);
        }

        // When the 'Save Card' option is selected and an error occurs, this condition should be triggered
        if(isset($orderId)){
            $order = DB::table('orders')->where('id', $orderId)->first();
            $orderData = [
                'order_id' => $order->increment_id,
                'status' => $order->status,
                'Name' => (!empty($order->customer_first_name) && !empty($order->customer_last_name))
                    ? $order->customer_first_name . ' ' . $order->customer_last_name
                    : $order->fbo_full_name,
                'Email' => !empty($order->customer_email)
                    ? $order->customer_email
                    : $order->fbo_email_address,
                'Mail_Heading' => "Payment Processing Failure",
                'Mail_Subject' => "Payment Failed Notification",
                'error_message' => $e->getMessage(),
            ];
            // Dispatch the job to send email
            SendOrderFailedEmail::dispatch($orderData);
        }

            return redirect()->route('shop.checkout.cart.index');
        }
    }

    /**
     * Call to delete saved card
     *
     *
     * @return string
     */

    public function deleteCard()
    {
        try {
            $customerId = request()->input('customerId');

            if (isset($customerId)) {
                $deleteIfFound = $this->mpauthorizenetRepository->findOneWhere(['id' => request()->input('id'), 'customers_id' => $customerId]);
            } else {
                $deleteIfFound = $this->mpauthorizenetRepository->findOneWhere(['id' => request()->input('id'), 'customers_id' => auth()->guard('customer')->user()->id]);
            }


            $result = $deleteIfFound->delete();

            return (string) $result;
        } catch (\Exception $e) {
            session()->flash('error', __('mpauthorizenet::app.error.something-went-wrong'));

            return redirect()->route('shop.checkout.cart.index');
        }

    }

}
