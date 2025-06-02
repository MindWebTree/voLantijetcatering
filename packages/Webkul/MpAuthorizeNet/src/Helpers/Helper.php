<?php
namespace Webkul\MpAuthorizeNet\Helpers;

use App\Http\Controllers\QuickBookController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Checkout\Facades\Cart;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetCartRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use Illuminate\Support\Facades\Log;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Repositories\OrderTransactionRepository;
use ACME\paymentProfile\Jobs\ProcessQuickBooksInvoice;
use ACME\paymentProfile\Jobs\UpdateQuickbookPayment;
use App\Jobs\SendOrderFailedEmail;


/**
 * MpAuthorizeNetConnect Helper Class
 *
 * @author  shaiv roy <shaiv.roy361@webkul.com>
 * @copyright 2019 Webkul Software Pvt Ltd (http://www.webkul.com)
 */

class Helper
{

    /**
     * Marketplace Seller Repository object
     */
    protected $marketplaceSellerRepository;

    /**
     * Marketplace product Repository object
     */
    protected $marketplaceProductRepository;

    /**
     * to hold merchantAuthentication value
     */
    protected $merchantAuthentication;

    /**
     * to hold loginID value
     */
    protected $loginID;

    /**
     * to hold transactionKey value
     */
    protected $transactionKey;

    /**
     * to hold reference Id value
     */
    protected $refId;

    /**
     * InvoiceRepository object
     *
     * @var object
     */
    protected $invoiceRepository;

    /**
     * OrderRepository object
     *
     * @var array
     */
    protected $orderRepository;

    /**
     * mpauthorizenetcartRepository object
     *
     * @var array
     */
    protected $mpauthorizenetcartRepository;

