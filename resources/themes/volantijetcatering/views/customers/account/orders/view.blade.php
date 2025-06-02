@extends('shop::customers.account.index')

@section('page_title')
    {{-- {{ __('shop::app.customer.account.order.view.page-tile', ['order_id' => $order->increment_id]) }} --}}
    Order #{{$order->increment_id}} | Volanti Jet Catering
@endsection

@section('seo')
<meta name="title" content="Order #{{$order->increment_id}} | Volanti Jet Catering" />
<meta name="description" content="Order #{{$order->increment_id}} | Volanti Jet Catering" />
<meta name="keywords" content="" />
@stop

@push('css')
    <style type="text/css">
        .account-content .account-layout .account-head {
            margin-bottom: 0px;
        }

        .sale-summary .dash-icon {
            margin-right: 30px;
            float: right;
        }
    </style>
@endpush

@section('page-detail-wrapper')
@php
    use Illuminate\Support\Facades\DB;

    //   sandeep add code for payment button 
            $status_log = DB::table('order_status_log')
                        ->leftJoin('order_status', 'order_status_log.status_id', '=', 'order_status.id')
                        ->where('order_id',$order->id)
                        ->select('order_status.status')
                        ->get();

            $paidExists = $status_log->contains('status', 'paid');
            $excludedStatuses = ['pending', 'canceled', 'rejected'];
            if ($paidExists) {
                $excludedStatuses[] = 'paid';
            }


