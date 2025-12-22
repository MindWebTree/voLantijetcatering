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

        try {
            // sandeep add code 
            $orderId = request()->input('order_id');
            log::info("inside collectToken");
            if(isset($orderId) && $orderId){
                DB::table('mpauthorizenet_cart')
                ->where('cart_id', $orderId)
                ->delete();
            }else{
                DB::table('mpauthorizenet_cart')
                ->where('cart_id', Cart::getCart()->id)
                ->delete();
            }

            if (request()->input('savedCardSelectedId')) {
                log::info("inside savedCardSelectedId");

                if (isset($orderId) && $orderId) {
log::info("inside saved card admin section");
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
log::info("inside saved card customer section");
                    if (auth()->guard('customer')->check()) {
                            $customer_id = auth()->guard('customer')->user()->id;
                        } else {
                            $token = session('token');
                            $customer = DB::table('customers')->where('token', $token)->first();
                            if ($customer) {
                                $customer_id = $customer->id;
                        } 
                    }

                    session()->forget('ADMIN_PAYMENT');
                    session()->forget('ADMIN_CARD');

                    $misc = $this->mpauthorizenetRepository->findOneWhere([
                        'id' => request()->input('savedCardSelectedId'),
                        'customers_id' => $customer_id,
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
                log::info("inside no saved card section");

                $misc = request()->input('response');
                if ((auth()->guard('customer')->check() || session()->has('token') ) && request()->input('result') == 'true') {
                log::info("inside no saved card and now click save card");

                $token = session('token');
                if (auth()->guard('customer')->check()) {
                        $customer_id = auth()->guard('customer')->user()->id;
                } else {
                    $customer = DB::table('customers')->where('token', $token)->first();
                    if ($customer) {
                        $customer_id = $customer->id;
                    } else {
                        $customer_id = DB::table('customers')->insertGetId([
                            'first_name' => '',
                            'last_name' => '',
                            'password' => '',
                            'token' => $token,
                        ]);
                    }
                    session(['customer_id' => $customer_id]);
                }

                    $last4 = $misc['encryptedCardData']['cardNumber'];

                    $cardExist = $this->mpauthorizenetRepository->findOneWhere([
                        'last_four' => $last4,
                        'customers_id' => $customer_id,
                    ]);

                    if ($cardExist) {
                        $result = $cardExist->update([
                            'token' => $misc['opaqueData']['dataValue'],
                            'misc' => json_encode($misc),
                        ]);

                    } else {
                        $result = $this->mpauthorizenetRepository->create([
                            'customers_id' => $customer_id,
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

                    log::info("inside no save card and click not save card");
                    //payment from admin or invoice view and card is not save
                    if (request()->input('order_id')) {
                        log::info("admin side no save card");
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
                        session()->forget('ADMIN_PAYMENT');
                        session()->forget('ADMIN_CARD');
                        log::info("admin side no save card");

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
        try {

            $cardBoolean = session()->get('card');
            $orderId = request()->input('order_id');

            //customer is login and customer has saved card or if session has ADMIN_CARD and order id
            if (((auth()->guard('customer')->check() || session()->has('token')) && $cardBoolean != 'false' && !isset($orderId)) || session()->has('ADMIN_CARD') && isset($orderId)) {

                if (session()->has('ADMIN_PAYMENT') && $orderId) {

                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => $orderId
                    ])->mpauthorizenet_token;

                } else {
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => Cart::getCart()->id
                    ])->mpauthorizenet_token;
                }
        
                $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);

                if (isset($MpauthorizeNetCardDecode->customerResponse)) {
                    if (session()->has('ADMIN_PAYMENT') && $orderId) {
                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($MpauthorizeNetCardDecode);

                        $this->mpauthorizenetcartRepository->deleteWhere([
                            'cart_id' => $orderId
                        ]);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);
                        if ($customerProfileResponse == 'true') {
                            session()->forget('ADMIN_PAYMENT');
                            session()->forget('ADMIN_CARD');
                            return $customerProfileResponse;
                        }   
                    } else {
                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($MpauthorizeNetCardDecode);

                        $this->mpauthorizenetcartRepository->deleteWhere([
                            'cart_id' => Cart::getCart()->id
                        ]);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);

                        if ($customerProfileResponse == 'true') {
                            if(auth()->guard('customer')->check()){
                                $customerEmail = Auth::user()->email;
                                $customer_id = Auth::user()->id;
                            } else {
                                $token = session('token');
                                $fboDetails = DB::table('fbo_details')
                                    ->where('customer_token', $token)
                                    ->whereNotNull('customer_token')
                                    ->orderBy('id', 'DESC')
                                    ->first();
                                $customerEmail = $fboDetails->email_address;
                                $customer = DB::table('customers')->where('token', $token)->first();
                                if ($customer) {
                                    $customer_id = $customer->id;
                                }
                            }


                            $cart = Cart::getCart();
                            CustomerProfileLog::create([
                                'profile_id' => $MpauthorizeNetCardDecode->customerResponse->customerProfileId,
                                'payment_profile_id' => $MpauthorizeNetCardDecode->customerResponse->paymentProfielId,
                                'email' => $customerEmail,
                                'customer_id' => $customer_id,
                            ]);
                    
                            return redirect()->route('shop.checkout.success');

                        } else {
                            session()->flash('warning', $customerProfileResponse);
                            return redirect()->route('shop.checkout.cart.index');
                        }
                    }

                } else {

                    $customerEmail = Cart::getCart()->customer_email;
                    $cutomerProfileResponse = $this->helper->createCustomerProfile($customerEmail, $MpauthorizeNetCardDecode);


                    if (($cutomerProfileResponse != null) && ($cutomerProfileResponse->getMessages()->getResultCode() == "Ok")) {
                        $paymentProfiles = $cutomerProfileResponse->getCustomerPaymentProfileIdList();

                        $customerResponse = [
                            'customerProfileId' => $cutomerProfileResponse->getCustomerProfileId(),
                            'paymentProfielId' => $paymentProfiles[0],
                        ];

                        $cardToken = $this->mpauthorizenetRepository->findOneWhere([
                            'token' => $MpauthorizeNetCardDecode->opaqueData->dataValue,
                        ])->misc;

                        $cardTokenDecode = json_decode($cardToken);

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

                        $savedCardPaymentResponse = $this->helper->chargeCustomerProfile($decodeUpdatedToken);

                        $customerProfileResponse = $this->helper->paymentResponse($savedCardPaymentResponse);

                        if ($customerProfileResponse == 'true') {
                            if(auth()->guard('customer')->check()){
                            $customerEmail = Auth::user()->email;
                            $customer_id = Auth::user()->id;
                        } else {
                            $token = session('token');
                            $fboDetails = DB::table('fbo_details')
                                ->where('customer_token', $token)
                                ->whereNotNull('customer_token')
                                ->orderBy('id', 'DESC')
                                ->first();
                            $customerEmail = $fboDetails->email_address;
                            $customer = DB::table('customers')->where('token', $token)->first();
                            if ($customer) {
                                $customer_id = $customer->id;
                            }
                        }
                            CustomerProfileLog::create([
                                'profile_id' => $customerResponse['customerProfileId'],
                                'payment_profile_id' => $customerResponse['paymentProfielId'],
                                'email' => $customerEmail,
                                'customer_id' => $customer_id,
                            ]);
                            return redirect()->route('shop.checkout.success');
                        } else {
                            session()->flash('warning', $customerProfileResponse);

                            return redirect()->route('shop.checkout.cart.index');
                        }

                    } else {
                        $this->helper->deleteCart();

                        $errorMessages = $cutomerProfileResponse->getMessages()->getMessage();

                        session()->flash('warning', $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText());

                        return redirect()->route('shop.checkout.cart.index');
                    }
                }
            } else {
                
                if (session()->has('ADMIN_PAYMENT')) {
                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => request()->input('order_id')
                    ])->mpauthorizenet_token;

                    $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);

                    // $guestPaymentprofile = $this->helper->createCustomerProfile('sandeep@mindwebtree.com', $MpauthorizeNetCardDecode);

                    // log::info("guestPaymentprofile",$guestPaymentprofile);

                    $token = session('token');

                    $guestResponse = $this->helper->createAnAcceptPaymentTransaction($MpauthorizeNetCardDecode);
Log::info('guestResponse', (array) $guestResponse);
                    // dd($guestResponse);

                    $this->mpauthorizenetcartRepository->deleteWhere([
                        'cart_id' => request()->input('order_id')
                    ]);

                    $paymentResponse = $this->helper->paymentResponse($guestResponse);



                    if ($paymentResponse == 'true') {
                        
                        session()->forget('ADMIN_PAYMENT');
                        return $paymentResponse;

                    } else {
                        $this->helper->deleteCart();

                        return redirect()->back();
                    }
                } else {

                    $MpauthorizeNetCard = $this->mpauthorizenetcartRepository->findOneWhere([
                        'cart_id' => Cart::getCart()->id
                    ])->mpauthorizenet_token;
                    

                    $MpauthorizeNetCardDecode = json_decode($MpauthorizeNetCard);

                    $token = session('token');

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
                
                    $guestPaymentprofile = $this->helper->createCustomerProfile($customerEmail, $MpauthorizeNetCardDecode);

                    $guestResponse = $this->helper->createAnAcceptPaymentTransaction($MpauthorizeNetCardDecode);

                    $this->mpauthorizenetcartRepository->deleteWhere([
                        'cart_id' => Cart::getCart()->id
                    ]);

                    $paymentResponse = $this->helper->paymentResponse($guestResponse);
                    if ($paymentResponse == 'true') {
                        // sandeep add auth check
                        if(!auth()->guard('customer')->check()){
                        if (!session()->has('customer_id')) {
                            //creating guest as customer for customer ID if doesn't exist
                            // $customer_id = DB::table('customers')->insertGetId([
                            //     'first_name' => '',
                            //     'last_name' => '',
                            //     'password' => '',
                            //     'token' => $token,
                            // ]);
                            $customer = DB::table('customers')->where('token', $token)->first();
                            if ($customer) {
                                $customer_id = $customer->id;
                            } else {
                                // Record not found, create new and get inserted ID
                                $customer_id = DB::table('customers')->insertGetId([
                                    'first_name' => '',
                                    'last_name' => '',
                                    'password' => '',
                                    'token' => $token,
                                ]);
                            }
                            session(['customer_id' => $customer_id]);

                        } else {
                            // Customer found, use the existing ID                   
                            $customer_id = session('customer_id');
                        }

                    }else{
                        $customer_id = Auth::user()->id;
                    }


                        CustomerProfileLog::create([
                            'profile_id' => $guestPaymentprofile->getCustomerProfileId(),
                            'payment_profile_id' => $guestPaymentprofile->getCustomerPaymentProfileIdList()[0],
                            'customer_id' => $customer_id,
                            'email' => $customerEmail
                        ]);

                        return redirect()->route('shop.checkout.success');

                    } else {
                        $this->helper->deleteCart();

                        session()->flash('warning', $guestResponse);
                        return redirect()->route('shop.checkout.cart.index');
                    }
                }
            }

        } catch (\Exception $e) {
            session()->flash('error', __('mpauthorizenet::app.error.something-went-wrong'));
            $order = session('order');
            log::error($e->getMessage());
            $errorFile = $e->getFile();
            $errorLine = $e->getLine();
            if(isset($order)){
            $orderData = [
                    'order_id' => $order['increment_id'],
                    'status' => $order['status'],
                    'Name' => $customerName ?? '',
                    'Email' => $customerEmail ?? '',
                    'Mail_Heading' => "Order Processing Failure",
                    'Mail_Subject' => "Order Failed Notification",
                    'error_message' => $e->getMessage(),
                    'error_file' => $errorFile,
                    'error_line' => $errorLine,
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
                    'error_file' => $errorFile,
                    'error_line' => $errorLine,
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
                $customer_id = null;
                if(auth()->guard('customer')->check()){
                    $customer_id = Auth::user()->id;
                } else {
                    $token = session('token');
                    $customer = DB::table('customers')->where('token', $token)->first();
                    if ($customer) {
                        $customer_id = $customer->id;
                    }
                }
                $deleteIfFound = $this->mpauthorizenetRepository->findOneWhere(['id' => request()->input('id'), 'customers_id' => $customer_id]);
            }


            $result = $deleteIfFound->delete();

            return (string) $result;
        } catch (\Exception $e) {
            session()->flash('error', __('mpauthorizenet::app.error.something-went-wrong'));

            return redirect()->route('shop.checkout.cart.index');
        }

    }

}