    public function __construct(
        OrderRepository $orderRepository,
        MpAuthorizeNetCartRepository $mpauthorizenetcartRepository,
        InvoiceRepository $invoiceRepository,
        protected OrderTransactionRepository $orderTransactionRepository

    ) {
        $this->orderRepository = $orderRepository;

        $this->mpauthorizenetcartRepository = $mpauthorizenetcartRepository;

        $this->invoiceRepository = $invoiceRepository;

        if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1') {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_transaction_key');
        } else {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.transaction_key');
        }
        /* Create a merchantAuthenticationType object with authentication details
       retrieved from the constants file */
        $this->merchantAuthentication = new AnetAPI\MerchantAuthenticationType();

        $this->loginID = $this->merchantAuthentication->setName($merchantLoginId);

        $this->transactionKey = $this->merchantAuthentication->setTransactionKey($merchantAuthentication);

        // Set the transaction's refId
        $this->refId = 'ref' . time();

    }

    /**
     * Create Customer Profile
     *
     * @return array
     */
    function createCustomerProfile($email = '', $MpauthorizeNetCardDecode = '')
    {
        log::info('email',['email',$email]);
        log::info('createCustomerProfile');
        // Create the payment object for a payment nonce
        $opaqueData = new AnetAPI\OpaqueDataType();
        $opaqueData->setDataDescriptor($MpauthorizeNetCardDecode->opaqueData->dataDescriptor);
        $opaqueData->setDataValue($MpauthorizeNetCardDecode->opaqueData->dataValue);
        log::info('opaqueData', ['opaqueData' => $opaqueData]);

        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setOpaqueData($opaqueData);
        log::info('paymentOne', ['paymentOne' => $paymentOne]);
        // $customer_id = Auth::user()->id;
        $billingAddress = Cart::getCart()->billing_address;
        // $billingAddress = DB::table('addresses')
        //     ->where('address_type', 'cart_shipping')
        //     ->where('customer_id', $customer_id)
        //     ->orderBy('id', 'DESC')
        //     ->first();
        // dd($billingAddress);

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName($billingAddress->first_name);
        $customerAddress->setLastName($billingAddress->last_name);
        $customerAddress->setAddress($billingAddress->address1);
        $customerAddress->setCity($billingAddress->city);
        $customerAddress->setState($billingAddress->state);
        $customerAddress->setZip($billingAddress->postcode);
        $customerAddress->setCountry($billingAddress->country);
        $customerAddress->setPhoneNumber($billingAddress->phone);

        log::info('customerAddress', ['customerAddress' => $customerAddress]);
        // Create a new CustomerPaymentProfile object
        $paymentProfile = new AnetAPI\CustomerPaymentProfileType();
        $paymentProfile->setCustomerType('individual');
        // $paymentProfile->setBillTo($customerAddress);
        $paymentProfile->setPayment($paymentOne);
        $paymentProfiles[] = $paymentProfile;

        // dd($paymentProfile);
        // Create an array of any shipping addresses

        // Create a new CustomerProfileType and add the payment profile object
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setDescription(core()->getConfigData('sales.paymentmethods.mpauthorizenet.description'));
        $customerProfile->setMerchantCustomerId("M_" . time());
        $customerProfile->setEmail($email);
        $customerProfile->setpaymentProfiles($paymentProfiles);
        log::info('customerProfile', ['customerProfile' => $customerProfile]);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateCustomerProfileRequest();
        $request->setMerchantAuthentication($this->merchantAuthentication);
        $request->setRefId($this->refId);
        $request->setProfile($customerProfile);
        log::info('request', ['request' => $request]);
        // Create the controller and get the response
        $controller = new AnetController\CreateCustomerProfileController($request);
        log::info('controller', ['controller' => $controller]);
        $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);
        // dd($response);
        log::info('response', ['response' => $response]);

        $responseArray = json_decode(json_encode($response), true);
        $messages = $responseArray['messages'] ?? [];
        if (
            isset($messages['resultCode']) &&
            $messages['resultCode'] === 'Error'
        ) {
            $errorMessage = $messages['message'][0]['text'] ?? 'Unknown error';
            log::error('Authorize.net error: ' . $errorMessage);
            
            $token = session('token');
            // sandeep add code
            if(auth()->guard('customer')->check()){
                    $customerName = Cart::getCart()->customer_first_name . ' ' . Cart::getCart()->customer_last_name ?? "";
            }else{
                    $fboDetails = DB::table('fbo_details')
                    ->where('customer_token', $token)
                    ->whereNotNull('customer_token')
                    ->orderBy('id', 'DESC')
                    ->first();
                    $customerName = $fboDetails->full_name ?? "";
            }

            log::info('billingAddress',['billingAddress'=>$billingAddress]);
            if(isset($errorMessage)){
                $orderData = [
                    'order_id' => "N/A",
                    'status' => 'N/A',
                    'Name' => $customerName,
                    'Email' => $email ?? "",
                    'Mail_Heading' => "Payment Processing Failure",
                    'Mail_Subject' => "Payment Failed Notification",
                    'error_message' => $errorMessage,
                ];
                // Dispatch the job to send email
                SendOrderFailedEmail::dispatch($orderData);
            }
        }

        return $response;
}

    /**
     * Charge Customer Profile
     *
     * @return boolean
     */

    function paymentResponse($savedCardPaymentResponse = '')
    {

    try {
        if (!session()->has('ADMIN_PAYMENT')) {
            log::info('customer payment');
            $order = $this->orderRepository->create(Cart::prepareDataForOrder());

            $this->order = $this->orderRepository->findOneWhere([
                'cart_id' => Cart::getCart()->id
            ]);

            if ($savedCardPaymentResponse != null) {

                log::info('customer save card payment');

                if ($savedCardPaymentResponse->getMessages->getResultCode == "Ok") {

                    $tresponse = $savedCardPaymentResponse->getTransactionResponse;

                    if ($tresponse != null && $tresponse->messages != null) {

                        $this->deleteCart();
                        
                        Cart::deActivateCart();
                        // sandeep add code
                        $orderData = $order->toArray();
                        //   $orderData['fillable'] = $order->getFillable();
                        //   $orderData['guarded'] = $order->getGuarded();
                        //   $orderData['statusLabel'] = $order->statusLabel;  
                        session()->put('order', $orderData);

                        $this->orderRepository->update(['status' => 'processing'], $this->order->id);

                        if ($this->order->canInvoice()) {

                            $this->invoiceRepository->create($this->prepareInvoiceData());
                        }

                        return true;

                    } else {

                        $this->deleteCart();

                        if ($tresponse->getErrors() != null) {

                            $error = $tresponse->getErrors()[0]->getErrorCode() . "  " . $tresponse->getErrors()[0]->getErrorText();

                            return $error;

                        }
                    }
                } else {

                    $this->deleteCart();

                    $tresponse = $savedCardPaymentResponse->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getErrors() != null) {

                        $error = $tresponse->getErrors()[0]->getErrorCode() . "  " . $tresponse->getErrors()[0]->getErrorText();

                        return $error;

                    } else {

                        $error = $savedCardPaymentResponse->getMessages()->getMessage()[0]->getCode() . "  " . $savedCardPaymentResponse->getMessages()->getMessage()[0]->getText();

                        return $error;

                    }
                }
            } else {
                $this->mpauthorizenetcartRepository->deleteWhere([
                    'cart_id' => \Cart::getCart()->id
                ]);

                $error = 'Payment Failed';

                return $error;
            }
        } else {

            if ($savedCardPaymentResponse != null) {

                if ($savedCardPaymentResponse->getMessages()->getResultCode() == "Ok") {

                    log::info('payment');
                    $tresponse = $savedCardPaymentResponse->getTransactionResponse();
                    // dd($this->deleteCart());

                    if ($tresponse != null && $tresponse->getMessages() != null) {

                        // $this->deleteCart();

                        // Cart::deActivateCart();

                        // session()->flash('order', $order);
                        log::info('payment2');


                        $this->orderRepository->update(['status' => 'paid', 'status_id' => 4], request()->input('order_id'));

                        $order = Order::where('id', request()->input('order_id'))->first();
                        // $email = $order->customer_email ?? $order->fbo_email_address;

                        // sandeep add create quickbook invoice code
                        $invoiceId = $order->quickbook_invoice_id;
                        if (!$invoiceId) {
                            $order_id = request()->input('order_id');
                            // $test = app(InvoicesController::class);
                            // $test->createInvoice($order_id);
                            ProcessQuickBooksInvoice::dispatch($order_id);
                        }

                        // sandeep add update payment status in quickbook invoice
                        // $orderNew = Order::where('id', request()->input('order_id'))->first();
                        // $invoiceId = $orderNew->quickbook_invoice_id;

                        // if (!empty($invoiceId) && $invoiceId !== "0") {
                        //     log::info('update payment');
                            $order_id = request()->input('order_id');
                            // $test = new QuickBookController;
                            // $test->updatePaymentInQuickBooks($order_id);
                            UpdateQuickbookPayment::dispatch($order_id);
                        // }

                        $admin_id = null;
                        if (Auth::guard('admin')->check()) {
                            $admin_id = Auth::guard('admin')->user()->id;
                        }

                        log::info('set order status log');
                        log::info('customer id', ['customer id' => request()->input('customerId')]);
                        log::info('admin id', ['admin id' => $admin_id]);


                        DB::table('order_status_log')->insert([
                            'order_id' => request()->input('order_id'),
                            'user_id' => isset($admin_id) ? $admin_id : request()->input('customerId'),
                            'is_admin' => isset($admin_id) ? 1 : 0,
                            'status_id' => 4,
                            'email' => $order->customer_email === null ? $order->fbo_email_address : $order->customer_email,
                        ]);
                        // if ($this->order->canInvoice()) {

                        //     $this->invoiceRepository->create($this->prepareInvoiceData());
                        // }

                        return true;

                    } else {

                        $this->deleteCart();

                        if ($tresponse->getErrors() != null) {

                            $error = $tresponse->getErrors()[0]->getErrorCode() . "  " . $tresponse->getErrors()[0]->getErrorText();

                            return $error;

                        }
                    }
                } else {

                    $this->deleteCart();

                    $tresponse = $savedCardPaymentResponse->getTransactionResponse();

                    if ($tresponse != null && $tresponse->getErrors() != null) {

                        $error = $tresponse->getErrors()[0]->getErrorCode() . "  " . $tresponse->getErrors()[0]->getErrorText();

                        return $error;

                    } else {

                        $error = $savedCardPaymentResponse->getMessages()->getMessage()[0]->getCode() . "  " . $savedCardPaymentResponse->getMessages()->getMessage()[0]->getText();

                        return $error;

                    }
                }
            } else {
                $this->mpauthorizenetcartRepository->deleteWhere([
                    'cart_id' => \Cart::getCart()->id
                ]);

                $error = 'Payment Failed';

                return $error;
            }
        }
        } catch (\Exception $e) {
            log::error('Exception in paymentResponse', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        }
    }

    /**
     * Charge Customer Profile
     *
     * @return array
     */
    function chargeCustomerProfile($decodeUpdatedToken)
    {
    if (session()->has('ADMIN_PAYMENT')) {
        try {
            $order_id = request()->input('order_id');
            $order = Order::where('id', $order_id)->first();
            $invoice = Invoice::select('id')->where('order_id', $order_id)->first();
            // $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
            // $profileToCharge->setCustomerProfileId($decodeUpdatedToken->customerResponse->customerProfileId);
            // $paymentProfile = new AnetAPI\PaymentProfileType();
            // $paymentProfile->setPaymentProfileId($decodeUpdatedToken->customerResponse->paymentProfielId);
            // $profileToCharge->setPaymentProfile($paymentProfile);

            // $transactionRequestType = new AnetAPI\TransactionRequestType();
            // $transactionRequestType->setTransactionType("authCaptureTransaction");
            // $transactionRequestType->setAmount(Cart::getCart()->base_grand_total);
            // $transactionRequestType->setProfile($profileToCharge);

            // $request = new AnetAPI\CreateTransactionRequest();
            // $request->setMerchantAuthentication($this->merchantAuthentication);
            // $request->setRefId($this->refId);
            // $request->setTransactionRequest($transactionRequestType);
            // $controller = new AnetController\CreateTransactionController($request);
            // $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

            $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
            $profileToCharge->setCustomerProfileId($decodeUpdatedToken->customerResponse->customerProfileId);
            $paymentProfile = new AnetAPI\PaymentProfileType();
            $paymentProfile->setPaymentProfileId($decodeUpdatedToken->customerResponse->paymentProfielId);
            $profileToCharge->setPaymentProfile($paymentProfile);

            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($order->base_grand_total);
            $transactionRequestType->setProfile($profileToCharge);

            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setRefId($this->refId);
            $request->setTransactionRequest($transactionRequestType);
            $controller = new AnetController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

            Log::info('Response123:', ['response' => json_encode($response)]);
            $transactionResponse = $response->getTransactionResponse();

            if ($transactionResponse != null && $transactionResponse->getMessages() != null) {
                $transactionId = $transactionResponse->getTransId();
                $this->orderTransactionRepository->create([
                    'transaction_id' => $transactionId,
                    'status' => "paid",
                    'type' => "mpAuthorize",
                    'payment_method' => "mpAuthorize",
                    'order_id' => $order_id,
                    'invoice_id' => $invoice->id,
                    'data' => json_encode([
                        ["paidAmount"],
                        [$order->base_grand_total]
                    ]),
                    'amount' => $order->base_grand_total
                ]);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Payment Error:', ['message' => $e->getMessage()]);
        }

        } else {

            $obj = new \stdClass();
            $obj->refId = 'ref1699511536';
            $obj->getMessages = new \stdClass();
            $obj->getMessages->getResultCode = "Ok";
            $obj->getMessages->message = [
                new \stdClass(),
            ];
            $obj->getMessages->message[0]->code = 'I00001';
            $obj->getMessages->message[0]->text = 'Successful.';
            $obj->sessionToken = null;
            $obj->getTransactionResponse = new AnetAPI\TransactionRequestType();
            $obj->getTransactionResponse->responseCode = '3';
            $obj->getTransactionResponse->rawResponseCode = null;
            // $obj->getTransactionResponse->authCode = '';
            $obj->getTransactionResponse->avsResultCode = 'P';
            $obj->getTransactionResponse->cvvResultCode = '';
            $obj->getTransactionResponse->cavvResultCode = '';
            $obj->getTransactionResponse->transId = '0';
            $obj->getTransactionResponse->refTransID = '';
            $obj->getTransactionResponse->transHash = '';
            $obj->getTransactionResponse->testRequest = '0';
            $obj->getTransactionResponse->accountNumber = 'XXXX1111';
            $obj->getTransactionResponse->accountType = 'Visa';
            // $obj->getTransactionResponse->splitTenderId = null;
            $obj->getTransactionResponse->prePaidCard = null;
            $obj->getTransactionResponse->messages = [new \stdClass()];
            $obj->getTransactionResponse->messages[0]->code = '252';
            $obj->getTransactionResponse->messages[0]->description = "Your order has been received. Thank you for your business!";
            $obj->getTransactionResponse->errors = [new \stdClass()];
            $obj->getTransactionResponse->transHashSha2 = '';
            // $obj->getTransactionResponse->profile = null;
            $obj->getTransactionResponse->networkTransId = null;

            // dd($obj);           
            return $obj;
        }
    }


    function createAnAcceptPaymentTransaction($MpauthorizeNetCardDecode)
    {
        log::info('createAnAcceptPaymentTransaction');
        // dd($MpauthorizeNetCardDecode);

    if (session()->has('ADMIN_PAYMENT')) {
        try {

            // Create the payment object for a payment nonce
            // $opaqueData = new AnetAPI\OpaqueDataType();
            // $opaqueData->setDataDescriptor($MpauthorizeNetCardDecode->opaqueData->dataDescriptor);
            // $opaqueData->setDataValue($MpauthorizeNetCardDecode->opaqueData->dataValue);


            // // Add the payment data to a paymentType object
            // $paymentOne = new AnetAPI\PaymentType();
            // $paymentOne->setOpaqueData($opaqueData);

            // $billingAddress = Cart::getCart()->billing_address;

            // // Set the customer's Bill To address
            // $customerAddress = new AnetAPI\CustomerAddressType();
            // $customerAddress->setFirstName($billingAddress->first_name);
            // $customerAddress->setLastName($billingAddress->last_name);
            // $customerAddress->setAddress($billingAddress->address1);
            // $customerAddress->setCity($billingAddress->city);
            // $customerAddress->setState($billingAddress->state);
            // $customerAddress->setZip($billingAddress->postcode);
            // $customerAddress->setCountry($billingAddress->country);
            // $customerAddress->setPhoneNumber($billingAddress->phone);

            // // Set the customer's identifying information
            // $customerData = new AnetAPI\CustomerDataType();
            // $customerData->setId("C_" . time());
            // $customerData->setEmail($billingAddress->email);

            // // Add values for transaction settings
            // $duplicateWindowSetting = new AnetAPI\SettingType();
            // $duplicateWindowSetting->setSettingName("duplicateWindow");
            // $duplicateWindowSetting->setSettingValue("60");

            // // Create a TransactionRequestType object and add the previous objects to it
            // $transactionRequestType = new AnetAPI\TransactionRequestType();
            // $transactionRequestType->setTransactionType("authCaptureTransaction");
            // $transactionRequestType->setAmount(Cart::getCart()->base_grand_total);
            // $transactionRequestType->setPayment($paymentOne);
            // $transactionRequestType->setBillTo($customerAddress);
            // $transactionRequestType->setCustomer($customerData);
            // $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

            // // Assemble the complete transaction request
            // $request = new AnetAPI\CreateTransactionRequest();
            // $request->setMerchantAuthentication($this->merchantAuthentication);
            // $request->setRefId($this->refId);
            // $request->setTransactionRequest($transactionRequestType);

            // // Create the controller and get the response
            // $controller = new AnetController\CreateTransactionController($request);
            // $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

            // return $response;
            $order_id = request()->input('order_id');
            $invoice = Invoice::select('id')->where('order_id', $order_id)->first();

            $order = Order::where('id', $order_id)->first();
            // dd($order->base_grand_total);
            // dd($order);
            $opaqueData = new AnetAPI\OpaqueDataType();
            $opaqueData->setDataDescriptor($MpauthorizeNetCardDecode->opaqueData->dataDescriptor);
            $opaqueData->setDataValue($MpauthorizeNetCardDecode->opaqueData->dataValue);
            log::info('opaqueData',['opaqueData'=>$opaqueData]);
            // dd($order->billing_address);

            // Add the payment data to a paymentType object
            $paymentOne = new AnetAPI\PaymentType();
            $paymentOne->setOpaqueData($opaqueData);
            log::info('paymentOne',['paymentOne'=>$paymentOne]);

            // $billingAddress = Cart::getCart()->billing_address;
            $billingAddress = $order->billing_address;
            // dd($billingAddress);

            // Set the customer's Bill To address
            $customerAddress = new AnetAPI\CustomerAddressType();
            $customerAddress->setFirstName($billingAddress->first_name);
            $customerAddress->setLastName($billingAddress->last_name);
            $customerAddress->setAddress($billingAddress->address1);
            $customerAddress->setCity($billingAddress->city);
            $customerAddress->setState($billingAddress->state);
            $customerAddress->setZip($billingAddress->postcode);
            $customerAddress->setCountry($billingAddress->country);
            $customerAddress->setPhoneNumber($billingAddress->phone);

            log::info('customerAddress',['customerAddress'=>$customerAddress]);

            // Set the customer's identifying information
            $customerData = new AnetAPI\CustomerDataType();
            $customerData->setId("C_" . time());

            log::info('customerData',['customerData'=>$customerData]);

            // $customerData->setEmail($billingAddress->email);

            // Add values for transaction settings
            $duplicateWindowSetting = new AnetAPI\SettingType();
            $duplicateWindowSetting->setSettingName("duplicateWindow");
            $duplicateWindowSetting->setSettingValue("60");

            log::info('duplicateWindowSetting',['duplicateWindowSetting'=>$duplicateWindowSetting]);


            // Create an OrderType object to set invoice details
            $orderType = new AnetAPI\OrderType();
            $orderType->setInvoiceNumber("INV_" . $invoice->id);
            $orderType->setDescription("Order #" . $order_id);

            log::info('orderType',['orderType'=>$orderType]);


            // Create a TransactionRequestType object and add the previous objects to it
            $transactionRequestType = new AnetAPI\TransactionRequestType();
            $transactionRequestType->setTransactionType("authCaptureTransaction");
            $transactionRequestType->setAmount($order->base_grand_total);
            $transactionRequestType->setPayment($paymentOne);
            $transactionRequestType->setBillTo($customerAddress);
            $transactionRequestType->setCustomer($customerData);
            $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);
            $transactionRequestType->setOrder($orderType);


            log::info('transactionRequestType',['transactionRequestType'=>$transactionRequestType]);


            // Assemble the complete transaction request
            $request = new AnetAPI\CreateTransactionRequest();
            $request->setMerchantAuthentication($this->merchantAuthentication);
            $request->setRefId($this->refId);
            $request->setTransactionRequest($transactionRequestType);
            Log::info('Response123:', ['response' => json_encode($request)]);
            // Create the controller and get the response
            $controller = new AnetController\CreateTransactionController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::PRODUCTION);

            log::info('response',['response'=>$response]);
            $responseArray = json_decode(json_encode($response), true);
            $messages = $responseArray['messages'] ?? [];
            if (
                isset($messages['resultCode']) &&
                $messages['resultCode'] === 'Error'
            ) {
                $errorMessage = $messages['message'][0]['text'] ?? 'Unknown error';
                log::error('Authorize.net error: ' . $errorMessage);

                if(isset($order)){
                $orderData = [
                    'order_id' => $order['increment_id'],
                    'status' => $order['status'],
                    'Name' => (!empty($order['customer_first_name']) && !empty($order['customer_last_name']))
                    ? $order['customer_first_name'] . ' ' . $order['customer_last_name']
                    : $order['fbo_full_name'],
                    'Email' => !empty($order['customer_email']) ? $order['customer_email'] : $order['fbo_email_address'],
                    'Mail_Heading' => "Payment Processing Failure",
                    'Mail_Subject' => "Payment Failed Notification",
                    'error_message' => $errorMessage,
                ];
                // Dispatch the job to send email
                SendOrderFailedEmail::dispatch($orderData);
            }
        }

            // Log::info('Response123:', ['response' => json_encode($response)]);
            $transactionResponse = $response->getTransactionResponse();

            log::info('transactionResponse',['transactionResponse'=>$transactionResponse]);

            if ($transactionResponse != null && $transactionResponse->getMessages() != null) {
                $transactionId = $transactionResponse->getTransId();

                $this->orderTransactionRepository->create([
                    'transaction_id' => $transactionId,
                    'status' => "paid",
                    'type' => "mpAuthorize",
                    'payment_method' => "mpAuthorize",
                    'order_id' => $order_id,
                    'invoice_id' => $invoice->id,
                    'data' => json_encode([
                        ["paidAmount"],
                        [$order->base_grand_total]
                    ]),
                    'amount' => $order->base_grand_total
                ]);

            }

            return $response;
        }catch (\Exception $e) {
            Log::error('Authorize.Net Transaction Failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }
    }else {

            $obj = new \stdClass();
            $obj->refId = 'ref1699511536';
            $obj->getMessages = new \stdClass();
            $obj->getMessages->getResultCode = "Ok";
            $obj->getMessages->message = [
                new \stdClass(),
            ];
            $obj->getMessages->message[0]->code = 'I00001';
            $obj->getMessages->message[0]->text = 'Successful.';
            $obj->sessionToken = null;
            $obj->getTransactionResponse = new AnetAPI\TransactionRequestType();
            $obj->getTransactionResponse->responseCode = '3';
            $obj->getTransactionResponse->rawResponseCode = null;
            // $obj->getTransactionResponse->authCode = '';
            $obj->getTransactionResponse->avsResultCode = 'P';
            $obj->getTransactionResponse->cvvResultCode = '';
            $obj->getTransactionResponse->cavvResultCode = '';
            $obj->getTransactionResponse->transId = '0';
            $obj->getTransactionResponse->refTransID = '';
            $obj->getTransactionResponse->transHash = '';
            $obj->getTransactionResponse->testRequest = '0';
            $obj->getTransactionResponse->accountNumber = 'XXXX1111';
            $obj->getTransactionResponse->accountType = 'Visa';
            // $obj->getTransactionResponse->splitTenderId = null;
            $obj->getTransactionResponse->prePaidCard = null;
            $obj->getTransactionResponse->messages = [new \stdClass()];
            $obj->getTransactionResponse->messages[0]->code = '252';
            $obj->getTransactionResponse->messages[0]->description = "Your order has been received. Thank you for your business!";
            $obj->getTransactionResponse->errors = [new \stdClass()];
            $obj->getTransactionResponse->transHashSha2 = '';
            // $obj->getTransactionResponse->profile = null;
            $obj->getTransactionResponse->networkTransId = null;
            // dd($obj);
            return $obj;
        }

    }

    /**
     * Prepares order's invoice data for creation
     *
     * @return array
     */
    protected function prepareInvoiceData()
    {
        $invoiceData = [
            "order_id" => $this->order->id
        ];
        foreach ($this->order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }
        return $invoiceData;
    }

    public function deleteCart()
    {
        $this->mpauthorizenetcartRepository->deleteWhere([
            'cart_id' => \Cart::getCart()->id
        ]);
    }

    // function chargeCustomerProfile($profileid, $paymentprofileid, $amount)
    // {
    //     $profileToCharge = new AnetAPI\CustomerProfilePaymentType();
    //     $profileToCharge->setCustomerProfileId($decodeUpdatedToken->customerResponse->customerProfileId);
    //     $paymentProfile = new AnetAPI\PaymentProfileType();
    //     $paymentProfile->setPaymentProfileId($decodeUpdatedToken->customerResponse->paymentProfielId);
    //     $profileToCharge->setPaymentProfile($paymentProfile);

    //     $transactionRequestType = new AnetAPI\TransactionRequestType();
    //     $transactionRequestType->setTransactionType("authCaptureTransaction");
    //     $transactionRequestType->setAmount(Cart::getCart()->base_grand_total);
    //     $transactionRequestType->setProfile($profileToCharge);

    //     $request = new AnetAPI\CreateTransactionRequest();
    //     $request->setMerchantAuthentication($this->merchantAuthentication);
    //     $request->setRefId($this->refId);
    //     $request->setTransactionRequest($transactionRequestType);
    //     $controller = new AnetController\CreateTransactionController($request);
    //     $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);

    //     return $response;
    // }

}
