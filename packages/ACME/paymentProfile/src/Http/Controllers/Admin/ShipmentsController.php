<?php

namespace ACME\paymentProfile\Http\Controllers\Admin;

use ACME\paymentProfile\Mail\OrderDeliver;
use Exception;
use Illuminate\Support\Facades\DB;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\ShipmentRepository;
use Webkul\Admin\DataGrids\OrderShipmentsDataGrid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Webkul\Admin\DataGrids\DeliveryOrderShipmentsDataGrid;
use Webkul\Sales\Models\Order;
use Webkul\User\Models\Admin;
use Illuminate\Support\Facades\Storage;
use ACME\paymentProfile\Jobs\OrderDeliverJob;


class ShipmentsController extends Controller
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
     * @param  \Webkul\Sales\Repositories\OrderItemRepository  $orderItemRepository
     * @param  \Webkul\Sales\Repositories\ShipmentRepository   $shipmentRepository
     * @return void
     */
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
        protected ShipmentRepository $shipmentRepository
    ) {
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
            // return app(OrderShipmentsDataGrid::class)->toJson();
            if (auth()->guard('admin') && auth()->guard('admin')->user()->role_id == 2) {
                return app(DeliveryOrderShipmentsDataGrid::class)->toJson();
            } else {
                return app(OrderShipmentsDataGrid::class)->toJson();
            }
        }

        return view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  int  $orderId
     * @return \Illuminate\View\View
     */
    public function create($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);
        $delivery_partners = Admin::where('role_id', 2)->get();
        if (!$order->channel || !$order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.creation-error'));

            return redirect()->back();
        }

        return view($this->_config['view'], compact('order', 'delivery_partners'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\Response
     */
    public function store($orderId)
    {
        $order = $this->orderRepository->findOrFail($orderId);

        if (!$order->canShip()) {
            session()->flash('error', trans('admin::app.sales.shipments.order-error'));

            return redirect()->back();
        }

        $this->validate(request(), [
            'shipment.source' => 'required',
            'shipment.items.*.*' => 'required|numeric|min:0',
        ]);

        $data = request()->all();
        // dd($data['delivery_partner']);
        if (!$this->isInventoryValidate($data)) {
            session()->flash('error', trans('admin::app.sales.shipments.quantity-invalid'));

            return redirect()->back();
        }


        $appNotifications = DB::table('app_notifications')->insert([
            'customer_id' => $order->customer_id,
            'order_id' => $orderId,
            'message' => "Order Shipped",
            'is_read' => 0
        ]);


        $admin_id = Auth::guard('admin')->user()->id;
        // dd($admin_id );
        DB::table('order_status_log')
            ->insert([
                'order_id' => $orderId,
                'user_id' => $admin_id,
                'is_admin' => 1,
                'status_id' => 8,
                'email' => $order->customer_email === null ? $order->fbo_email_address : $order->customer_email,
            ]);


        $this->shipmentRepository->create(array_merge($data, [
            'order_id' => $orderId,
        ]));

        Order::where('id', $orderId)->update([
            'status_id' => 8,
            'status' => 'shipped'
        ]);

        session()->flash('success', trans('admin::app.sales.shipments.create-success'));

        return redirect()->route($this->_config['redirect'], $orderId);
    }

    /**
     * Checks if requested quantity available or not.
     *
     * @param  array  $data
     * @return bool
     */
    public function isInventoryValidate(&$data)
    {
        if (!isset($data['shipment']['items'])) {
            return;
        }

        $valid = false;

        $inventorySourceId = $data['shipment']['source'];

        foreach ($data['shipment']['items'] as $itemId => $inventorySource) {
            $qty = $inventorySource[$inventorySourceId];

            if ((int) $qty) {
                $orderItem = $this->orderItemRepository->find($itemId);

                if ($orderItem->qty_to_ship < $qty) {
                    return false;
                }

                if ($orderItem->getTypeInstance()->isComposite()) {
                    foreach ($orderItem->children as $child) {
                        if (!$child->qty_ordered) {
                            continue;
                        }

                        $finalQty = ($child->qty_ordered / $orderItem->qty_ordered) * $qty;

                        $availableQty = $child->product->inventories()
                            ->where('inventory_source_id', $inventorySourceId)
                            ->sum('qty');

                        if (
                            $child->qty_to_ship < $finalQty
                            || $availableQty < $finalQty
                        ) {
                            return false;
                        }
                    }
                } else {
                    $availableQty = $orderItem->product->inventories()
                        ->where('inventory_source_id', $inventorySourceId)
                        ->sum('qty');

                    if (
                        $orderItem->qty_to_ship < $qty
                        || $availableQty < $qty
                    ) {
                        return false;
                    }
                }

                $valid = true;
            } else {
                unset($data['shipment']['items'][$itemId]);
            }
        }

        return $valid;
    }

    /**
     * Show the view for the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        $shipment = $this->shipmentRepository->findOrFail($id);
        $order = $this->orderRepository->findOrFail($shipment->order_id);

        //need check here
        $deliver_partners = Admin::where('role_id', 2)->get();
        $delivery_images = DB::table('shipment_attachment')->select('attachment')->where('shipment_id', $id)->get();
// dd($delivery_image);
        return view($this->_config['view'], compact('shipment', 'deliver_partners', 'delivery_images','order'));
    }






    public function delivery(Request $request)
    {
        // dd('dfdfb');
        $rules = [
            'images' => 'required|array',
            'images.*' => 'file|image|max:5120', // 5MB per image
            'order_id' => 'required|integer|exists:orders,id',
            'shipment_id' => 'required|integer|exists:shipments,id'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $order = Order::findOrFail($request->input('order_id'));

            $destinationPath = 'Delivery_image';
            $uploadedImages = [];

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path($destinationPath), $filename);

                    $uploadedImages[] = $destinationPath . '/' . $filename;
            
                    DB::table('shipment_attachment')->insert([
                        'order_id' => $request->input('order_id'),
                        'shipment_id' => $request->input('shipment_id'),
                        'attachment' => $destinationPath . '/' . $filename,
                    ]);
                }
            }


            $order->update([
                'status' => 'delivered',
                'status_id' => 9 // Assuming you have a constant defined
            ]);


        $appNotifications = DB::table('app_notifications')->insert([
            'customer_id' => $order->customer_id,
            'order_id' => $order->id,
            'message' => "Order Delivered",
            'is_read' => 0
        ]);

            $admin_id = Auth::guard('admin')->user()->id;

            DB::table('order_status_log')->insert([
                'order_id' => $order->id,
                'user_id' => $admin_id,
                'is_admin' => 1,
                'status_id' => 9, // Consider using a constant or config value
                'email' => $order->customer_email ?? $order->fbo_email_address,
            ]);

            $recipientEmail = $order->customer_email ?? $order->fbo_email_address;

            // sandeep add code for send order delivery mail using queue
            try{
                OrderDeliverJob::dispatch($order, $recipientEmail);
            }catch(Exception $e){
               
            }

            DB::commit();

            // session()->flash('success', 'Delivery initiated successfully');
            // return back();
            return response()->json(['success' => 'Delivery initiated successfully', 'uploadedImages' => $uploadedImages]);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'An error occurred: ' . $e->getMessage()]);
        }
    }

    public function update_delivery($id)
    {

        $this->shipmentRepository->where('id', $id)->update([
            'delivery_partner' => request()->input('delivery_partner'),
        ]);
        return redirect()->route('admin.paymentprofile.shipments.view', $id);
    }
}
