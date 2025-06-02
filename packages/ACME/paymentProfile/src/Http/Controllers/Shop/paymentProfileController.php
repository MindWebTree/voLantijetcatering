<?php

namespace ACME\paymentProfile\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Webkul\Sales\Models\Order;
use Illuminate\Support\Facades\View;
use ACME\paymentProfile\Models\{agentHandler, CustomerInquery};
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;

use App\Pdf\PdfWithHeaderFooter;
use Dompdf\Dompdf;
use Dompdf\Options;
use Webkul\Sales\Models\OrderItem;
use Illuminate\Support\Facades\Storage;
use Barryvdh\Debugbar\Facades\Debugbar;

// use App\packages\ACME\paymentProfile\src\Models\CustomerInquery;

class paymentProfileController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view($this->_config['view']);
    }

    // Invoice front
    public function view(Request $request)
    {
        $data = $request->all();
        $order_detail = json_decode(json_encode($data));

        return view($this->_config['view'], compact('order_detail'));
    }
    

    public function invoice_detail(Request $request)
    {

        $orderid = $request->query('orderid');
        $customerid = $request->query('customerid');

        $detail = $request->all();
        $order = Order::where('id', $detail['orderid'])->first();

        if (
            ($order->customer_email !== null && $detail['email'] === $order->customer_email && $detail['tail_number'] === $order->fbo_tail_number) ||
            ($order->customer_email === null && $detail['email'] === $order->fbo_email_address && $detail['tail_number'] === $order->fbo_tail_number)
        ) {
            $agent = agentHandler::where('order_id', $detail['orderid'])->first();

            // return view($this->_config['view'], compact('order','agent'));
            Session::put('invoice-form-fill', true);
            Session::put('invoice-order-id', $orderid);
            Session::put('invoice-customer-id', $customerid);

            return redirect()->route('invoice.detail', ['orderid' => $orderid, 'customerid' => $customerid]);

        } else {
            session()->flash('warning', 'Wrong email or tail number');
            return redirect()->back();
        }

    }

    public function success_message()
    {
        session()->flash('success', 'Payment collected successfully');
        return redirect()->back();
    }
    public function error_message()
    {
        session()->flash('warning', 'Unsuccessful! Payment not collected');
        return redirect()->back();
    }

    public function payment_details(Request $request)
    {

        $form_fill = Session::get('invoice-form-fill');
        $form_fill_orderId = Session::get('invoice-order-id');
        $form_fill_customerId = Session::get('invoice-customer-id');

        if (!isset($request->orderid) || !isset($request->customerid)) {
            return redirect('/');
        }

        if (!$form_fill || $form_fill_orderId != $request->orderid || $form_fill_customerId != $request->customerid) {
            return redirect()->route('order-invoice-view', ['orderid' => $request->orderid, 'customerid' => $request->customerid]);
        }

        $order = Order::where('id', $request->orderid)->first();
        $agent = agentHandler::where('order_id', $request->orderid)->first();

        return view('shop::sales.invoice-detail', compact('order', 'agent'));

    }


    // sandeep || show inquery page 
    public function showInquery()
    {
        Debugbar::info('Debugbar test');
        return view('shop::products.customization');
    }

    // sandeep || store inquery
    public function storeinquery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:25',
            'lname' => 'required|string|max:25',    
            'email' => 'required|email',
            'mobile_number' => 'required|regex:/^\(\d{3}\) \d{3}-\d{4}$/',
            'message' => 'required',
            'g-recaptcha-response' => 'required|captcha',
            'uploadfile.*' => 'required|mimes:doc,docx,xls,xlsx,pdf|max:2048',
        ], [
            'uploadfile.*.mimes' => 'File must be a type: doc, docx, xls, xlsx, pdf.',
            'uploadfile.*.max' => 'File may not be greater than 2048 kilobytes.',
        ]);

        // for normal form
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $customers = new CustomerInquery();
        $customers->fname = $request->fname;
        $customers->lname = $request->lname;
        $customers->email = $request->email;
        $customers->mobile_number = $request->mobile_number;
        $customers->message = $request->message;
        $customers->save();

        $id = $customers->id;



        if ($request->hasFile('uploadfile')) {
            foreach ($request->file('uploadfile') as $key => $file) {
                if ($file->isValid()) {
                    $fileName = $id . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
                    $filePath = $file->storeAs('customerinquery', $fileName);
                } else {
                    return redirect()->back()->with('error', 'One or more files could not be uploaded.');
                }
            }
        } else {
            return redirect()->back()->with('error', 'No files uploaded.');
        }

        // Return  thankyou page
        return redirect(url('/thankyou?' . $id));
        // return redirect()->route('inquery.thankyou_page', ['id' => $id]);

    }

    //   sandeep || return thankyou page with inquery data 
    public function inquery_thankyou_page(request $request)
    {

        $id = intval($request->getQueryString());

        $inqueryData = CustomerInquery::find($id);

        $directory = 'customerinquery/';

        //    dd($directory);
        try {
            // Get all files from the directory
            $files = Storage::files($directory);
            // dd($files);
            $selectFiles = [];
            $prefix = $inqueryData->id . '_';

            foreach ($files as $file) {
                $fileName = basename($file);
                if (strpos($fileName, $prefix) === 0) {
                    $selectFiles[] = $file;
                }
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching files: ' . $e->getMessage());
        }

        return view('shop::products.inquery-thankyou-page', compact('inqueryData', 'selectFiles'));
    }


    public function downloadfile($file)
    {
        $filePath = storage_path('app/public/customerinquery/' . $file);

        // Check if file exists
        if (!Storage::disk('public')->exists('customerinquery/' . $file)) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Generate download response
        return response()->download($filePath);
    }


}
