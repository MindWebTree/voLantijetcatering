<?php

namespace ACME\paymentProfile\Http\Controllers\Admin;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use QuickBooksOnline\API\DataService\DataService;
use QuickBooksOnline\API\Facades\Invoice;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use ACME\paymentProfile\Models\agentHandler;



class QuickBookController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('paymentprofile::admin.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('paymentprofile::admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\View\View
     */
    public function edit(int $id)
    {
        return view('paymentprofile::admin.edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(int $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        
    }



    // function to get quickbook account details
    private function getQuickBooksConfig()
    {
        return [
            'client_id' => config('app.client_id'),
            'client_secret' => config('app.client_secret'),
            'redirect_uri' => config('app.redirect_uri'),
            'baseUrl' => config('app.baseUrl'),
            'company_id' => config('app.company_id'),
            'scope' => config('app.scope'),
        ];
    }



// function to update invoice status
public function checkInvoiceStatus(Request $request)
{
  log::info('check invoice status function');
    $data = $request->all();
    $statusId = "4"; 
    $status = "paid";

    log::info($data);

    $configData = $this->getQuickBooksConfig();

    $tokenData = DB::table('quickbook_tokens')->where('client_id', $configData['client_id'])->first();
    if (!$tokenData) {
        return response()->json(['error' => 'No tokens found. Please connect again.'], 401);
    }

    $accessToken = $tokenData->access_token;
    $expiresAt = strtotime($tokenData->access_token_expires_at);
    $refreshToken = $tokenData->refresh_token;
    $refreshTokenExpiresAt = strtotime($tokenData->refresh_token_expires_at);

    if (empty($refreshToken) || (time() >= $refreshTokenExpiresAt)) {
        return $this->connect();
    }

    if (empty($accessToken) || (time() >= $expiresAt)) {
        $tokens = $this->refreshAccessToken($configData['client_id'], $configData['client_secret'], $refreshToken, $configData['company_id']);
        if ($tokens) {
            $accessToken = $tokens['access_token'];
        } else {
            return response()->json(['error' => 'Failed to refresh access token'], 401);
        }
    }

    if (isset($data['eventNotifications']) && !empty($data['eventNotifications'])) {
        foreach ($data['eventNotifications'] as $event) {
            if (isset($event['dataChangeEvent']['entities']) && !empty($event['dataChangeEvent']['entities'])) {
                $entity = $event['dataChangeEvent']['entities'][0];

                if (isset($entity['id'], $entity['name'])) {
                    $eventId = $entity['id'];
                    $eventName = $entity['name'];

                    if ($eventName === "Payment") {
                        $paymentDetails = $this->getPaymentDetails($eventId, $configData['company_id'], $accessToken);
                        log::info('paymnetDetail');
                        log::info($paymentDetails);

                        if (!empty($paymentDetails['Payment']['Line'])) {
                            $lines = is_array($paymentDetails['Payment']['Line']) && isset($paymentDetails['Payment']['Line']['Amount'])
                                ? [$paymentDetails['Payment']['Line']]
                                : $paymentDetails['Payment']['Line'];

                            foreach ($lines as $line) {
                                $invoiceId = $line['LinkedTxn']['TxnId'] ?? null;
                                $txnOpenBalance = (float)($line['LineEx']['NameValue'][1]['Value'] ?? 0);
                                $paidAmount = (float)($line['Amount'] ?? 0);

                                if ($paidAmount >= $txnOpenBalance) {
                                    $order = DB::table('orders')->where('quickbook_invoice_id', $invoiceId)->first();

                                    if ($order) {
                                        DB::table('orders')->where('quickbook_invoice_id', $invoiceId)->update([
                                            'status_id' => $statusId,
                                            'status' => $status,
                                        ]);

                                        $existingStatusLog = DB::table('order_status_log')
                                        ->where('order_id', $order->id)
                                        ->where('status_id', $statusId)
                                        ->first();

                                        if (!$existingStatusLog) {
                                        // Log the status update
                                        DB::table('order_status_log')->insert([
                                            'order_id' => $order->id,
                                            'status_id' => $statusId,
                                            'user_id' => '1',
                                            'is_admin' => '1',
                                            'email' => $order->customer_email ?? $order->fbo_email_address,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    }

                                    } else {
                                        
                                    }
                                }
                            }
                        }
                    } else {
                        $invoiceDetails = $this->getInvoiceDetails($eventId, $configData['company_id'], $accessToken);
                        log::info($invoiceDetails);

                        if (isset($invoiceDetails['Invoice']['Id'], $invoiceDetails['Invoice']['Balance'])) {
                            $invoiceId = $invoiceDetails['Invoice']['Id'];  
                            $balance = $invoiceDetails['Invoice']['Balance'];

                            if ($balance == "0") {
                                $order = DB::table('orders')->where('quickbook_invoice_id', $invoiceId)->first();

                                if ($order) {
                                    DB::table('orders')->where('quickbook_invoice_id', $invoiceId)->update([
                                        'status_id' => $statusId,
                                        'status' => $status,
                                    ]);

                                      $existingStatusLog = DB::table('order_status_log')
                                        ->where('order_id', $order->id)
                                        ->where('status_id', $statusId)
                                        ->first();

                                        if (!$existingStatusLog) {
                                        // Log the status update
                                        DB::table('order_status_log')->insert([
                                            'order_id' => $order->id,
                                            'status_id' => $statusId,
                                            'user_id' => '1',
                                            'is_admin' => '1',
                                            'email' => $order->customer_email ?? $order->fbo_email_address,
                                            'created_at' => now(),
                                            'updated_at' => now(),
                                        ]);
                                    }
                                } else {
                                    log::error('Order not found for invoice ID: ' . $invoiceId);
                                }
                            }
                        }
                    }
                } else {
                    log::error('Entity missing "id" or "name".');
                }
            } else {
                log::error('No entities found in the dataChangeEvent.');
            }
        }
    }

    return response()->json(['status' => 'success']);
}

//   function to get paymnent detail
    private function getPaymentDetails($paymentId, $companyId, $accessToken)
    {
        $url = "https://quickbooks.api.intuit.com/v3/company/{$companyId}/payment/{$paymentId}";

        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get($url);

        $createResponseData = json_decode(json_encode(simplexml_load_string($response->body())), true);

        if ($response->successful()) {
            return $createResponseData;
        } else {
            Log::error("Failed to fetch payment details: " . $response->body());
            return null;
        }
    }



    // funtion to get invoice detail
    private function getInvoiceDetails($invoiceId, $companyId, $accessToken){

        $url = "https://quickbooks.api.intuit.com/v3/company/{$companyId}/invoice/{$invoiceId}";
        
        $response = Http::withToken($accessToken)
            ->withHeaders(['Content-Type' => 'application/json'])
            ->get($url);

            $createResponseData = json_decode(json_encode(simplexml_load_string($response->body())), true);

            if ($response->successful()) {
                return $createResponseData;
            } else {
                Log::error("Failed to fetch invoice details: " . $response->body());
                return null;
            }

    }



    // function to update paymnet status in quickbook
public function updatePaymentInQuickBooks($orderId)
    {

        log::info('update payment');
        $configData = $this->getQuickBooksConfig();

        $tokenData = DB::table('quickbook_tokens')->where('client_id', $configData['client_id'])->first();
        if (!$tokenData) {
            return response()->json(['error' => 'No tokens found. Please connect again.'], 401);
        }

        $accessToken = $tokenData->access_token;
        $expiresAt = strtotime($tokenData->access_token_expires_at);
        $refreshToken = $tokenData->refresh_token;
        $refreshTokenExpiresAt = strtotime($tokenData->refresh_token_expires_at);

        if (empty($refreshToken) || (time() >= $refreshTokenExpiresAt)) {
            return $this->connect();
        }

        if (empty($accessToken) || (time() >= $expiresAt)) {
            $tokens = $this->refreshAccessToken($configData['client_id'], $configData['client_secret'], $refreshToken, $configData['company_id']);
            if ($tokens) {
                $accessToken = $tokens['access_token'];
            } else {
                return response()->json(['error' => 'Failed to refresh access token'], 401);
            }
        }

        $agent = agentHandler::where('order_id', $orderId)->first();
        
        $orderDetails = DB::table('orders')
            ->join('customers', 'orders.customer_id', '=', 'customers.id')
            ->where('orders.id', $orderId)
            ->select('orders.id as order_id', 'orders.customer_id as order_customer_id', 'orders.sub_total as totalAmount', 'orders.quickbook_invoice_id', 'customers.id as customer_id', 'customers.quickbook_customer_id','orders.tax_amount as tax_amount')
            ->get();

        $companyId = $configData['company_id'];
        $customerRefId = $orderDetails['0']->quickbook_customer_id;
        $invoiceId = $orderDetails['0']->quickbook_invoice_id;
        $totalAmount = $orderDetails['0']->totalAmount + $orderDetails['0']->tax_amount;
        if (isset($agent->Handling_charges) && is_numeric($agent->Handling_charges)) {
            $totalAmount += $agent->Handling_charges;
        }

        log::info('total amount',$totalAmount);

        $url = "https://quickbooks.api.intuit.com/v3/company/{$companyId}/payment";

        
            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    "CustomerRef" => [
                        "value" => $customerRefId
                    ],
                    "TotalAmt" => $totalAmount,
                    "Line" => [
                        [
                            "Amount" => $totalAmount,
                            "LinkedTxn" => [
                                [
                                    "TxnId" => $invoiceId,
                                    "TxnType" => "Invoice"
                                ]
                            ]
                        ]   
                    ],
                ]);
                
                if ($response->successful()) {
                    return [
                        'status' => 'success',
                        'message' => 'Payment updated in QuickBooks successfully.'
                    ];
                }
    }
    
            // function to generate refreshAccessToken token
  private function refreshAccessToken($client_id, $client_secret, $refreshToken, $companyId)
    {
        $tokenUrl = "https://oauth.platform.intuit.com/oauth2/v1/tokens/bearer";

        try {
            $client = new Client();
            $response = $client->post($tokenUrl, [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode($client_id . ':' . $client_secret),
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refreshToken,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            DB::table('quickbook_tokens')->updateOrInsert(
                ['client_id' => $client_id],
                [
                    'access_token' => $data['access_token'],
                    'refresh_token' => $data['refresh_token'],
                    'company_id' => $companyId,
                    'access_token_expires_at' => date('Y-m-d H:i:s', time() + $data['expires_in']),
                    'refresh_token_expires_at' => date('Y-m-d H:i:s', time() + $data['x_refresh_token_expires_in']),
                ]
            );
            return [
                'access_token' => $data['access_token'],
                'expires_in' => $data['expires_in'],
            ];
        } catch (\Exception $e) {
            file_put_contents('debug.log', 'Error refreshing access token: ' . $e->getMessage() . "\n");
            return null;
        }
    }
}
