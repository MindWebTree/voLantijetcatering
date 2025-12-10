<?php

namespace ACME\paymentProfile\Http\Controllers\Admin;


// use ACME\paymentProfile\DataGrids\OrdersDataGrid;

use ACME\paymentProfile\Mail\OrderAccept;
use ACME\paymentProfile\Mail\OrderCancel;
use ACME\paymentProfile\Mail\OrderReject;
// use ACME\paymentProfile\Mail\OrderNote;
use ACME\paymentProfile\Models\OrderNotes;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Product\Models\ProductInventoryIndex;
use Webkul\Product\Models\ProductOrderedInventory;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;
use Webkul\Sales\Repositories\OrderRepository;
use \Webkul\Sales\Repositories\OrderCommentRepository;
use Webkul\MpAuthorizeNet\Helpers\Helper;
use ACME\paymentProfile\Models\agentHandler;
use DateTime;
use ACME\paymentProfile\Models\packaging;
use ACME\paymentProfile\Models\packaging_meta;
use Webkul\Admin\DataGrids\OrdersDataGrid;
use Webkul\Admin\DataGrids\DeliveryOrdersDatagrid;
use Dompdf\Dompdf;
use Dompdf\Options;

use Picqer\Barcode\BarcodeGeneratorHTML;
use Illuminate\Support\Facades\Storage;
use Webkul\Sales\Repositories\ShipmentRepository;
use Webkul\User\Models\Admin;
use Illuminate\Support\Facades\Log;
use Webkul\Tax\Helpers\Tax;
use Webkul\Tax\Repositories\TaxCategoryRepository;
use Webkul\Shipping\Facades\Shipping;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Checkout\Models\Cart as CartModel;
use Webkul\Checkout\Cart;
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use ACME\paymentProfile\Jobs\ProcessQuickBooksInvoice;
use ACME\paymentProfile\Jobs\OrderNoteJob;
use ACME\paymentProfile\Jobs\OrderRejectJob;
use ACME\paymentProfile\Jobs\OrderCancelJob;
use ACME\paymentProfile\Jobs\OrderAcceptJob;




