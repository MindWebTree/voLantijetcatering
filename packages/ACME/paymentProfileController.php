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
        if (!isset($request->orderid) || !isset($request->customerid)) {
            return redirect('/');
        }
        if (!$form_fill) {
            return redirect()->route('order-invoice-view', ['orderid' => $request->orderid, 'customerid' => $request->customerid]);
        }

        $order = Order::where('id', $request->orderid)->first();
        $agent = agentHandler::where('order_id', $request->orderid)->first();

        return view('shop::sales.invoice-detail', compact('order', 'agent'));

    }


    public function showInquery(){
        return view('shop::products.customization');
    }

    public function storeinquery(Request $request)
    {
       // dd($request->all()); // Check all request data
// dd($request->files->all()); // Check files specifically

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:25',
            'email' => 'required|email',
            'mobile_number' => 'required|min:10|max:12',
            'message' => 'required',
            'uploadfile.*' => 'required|mimes:doc,docx,xls,xlsx,pdf|max:2048',
        ], [
            'uploadfile.*.mimes' => 'File must be a type: doc, docx, xls, xlsx, pdf.',
            'uploadfile.*.max' => 'File may not be greater than 2048 kilobytes.',
        ]);

        // for normal form
        if ($validator->fails()) {
            // Redirect back with input and validation errors
            return redirect()->back()
                             ->withErrors($validator)
                             ->withInput();
        }

        // ajax code
        // if ($validator->fails()) {
        //     $errors = $validator->errors()->all();
        //     $uniqueErrors = array_unique($errors);

        //     return response()->json(['errors' => $uniqueErrors], 422);
        // }

        // $fileCount = count($request->file('uploadfile'));
        // if ($fileCount > 5) {
        //     return response()->json(['error' => 'You can only upload a maximum of 5 files'], 422);
        // }

        $customers = new CustomerInquery();
        $customers->name = $request->name;
        $customers->email = $request->email;
        $customers->mobile_number = $request->mobile_number;
        $customers->message = $request->message;
        $customers->save();

        $id = $customers->id;
 
       
// sandeep form normal
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

    // Return a success message
    return redirect()->back()->with('success', 'Thank you for contacting us. One of our sales reps will get in touch with you soon.');
        //   alax code
        // if ($request->hasFile('uploadfile')) {
        //     foreach ($request->file('uploadfile') as $key => $file) {
        //         $fileName = $id . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
        //         $filePath = $file->storeAs('customerinquery', $fileName);
        //     }
        // } else {
        //     return response()->json(['error' => 'Files not uploaded'], 422);
        // }

        // return response()->json(['success' => 'Thank you for contacting us. One of our sales rep will get in touch with you soon.'], 200);
    }


}
