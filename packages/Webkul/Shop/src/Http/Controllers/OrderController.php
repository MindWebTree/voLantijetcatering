<?php

namespace Webkul\Shop\Http\Controllers;

use ACME\paymentProfile\Models\OrderNotes;
use Webkul\Core\Traits\PDFHandler;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Shop\DataGrids\OrderDataGrid;
use Illuminate\Support\Facades\DB;
use Auth;
use Webkul\Sales\Models\OrderItem;
use ACME\paymentProfile\Models\agentHandler;

class OrderController extends Controller
{
    use PDFHandler;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Sales\Repositories\OrderRepository  $orderRepository
     * @param  \Webkul\Sales\Repositories\InvoiceRepository  $invoiceRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
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

        // sandeep add code
        $agent = agentHandler::where('order_id', $id)->first();

        $customer = auth()->guard('customer')->user();

        $order = $this->orderRepository->findOneWhere([
            'customer_id' => $customer->id,
            'id' => $id,
        ]);
        // dd($order);

        if (!$order) {
            abort(404);
        }

        $order_status_id = [3, 5, 6, 7, 10, 11];
        $status_update = null;
        if ((isset($order->status_id) && $order->status_id == 10) || $order->status_id == 11) {
            $order_status = DB::table('order_status_log')
                ->leftJoin('order_status', 'order_status.id', 'order_status_log.status_id')
                ->where('order_status_log.order_id', $id)
                ->select('order_status_log.*', 'order_status.status')
                ->whereNotIn('status_id', [3, 5])
                ->get();

                // sandeep add status update code
                $status_update = DB::table('order_status_log')
                ->join('order_status', 'order_status.id', 'order_status_log.status_id')
                ->where('order_id', $id)
                ->select('order_status_log.updated_at', 'order_status_log.status_id', 'order_status.status')
                ->whereNotIn('status_id', [3, 5])
                ->get();
        } else {
            $order_status = DB::table('order_status')
                ->whereNotIn('id', $order_status_id)
                ->get();

            $status_update = DB::table('order_status_log')
                ->join('order_status', 'order_status.id', 'order_status_log.status_id')
                ->where('order_id', $id)
                ->select('order_status_log.updated_at', 'order_status_log.status_id', 'order_status.status')
                ->whereNotIn('status_id', [3, 5])
                ->get();
        }

        $admin_notes = OrderNotes::where('order_id', $id)->where('customer_notified', 1)->orderby('id', 'desc')->first();

        $result = $this->createOrderTimeline($order_status, $status_update);
        // dd($result);
        return view($this->_config['view'], compact(['order', 'agent', 'order_status', 'admin_notes', 'status_update', 'result']));
    }

    function createOrderTimeline($order_status, $status_update)
    {
        // sandeep || add code for status update null
        $status_update = $status_update ?? collect();

        // Create a map of status to its order and details in $status_update
        $dynamicOrder = $status_update->pluck('status')->flip()->toArray();
        $dynamicDetails = $status_update->keyBy('status');

        // Sort $order_status based on the order in $status_update
        $sortedStatuses = $order_status->sort(function ($a, $b) use ($dynamicOrder) {
            $aOrder = array_key_exists($a->status, $dynamicOrder) ? $dynamicOrder[$a->status] : PHP_INT_MAX;
            $bOrder = array_key_exists($b->status, $dynamicOrder) ? $dynamicOrder[$b->status] : PHP_INT_MAX;
            return $aOrder - $bOrder;
        });

        // Update the timestamps and add dynamic data
        $result = $sortedStatuses->map(function ($item) use ($dynamicDetails) {
            $updateObject = $dynamicDetails->get($item->status);
            if ($updateObject) {
                $item->updated_at = $updateObject->updated_at;
            } else {
                $item->updated_at = null;
            }
            return $item;
        });

        return $result;
    }

    /**
     * Print and download the for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function printInvoice($id)
    {
        $customer = auth()->guard('customer')->user();

        $invoice = $this->invoiceRepository->findOrFail($id);

        if ($invoice->order->customer_id !== $customer->id) {
            abort(404);
        }

        return $this->downloadPDF(
            view('shop::customers.account.orders.pdf', compact('invoice'))->render(),
            'invoice-' . $invoice->created_at->format('d-m-Y')
        );
    }

    /**
     * Cancel action for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel($id)
    {
        $customer = auth()->guard('customer')->user();

        /* find by order id in customer's order */
        $order = $customer->all_orders()->find($id);

        /* if order id not found then process should be aborted with 404 page */
        if (!$order) {
            abort(404);
        }

        $result = $this->orderRepository->cancel($order);

        if ($result) {
            session()->flash('success', trans('admin::app.response.cancel-success', ['name' => 'Order']));
        } else {
            session()->flash('error', trans('admin::app.response.cancel-error', ['name' => 'Order']));
        }

        return redirect()->back();
    }
}
