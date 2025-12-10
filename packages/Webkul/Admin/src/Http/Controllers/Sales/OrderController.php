<?php

namespace Webkul\Admin\Http\Controllers\Sales;

use ACME\paymentProfile\Models\CustomerInquery;
use Illuminate\Support\Facades\Event;
use Webkul\Admin\DataGrids\OrderDataGrid;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use \Webkul\Sales\Repositories\OrderCommentRepository;
use Webkul\Admin\DataGrids\CustomerInqueryDataGrid;
use Illuminate\Support\Facades\Storage;



class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\OrderCommentRepository  $orderCommentRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderCommentRepository $orderCommentRepository
    )
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
        if (request()->ajax()) {
            return app(OrderDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        return view($this->_config['view'], compact('order'));
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $result = $this->orderRepository->cancel($id);

        if ($result) {
            session()->flash('success', trans('admin::app.sales.orders.cancel-error'));
        } else {
            session()->flash('error', trans('admin::app.sales.orders.create-success'));
        }

        return redirect()->back();
    }

    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comment($id)
    {
        Event::dispatch('sales.order.comment.create.before');

        $comment = $this->orderCommentRepository->create(array_merge(request()->all(), [
            'order_id'          => $id,
            'customer_notified' => request()->has('customer_notified'),
        ]));

        Event::dispatch('sales.order.comment.create.after', $comment);

        session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

        return redirect()->back();
    }

    public function displayInquerys()
    {
        if (request()->ajax()) {
            return app(CustomerInqueryDataGrid::class)->toJson();
        }

        return view($this->_config['view']);
    }

    
     public function viewInquery($id)
    {
        $inquerys = CustomerInquery::find($id);
        $directory = 'customerinquery/';

    //    dd($directory);
        try {
            // Get all files from the directory
            $files = Storage::files($directory);
            // dd($files);
            $selectFiles = [];
            $prefix = $inquerys->id . '_'; // Assuming inquery id is prefixed to each file
    
            foreach ($files as $file) {
                $fileName = basename($file);
                if (strpos($fileName, $prefix) === 0) {
                    $selectFiles[] = $file;
                }
            }

// $url1= "app/public/";
// $url = Storage::url($file);
// dd($url);
            // $url = "http://localhost/volante-cattering/storage/app/public/customerinquery/".$file;
            // dd($url);

            // dd($selectFiles);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error fetching files: ' . $e->getMessage());
        }
    
        return view('admin::sales.customersInquery.view', compact('inquerys', 'selectFiles'));
       
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




    public function destroyInquery($id)
{
    $inquery = CustomerInquery::findOrFail($id);
    try {
        if ($inquery) {
        
            $inquery->delete($id);

            return response()->json(['message' => 'User delete succesfully']);
        }
    } catch (\Exception $e) {}

    return response()->json(['message' => 'delete Failed'], 400);
}      

}