class OrdersController extends Controller
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
        protected OrderCommentRepository $orderCommentRepository,
        protected ShipmentRepository $shipmentRepository,
        protected Helper $helper
    ) {
        $this->_config = request('_config');
        // $this->helper = $helper;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

        // dd(auth()->guard('admin')->user()->id);
        if (request()->ajax()) {
            // if (auth()->guard('admin') && auth()->guard('admin')->user()->role_id == 2) {
            //     return app(DeliveryOrdersDatagrid::class)->toJson();
            // } else {
            return app(OrdersDataGrid::class)->toJson();
            // }
            // return app(OrdersDataGrid::class)->toJson();

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
        $agent = agentHandler::where('order_id', $id)->first();
        $countries = Db::table('countries')->get();
        $shipment = $this->shipmentRepository->where('order_id', $id)->first();
        $states = Db::table('country_states')->where('country_code', 'US')->get();
        $order = $this->orderRepository->findOrFail($id);
        $deliver_partner = '';
        if ($shipment) {
            $deliver_partner = Admin::where('id', $shipment->delivery_partner)->first();
        }

        $airport_fbo = DB::table('airport_fbo_details')
            ->where('id', $order->airport_fbo_id)
            ->select('id', 'name', 'address', 'airport_id', 'customer_id')->first();
        // dd($airport_fbo);
        return view($this->_config['view'], compact('order', 'countries', 'states', 'agent', 'shipment', 'deliver_partner', 'airport_fbo'));
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


    // /**
    //  * Add comment to the order
    //  *
    //  * @param  int  $id
    //  * @return \Illuminate\Http\Response
    //  */
    // public function comment($id)
    // {
    //     // dd('sdhdfh');
    //     Event::dispatch('sales.order.comment.create.before');

    //     $comment = $this->orderCommentRepository->create(array_merge(request()->all(), [
    //         'order_id' => $id,
    //         'customer_notified' => request()->has('customer_notified'),
    //     ]));

    //     Event::dispatch('sales.order.comment.create.after', $comment);

    //     session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

    //     return redirect()->back();
    // }


    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function comments($id)
    {
        $admin_id = Auth::guard('admin')->user()->id;
        $order = Order::where('id', $id)->first();

        $comment = OrderNotes::create([
            'order_id' => $id,
            'user_id' => $admin_id,
            'is_admin' => 1,
            'notes' => request('comment'),
            'customer_notified' => request()->has('customer_notified'),
        ]);
        DB::table('order_status_log')
            ->insert([
                'order_id' => $id,
                'user_id' => $admin_id,
                'is_admin' => 1,
                'status_id' => 5,
                'email' => $order->customer_email === null ? $order->fbo_email_address : $order->customer_email,
            ]);

        if ($comment->customer_notified) {
            // sandeep send mail using queue
            try {
                OrderNoteJob::dispatch($comment, $order);
            } catch (QueryException $e) {
            }
        }

        session()->flash('success', trans('admin::app.sales.orders.comment-added-success'));

        return redirect()->back();
    }


    /**
     * Get all products
     * 
     */
    public function get_product(Request $request)
    {
        if ($request->ajax()) {
            $products = DB::table('product_flat as pf1')
                ->leftJoin('product_flat as pf2', 'pf1.product_id', '=', 'pf2.parent_id')
                ->leftJoin('product_inventory_indices as pi1', 'pf1.product_id', '=', 'pi1.product_id')
                ->leftJoin('product_inventory_indices as pi2', 'pf2.product_id', '=', 'pi2.product_id')
                ->where('pf1.name', 'like', '%' . $request->name . '%')
                ->whereNull('pf1.parent_id')
                ->select(
                    'pf1.product_id',
                    'pf2.product_id as option_id',
                    'pf1.name',
                    'pf2.name as options',
                    'pf1.description',
                    'pf1.price as parent_price',
                    'pf2.price as child_price',
                    'pf1.type as parent_type',
                    'pf2.type as child_type',
                    'pi1.qty as parent_qty',
                    'pi2.qty as child_qty'
                )
                ->get();

            $finalResult = collect($products)->map(function ($item) {
                return [
                    'product_id' => $item->product_id,
                    'name' => $item->name,
                    'options' => is_null($item->options) ? [] : [$item->options],
                    'description' => $item->description,
                    'price' => $item->parent_type === 'simple' ? $item->parent_price : ($item->child_type === 'simple' ? ($item->child_price ?? 0.0) : 0.0),
                    'qty' => $item->parent_type === 'simple' ? ($item->parent_qty ?? 0) : ($item->child_type === 'simple' ? ($item->child_qty ?? 0) : 0),
                ];
            });

            $mergedProducts = [];

            foreach ($finalResult as $product) {
                $key = $product['name'];
                if (!isset($mergedProducts[$key])) {
                    $mergedProducts[$key] = $product;
                } else {
                    $mergedProducts[$key]['options'] = array_merge(
                        (array) $mergedProducts[$key]['options'],
                        (array) $product['options']
                    );

                    // Handle 'qty' similarly to 'options'
                    $mergedProducts[$key]['qty'] = array_merge(
                        (array) $mergedProducts[$key]['qty'],
                        (array) $product['qty']
                    );
                }
            }

            // Convert associative array back to indexed array
            $mergedProducts = array_values($mergedProducts);

            function allValuesAreZero($array)
            {
                foreach ($array as $value) {
                    if ($value > 0) {
                        return false;
                    }
                }
                return true;
            }

            $output = '';
            if (count($mergedProducts) > 0) {
                foreach ($mergedProducts as $product) {
                    $output .= "<div class='row search_product_list'>"
                        . "<div class='col-1' id='product_checkbox'>"
                        . "<input type='hidden' id='product_qty' value='" . implode(',', (array) $product['qty']) . "' disabled />";
                    // if (!empty($product['options'])) {
                    //     $output .= "<input type='hidden' class='option-quantities' value='" . implode(',', $product['options']) . "' />";
                    // }
                    // dd($product);
                    $options = $product['options'];
                    $quantities = is_array($product['qty']) ? $product['qty'] : [$product['qty']];
                    // foreach($quantities as $proQuantity){
                    // if ($product['qty'] <= 0 || is_array($quantities) && allValuesAreZero($quantities)) {
                    //     $output .= "<input type='checkbox' name ='{$product['name']}' id='{$product['product_id']}' class='largerCheckbox search_product_checkbox checkboxName' disabled />";
                    // } else {
                    $output .= "<input type='checkbox' name ='{$product['name']}' id='{$product['product_id']}' class='largerCheckbox search_product_checkbox checkboxName' />";
                    // }
                    // }

                    $output .= "</div>"
                        // . "<div class='col-2 p-0'>"
                        // . "<img src='/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png' class='h-50 w-100' alt='' />"
                        // . "</div>"
                        . "<div class='col-8 product__detail'>"
                        . "<h5 class='m-0'>{$product['name']}</h5>"
                        . "<p class='m-0'>{$product['description']}</p>"
                        . "<div class='options'>";



                    // dd(($quantities));
                    if (count($options) === count($quantities)) {
                        foreach (array_combine($options, $quantities) as $option => $qty) {
                            // if ($qty > 0) {
                            $idObject = DB::table('product_flat')
                                ->select('id')
                                ->where('name', $option)
                                ->where('parent_id', $product['product_id'])
                                ->first();
                            $id = $idObject ? $idObject->id : null;
                            $radioGroupName = 'options' . $product['product_id'];


                            // $output .= "<input type='hidden' class='option-quantities' value='" . implode(',', $product['options']) . "' />";
                            $output .= "<input type='hidden' class='option-quantities' value='{$qty}' />";

                            $output .= "<input type='radio' id='{$id}' name='{$radioGroupName}' class='checkbox-button product-option'";
                            $output .= " data-product-id='{$product['product_id']}' data-option-id='{$id}'>";
                            $output .= "<label for='{$id}' class='custom-checkbox";
                            if ($qty < 10) {
                                $output .= " low-stock";
                            }
                            $output .= "'>{$option}</label>";
                            // }
                        }
                    }
                    $output .= "</div>"
                        . "<div class='row justify-content-start product__equi'>"
                        . "<div class='col-12'>"
                        . "<div class='input-group mb-2' style=' gap: 12px;'>"
                        . "<div class='input-group-prepend'>"
                        . "<img id='minusBtn' src='/themes/volantijetcatering/assets/images/minus.png' alt=''>"
                        . "</div>"
                        . "<input type='text' class='form-control text-center quantity__inp' aria-label='Quantity' aria-describedby='basic-addon1' value='1' id='quantityInput'/>"
                        . "<div class='input-group-append'>"
                        . "<img id='plusBtn' src='/themes/volantijetcatering/assets/images/plus-small.png' alt=''>"
                        . "</div>"
                        . "</div>"
                        . "</div>";
                    // . "<div class='col-3 p-0'>";
                    $price = $product['price'];
                    $formattedPrice = number_format($price, 2, '.', '');
                    //  "</div>"
                    $output .= "</div>"
                        . "<span class='quantity-limit-message text-danger'></span>"
                        . "<div><input type='number' id='productPrice' value='{$formattedPrice}' min='1' max='5'/></div>";
                    // dd($product['qty']);

                    if ($product['qty'] <= 0 || is_array($quantities) && allValuesAreZero($quantities)) {
                        $output .= "<p class='text-danger out_of_stock mb-2'>Out of Stock</p>";
                    } elseif ($product['qty'] < 10) {
                        $output .= "<p class='product__left'>Only {$product['qty']} left</p>";
                    }
                    $output .= "</div>"
                        . "</div>";
                }
            } else {
                $output .= '<div class="list-group-item no_product_found">No products found!</div>';
            }
            return $output;
        }
    }


    /**
     * 
     *  comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update_order_fbo(Request $request, $order_id)
    {

        $dateString = $request->delivery_date;
        // dd($dateString);
        // Check if the requested date is "Today" or "Tomorrow"
        if ($dateString == "Today") {

            $date = new DateTime(); // Set date to today
        } elseif ($dateString == "Tomorrow") {
            $date = new DateTime('+1 day'); // Set date to tomorrow
        } else {
            // Parse the date string
            $date = DateTime::createFromFormat('l n/j', $dateString);
        }

        // Format the date as desired
        $formattedDate = $date->format('Y-n-j');

        DB::table('orders')
            ->where('increment_id', $order_id)
            ->update([
                'fbo_full_name' => $request->fullname,
                'fbo_phone_number' => $request->phonenumber,
                'fbo_email_address' => $request->email,
                'fbo_tail_number' => $request->tailnumber,
                'fbo_packaging' => $request->packagingsection,
                'fbo_service_packaging' => $request->servicePackaging,
                'delivery_date' => $formattedDate,
                'delivery_time' => $request->delivery_time,
            ]);

        // sandeep update quickbook invoice
        $quickbookInvoiceId = DB::table('orders')->where('id', $order_id)->select('quickbook_invoice_id')->first();
        if ($quickbookInvoiceId->quickbook_invoice_id) {
            ProcessQuickBooksInvoice::dispatch($order_id);
        }


        return redirect()->back();
    }

    /**
     * Search address
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function search_address(Request $request)
    {

        if ($request->ajax() && $request->type === 'address_search') {

            // $addresses = Db::table('delivery_location_airports')->where('name', 'like', '%' . $request->name . '%')->orWhere('address', 'like', '%' . $request->name . '%')->get();

            $addresses = DB::table('delivery_location_airports')
                ->where('active', '1')
                ->where(function ($query) use ($request) {
                    $query->where('name', 'like', '%' . $request->name . '%')
                        ->orWhere('address', 'like', '%' . $request->name . '%');
                })
                ->get();


            $output = '';

            if (count($addresses) > 0) {
                $output .= '<ul class="list-group address_list">';
                foreach ($addresses as $addresse) {
                    $output .= "<li class='list-group-item p-2' attr='$addresse->id' data-attr='$addresse->name'>
                                    <div class='row m-0 add_listing_row'>
                                        <div class='suggestion-img-div'>
                                            <img class='suggestion-icon' src='/themes/velocity/assets/images/location-pin.png'>
                                        </div>
                                        <div class='address-name-list'>"
                        . "<b class='airport-name'>"
                        . $addresse->name
                        . "</b>"
                        . "<br>"
                        . $addresse->address
                        . "</div>
                                    </div>
                                </li>";
                }
                $output .= '</ul>';
            } else {

                $output .= '<li class="list-group-item no_location_text">  No any delivery location found <li>';
            }

            return $output;
        } elseif ($request->ajax() && $request->type == 'airport_fbo_detail') {
            // $airport_fbo_details = DB::table('airport_fbo_details')
            //     ->where('airport_id', $request->airport_id == '' ? $request->airport_fbo_airport_id : $request->airport_id)
            //     ->where('customer_id', $request->airport_fbo_customer_id)
            //     ->get();

            $airportId = $request->airport_id != '' ? $request->airport_id : $request->airport_fbo_airport_id;

            // dd(request()->input('_token'));
            // $airport_fbo_details = DB::table('airport_fbo_details')
            //     ->where('airport_id', $airportId)
            //     ->where('customer_id', $request->airport_fbo_customer_id)
            //     ->orWhere('customer_token', request()->input('_token'))
            //     ->orWhereNull('customer_id')
            //     ->whereNull('customer_token')
            //     ->get();
            // dd(request()->all());

            // $airport_fbo_details = DB::table('airport_fbo_details')
            // ->where('airport_id', $airportId)
            // ->whereNull('customer_id')
            // ->whereNull('customer_token')
            // ->orwhere(function ($query) use ($request) {
            //     if ($request->airport_fbo_customer_id) {
            //         $query->where('customer_id', $request->airport_fbo_customer_id);
            //     } else {
            //         $query->where('customer_token', request()->input('_token'));
            //     }
            // })
            // ->get();

            // sandeep || add code to get airport fbo details
            $airport_fbo_details = DB::table('airport_fbo_details')
                ->where('airport_id', $airportId)
                ->where(function ($query) use ($request) {
                    $query->where(function ($subquery) {
                        $subquery->whereNull('customer_id')
                            ->whereNull('customer_token');
                    })
                        ->orWhere(function ($subquery) use ($request) {
                            if ($request->airport_fbo_customer_id) {
                                $subquery->where('customer_id', $request->airport_fbo_customer_id);
                            } else {
                                $subquery->where('customer_token', request()->input('_token'));
                            }
                        });
                })
                ->get();


            // dd($airport_fbo_details);
            $output = '';
            if (count($airport_fbo_details) > 0) {
                foreach ($airport_fbo_details as $airport_fbo_detail) {
                    $output .= "<div class='custom-option text-dark' id='add_airport_fbo' data-id='{$airport_fbo_detail->id}' data-attr='{$airport_fbo_detail->name}'>
                                    <div class='d-flex m-0'>
                                        <div class='suggestion-img-div'>
                                            <img class='suggestion-icon' src='/themes/volantijetcatering/assets/images/admin/pin-2-map.svg'>
                                        </div>
                                        <div class='text-start'>
                                            <strong class='airport-name'>{$airport_fbo_detail->name}</strong><br>
                                            {$airport_fbo_detail->address}
                                        </div>
                                    </div>
                                </div>";
                }
                $output .= "<div class='custom-option d-flex justify-content-center add_fbo_detail modal_open_button' id='option_id' data-toggle='modal' data-target='#add_fbo_modal'>
                                <div class='add_fbo_wrap d-flex m-0' >
                                    <div class='suggestion-img-div'>
                                        <img class='suggestion-icon' src='/themes/volantijetcatering/assets/images/admin/plus-circle.svg'>
                                    </div>
                                    <div class='text-start'>
                                        <strong class='airport-name text-dark'>Option (Add)</strong>
                                    </div>
                                </div>
                            </div>";
                // dd($output);
            } else {
                $output .= '<div class="custom-option font-weight-bolder text-danger">No FBO details found</div>';
                $output .= "<div class='custom-option d-flex justify-content-center add_fbo_detail modal_open_button' id='option_id'  data-toggle='modal' data-target='#add_fbo_modal'>
                                <div class='add_fbo_wrap d-flex m-0'>
                                    <div class='suggestion-img-div'>
                                        <img class='suggestion-icon' src='/themes/volantijetcatering/assets/images/admin/plus-circle.svg'>
                                    </div>
                                    <div class='text-start'>
                                        <strong class='airport-name text-dark'>Option (Add)</strong>
                                    </div>
                                </div>
                            </div>";
            }

            return response()->json(['options' => $output]);
        }

        return redirect()->back();
    }




    public function create_address(Request $request)
    {
        if ($request->selected_fbo_id != null) {
            Order::where('id', $request->order_id)->update(['airport_fbo_id' => $request->selected_fbo_id]);
        }

        if ($request->airport_id) {

            $airport_data = Db::table('delivery_location_airports')->where('id', $request->airport_id)->first();
            // dd($airport_data);
            $country_states = Db::table('country_states')->where('id', $airport_data->state)->first();

            $existingAddress = DB::table('addresses')
                ->where('address_type', 'order_shipping')
                ->where('order_id', $request->order_id)
                ->first();

            if ($existingAddress) {
                // Update the existing address
                DB::table('addresses')
                    ->where('id', $existingAddress->id)
                    ->update([
                        'postcode' => $airport_data->zipcode,
                        'state' => $country_states->code,
                        'country' => $country_states->country_code,
                        'airport_fbo_id' => $request->selected_fbo_id,
                        'address1' => $airport_data->address,
                        'airport_name' => $airport_data->name,
                    ]);
            } else {
                // Create a new address
                DB::table('addresses')->insert([
                    'address_type' => 'order_shipping',
                    'order_id' => $request->order_id,
                    'postcode' => $airport_data->zipcode,
                    'state' => $country_states->code,
                    'country' => $country_states->country_code,
                    'address1' => $airport_data->address,
                    'airport_name' => $airport_data->name,
                    'airport_fbo_id' => $request->selected_fbo_id
                ]);
            }
            echo json_encode(['status' => 'successfully update data...!']);
        }

        // sandeep add code for tax add 
        $cartInstance = app(Cart::class);
        $cartInstance->calculateItemsTax($request->order_id);

        // get tax total and update to orders table
        $order = Order::where('id', $request->order_id)->first();
        $order->tax_amount = Tax::getTaxTotal($order, false);
        $order->base_tax_amount = Tax::getTaxTotal($order, true);
        $order->tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->base_tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->grand_total = $order->tax_amount + $order->sub_total;
        $order->base_grand_total = $order->tax_amount + $order->sub_total;
        $order->grand_total_invoiced = $order->tax_amount + $order->sub_total;
        $order->base_grand_total_invoiced = $order->tax_amount + $order->sub_total;
        $order->save();


        // sandeep update quickbook invoice
        if ($order->quickbook_invoice_id) {
            ProcessQuickBooksInvoice::dispatch($request->order_id);
        }
    }

    public function add_orders(Request $request)
    {
        // dd($request);   
        $order = DB::table('orders')
            ->where('increment_id', $request->order_id)
            ->first();

        // dd(($request->product_info));

        $totalQuantity = 0;
        $totalItem = [];
        // $totalPrice = 0;


        foreach ($request->product_info as $productId) {
            // dd($productId);

            if ($productId['qty'] <= 0) {
                session()->flash('error', 'Product quantity cannot be zero');
                return redirect()->back();
            }
            $totalItem[] = $productId['product_id'];
            $totalQuantity += $productId['qty'];
            if (isset($productId['option_id']) && $productId['option_id']) {
                $productsArray = DB::table('product_flat')
                    ->where('id', $productId['product_id'])
                    ->orwhere('id', $productId['option_id'])
                    ->get();
            } else {

                $productsArray = DB::table('product_flat')
                    ->where('id', $productId['product_id'])
                    // ->orwhereNull('product_id')
                    ->get();
            }


            foreach ($productsArray as $productArr) {

                // $totalPrice += $productId['price'] * $productId['qty'];
                if ($productArr->parent_id === null) {
                    if ($productArr->type === 'simple') {
                        DB::table('order_items')
                            ->insert([
                                'sku' => $productArr->sku,
                                'name' => $productArr->name,
                                'order_id' => $request->order_id,
                                'type' => $productArr->type,
                                'weight' => $productArr->weight,
                                'total_weight' => $productArr->weight * $productId['qty'],
                                'qty_ordered' => $productId['qty'],
                                'price' => $productId['price'],
                                'base_price' => $productId['price'],
                                'total' => $productId['price'] * $productId['qty'],
                                'base_total' => $productId['price'] * $productId['qty'],
                                'total_invoiced' => $productId['price'] * $productId['qty'],
                                'base_total_invoiced' => $productId['price'] * $productId['qty'],
                                'product_id' => $productId['product_id'],
                                'product_type' => 'Webkul\Product\Models\Product',
                                'additional' => json_encode([
                                    'is_buy_now' => '0',
                                    '_token' => $request->_token,
                                    'product_id' => $productId['product_id'],
                                    'quantity' => $productId['qty'],
                                    'locale' => $productArr->locale,
                                ]),
                                'created_at' => now(), // Current timestamp for creation
                                'updated_at' => now(), // Current timestamp for update
                            ]);
                    } else {

                        DB::table('order_items')
                            ->insert([
                                'sku' => $productArr->sku,
                                'name' => $productArr->name,
                                'order_id' => $request->order_id,
                                'type' => $productArr->type,
                                'weight' => $productArr->weight,
                                'total_weight' => $productArr->weight * $productId['qty'],
                                'qty_ordered' => $productId['qty'],
                                'price' => $productId['price'],
                                'base_price' => $productId['price'],
                                'total' => $productId['price'] * $productId['qty'],
                                'base_total' => $productId['price'] * $productId['qty'],
                                'total_invoiced' => $productId['price'] * $productId['qty'],
                                'base_total_invoiced' => $productId['price'] * $productId['qty'],
                                'product_id' => $productId['product_id'],
                                'product_type' => 'Webkul\Product\Models\Product',
                                'created_at' => now(), // Current timestamp for creation
                                'updated_at' => now(), // Current timestamp for update
                            ]);
                    }
                } else {
                    $attr = DB::table('product_flat')
                        ->join('attribute_options', 'product_flat.name', '=', 'attribute_options.admin_name')
                        ->where('product_flat.name', $productArr->name)
                        ->select('attribute_options.*')
                        ->first();
                    // dd($attr);
                    DB::table('order_items')
                        ->insert([
                            'sku' => $productArr->sku,
                            'name' => $productArr->name,
                            'order_id' => $request->order_id,
                            'type' => $productArr->type,
                            'weight' => 0.0000,
                            'total_weight' => 0.0000,
                            'qty_ordered' => $productId['qty'],
                            'price' => 1.0000,
                            'total_invoiced' => 1.0000,
                            'product_id' => $productId['option_id'],
                            'product_type' => 'Webkul\Product\Models\Product',
                            'additional' => json_encode([
                                'product_id' => $productId['option_id'],
                                'parent_id' => $productId['product_id'],
                                'locale' => $productArr->locale,
                            ]),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    $parent_id = DB::table('order_items')
                        ->select('id')
                        ->where('product_id', $productId['product_id'])
                        ->latest('created_at')
                        ->first();
                    // dd($parent_id->id);
                    $latest_created_at = DB::table('order_items')
                        ->where('product_id', $productId['product_id'])
                        ->max('created_at');
                    if ($parent_id) {
                        // Update the latest record with the specified option_id
                        DB::table('order_items')
                            ->where('product_id', $productId['option_id'])
                            ->where('created_at', $latest_created_at)
                            ->update([
                                'parent_id' => $parent_id->id
                            ]);
                    }

                    DB::table('order_items')
                        ->where('id', $parent_id->id)
                        ->update([
                            'additional' => json_encode([
                                'is_buy_now' => '0',
                                '_token' => $request->_token,
                                'product_id' => $productId['product_id'],
                                'super_attribute' => [$attr->attribute_id => $attr->id],
                                'quantity' => $productId['qty'],
                                'attributes' => [
                                    'options' => [
                                        'option_id' => $attr->id,
                                        'option_label' => $attr->admin_name,
                                    ]
                                ],
                                'locale' => $productArr->locale,
                            ]),
                            'weight' => $productArr->weight,
                            'total_weight' => $productArr->weight * $productId['qty'],

                        ]);
                }
            }

            $product_inventory = DB::table('product_inventory_indices')
                ->where('product_id', $productId['product_id'])
                ->first();

            if ($product_inventory->qty === 0 && isset($productId['option_id'])) {
                $option_inventory = DB::table('product_inventory_indices')
                    ->where('product_id', $productId['option_id'])
                    ->first();
                $orderInventory = ProductOrderedInventory::where('product_id', $productId['option_id'])->first();
                // dd($productId['qty']);
                // dd($productId['qty']);
                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty + $productId['qty'],
                    ]);
                }
                DB::table('product_inventory_indices')
                    ->where('product_id', $productId['option_id'])
                    ->update([
                        'qty' => $option_inventory->qty - $productId['qty']
                    ]);
            } else {
                // dd($productId['qty']);
                $orderInventory = ProductOrderedInventory::where('product_id', $productId['product_id'])->first();
                // dd($productId['qty']);
                // dd($productId['qty']);
                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty + $productId['qty'],
                    ]);
                }

                DB::table('product_inventory_indices')
                    ->where('product_id', $productId['product_id'])
                    ->update([
                        'qty' => $product_inventory->qty - $productId['qty']
                    ]);
            }

            session()->flash('success', 'Product added Successfully!');
        }



        //--------------------------------Updating order table----------------------------------------------// 
        $totalProducts = count($totalItem);

        $totalPrice = DB::table('order_items')
            ->where('order_id', $request->order_id)
            ->where('parent_id', null)
            ->sum('base_total');
        // dd($totalPrice);

        DB::table('orders')
            ->where('increment_id', $request->order_id)
            ->update([
                'total_item_count' => $totalProducts,
                'total_qty_ordered' => $totalQuantity,
                'grand_total' => $totalPrice,
                'base_grand_total' => $totalPrice,
                'grand_total_invoiced' => $totalPrice,
                'base_grand_total_invoiced' => $totalPrice,

                'sub_total' => $totalPrice,
                'base_sub_total' => $totalPrice,
                'sub_total_invoiced' => $totalPrice,
                'base_sub_total_invoiced' => $totalPrice,
            ]);
        // sandeep add code for tax add 
        $cartInstance = app(Cart::class);
        $cartInstance->calculateItemsTax($request->order_id);

        // get tax total and update to orders table
        $order = Order::where('id', $request->order_id)->first();
        $order->tax_amount = Tax::getTaxTotal($order, false);
        $order->base_tax_amount = Tax::getTaxTotal($order, true);
        $order->tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->base_tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->grand_total = $order->tax_amount + $totalPrice;
        $order->base_grand_total = $order->tax_amount + $totalPrice;
        $order->grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->base_grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->save();

        // sandeep update quickbook invoice
        // if($order->quickbook_invoice_id){
        //  $quickbookInvoice = app(InvoicesController::class);
        //  $quickbookInvoice->createInvoice($request->order_id);
        // }

    }

    /**
     * Add comment to the order
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function order_accept($id)
    {

        $admin_id = Auth::guard('admin')->user()->id;

        $order = DB::table('orders')->where('id', $id)->first();

        try {
            DB::table('orders')
                ->where('id', $id)
                ->update([
                    'status' => 'accepted',
                    'status_id' => 2,
                ]);

            DB::table('order_status_log')
                ->insert([
                    'order_id' => $id,
                    'user_id' => $admin_id,
                    'is_admin' => 1,
                    'status_id' => 2,
                    'email' => $order->customer_email === null ? $order->fbo_email_address : $order->customer_email,
                ]);


            // sandeep add code for send order accept mail
            try {
                OrderAcceptJob::dispatch($order);
            } catch (QueryException $e) {
            }

            // sandeep add notification
            $this->AppNotification($id, "Order Accept");


            session()->flash('success', 'Order accepted Successfully!');
            return redirect()->back();
        } catch (QueryException $e) {
            session()->flash('error', 'Error accepting the order. Please try again.');
            return redirect()->back();
        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred. Please try again.');
            return redirect()->back();
        }
    }

    public function order_reject(Request $request)
    {
        // dd(Auth::guard('admin')->user()->id);
        //dd(Auth::guard('admin')->user()->name);
        $data = $request->all();
        $admin_id = Auth::guard('admin')->user()->id;


        try {
            $order = Order::findOrFail($data['orderId']);

            DB::table('order_log')->insert([
                'status_id' => $data['action'] === 'reject' ? 10 : 11,
                'is_admin' => 1,
                'updated_by' => Auth::guard('admin')->user()->id,
                'comments' => $data['note'],
                'update_date' => now(),
                'order_id' => $data['orderId']
            ]);
            DB::table('order_status_log')
                ->insert([
                    'order_id' => $order->id,
                    'user_id' => $admin_id,
                    'is_admin' => 1,
                    'status_id' => $data['action'] === 'reject' ? 10 : 11,
                    'email' => $order->customer_email === null ? $order->fbo_email_address : $order->customer_email,
                ]);


            $orderItems = OrderItem::select('product_id', 'qty_ordered')
                ->where('order_id', $order->increment_id)
                ->where('type', 'simple')
                ->get();

            foreach ($orderItems as $item) {
                $inventory = ProductInventoryIndex::where('product_id', $item->product_id)->first();
                $orderInventory = ProductOrderedInventory::where('product_id', $item->product_id)->first();

                if ($inventory) {
                    $inventory->update([
                        'qty' => $inventory->qty + $item->qty_ordered,
                    ]);
                }

                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty - $item->qty_ordered,
                    ]);
                }
            }

            $statusData = ($data['action'] === 'reject')
                ? ['status' => 'rejected', 'status_id' => 10]
                : ['status' => 'canceled', 'status_id' => 11];

            $order->update($statusData);

            // $recipientEmail = config('mail.recipient_email', 'tanish@mindwebtree.com');

            // dd($recipientEmail);
            // dd($order->customer_email);
            if ($order->customer_email !== null) {
                $recipientEmail = $order->customer_email;
                // $recipientEmail = 'tanish@mindwebtree.com';
            } else {
                $recipientEmail = $order->fbo_email_address;
                // $recipientEmail = 'tanish@mindwebtree.com';
            }

            // sandeep send mail order accept and reject ussing queue
            if ($data['action'] === 'reject') {
                // sandeep add app notification
                $this->AppNotification($data['orderId'], "Order Reject");
                OrderRejectJob::dispatch($order, $recipientEmail);
            } else {
                // sandeep add app notification
                $this->AppNotification($data['orderId'], "Order Cancel");
                OrderCancelJob::dispatch($order, $recipientEmail);
            }

            $successMessage = ($data['action'] === 'reject')
                ? 'Order Rejected Successfully!'
                : 'Order Cancelled Successfully!';
            session()->flash('success', $successMessage);

            return redirect()->back();
        } catch (QueryException $queryException) {


            $errorMessage = ($data['action'] === 'reject')
                ? 'Error rejecting the order. Please try again.'
                : 'Error cancelling the order. Please try again.';

            session()->flash('error', $errorMessage);
            return redirect()->back();
        } catch (\Exception $e) {



            $errorMessage = ($data['action'] === 'reject')
                ? 'Error rejecting the order. Please try again.'
                : 'Error cancelling the order. Please try again.';

            session()->flash('error', $errorMessage);
            return redirect()->back();
        }
    }

    public function add_note(Request $request)
    {
        $data = $request->all();
        // dd($data);

        DB::table('order_items')
            ->where('id', $data['id'])
            ->where('order_id', $data['orderId'])
            ->update([
                'additional_notes' => $data['note'],
            ]);
        // dd($note);




        return redirect()->back();
    }


    public function edit_product(Request $request)
    {

        foreach ($request['productInfo'] as $product) {

            if ($product['itemType'] === 'simple') {

                //product inventory indice
                $qtyCount = DB::table('product_inventory_indices')
                    ->where('product_id', $product['productId'])
                    ->select('qty')
                    ->first();
                // dd($qtyCount);

                //product order inventory
                $orderInventory = ProductOrderedInventory::where('product_id', $product['productId'])->first();

                DB::table('product_inventory_indices')
                    ->where('product_id', $product['productId'])
                    ->update([
                        'qty' => abs($qtyCount->qty - intval($product['newQty']))
                    ]);

                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $product['newQty'] + $orderInventory->qty,
                    ]);
                }
            } else {
                $option = DB::table('order_items')
                    ->where('parent_id', $product['itemId'])
                    ->select('product_id')
                    ->first();
                // dd($option);

                $qtyCount = DB::table('product_inventory_indices')
                    ->where('product_id', $option->product_id)
                    ->select('qty')
                    ->first();
                // dd($qtyCount);
                // dd($product['newQty']);
                // $newQty = intval($product['newQty']);
                // dd($newQty);

                $orderInventory = ProductOrderedInventory::where('product_id', $option->product_id)->first();

                DB::table('product_inventory_indices')
                    ->where('product_id', $option->product_id)
                    ->update([
                        'qty' => abs($qtyCount->qty - intval($product['newQty']))
                    ]);

                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty + $product['newQty'],
                    ]);
                }
            }


            DB::table('order_items')
                ->where('id', $product['itemId'])
                ->update([
                    'total_weight' => $product['itemWeight'] * $product['quantity'],
                    'qty_ordered' => $product['quantity'],
                    'qty_invoiced' => $product['quantity'],
                    'price' => $product['itemprice'],
                    'base_price' => $product['itemprice'],
                    'total' => $product['itemprice'] * $product['quantity'],
                    'base_total' => $product['itemprice'] * $product['quantity'],
                    'total_invoiced' => $product['itemprice'] * $product['quantity'],
                    'base_total_invoiced' => $product['itemprice'] * $product['quantity'],
                ]);
            DB::table('order_items')
                ->where('parent_id', $product['itemId'])
                ->update([
                    'qty_ordered' => $product['quantity'],
                    'qty_invoiced' => $product['quantity'],
                ]);
        }

        $totalQuantity = DB::table('order_items')
            ->where('order_id', $request['orderID'])
            ->where('parent_id', null)
            ->sum('qty_ordered');

        $totalPrice = DB::table('order_items')
            ->where('order_id', $request['orderID'])
            ->where('parent_id', null)
            ->sum('base_total');

        $totalItems = DB::table('order_items')
            ->where('order_id', $request['orderID'])
            ->count();

        // dd($totalPrice);
        DB::table('orders')
            ->where('increment_id', $request['orderID'])
            ->update([
                'total_item_count' => $totalItems,
                'total_qty_ordered' => $totalQuantity,
                'grand_total' => $totalPrice,
                'base_grand_total' => $totalPrice,
                'grand_total_invoiced' => $totalPrice,
                'base_grand_total_invoiced' => $totalPrice,

                'sub_total' => $totalPrice,
                'base_sub_total' => $totalPrice,
                'sub_total_invoiced' => $totalPrice,
                'base_sub_total_invoiced' => $totalPrice,
            ]);

        // sandeep add code for tax add 
        $cartInstance = app(Cart::class);
        $cartInstance->calculateItemsTax($request['orderID']);

        // get tax total and update to orders table
        $order = Order::where('id', $request['orderID'])->first();
        $order->tax_amount = Tax::getTaxTotal($order, false);
        $order->base_tax_amount = Tax::getTaxTotal($order, true);
        $order->tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->base_tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->grand_total = $order->tax_amount + $totalPrice;
        $order->base_grand_total = $order->tax_amount + $totalPrice;
        $order->grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->base_grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->save();

        // sandeep update quickbook invoice
        // if($order->quickbook_invoice_id){
        //  $quickbookInvoice = app(InvoicesController::class);
        //  $quickbookInvoice->createInvoice($request['orderID']);
        // }

    }
    public function remove_product($order_id, $id)
    {
        $item = OrderItem::where('id', $id)
            ->orWhere('parent_id', $id)
            ->first();
        $productId = $item->product_id;
        // $order_id = $item->order_id;
        $quantity = $item->qty_ordered;
        // dd($item);
        if ($item) {
            if ($item->type === 'simple') {
                $qtyCount = DB::table('product_inventory_indices')
                    ->where('product_id', $productId)
                    ->select('qty')
                    ->first();

                $orderInventory = ProductOrderedInventory::where('product_id', $productId)->first();

                DB::table('product_inventory_indices')
                    ->where('product_id', $productId)
                    ->update([
                        'qty' => $qtyCount->qty + $quantity
                    ]);
                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty - $quantity,
                    ]);
                }
            } else {
                $option = OrderItem::where('parent_id', $id)
                    ->select('product_id')
                    ->first();
                // dd($option);

                $qtyCount = DB::table('product_inventory_indices')
                    ->where('product_id', $option->product_id)
                    ->select('qty')
                    ->first();
                // dd($qtyCount);
                $orderInventory = ProductOrderedInventory::where('product_id', $option->product_id)->first();

                DB::table('product_inventory_indices')
                    ->where('product_id', $option->product_id)
                    ->update([
                        'qty' => $qtyCount->qty + $quantity
                    ]);

                if ($orderInventory) {
                    $orderInventory->update([
                        'qty' => $orderInventory->qty - $quantity,
                    ]);
                }
            }

            $item = $item->delete();

            $totalQuantity = DB::table('order_items')
                ->where('order_id', $order_id)
                ->where('parent_id', null)
                ->sum('qty_ordered');

            $totalPrice = DB::table('order_items')
                ->where('order_id', $order_id)
                ->where('parent_id', null)
                ->sum('base_total');

            $totalItems = DB::table('order_items')
                ->where('order_id', $order_id)
                ->where('parent_id', null)
                ->count();

            DB::table('orders')
                ->where('increment_id', $order_id)
                ->update([
                    'total_item_count' => $totalItems,
                    'total_qty_ordered' => $totalQuantity,
                    'grand_total' => $totalPrice,
                    'base_grand_total' => $totalPrice,
                    'grand_total_invoiced' => $totalPrice,
                    'base_grand_total_invoiced' => $totalPrice,

                    'sub_total' => $totalPrice,
                    'base_sub_total' => $totalPrice,
                    'sub_total_invoiced' => $totalPrice,
                    'base_sub_total_invoiced' => $totalPrice,
                ]);




            if ($item > 0) {
                session()->flash('success', 'Successfully removed');
            } else {
                session()->flash('error', 'Failed to delete item');
            }
        } else {
            session()->flash('error', 'Item not found');
        }

        // sandeep add code for tax add 
        $cartInstance = app(Cart::class);
        $cartInstance->calculateItemsTax($order_id);

        // get tax total and update to orders table
        $order = Order::where('id', $order_id)->first();
        $order->tax_amount = Tax::getTaxTotal($order, false);
        $order->base_tax_amount = Tax::getTaxTotal($order, true);
        $order->tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->base_tax_amount_invoiced = Tax::getTaxTotal($order, true);
        $order->grand_total = $order->tax_amount + $totalPrice;
        $order->base_grand_total = $order->tax_amount + $totalPrice;
        $order->grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->base_grand_total_invoiced = $order->tax_amount + $totalPrice;
        $order->save();

        // sandeep update quickbook invoice
        // if($order->quickbook_invoice_id){
        // $quickbookInvoice = app(InvoicesController::class);
        // $quickbookInvoice->createInvoice($order_id);
        // }

        return redirect()->back();
    }


    // public function update_billing_address(request $request)
    // {

    //     dd($request->all());
    //     DB::table('addresses')
    //         ->where('order_id', $request->order_id)
    //         ->where('address_type', 'order_billing')
    //         ->update([
    //             'address1' => $request->Address,
    //             'postcode' => $request->postCode,
    //             // 'city' => $request->Select_State,
    //             'address2' => $request->Address2,
    //             'city' => $request->city,
    //             'phone' => $request->mobile,
    //             'vat_id' => $request->vat,
    //             'state' => $request->Select_State,
    //             'delivery_date' => $request->delivery_date,
    //             'delivery_time' => $request->delivery_time,

    //         ]);

    //     // sandeep update quickbook invoice
    //     $quickbookInvoiceId = DB::table('orders')->where('id', $request->order_id)->select('quickbook_invoice_id')->first();
    //     if ($quickbookInvoiceId->quickbook_invoice_id) {
    //         ProcessQuickBooksInvoice::dispatch($request->order_id);
    //     }

    //     return redirect()->back();
    // }

public function update_billing_address(Request $request)
{
    $updateData = [
        'address1'       => $request->Address,
        'address2'       => $request->Address2,
        'delivery_date'  => $request->delivery_date,  // always included
        'delivery_time'  => $request->delivery_time,  // always included
    ];

    // ---- Date parsing ----
    if ($request->filled('delivery_date')) {

        $input = trim($request->delivery_date);

        if (strtolower($input) === 'today') {
            $finalDate = Carbon::today();

        } elseif (strtolower($input) === 'tomorrow') {
            $finalDate = Carbon::tomorrow();

        } else {
            $clean = preg_replace('/^[A-Za-z]+\s*/', '', $input);
            $clean = str_replace('-', '/', $clean);
            [$month, $day] = explode('/', $clean);

            $currentYear = date('Y');
            $todayMonth = date('n');
            $year = ($month < $todayMonth) ? $currentYear + 1 : $currentYear;

            $finalDate = Carbon::create($year, $month, $day);
        }

        $updateData['delivery_date'] = $finalDate->format('Y-m-d');
    }

    // ---- UPDATE ADDRESSES TABLE ----
    DB::table('addresses')
        ->where('order_id', $request->order_id)
        ->where('address_type', 'order_billing')
        ->update($updateData);

    // ---- UPDATE ORDERS TABLE ----
    DB::table('orders')
        ->where('id', $request->order_id)
        ->update([
            'delivery_date' => $updateData['delivery_date'],
            'delivery_time' => $updateData['delivery_time'],
        ]);

    return back()->with('success', 'Billing Address Updated');
}





    public function update_purchase_no(request $request)
    {
        // dd($request);    
        DB::table('orders')
            ->where('id', $request->order_id)
            ->update([
                'purchase_order_no' => $request->Purchase_order_no,
            ]);
        return redirect()->back();
    }
    // public function add_handler_agent(request $request)
    // {
    //     dd($request->all());
    //     $validate = request()->validate([
    //         'mobile' => 'required|min:10|max:14',
    //     ]);
    //     $agent = agentHandler::where('order_id', $request->order_id)->first();
    //     // dd($request);
    //     if ($agent) {

    //         $agent->update([
    //             // Update fields as needed
    //             'Name' => $request->name,
    //             'PPR_Permit' => $request->ppr_permit,
    //             'Handling_charges' => $request->handling_charges,
    //             'Mobile' => $validate['mobile'],
    //         ]);
    //     } else {

    //         agentHandler::create([
    //             'Name' => $request->name,
    //             'PPR_Permit' => $request->ppr_permit,
    //             'Handling_charges' => $request->handling_charges,
    //             'order_id' => $request->order_id,
    //             'Mobile' => $validate['mobile'],
    //             // Add more fields as needed
    //         ]);
    //     }

    //     // sandeep update quickbook invoice
    //     $quickbookInvoiceId = DB::table('orders')->where('id', $request->order_id)->select('quickbook_invoice_id')->first();
    //     if ($quickbookInvoiceId->quickbook_invoice_id) {
    //         ProcessQuickBooksInvoice::dispatch($request->order_id);
    //     }

    //     return redirect()->back();
    // }

    public function add_handler_agent(Request $request)
{
    $validate = $request->validate([
        'mobile' => 'required|min:10|max:20',
    ]);

    // dd($request->all());
    // // convert mobile to digits only
    // $mobile = preg_replace('/\D/', '', $request->mobile); // remove non-digits

    // // avoid leading zero so MySQL does not convert to 0
    // $mobile = ltrim($mobile, '0');

    $agent = agentHandler::where('order_id', $request->order_id)->first();

    if ($agent) {
        $agent->update([
            'Name'              => $request->name,
            'PPR_Permit'        => $request->ppr_permit,
            'Handling_charges'  => $request->handling_charges,
            'Mobile'            => $validate['mobile'],
        ]);
    } else {
        agentHandler::create([
            'Name'              => $request->name,
            'PPR_Permit'        => $request->ppr_permit,
            'Handling_charges'  => $request->handling_charges,
            'order_id'          => $request->order_id,
            'Mobile'            => $validate['mobile'],
        ]);
    }

    $quickbookInvoiceId = DB::table('orders')->where('id', $request->order_id)->select('quickbook_invoice_id')->first();
    if ($quickbookInvoiceId->quickbook_invoice_id) {
        ProcessQuickBooksInvoice::dispatch($request->order_id);
    }

    return redirect()->back();
}

    public function package_slip($id)
    {
        $order = $this->orderRepository->findOrFail($id);

        $packagingData = packaging::where('order_id', $id)
            ->join('packaging_meta', 'packaging.id', '=', 'packaging_meta.packaging_id')
            ->select('packaging.id', 'packaging.name', 'packaging_meta.item_id', 'packaging_meta.qty')
            ->get();

        $packaging_meta_data = [];


        foreach ($packagingData as $data) {
            // Check if item ID already exists in packaging_meta_data
            if (isset($packaging_meta_data[$data->item_id])) {
                // If item ID exists, add the quantity to the existing quantity
                $packaging_meta_data[$data->item_id]['qty'] += $data->qty;
            } else {
                // If item ID doesn't exist, add it to the array with the quantity
                $packaging_meta_data[$data->item_id] = [
                    'item' => $data->item_id,
                    'qty' => $data->qty,
                ];
            }
        }



        $packaging_lists = [];

        // Iterate over each item in the collection
        foreach ($packagingData as $item) {
            // Get the packaging ID
            $packagingId = $item->id;

            // Check if the packaging ID exists in the new array
            if (!isset($packaging_lists[$packagingId])) {
                // If the packaging ID does not exist, initialize it with an empty array
                $packaging_lists[$packagingId] = [
                    'packaging_id' => $packagingId,
                    'total_qty' => 0,
                    'name' => $item->name,
                    'items' => [],
                ];
            }

            // Add the item to the corresponding packaging in the new array
            $packaging_lists[$packagingId]['items'][] = [
                'item_id' => $item->item_id,
                'qty' => $item->qty,
            ];

            // Increment the total quantity for the packaging
            $packaging_lists[$packagingId]['total_qty'] += $item->qty;
        }

        // Dump and die the new array




        // dd($packaging_meta_data);

        return view($this->_config['view'], compact('order', 'packaging_meta_data', 'packaging_lists'));
    }
    public function add_packaging_slip(Request $request)
    {
        $data = [
            'order_id' => $request->order_id,
            'name' => $request->slip_name,
        ];
        $packaging_model = packaging::create($data);
        $insertedId = $packaging_model->id;
        foreach ($request->items as $item) {
            $itemMeta = OrderItem::where('id', $item['item_id'])->pluck('product_id')->first();


            // Package data to be inserted into the meta table
            $packaging_meta_data = [
                'packaging_id' => $insertedId,
                'item_id' => $item['item_id'],
                'qty' => $item['qty'],
                'product_id' => $itemMeta,

            ];

            // Insert data into meta table
            packaging_meta::create($packaging_meta_data);
        }
    }
    public function delete_packaging_slip($id)
    {
        // dd('sss');
        $packaging = Packaging::find($id);
        if ($packaging) {
            // If the packaging record exists, delete it along with associated packaging_meta records
            $packaging->delete();
            // Optionally, you can add a success message or perform any other actions after deletion
        }
        return redirect()->back();
    }
    public function download_packaging_slip($slip_id, $order_id)
    {
        $packagingMeta = packaging_meta::where('packaging_id', $slip_id)->get();
        $packaging = packaging::where('id', $slip_id)->get();
        $order = Order::where('orders.id', $order_id)
            ->select('orders.fbo_tail_number', 'orders.purchase_order_no', 'orders.delivery_date', 'orders.delivery_time', 'orders.id', 'handling-agent.Name', 'handling-agent.Mobile', 'handling-agent.PPR_Permit')
            ->leftjoin('handling-agent', 'handling-agent.order_id', '=', 'orders.id') // Adjust table name accordingly
            ->first();

        $addresses = Db::table('addresses')->where('address_type', 'order_shipping')
            ->where('order_id', $order_id)
            ->select('airport_name')
            ->get();

        $dataArray = [];

        foreach ($packagingMeta as $packing_id) {

            $oder_datils = DB::table('packaging_meta')
                ->where('packaging_meta.id', $packing_id->id)
                ->join('product_flat', 'packaging_meta.product_id', '=', 'product_flat.id')
                ->join('order_items', 'order_items.id', '=', 'packaging_meta.item_id')
                ->select('order_items.additional', 'packaging_meta.qty', 'order_items.product_id', 'product_flat.name', 'product_flat.description')
                ->get();
            // dd($oder_datils);
            // Append the oder_datils data to the dataArray
            foreach ($oder_datils as $order_datils) {
                $dataArray[] = [
                    'additional' => $order_datils->additional,
                    'product_id' => $order_datils->product_id,
                    'product_name' => $order_datils->name,
                    'product_description' => $order_datils->description,
                    'qty' => $order_datils->qty
                ];
            }
        }



        // Access the value of the "special_instruction" key
        //     $specialInstruction = $dataArray['special_instruction'];
        // DD($specialInstruction);
        // dd($packaging[0]->name);

        $generator = new BarcodeGeneratorHTML();
        $order_id_with_sequence = $order_id . $packaging[0]->slip_sequence;
        $order_id_str = (string) $order_id_with_sequence;
        $barcode_no = str_pad($order_id_str, 11, '0', STR_PAD_LEFT);



        $barcode = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => false,
                'allow_self_signed' => true, // Typo corrected
            ],
        ]);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', FALSE);
        //  $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->setHttpContext($context);


        $html = view('paymentprofile::admin.sales.orders.pdf.packageSlip', compact('addresses', 'order', 'packaging', 'packagingMeta', 'dataArray', 'barcode', 'barcode_no'));
        //  $dompdf->loadHtml(ob_get_clean());
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();
        return $dompdf->stream('packaging-slip.pdf');
    }


    public function create_print_pdf($slip_id, $order_id)
    {
        $packagingMeta = packaging_meta::where('packaging_id', $slip_id)->get();
        $packaging = packaging::where('id', $slip_id)->get();
        $order = Order::where('orders.id', $order_id)
            ->select('orders.fbo_tail_number', 'orders.purchase_order_no', 'orders.delivery_date', 'orders.delivery_time', 'orders.id', 'handling-agent.Name', 'handling-agent.Mobile', 'handling-agent.PPR_Permit')
            ->leftjoin('handling-agent', 'handling-agent.order_id', '=', 'orders.id') // Adjust table name accordingly
            ->first();

        $addresses = Db::table('addresses')->where('address_type', 'order_shipping')
            ->where('order_id', $order_id)
            ->select('airport_name')
            ->get();

        $dataArray = [];

        foreach ($packagingMeta as $packing_id) {

            $oder_datils = DB::table('packaging_meta')
                ->where('packaging_meta.id', $packing_id->id)
                ->join('product_flat', 'packaging_meta.product_id', '=', 'product_flat.id')
                ->join('order_items', 'order_items.id', '=', 'packaging_meta.item_id')
                ->select('order_items.additional', 'packaging_meta.qty', 'order_items.product_id', 'product_flat.name', 'product_flat.description')
                ->get();
            // dd($oder_datils);
            // Append the oder_datils data to the dataArray
            foreach ($oder_datils as $order_datils) {
                $dataArray[] = [
                    'additional' => $order_datils->additional,
                    'product_id' => $order_datils->product_id,
                    'product_name' => $order_datils->name,
                    'product_description' => $order_datils->description,
                    'qty' => $order_datils->qty
                ];
            }
        }



        // Access the value of the "special_instruction" key
        //     $specialInstruction = $dataArray['special_instruction'];
        // DD($specialInstruction);
        // dd($packaging[0]->name);

        $generator = new BarcodeGeneratorHTML();
        $order_id_with_sequence = $order_id . $packaging[0]->slip_sequence;
        $order_id_str = (string) $order_id_with_sequence;
        $barcode_no = str_pad($order_id_str, 11, '0', STR_PAD_LEFT);



        $barcode = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);

        $context = stream_context_create([
            'ssl' => [
                'verify_peer' => true,
                'verify_peer_name' => false,
                'allow_self_signed' => true, // Typo corrected
            ],
        ]);
        $options = new Options();
        $options->set('isHtml5ParserEnabled', FALSE);
        //  $options->set('isRemoteEnabled', false);
        $dompdf = new Dompdf();
        $dompdf->setOptions($options);
        $dompdf->setHttpContext($context);


        $html = view('paymentprofile::admin.sales.orders.pdf.packageSlip', compact('addresses', 'order', 'packaging', 'packagingMeta', 'dataArray', 'barcode', 'barcode_no'));
        //  $dompdf->loadHtml(ob_get_clean());
        $dompdf->loadHtml($html);

        // Set paper size and orientation
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF
        $dompdf->render();
        // return $dompdf->stream('packaging-slip.pdf');
        // // return   view('paymentprofile::admin.sales.orders.pdf.packageSlip', compact('addresses', 'order', 'packaging', 'packagingMeta','dataArray','barcode','barcode_no'));
        // return redirect()->back();



        $pdfFilename = 'packaging-slip-' . time() . '.pdf';

        // Save the PDF to storage




        $pdfFilename = 'package-slip-' . $order_id . $slip_id . '.pdf';
        $pdfPath = public_path('package-slip/' . $pdfFilename);
        file_put_contents($pdfPath, $dompdf->output());



        $dompdf->stream($pdfPath);
        return response()->json(['pdf_url' => url('temp/packaging-slip.pdf')]);

        return 'package-slip/' . $pdfFilename;
    }

    public function print_package_slip($slip_id, $order_id)
    {

        $packagingMeta = packaging_meta::where('packaging_id', $slip_id)->get();
        $packaging = packaging::where('id', $slip_id)->get();
        $order = Order::where('orders.id', $order_id)
            ->select('orders.fbo_tail_number', 'orders.purchase_order_no', 'orders.delivery_date', 'orders.delivery_time', 'orders.id', 'handling-agent.Name', 'handling-agent.Mobile', 'handling-agent.PPR_Permit')
            ->leftjoin('handling-agent', 'handling-agent.order_id', '=', 'orders.id') // Adjust table name accordingly
            ->first();

        $addresses = Db::table('addresses')->where('address_type', 'order_shipping')
            ->where('order_id', $order_id)
            ->select('airport_name')
            ->get();

        $dataArray = [];

        foreach ($packagingMeta as $packing_id) {

            $oder_datils = DB::table('packaging_meta')
                ->where('packaging_meta.id', $packing_id->id)
                ->join('product_flat', 'packaging_meta.product_id', '=', 'product_flat.id')
                ->join('order_items', 'order_items.id', '=', 'packaging_meta.item_id')
                ->select('order_items.additional', 'packaging_meta.qty', 'order_items.product_id', 'product_flat.name', 'product_flat.description')
                ->get();


            foreach ($oder_datils as $order_datils) {
                $dataArray[] = [
                    'additional' => $order_datils->additional,
                    'product_id' => $order_datils->product_id,
                    'product_name' => $order_datils->name,
                    'product_description' => $order_datils->description,
                    'qty' => $order_datils->qty
                ];
            }
        }



        $generator = new BarcodeGeneratorHTML();
        $order_id_with_sequence = $order_id . $packaging[0]->slip_sequence;
        $barcode_no = str_pad($order_id_with_sequence, 11, '0', STR_PAD_LEFT);
        $barcode = $generator->getBarcode($barcode_no, $generator::TYPE_CODE_128);

        return view('paymentprofile::admin.sales.orders.print.print-package-slip', compact('addresses', 'order', 'packaging', 'packagingMeta', 'dataArray', 'barcode', 'barcode_no'));
    }


    // store airport fbo
    public function store_fbo_detail()
    {
        $validatedData = $this->validate(request(), [
            'name' => 'required',
            'address' => 'required',
        ]);

        // dd(request()->all());
        $insertedId = DB::table('airport_fbo_details')->insertGetId([
            'name' => request()->input('name'),
            'address' => request()->input('address'),
            'airport_id' => request()->input('airport_id'),
            'notes' => request()->input('notes'),
            'customer_id' => request()->input('customer_id'),
        ]);
        // Order::where('is',request()->input('order_id'))->update([
        //     'airport_fbo_id' => $insertedId
        // ])
        // Log::info('Inserted record ID: ' . $insertedId);
        $insertedRecord = DB::table('airport_fbo_details')->where('id', $insertedId)->first();
        // if ($inserted) {
        session()->flash('success', trans('Airport Fbo Details added successfully'));

        return response()->json(['response' => true, 'data' => $insertedRecord]);
    }
    public function custom_order()
    {
        return view($this->_config['view']);
    }
    // public function create_custom_order(Request $request)
    // {
    //     $valdiate = $request->validate([
    //         'name' => 'required|string|max:100',
    //         'email' => 'required|email|max:100|unique:customers,email',
    //         'phone' => 'required|min:10|max:14',
    //     ]);
    //     [$firstName, $lastName] = array_pad(explode(' ', $valdiate['name'], 2), 2, '');

    //     $customer = \Webkul\Customer\Models\Customer::create([
    //         'token' => $request->input('_token'),
    //         'first_name' => $firstName,
    //         'last_name' => $lastName,
    //         'email' => $valdiate['email'],
    //         'phone' => $valdiate['phone'],
    //     ]);
    //     $customerDetail = \Webkul\Customer\Models\Customer::find($customer->id);
    //     $incrementId = Order::max('id') + 1; // Generate a unique increment ID
    //     $order = Order::create([
    //         'increment_id' => $incrementId,
    //         'status_id' => 1,
    //         'channel_name' => 'Default',
    //         'status' => 'pending',
    //         'customer_email' => $customerDetail->email,
    //         'customer_first_name' => $customerDetail->first_name,
    //         'customer_last_name' => $customerDetail->last_name,
    //         'shipping_method' => 'free_free',
    //         'shipping_title' => 'Free Shipping - Free Shipping',
    //         'shipping_description' => 'Free Shipping',
    //         'customer_id' => $customer->id,
    //         'customer_type' => 'Webkul\Customer\Models\Customer',
    //         'channel_id' => 1,
    //         'channel_type' => 'Webkul\Core\Models\Channel',
    //     ]);

    //     DB::table('order_status_log')->insert([
    //         'order_id' => $order->id,
    //         'user_id' => auth()->guard('admin')->user()->id,
    //         'status_id' => 1,
    //         'is_admin' => 1,
    //         'email' => $customerDetail->email,
    //     ]);

    //     DB::table('addresses')->insert([
    //         [
    //             'address_type' => 'order_shipping',
    //             'order_id' => $order->id,
    //         ],
    //         [
    //             'address_type' => 'order_billing',
    //             'order_id' => $order->id,
    //         ]
    //     ]);

    //     session()->flash('success', 'Order created succesfully!');
    //     // return view($this->_config['redirect']);
    //     return redirect()->route('admin.sale.order.view', $order->id);
    // }

    public function create_custom_order(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'phone' => 'required|min:10|max:12',
        ]);

        [$firstName, $lastName] = array_pad(explode(' ', $request->input('name'), 2), 2, '');

        // Check if customer already exists
        $customer = \Webkul\Customer\Models\Customer::where('email', $request->email)->first();

        // If not exists, create new one
        if (!$customer) {
            $customer = \Webkul\Customer\Models\Customer::create([
                'token'       => $request->input('_token'),
                'first_name'  => $firstName,
                'last_name'   => $lastName,
                'email'       => $request->email,
                'phone'       => $request->phone,
            ]);
        }

        $incrementId = Order::max('id') + 1;

        $order = Order::create([
            'increment_id'        => $incrementId,
            'status_id'           => 1,
            'channel_name'        => 'Default',
            'status'              => 'pending',
            'customer_email'      => $customer->email,
            'customer_first_name' => $customer->first_name,
            'customer_last_name'  => $customer->last_name,
            'shipping_method'     => 'free_free',
            'shipping_title'      => 'Free Shipping - Free Shipping',
            'shipping_description' => 'Free Shipping',
            'customer_id'         => $customer->id,
            'customer_type'       => 'Webkul\Customer\Models\Customer',
            'channel_id'          => 1,
            'channel_type'        => 'Webkul\Core\Models\Channel',
        ]);

        DB::table('order_status_log')->insert([
            'order_id' => $order->id,
            'user_id'  => auth()->guard('admin')->user()->id,
            'status_id' => 1,
            'is_admin' => 1,
            'email'    => $customer->email,
        ]);

        DB::table('addresses')->insert([
            ['address_type' => 'order_shipping', 'order_id' => $order->id],
            ['address_type' => 'order_billing',  'order_id' => $order->id],
        ]);

        session()->flash('success', 'Order created successfully!');
        return redirect()->route('admin.sale.order.view', $order->id);
    }


    // sandeep add code for app notification
    private function AppNotification($id, $message)
    {
        $order = DB::table('orders')->where('id', $id)->select('customer_id')->first();
        $appNotifications = DB::table('app_notifications')->insert([
            'customer_id' => $order->customer_id,
            'order_id' => $id,
            'message' => $message,
            'is_read' => 0
        ]);
    }
}