@endphp

    <div class="account-head text-center">
        <div class="head w-100">
            <div class="back-icon text-left">
            <a href="/customer/account/orders">
                <img src="/themes/volantijetcatering/assets/images/left-arrow.png" style="height:25px; width:25px;" alt="Left Arrow">
            </a>

    </div>
            <div class="order-detail first-section mt-3">
                <h2 class="m-0">Thank you for your order !</h2>
                <br>
                <span class="order-no">Order No.(#{{ $order->id }})</span>
            </div>
            <div class="order-detail second-section text-left ">
                <div class="discription p-3 my-4">
                    <p class="m-0 ">You will receive an email confirmation shortly at volantijetcatering@gmail.com</p>
                    {{-- {{ route('shop.customer.orders.print', $order->invoices[0]->id) }} --}}
                    {{-- <p class="m-0">You can <a href="#">Print</a> or <a href="">PDF</a> Invoice order
                    receipt.
                </p> --}}
                </div>

                {{-- <div class="container my-5">
                    <div class="row">
                        <div class="col">
                            <div class="timeline-steps aos-init aos-animate {{ $order->status_id == 10 || $order->status_id == 11 ? 'rejected' : '' }}"
                                data-aos="fade-up">
                                @foreach ($order_status as $index => $status)
                                    <div class="timeline-step {{ $status->id <= $order->status_id ? 'completed' : '' }} ">
                                        <div class="timeline-content">
                                            <div class="inner-circle"></div>
                                            <p style="font-size: 13px;font-weight: 600;" class="h6 mt-3 mb-1">
                                                {{ $status->status }}</p>
                                            <span style="font-weight: 600;font-size: 11px;">
                                                {{ $status_update !== null ? $status_update[$index]->updated_at ?? '' : $status->created_at }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="container my-5">
                    <div class="row">
                        <div class="col">
                            <div class="timeline-steps aos-init aos-animate {{ $order->status_id == 10 || $order->status_id == 11 ? 'rejected' : '' }}" data-aos="fade-up">
                                @foreach ($result as $index => $status)
                                
                                    <div class="timeline-step">
                                        <div class="timeline-content 
                                            {{ ($order->status_id == 10 || $order->status_id == 11) ? ($status->status == 'cancel' || $status->status == 'rejected' ? '' : 'completed') : ($status->updated_at !== null ? 'completed' : '') }}">
                                            <div class="inner-circle"></div>
                                            <p style="font-size: 13px; font-weight: 600;" class="h6 mt-3 mb-1 capitalize-first">
                                                @if ($status->status == 'cancel')
                                                    Cancelled
                                                @else
                                                    {{ $status->status }}
                                                @endif
                                            </p>
                                            <span style="font-weight: 600; font-size: 11px;">
                                                {{-- sandeep change time formate --}}
                                                {{ $status->updated_at ? date('m-d-Y h:i:s A', strtotime($status->updated_at)) : '' }}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            {{-- <div class="timeline-steps aos-init aos-animate {{ $order->status_id == 10 || $order->status_id == 11 ? 'rejected' : '' }}"
                                data-aos="fade-up">
                                @foreach ($result as $index => $status)
                                    <div class="timeline-step">
                                        <div class="timeline-content {{ $status->updated_at !== null ? 'completed' : '' }} {{ $status->status == 'cancel' || $status->status == 'rejected' ? '' : 'completed' }}">
                                            <div class="inner-circle"></div>
                                            <p style="font-size: 13px;font-weight: 600;" class="h6 mt-3 mb-1 capitalize-first">
                                              @if ($status->status == 'cancel')
                                                Cancelled
                                              @else
                                                {{ $status->status }}
                                              @endif
                                                </p>
                                            <span style="font-weight: 600;font-size: 11px;"> --}}
                                                {{-- {{ $status_update !== null ? $status_update[$index]->updated_at ?? '' : $status->created_at }} --}}
                                                {{-- {{ $status->updated_at??''}}
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div> --}}
                        </div>
                    </div>
                </div>
                <h3 class="m-0 text-center">order Details</h3>
            </div>
            <div class='row w-70 order-location-section my-4 mx-0    '>
                <div class="airport-address col-lg-5 col-md-6 col-12 p-3">
                    <div class="row pl-2">
                        <div class="col-12 p-0 title text-left shipping_address">
                            <span id="shipping_address_heading" style="font-weight: bold">Shipping Address: </span>
                        </div>
                        {{-- @dd($order->shipping_address) --}}
                        <div class="col-lg-9 col-md-9 col-12 mt-2 mt-lg-3 mt-md-3">
                            @if (!is_null($order->shipping_address))
                                <div class="order-address">
                                    <div class="row pl-2">
                                        <img src="/themes/volantijetcatering/assets/images/location-black.png"
                                            class="location-icon mt-2" alt="">

                                        <div class="col-lg-10 col-md-10 col-11  text-left">
                                            <span class="airport-name">{{ $order->shipping_address->airport_name }}</span>
                                            <br>
                                            <span class="airport-address">{{ $order->shipping_address->address1 }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            
                <div class="Fbo-details col-lg-5 col-md-5 p-3">
                    <div class="row w-100">
                        <div class="col-12 p-0 title text-left">
                            <span id="account_information" style="font-weight: bold">{{ __('shop::app.fbo-detail.client-info') }} : </span>
                        </div>
                        <div class="col-lg-9 col-md-9 col-12 user_information">
                            @if (!is_null($order->billing_address))
                                <div class="order-address">
                                    <div class="row">
                                        <!-- sandeep remove image -->
                                    <!-- <img src="/themes/volantijetcatering/assets/images/profile-user.png" style="height:20px"> -->
                                        <div class="col-lg-10 col-md-10 col-12 mt-2 mt-lg-3 mt-md-3 text-left account_information">
                                        
                                           
                                            <span class="fbo-customer-name  ">{{ $order->fbo_full_name }}</span>
                                            <br>
                                            <span
                                                class="fbo-customer-mobile fbo-data">{{ $order->fbo_phone_number }}</span>
                                            <br>
                                            <span
                                                class="fbo-customer-email fbo-data">{{ $order->fbo_email_address }}</span>
                                            <br>
                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>

                        <div class="col-12 p-0 title text-left mt-3">
                            <span id="aircraft_information" style="font-weight: bold">{{ __('shop::app.fbo-detail.aircraft-info') }} : </span>
                        </div>
                        <div class="col-lg-9 col-md-9 col-12 mt-2 mt-lg-3 mt-md-3 aircraft_info">
                            @if (!is_null($order->billing_address))
                                <div class="order-address"> 
                                    <div class="row">
                                        <div class="col-lg-10 col-md-10 col-12  text-left aircraft_information">
                                            <span class="fbo-tail-no fbo-data"> {{ $order->fbo_tail_number }}</span>
                                            <br>
                                            @if (isset($order->fbo_packaging) && $order->fbo_packaging != '')
                                                <span class="fbo-tail-no fbo-data"> {{ $order->fbo_packaging }}</span>
                                            @endif
                                            <br>
                                            {{-- sandeep add service packaging --}}
                                            @if (isset($order->fbo_service_packaging) && $order->fbo_service_packaging != '')
                                                <span class="fbo-tail-no fbo-data"> {{ $order->fbo_service_packaging }}</span>
                                            @endif
                                            <br>
                        
                                            @if (isset($order->delivery_date) && $order->delivery_date != '')
                                                @php
                                                    $date = $order->delivery_date;

                                                    // Create a DateTime object from the date string
                                                    $dateObj = new DateTime($date);

                                                    // Get today's date
$today = new DateTime('today');

// Get tomorrow's date
                                                    $tomorrow = new DateTime('tomorrow');

                                                    // Compare the delivery date with today's date and tomorrow's date
                                                    if ($dateObj->format('Y-m-d') == $today->format('Y-m-d')) {
                                                        // If delivery date is today, show today's date in the desired format
    $formattedDate = 'Today'; // Example format: "Thursday 3/26"
} elseif ($dateObj->format('Y-m-d') == $tomorrow->format('Y-m-d')) {
    // If delivery date is tomorrow, show tomorrow's date in the desired format
                                                        $formattedDate = 'Tomorrow'; // Example format: "Friday 3/27"
                                                    } else {
                                                        // If not today or tomorrow, show the delivery date in the original format
                                                        // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
                                                        $dayOfWeek = $dateObj->format('w');

                                                        // Array of days of the week
                                                        $daysOfWeek = [
                                                            'Sunday',
                                                            'Monday',
                                                            'Tuesday',
                                                            'Wednesday',
                                                            'Thursday',
                                                            'Friday',
                                                            'Saturday',
                                                        ];

                                                        // Get the day of the week name
                                                        $dayName = $daysOfWeek[$dayOfWeek];

                                                        // Get the month
                                                        $month = $dateObj->format('n');

                                                        // Get the day of the month
                                                        $dayOfMonth = $dateObj->format('j');

                                                        // Format the date string
                                                        $formattedDate = $dayName . ' ' . $month . '/' . $dayOfMonth;
                                                    }
                                                @endphp
                                                <span class="fbo-tail-no fbo-data"> {{ $formattedDate }}</span>
                                            @endif
                                            <br>
                                            @if (isset($order->delivery_time) && $order->delivery_time != '')
                                                <span class="fbo-tail-no fbo-data"> {{ $order->delivery_time }}</span>
                                            @endif
                                            <br>
                                            <span class="fbo-tail-no fbo-data"><b>Airport FBO:</b>

                                             <p class="m-0">
                                                {{ DB::table('airport_fbo_details')->where('id', $order->airport_fbo_id)->value('name') }}
                                             </p>
                                             <p class="m-0"> 
                                                {{ DB::table('airport_fbo_details')->where('id', $order->airport_fbo_id)->value('address') }}
                                             </p>
                                            </span>

                                        </div>
                                    </div>

                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- @dd($order->billing_address) --}}
                @if ($order->billing_address->address1 !== '')
                    <div class="airport-address col-lg-5  col-md-6 col-12 p-3">
                        <div class="row">
                            <div class="col-12 p-0 pl-lg-1 pl-md-2 title text-left">
                                <span id="billing_address" style="font-weight: bold">Billing Address: </span>
                            </div>
                            <div class="col-lg-9 col-md-9 col-12 mt-lg-3 mt-md-3 mt-2">
                                @if (isset($order->billing_address->address1) && $order->billing_address->address1 != '')
                                    <div class="order-address">
                                        <div class="row pl-lg-3 pl-md-3">
                                            <img src="/themes/volantijetcatering/assets/images/location-black.png"
                                                class="location-icon mt-2" alt="">

                                            <div class="col-lg-10 col-md-10 col-11  text-left">
                                                <span class="airport-name">
                                                    {{ isset($order->billing_address->address1) ? $order->billing_address->address1 . ',' : '' }}
                                                    {{ isset($order->billing_address->city) ? $order->billing_address->city . ',' : '' }}
                                                    {{ isset($order->billing_address->postcode) ? $order->billing_address->postcode . ',' : '' }}
                                                    {{ isset($order->billing_address->state) ? $order->billing_address->state : '' }}
                                                </span>
                                                <br>
                                                <span class="m-0">Phone:
                                                    {{ isset($order->billing_address->phone) ? $order->billing_address->phone : '' }}
                                                </span>
                                                <br>
                                                <span class="m-0">Vat:
                                                    {{ isset($order->billing_address->vat_id) ? $order->billing_address->vat_id : '' }}
                                                </span>


                                            </div>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>

                    </div>
                @endif

            </div>

        </div>


        @if ($order->canCancel())
            <span class="account-action">
                <form id="cancelOrderForm" action="{{ route('shop.customer.orders.cancel', $order->id) }}" method="post">
                    @csrf
                </form>

                <a href="javascript:void(0);" class="cancel-order theme-btn light unset float-right"
                    onclick="cancelOrder('{{ __('shop::app.customer.account.order.view.cancel-confirm-msg') }}')"
                    style="float: right">
                    {{ __('shop::app.customer.account.order.view.cancel-btn-title') }}
                </a>
            </span>
        @endif
    </div>

    {!! view_render_event('bagisto.shop.customers.account.orders.view.before', ['order' => $order]) !!}


    <div class="sale-container mt10">

        {{-- <tabs> --}}
        {{-- <tab name="{{ __('shop::app.customer.account.order.view.info') }}">

    <div class="sale-section py-2">
        {{-- <div class="section-title">
                    <span>{{ __('shop::app.customer.account.order.view.individual-invoice', ['invoice_id' =>
                        $invoice->increment_id ?? $invoice->id]) }}</span>

        <a href="{{ route('shop.customer.orders.print', $invoice->id) }}" class="float-right">
            {{ __('shop::app.customer.account.order.view.print') }}
        </a>
    </div> --}}

        <div class="section-content">
            <div class="table-responsive" style="max-height: 500px">
                <div class="table order-detail-table">
                    <table class="customer_order_view_table">
                        <!-- sandeep add code  -->
                        <thead>
                                    <tr style="text-align:center">
                                        <!-- <th>{{ __('shop::app.customer.account.order.view.SKU') }}</th> -->
                    <th class="order_view_heading">{{ __('shop::app.customer.account.order.view.product-name') }}</th>
                    <th class="order_view_heading">{{ __('shop::app.customer.account.order.view.price') }}</th>
                    <th class="order_view_heading">{{ __('shop::app.customer.account.order.view.qty') }}</th>
                    <!-- <th>{{ __('shop::app.customer.account.order.view.subtotal') }}</th> -->
                    <!-- <th>{{ __('shop::app.customer.account.order.view.tax-amount') }}</th> -->
                    <th class="order_view_heading">{{ __('shop::app.customer.account.order.view.total') }}</th>
                    <th class="order_view_heading">Order Notes</th>
                    </tr>
                    </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                {{-- @dd($item) --}}
                                <tr style="text-align:center">
                                    {{-- <td data-value="{{ __('shop::app.customer.account.order.view.SKU') }}">
                            {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
                            </td> --}}
                                    <td class="order_view_data" style="border-right: 1px solid #cccccc !important;">
                                        <div class="row m-0 justify-content-center">
                                            <!-- <div class="col-lg-3 col-md-4 col-4 single_order_img" {{-- style="
                                            max-width: 310px !important;
                                            max-height: 120px !important;" --}}>
                                                 <img class="order-img"
                                                    src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                    alt=""> -->
                                                <!-- <p>{{ $item->additional_notes }} </p> -->
                                            <!-- </div>  -->
                                            <!-- <div class="col-lg-8 col-md-7 col-8"> -->
                                                {{-- @dd($item->additional['special_instruction']); --}}
                                                <div class="customer_single_order_view">
                                                    <span class="order-name" style="font-weight: 500;"> *{{ $item->name }}</span>
                                                    <br><br>
                                                  {{-- sandeep add code --}}
                                                    @php
                                                    $optionLabel = null;
                                                    
                                                    if(isset($item->additional['attributes'])){
                                                    $attributes = $item->additional['attributes'];
                    
                                                      foreach ($attributes as $attribute) {
                                                      if(isset($attribute['option_label']) && $attribute['option_label']!=''){
                                                      $optionLabel = $attribute['option_label'];
                                                    }
                                                  }
                                                }
                                                  @endphp

                                                    @if (isset($optionLabel))
                                                        <p><strong>Preference:
                                                            </strong><span>{{ $optionLabel }}</span>
                                                        </p>
                                                    @endif

                                                    @if (isset($item->additional) &&
                                                            isset($item->additional['special_instruction']) &&
                                                            $item->additional['special_instruction'] != '')
                                                        <p class="m-0"><strong>Special Instruction:</strong>
                                                        <div class="word_wrap" style="
                                                            overflow-y: auto;">
                                                            <span>{{ $item->additional['special_instruction'] }}</span>
                                                        </div>
                                                        </p>
                                                    @endif
                                                </div>
                                            <!-- </div> -->

                                        </div>
                                    </td>

                                    <td data-value="{{ __('shop::app.customer.account.order.view.price') }}"
                                        class="order-price-col order_view_data" style="border-right: 1px solid #cccccc !important;">
                                        @if ($order->status != 'pending')
                                            <div class="order-price mb-2">
                                                {{-- <span>Price: </span> --}}
                                                {{ core()->formatPrice($item->price, $order->order_currency_code) }}
                                            </div>
                                            @else
                                              <span>N/A</span>
                                        @endif
                                    </td>


                                    <td data-value="{{ __('shop::app.customer.account.order.view.price') }}"
                                        class="order-price-col order_view_data" style="border-right: 1px solid #cccccc !important;">
                                        <div class="order-qty">
                                            {{ $item->qty_ordered }}
                                        </div>

                                    </td>


                                    <td class="total-col text-right order_view_data"
                                        data-value="{{ __('shop::app.customer.account.order.view.grand-total') }}" style="border-right: 1px solid #cccccc !important;">
                                        @if ($order->status != 'pending')
                                            {{ core()->formatPrice($item->total, $order->order_currency_code) }}
                                            {{-- <div class="order-tax">
                                                <span class="extra-price">+</span>
                                                <span class="extra-price">{{ $item->tax_amount }}</span>
                                            </div> --}}


                                            @if ($item->discount_amount > 0)
                                                <div class="discount">
                                                    <span class="extra-price">-</span>
                                                    <span
                                                        class="discount extra-price">{{ core()->formatPrice($item->discount_amount, $order->order_currency_code) }}</span>
                                                </div>
                                            @endif

                                            <div class="total">
                                                <span>Item total:
                                                    {{ core()->formatPrice($item->tax_amount + $item->total, $order->order_currency_code) }}</span>

                                            </div>
                                        @else
                                            <div class="order-tax">
                                                <span class="extra-price">N/A</span>
                                            </div>



                                            <!-- <div class="discount">
                                                <span>N/A</span>
                                            </div>


                                            <div class="total">
                                                <span>N/A</span>

                                            </div> -->
                                        @endif

                                    </td>
                                     <!-- sandeep  -->
                                    <td class="order_view_data" style="border-right: 1px solid #cccccc !important;">
                                        <div class="notes">
                                    <p>{{ $item->additional_notes }} </p>
                                    </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="totals price-total-table mt-3">
                @if (isset($admin_notes) && $admin_notes !== null)
                    <div class="col-12 col-md-6 col-lg-6 order__view_admin_comments py-3">
                        <h3 class="text-start mt-2" style="text-align: left;">Notes</h3>
                        <div class="notes mb-4">
                            <div class="table m-0 d-flex">
                                <tbody>
                                    <tr>
                                        <td><strong class="" style="color: rgb(101 101 101);">Support:</strong></td>
                                        <td><span class="pl-2" style="color: #9d9d9d;">{{ $admin_notes->notes }}</span>
                                        </td>
                                        <td>
                                            {{-- sandeep change date time formate --}}
                                            <span class="float-right"
                                                style="color: #9d9d9d;">({{ date('m-d-Y h:i:s A', strtotime($admin_notes->created_at)) }})</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12 col-md-6 col-lg-6">
                    <table class="sale-summary ml-auto">
                        <tr>
                            {{-- <td>{{ __('shop::app.customer.account.order.view.subtotal') }} --}}
                            @if ($order->status != 'pending')
                                <td>Sub-Total:
                                    {{ core()->formatPrice($order->sub_total, $order->order_currency_code) }}
                                </td>
                            @else
                                <td>Sub-Total: N/A</td>
                            @endif
                        </tr>

                        {{-- <tr>
                                    <td>{{ __('shop::app.customer.account.order.view.shipping-handling') }}
                    <span class="dash-icon">-</span>
                    </td>
                    <td>{{ core()->formatPrice($invoice->shipping_amount, $order->order_currency_code)
                                        }}</td>
                    </tr> --}}

                        @if ($order->discount_amount > 0)
                            <tr>
                                {{-- <td>{{ __('shop::app.customer.account.order.view.discount') }}
                        <span class="dash-icon">-</span>
                        </td> --}}
                                <td class="extra-price">Offer Discount:
                                    {{ core()->formatPrice($order->discount_amount, $order->order_currency_code) }}
                                </td>

                            </tr>
                        @endif

                        @if ($order->tax_amount > 0)
                            <tr>
                                @if ($order->status != 'pending')
                                    <td class="extra-price">Tax:
                                        {{ core()->formatPrice($order->tax_amount, $order->order_currency_code) }}</td>
                                @else
                                    <td>Tax: N/A</td>
                                @endif
                            </tr>
                        @endif
                        {{-- sandeep add code --}}
                        <tr class="">
                            {{-- <td>{{ __('shop::app.customer.account.order.view.grand-total') }}
                        <span class="dash-icon">-</span>
                        </td> --}}
                            @if ($order->status != 'pending')
                                <td class="">Agent Handling:
                                    @if (isset($agent) && $agent->Handling_charges != null)
                                   {{ core()->formatBasePrice($agent->Handling_charges) }}
                                @else
                                    {{ core()->formatBasePrice(0) }}
                                @endif
                                </td>

                            @else
                                <td>Agent Handling: N/A</td>
                            @endif


                            {{-- <td></td> --}}
                        </tr>

                        <tr class="fw6">
                            {{-- <td>{{ __('shop::app.customer.account.order.view.grand-total') }}
                        <span class="dash-icon">-</span>
                        </td> --}}
                            @if ($order->status != 'pending')
                                <td class="total-price">Order Total:
                                    @if (isset($agent->Handling_charges))
                                    {{ core()->formatPrice($order->grand_total + $agent->Handling_charges, $order->order_currency_code) }}
                                    @else
                                    {{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}
                                    @endif
                                    
                            @else
                                <td>Order Total: N/A</td>
                            @endif


                            {{-- <td></td> --}}
                        </tr>

                    </table>
                </div>


            </div>

            @if (
                !in_array($order->status, ['pending', 'canceled', 'rejected', 'paid', 'processing']) &&
                    !in_array('paid', $excludedStatuses))
            <div class="payment_button pt-3">
                <form action="{{ route('order-invoice-view-detail', ['orderid'=> $order->id, 'customerid'=> $order->customer_id]) }}" method="POST">
                    @csrf
                    <input type="hidden" name="email" value="{{ $order->customer_email }}">
                    <input type="hidden" name="tail_number" value="{{ $order->fbo_tail_number }}">
                    <button class="invoice_view_pay_button">Pay Now</button>
                </form>
            @endif
        </div>
    </div>
    {{--
        </tab> --}}

    {{-- @if ($order->invoices->count())


        @foreach ($order->invoices as $invoice)

        <div class="sale-section py-2">
            {{-- <div class="section-title">
                <span>{{ __('shop::app.customer.account.order.view.individual-invoice', ['invoice_id' =>
                    $invoice->increment_id ?? $invoice->id]) }}</span>

<a href="{{ route('shop.customer.orders.print', $invoice->id) }}" class="float-right">
    {{ __('shop::app.customer.account.order.view.print') }}
</a>
</div> --}}

    {{-- <div class="section-content">
                <div class="table-responsive">
                    <div class="table order-detail-table">
                        <table> --}}
    {{-- <thead>
                                <tr>
                                    <th>{{ __('shop::app.customer.account.order.view.SKU') }}</th>
<th>{{ __('shop::app.customer.account.order.view.product-name') }}</th>
<th>{{ __('shop::app.customer.account.order.view.price') }}</th>
<th>{{ __('shop::app.customer.account.order.view.qty') }}</th>
<th>{{ __('shop::app.customer.account.order.view.subtotal') }}</th>
<th>{{ __('shop::app.customer.account.order.view.tax-amount') }}</th>
<th>{{ __('shop::app.customer.account.order.view.grand-total') }}</th>
</tr>
</thead> --}}

    {{-- <tbody>

                                @foreach ($invoice->items as $item)

                                <tr> --}}
    {{-- <td data-value="{{ __('shop::app.customer.account.order.view.SKU') }}">
{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}
</td> --}}
    {{-- <td>
                                        <div class="row">
                                            <div class="col-lg-3 col-md-4 col-4">
                                                <img class="order-img"
                                                    src="http://127.0.0.1:8000/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                    alt="">
                                            </div>
                                            <div class="col-lg-8 col-md-7 col-8">
                                                <span class="order-name"> *{{ $item->name }}</span>
</div>

</div>
</td>


<td data-value="{{ __('shop::app.customer.account.order.view.price') }}" class="order-price-col">
    <div class="order-price mb-2">
        <span>Price: </span>
        {{ core()->formatPrice($item->price, $order->order_currency_code) }}
    </div>
    <div class="order-qty">
        <span>Quantity: </span> {{ $item->qty }}
    </div>

</td>
<td>
    <div class="order-address">
        <div class="row">
            <div class="col-lg-1 col-md-1 col-2 text-right p-0">

                <img src="/themes/volantijetcatering/assets/images/location-black.png" class="location-icon" alt="">
            </div>
            <div class="col-lg-10 col-md-10 col-10">
                <span class="airport-name">{{$order->billing_address->airport_name}}</span>
                <br>
                <span class="airport-address">{{$order->billing_address->address1}}</span>
            </div>
        </div>

    </div>
</td> --}}


    {{-- <td data-value="{{ __('shop::app.customer.account.order.view.subtotal') }}">
{{ core()->formatPrice($item->total, $order->order_currency_code) }}
</td> --}}
    {{--
                                    <td data-value="{{ __('shop::app.customer.account.order.view.tax-amount') }}">
{{ core()->formatPrice($item->tax_amount, $order->order_currency_code) }}
</td> --}}

    {{-- <td class="total-col text-right"
                                        data-value="{{ __('shop::app.customer.account.order.view.grand-total') }}">
{{ core()->formatPrice($item->total, $order->order_currency_code) }}
<div class="order-tax">
    <span class="extra-price">-</span>
    <span class="extra-price">{{ $item->tax_amount}}</span>
</div>


@if ($invoice->base_discount_amount > 0)
<div class="discount">
    <span class="extra-price">-</span>
    <span class="discount extra-price">{{
                                                core()->formatPrice($invoice->discount_amount,
                                                $order->order_currency_code) }}</span>
</div>
@endif

<div class="total">
    <span>Item total: {{ core()->formatPrice( $item->tax_amount+$item->total,
                                                $order->order_currency_code) }}</span>

</div>


</>
</tr>
@endforeach
</tbody>
</table>
</div>
</div> --}}

    {{-- <div class="totals price-total-table mt-3">
                    <table class="sale-summary">
                        <tr>
                            {{-- <td>{{ __('shop::app.customer.account.order.view.subtotal') }} --}}
    {{--
                            <td>Sub-Total: {{ core()->formatPrice($invoice->sub_total, $order->order_currency_code) }}
</td>


</tr> --}}

    {{-- <tr>
                            <td>{{ __('shop::app.customer.account.order.view.shipping-handling') }}
<span class="dash-icon">-</span>
</td>
<td>{{ core()->formatPrice($invoice->shipping_amount, $order->order_currency_code) }}</td>
</tr> --}}

    {{-- @if ($invoice->base_discount_amount > 0)
                        <tr> --}}
    {{-- <td>{{ __('shop::app.customer.account.order.view.discount') }}
<span class="dash-icon">-</span>
</td> --}}
    {{-- <td class="extra-price">Offer Discount: {{
                                core()->formatPrice($invoice->discount_amount, $order->order_currency_code) }}</td>

</tr>
@endif

@if ($invoice->tax_amount > 0)
<tr>
    <td class="extra-price">Tax: {{ core()->formatPrice($invoice->tax_amount,
                                $order->order_currency_code) }}</td>
</tr>
@endif
<tr class="fw6"> --}}
    {{-- <td>{{ __('shop::app.customer.account.order.view.grand-total') }}
    <span class="dash-icon">-</span>
    </td> --}}
    {{-- <td class="total-price">Order Total: {{ core()->formatPrice($invoice->grand_total,
                                $order->order_currency_code) }}</td> --}}
    {{-- <td></td> --}}
    {{-- </tr>
                    </table>

                </div>
            </div>
        </div>

        @endforeach


        @endif --}}

    {{-- @if ($order->shipments->count())
        <tab name="{{ __('shop::app.customer.account.order.view.shipments') }}">

    @foreach ($order->shipments as $shipment)

    <div class="sale-section">
        <div class="section-content">
            <div class="row col-12">
                <label class="mr20">
                    {{ __('shop::app.customer.account.order.view.tracking-number') }}
                </label>

                <span class="value">
                    {{ $shipment->track_number }}
                </span>
            </div>
        </div>
    </div>

    <div class="sale-section">
        <div class="section-title">
            <span>{{ __('shop::app.customer.account.order.view.individual-shipment', ['shipment_id' =>
                        $shipment->id]) }}</span>
        </div>

        <div class="section-content">

            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('shop::app.customer.account.order.view.SKU') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.product-name') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.qty') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($shipment->items as $item)

                        <tr>
                            <td data-value="{{  __('shop::app.customer.account.order.view.SKU') }}">{{
                                        $item->sku }}</td>
                            <td data-value="{{  __('shop::app.customer.account.order.view.product-name') }}">{{
                                        $item->name }}</td>
                            <td data-value="{{  __('shop::app.customer.account.order.view.qty') }}">{{
                                        $item->qty }}</td>
                        </tr>

                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @endforeach

    </tab>
    @endif --}}

    {{-- @if ($order->refunds->count())
        <tab name="{{ __('shop::app.customer.account.order.view.refunds') }}">

    @foreach ($order->refunds as $refund)

    <div class="sale-section">
        <div class="section-title">
            <span>{{ __('shop::app.customer.account.order.view.individual-refund', ['refund_id' => $refund->id])
                        }}</span>
        </div>

        <div class="section-content">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            <th>{{ __('shop::app.customer.account.order.view.SKU') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.product-name') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.price') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.qty') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.subtotal') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.tax-amount') }}</th>
                            <th>{{ __('shop::app.customer.account.order.view.grand-total') }}</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach ($refund->items as $item)
                        <tr>
                            <td data-value="{{ __('shop::app.customer.account.order.view.SKU') }}">{{
                                        $item->child ? $item->child->sku : $item->sku }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.product-name') }}">{{
                                        $item->name }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.price') }}">{{
                                        core()->formatPrice($item->price, $order->order_currency_code) }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.qty') }}">{{ $item->qty
                                        }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.subtotal') }}">{{
                                        core()->formatPrice($item->total, $order->order_currency_code) }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.tax-amount') }}">{{
                                        core()->formatPrice($item->tax_amount, $order->order_currency_code) }}</td>
                            <td data-value="{{ __('shop::app.customer.account.order.view.grand-total') }}">{{
                                        core()->formatPrice($item->total + $item->tax_amount,
                                        $order->order_currency_code) }}</td>
                        </tr>
                        @endforeach

                        @if (!$refund->items->count())
                        <tr>
                            <td class="empty" colspan="7">{{ __('shop::app.common.no-result-found') }}</td>
                        <tr>
                            @endif
                    </tbody>
                </table>
            </div>

            <div class="totals">
                <table class="sale-summary">
                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.subtotal') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->sub_total, $order->order_currency_code) }}</td>
                    </tr>

                    @if ($refund->shipping_amount > 0)
                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.shipping-handling') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->shipping_amount, $order->order_currency_code) }}
                        </td>
                    </tr>
                    @endif

                    @if ($refund->discount_amount > 0)
                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.discount') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($order->discount_amount, $order->order_currency_code) }}</td>
                    </tr>
                    @endif

                    @if ($refund->tax_amount > 0)
                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.tax') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->tax_amount, $order->order_currency_code) }}</td>
                    </tr>
                    @endif

                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.adjustment-refund') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->adjustment_refund, $order->order_currency_code) }}
                        </td>
                    </tr>

                    <tr>
                        <td>{{ __('shop::app.customer.account.order.view.adjustment-fee') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->adjustment_fee, $order->order_currency_code) }}</td>
                    </tr>

                    <tr class="fw6">
                        <td>{{ __('shop::app.customer.account.order.view.grand-total') }}
                            <span class="dash-icon">-</span>
                        </td>
                        <td>{{ core()->formatPrice($refund->grand_total, $order->order_currency_code) }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    @endforeach

    </tab>
    @endif --}}
    </tabs>

    <div class="sale-section">
        <div class="section-content" style="border-bottom: 0">
            <div class="order-box-container">

                {{-- @if ($order->billing_address)
                <div class="box">
                    <div class="box-title">
                        {{ __('shop::app.customer.account.order.view.billing-address') }}
            </div>

            <div class="box-content">
                @include ('admin::sales.address', ['address' => $order->billing_address])

                {!! view_render_event('bagisto.shop.customers.account.orders.view.billing-address.after',
                ['order' => $order]) !!}
            </div>
        </div>
        @endif --}}

                {{-- @if ($order->shipping_address)
                <div class="box">
                    <div class="box-title">
                        {{ __('shop::app.customer.account.order.view.shipping-address') }}
    </div>

    <div class="box-content">
        @include ('admin::sales.address', ['address' => $order->shipping_address])

        {!! view_render_event('bagisto.shop.customers.account.orders.view.shipping-address.after',
        ['order' => $order]) !!}
    </div>
    </div>

    <div class="box">
        <div class="box-title">
            {{ __('shop::app.customer.account.order.view.shipping-method') }}
        </div>

        <div class="box-content">
            {{ $order->shipping_title }}

            {!! view_render_event('bagisto.shop.customers.account.orders.view.shipping-method.after',
            ['order' => $order]) !!}
        </div>
    </div>
    @endif --}}

                {{-- <div class="box">
                    <div class="box-title">
                        {{ __('shop::app.customer.account.order.view.payment-method') }}
    </div>

    <div class="box-content">
        {{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') }}

        @php $additionalDetails =
        \Webkul\Payment\Payment::getAdditionalDetails($order->payment->method); @endphp

        @if (!empty($additionalDetails))
        <div class="instructions">
            <label>{{ $additionalDetails['title'] }}</label>
            <p>{{ $additionalDetails['value'] }}</p>
        </div>
        @endif

        {!! view_render_event('bagisto.shop.customers.account.orders.view.payment-method.after',
        ['order' => $order]) !!}
    </div>
    </div> --}}
            </div>
        </div>
    </div>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.orders.view.after', ['order' => $order]) !!}
@endsection

@push('scripts')
    <script>
        function cancelOrder(message) {
            if (!confirm(message)) {
                return;
            }

            $('#cancelOrderForm').submit();
        }
    </script>
@endpush
