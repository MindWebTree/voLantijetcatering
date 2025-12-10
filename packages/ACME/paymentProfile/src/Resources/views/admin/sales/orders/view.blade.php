@extends('admin::layouts.master')

@section('page_title')
{{ __('admin::app.sales.orders.view-title', ['order_id' => $order->id]) }}
@stop
@section('head')


@include('paymentprofile::admin.links')

@stop

@section('content-wrapper')

<div class="content full-page order_view_fullpage">
    {{-- ------------------------------------------------------custom Order--------------------------------------------------- --}}

    @php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    use Webkul\Checkout\Facades\Cart;
    use Illuminate\Support\Facades\Request;
    use Webkul\Checkout\Repositories\CartRepository;
    use ACME\paymentProfile\Models\OrderNotes;
    use Carbon\Carbon;

    $guestToken = Session::token();
    $airportArr = DB::table('delivery_location_airports')->pluck('name')->toArray();

    // Retrieve the guest session ID
    $guestSessionId = Session::getId();
    $cartItems = Session::get('cart');

    $customer = auth()->guard('customer')->user();

    if (Auth::check()) {
    $islogin = 1;
    $address = Db::table('addresses')
    ->where('customer_id', $customer->id)
    ->where('address_type', 'customer')
    ->first();
    } else {
    $islogin = 0;
    $address = Db::table('addresses')
    ->where('customer_token', $guestToken)
    ->where('address_type', 'customer')
    ->first();
    }

    if (isset($order->delivery_date)) {
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
    $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    // Get the day of the week name
    $dayName = $daysOfWeek[$dayOfWeek];

    // Get the month
    $month = $dateObj->format('n');

    // Get the day of the month
    $dayOfMonth = $dateObj->format('j');

    // Format the date string
    $formattedDate = $dayName . ' ' . $month . '/' . $dayOfMonth;
    }
    }
    $customerId = $order->customer_id;

    $customer = DB::table('customers')

    ->where('id', $customerId)

    ->first();

    $dateOfBirth = $customer->date_of_birth ?? null;

    @endphp

    <div class="page-header order_view_header">
        <div class="page-title order_view_title">
            <h1 class="text-secondary">
                {!! view_render_event('sales.order.title.before', ['order' => $order]) !!}

                <i class="icon angle-left-icon back-link mb-1" onclick="window.location = '{{ route('admin.sales.order.index') }}'"></i>

                Order ID: <strong id="orderID" class="text-dark">{{ $order->id }}</strong>
                {!! view_render_event('sales.order.title.after', ['order' => $order]) !!}
            </h1>
        </div>

        <div class="page-action order_view_status" style="margin-top:0px !important">
            <p class="text-secondary">Order Status: <span class="text-uppercase {{ $order->status }}">{{ $order->status }}</span></p>

            {{-- @dd($states) --}}
        </div>
    </div>
    <div class="page-content">
        {!! view_render_event('sales.order.tabs.before', ['order' => $order]) !!}
        <div class="row order__view__content">
            <div class="col-sm-12 col-md-8 {{ auth('admin')->user()->role_id != 1 ? 'col-lg-12' : 'col-lg-8' }} order__view__left border-right">
                <div class="row my-3 justify-content-start ml-auto" style="column-gap: 0;">
                    <div class="col-sm-12 col-md-6 col-lg-4 customer__info p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row fbo__detail">
                                <div class="col-9 text-break">
                                    <h5 class="mt-3">{{ __('shop::app.fbo-detail.client-info') }}</h5>
                                    <p class="m-0">{{ $order->fbo_full_name }}</p>
                                    <p class="m-0">{{ $order->fbo_email_address }}</p>
                                    <p class="m-0">{{ $order->fbo_phone_number }}</p>
                                    <p class="m-0"> Date of birth : {{ $dateOfBirth ?  $dateOfBirth : 'Not found'}}</p>
                                    <h5 class="mt-3">{{ __('shop::app.fbo-detail.aircraft-info') }}</h5>
                                    <p class="m-0">{{ $order->fbo_tail_number }}</p>
                                    <p class="m-0">Packaging: {{ $order->fbo_packaging }}</p>
                                    <p class="m-0">Service Packaging: {{ $order->fbo_service_packaging }}</p>
                                    <h5 class="mt-3">Delivery Time</h5>
                                    <p class="m-0">Delivery Date: {{ date('m-d-Y', strtotime($order->delivery_date)) }}</p>
                                    <p>Delivery Time: {{ $order->delivery_time }}</p>

                                </div>
                                <div class='col-3 fbo-edit text-right p-3'>
                                    @if (
                                    $order->status != 'canceled' &&
                                    $order->status != 'rejected' &&
                                    $order->status != 'delivered' &&
                                    $order->status != 'paid' &&
                                    $order->status != 'shipped' &&
                                    auth('admin')->user()->role_id == 1)
                                    <span class="text-danger pointer" data-toggle="modal" data-target="#fboModal">edit</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 fbo__details  mt-md-0 mt-lg-0 p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row search-address">
                                <div class="col-9 text-break">
                                    <h5 class="mt-3">Address</h5>
                                    @if (isset($order->shipping_address))
                                    <strong style="font-size:15px">{{ $order->shipping_address->airport_name }}</strong>
                                    <p> {{ $order->shipping_address->address1 }} </p>
                                    @if (isset($airport_fbo))
                                    <h5 class="mt-3">Airport FBO</h5>
                                    <input type="hidden" id="airport_fbo_airport_id" value="{{ $airport_fbo->airport_id }}" attr="{{ $order->customer_id }}">
                                    <p class="m-0">{{ $airport_fbo->name }}</p>
                                    <p class="m-0">{{ $airport_fbo->address }}</p>
                                    @endif
                                    @endif
                                </div>
                                <div class="col-3 text-right p-3">

                                    @if (
                                    $order->status != 'canceled' &&
                                    $order->status != 'rejected' &&
                                    $order->status != 'delivered' &&
                                    $order->status != 'paid' &&
                                    $order->status != 'shipped' &&
                                    auth('admin')->user()->role_id == 1)
                                    <span class="text-danger pointer edit-airport click-edit-airport" data-toggle="modal" data-target="#exampleModal">edit</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    @if (auth('admin')->user()->role_id == 2 && isset($shipment))
                    <div class="col-sm-12 col-md-8 col-lg-4 fbo__details  mt-md-3 mt-lg-0 p-2">
                        <a href="{{ route('admin.paymentprofile.shipments.view', $shipment->id) }}" style="font-size: 17px">View or Upload Images</a>
                    </div>
                    @endif

                    {{-- billing address --}}
                    @if (auth('admin')->user()->role_id == 1)


                    <div class="col-sm-12 col-md-6 col-lg-4 fbo__details  mt-md-3 mt-lg-0 p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row search-address">
                                <div class="col-9 text-break">
                                    <h5 class="mt-3">Billing Address</h5>
                                    {{-- <strong>{{ $order->billing_address->airport_name }}</strong> --}}
                                    @if (isset($order->billing_address) && $order->billing_address->address1 != null)
                                    <p class="m-0">
                                        {{ isset($order->billing_address->address1) ? $order->billing_address->address1 . ',' : '' }}
                                        {{-- {{ isset($order->billing_address->city) ? $order->billing_address->city . ',' : '' }}
                                        {{ isset($order->billing_address->postcode) ? $order->billing_address->postcode . ',' : '' }}
                                        {{ isset($order->billing_address->state) ? $order->billing_address->state : '' }} --}}
                                        {{ isset($order->billing_address->address2) ? $order->billing_address->address2 . ',' : '' }}
                                    </p>
<p class="m-0"><strong>Delivery Date:</strong>
    {{ isset($order->delivery_date) ? \Carbon\Carbon::parse($order->delivery_date)->format('l n/j') : '' }}
</p>
<p class="m-0"><strong>Delivery Time:</strong>
    {{ isset($order->delivery_time) ? $order->delivery_time : '' }}
</p>

                                    {{-- <p class="m-0">Phone
                                        {{ isset($order->billing_address->phone) ? $order->billing_address->phone : '' }}
                                    </p> --}}
                                    {{-- <p class="m-0">Phone:
                                        {{ isset($order->billing_address->phone) ? $order->billing_address->phone : '' }}
                                    </p>
                                    @if (isset($order->billing_address->vat_id))
                                    <p class="m-0">Vat:
                                        {{ isset($order->billing_address->vat_id) ? $order->billing_address->vat_id : '' }}
                                    </p>
                                    @endif --}}

                                    @endif
                                </div>
                                <div class="col-3 text-right p-3">
                                    @if (
                                    $order->status != 'canceled' &&
                                    $order->status != 'delivered' &&
                                    $order->status != 'rejected' &&
                                    $order->status != 'paid' &&
                                    $order->status != 'shipped')
                                    @if (isset($order->billing_address) && $order->billing_address->address1 != null)
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#billingAddress">edit</span>
                                    @else
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#billingAddress">Add</span>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 fbo__details  mt-md-3 mt-lg-0 p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row search-address">
                                <div class="col-9 text-break">
                                    <h5 class="mt-3">Handling Agent</h5>
                                    {{-- <strong>{{ $order->billing_address->airport_name }}</strong> --}}
                                    <p class="m-0"> {{ isset($agent->Name) ? $agent->Name : '' }}</p>
                                    <p class="m-0"> {{ isset($agent->Mobile) ? $agent->Mobile : '' }}</p>
                                    <p class="m-0"> {{ isset($agent->PPR_Permit) ? $agent->PPR_Permit : '' }}
                                    </p>
                                    <p class="m-0">
                                        @if (isset($agent->Handling_charges))
                                        {{ core()->formatBasePrice($agent->Handling_charges) }}
                                        @endif
                                    </p>



                                </div>
                                <div class="col-3 text-right p-3">
                                    @if (
                                    $order->status != 'canceled' &&
                                    $order->status != 'delivered' &&
                                    $order->status != 'rejected' &&
                                    $order->status != 'paid' &&
                                    $order->status != 'shipped')
                                    @if (isset($agent->Name) && $agent->Name != '')
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#handlingAgent">edit</span>
                                    @else
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#handlingAgent">add</span>
                                    @endif

                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="col-sm-12 col-md-6 col-lg-4 fbo__details  mt-md-3 mt-lg-0 p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row search-address">
                                <div class="col-9">
                                    <h5 class="mt-3">Purchase Order No.</h5>

                                    <p> {{ $order->purchase_order_no }}
                                    </p>
                                </div>
                                <div class="col-3 text-right p-3">
                                    @if (
                                    $order->status != 'canceled' &&
                                    $order->status != 'delivered' &&
                                    $order->status != 'rejected' &&
                                    $order->status != 'paid' &&
                                    $order->status != 'shipped')
                                    @if ($order->purchase_order_no == '')
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#Purchase_number">Add</span>
                                    @else
                                    <span class="text-danger pointer edit-airport" data-toggle="modal" data-target="#Purchase_number">edit</span>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- sandeep add deliverd status --}}
                    @if (in_array($order->status, ['shipped', 'paid','delivered']) && isset($deliver_partner) && isset($shipment))
                    <div class="col-sm-12 col-md-8 col-lg-4 fbo__details  mt-md-3 mt-lg-0 p-2">
                        <div class="border p-3 details_grid_wrapper">
                            <div class="row search-address">
                                <div class="col-9">
                                    <h5 class="mt-3">Shipment</h5>
                                    <p class="m-0"> {{ $deliver_partner->name }}</p>
                                    <p class="m-0"> {{ $deliver_partner->email }}</p>
                                    <p class="m-0"> {{ date('m-d-Y h:i:s A', strtotime($shipment->created_at)) }}</p>
                                    <p class="m-0">
                                        @isset($shipment->carrier_title)
                                        {{ $shipment->carrier_title }}
                                        @endisset
                                    </p>
                                    <p class="m-0">
                                        @isset($shipment->track_number)
                                        {{ $shipment->track_number }}
                                        @endisset
                                    </p>

                                </div>
                                <div class="col-3 text-right p-3">
                                    <a href="{{ route('admin.paymentprofile.shipments.view', $shipment->id) }}" class="text-danger pointer edit-airport">View</a>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif


                    @endif

                    {{-- end billing address --}}
                </div>
                {{-- fbo modal start --}}

                <div class="modal fade" id="fboModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header fbo-header validate_form">
                                <h5 class="fs24 fw6 pl-2">
                                    {{ __('shop::app.fbo-detail.fbo-head') }}
                                </h5>
                                <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body popup-content modal__height">
                                <div class="body col-12 border-0">
                                    <form id="formFboValidate" class="fbo_cutomer_form" action="{{ route('order-view.fbo-update', $order->increment_id) }}" method="post">
                                        {{ csrf_field() }}
                                        <div class="row mb-3">
                                            <div class="col-12 mb-3">
                                                <h4 class="fs24 fw6 text-dark text-center">
                                                    {{ __('shop::app.fbo-detail.client-info') }}</h4>
                                            </div>
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('fullname') ? 'has-error' : '']">
                                                <label for="fullname" class="required label-style mandatory">
                                                    {{ __('shop::app.fbo-detail.fullname') }}
                                                </label>
                                                <input type="text" class="form-control form-control-lg" value="{{ $order->fbo_full_name }}" v-validate="'required'" name='fullname' />
                                                <span class="control-error" v-if="errors.has('fullname')" v-text="errors.first('fullname')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('phonenumber') ? 'has-error' : '']">
                                                <label for="phone number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.phone-number') }}
                                                </label>

                                                <input type="text" class="form-control form-control-lg usa_mobile_number" value="{{ $order->fbo_phone_number }}" name="phonenumber" id="customer_mobile" v-validate="'required'" />
                                                <span class="" style="color:#FC6868;" id="customermobile-error"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('email') ? 'has-error' : '']">
                                                <label for="email" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.email-address') }}
                                                </label>
                                                <input type="email" class="form-control form-control-lg" value="{{ $order->fbo_email_address }}" name="email" v-validate="'required'" />
                                                <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <div class="col-12 mb-3">
                                                <h4 class="fs24 fw6 text-dark text-center">
                                                    {{ __('shop::app.fbo-detail.aircraft-info') }}</h1>
                                            </div>
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('tailnumber') ? 'has-error' : '']">
                                                <label for="tail number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.tail-number') }}
                                                </label>
                                                <input type="text" class="form-control form-control-lg" value="{{ $order->fbo_tail_number }}" name="tailnumber" v-validate="'required'">
                                                <span class="control-error" v-if="errors.has('tailnumber')" v-text="errors.first('tailnumber')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 mb-3 packagingsection" :class="[errors.has('packagingsection') ? 'has-error' : '']">

                                                <label for="packaging section" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.packaging-section') }}
                                                </label>

                                                <div class="custom-dropdown">
                                                    <select class="form-control form-control-lg packagingsection" name="packagingsection" v-validate="'required'">
                                                        <option value="" disabled>Select Packaging</option>
                                                        <option value="Microwave" {{ $order->fbo_packaging == 'Microwave' ? 'selected' : '' }}>
                                                            Microwave</option>
                                                        <option value="Oven" {{ $order->fbo_packaging == 'Oven' ? 'selected' : '' }}>
                                                            Oven</option>
                                                        <option value="Both" {{ $order->fbo_packaging == 'Both' ? 'selected' : '' }}>
                                                            Both</option>
                                                    </select>
                                                </div>
                                                <span class="control-error" v-if="errors.has('packagingsection')" v-text="errors.first('packagingsection')"></span>
                                            </div>


                                            {{-- Service packaging --}}
                                            <div class="control-group col-sm-12 col-md-6 mb-3 packagingsection" :class="[errors.has('packagingsection') ? 'has-error' : '']">

                                                <label for="packaging section" class="required label-style">
                                                    Service Packaging
                                                </label>

                                                <div class="custom-dropdown">
                                                    <select class="form-control form-control-lg packagingsection" name="servicePackaging" v-validate="'required'">
                                                        <option disabled>Service Packaging</option>
                                                        <option value="Bulk Packaging" {{ $order->fbo_service_packaging == 'Bulk Packaging' ? 'selected' : '' }}>
                                                            Bulk Packaging</option>
                                                        <option value="Ready For Services" {{ $order->fbo_service_packaging == 'Ready For Services' ? 'selected' : '' }}>
                                                            ready For Services</option>
                                                    </select>
                                                </div>
                                                <span class="control-error" v-if="errors.has('packagingsection')" v-text="'The packaging section field is required'"></span>
                                            </div>
                                        </div>
                                        <h4 class="fs24 fw6 text-dark text-center">Delivery Time</h4>
                                        <div class="row">
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('delivery_date') ? 'has-error' : '']">
                                                <label for="tail number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.fbo-delivery-date') }}
                                                </label>
<input type="text"
       class="form-control form-control-lg js-day-select"
       value="{{ isset($formattedDate) ? $formattedDate : '' }}"
       name="delivery_date"
       readonly
       v-validate="'required'">

<div class="delivery_select_date delivery_select">
    <ul class="js-day-list"></ul>
</div>

                                                {{-- <span class="control-error" v-if="errors.has('delivery_date')"
                                                        v-text="errors.first('delivery_date')"></span> --}}
                                                <span class="fbo_add_error_date text-danger"></span>

                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('delivery_time') ? 'has-error' : '']">
                                                <label for="tail number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.fbo-delivery-time') }}
                                                </label>
<input type="text"
       class="form-control form-control-lg js-time-select"
       value="{{ $order->delivery_time }}"
       name="delivery_time"
       readonly
       v-validate="'required'">

<div class="delivery_select_time delivery_select">
    <ul class="js-time-list"></ul>
</div>
                                                <span class="fbo_add_error_time text-danger"></span>
                                                {{-- <span class="control-error" v-if="errors.has('delivery_time')"
                                                        v-text="errors.first('delivery_time')"></span> --}}
                                            </div>




                                        </div>

                                        <button class="fbo-btn mt-3 m-auto fbo_detail_submit" type="submit">
                                            {{ __('shop::app.fbo-detail.fbo-update') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- fbo modal end --}}

                {{-- billing address modal --}}
                <div class="modal fade" id="billingAddress" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header fbo-header">
                                <h5 class="fs24 fw6 pl-2">
                                    {{ __('shop::app.billing-address.page-head') }}
                                </h5>
                                <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body popup-content">
                                <div class="body col-12 border-0 p-3">
                                    <form class="Billingform" action="{{ route('order-view.update-billing-address') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $order->id }}" name="order_id">

                                <div class="row mb-3">
                                    <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3">
                                        <label for="Address" class="required label-style mandatory">
                                            {{ __('shop::app.billing-address.Address') }}
                                        </label>
                                        <input type="text"
                                            class="form-control form-control-lg"
                                            value="{{ $order->billing_address->address1 ?? '' }}"
                                            name="Address"
                                            required />
                                    </div>

                                    <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3">
                                        <label for="Address2" class="required label-style mandatory">
                                            Address 2
                                        </label>
                                        <input type="text"
                                            class="form-control form-control-lg"
                                            value="{{ $order->billing_address->address2 ?? '' }}"
                                            name="Address2"
                                            required />
                                    </div>
                                </div>

                                <h4 class="fs24 fw6 text-dark text-center">Delivery Time</h4>

                                <div class="row">
                                    <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3">
                                        <label class="required label-style">
                                            {{ __('shop::app.fbo-detail.fbo-delivery-date') }}
                                        </label>
                                       <input type="text"
       class="form-control form-control-lg js-day-select"
       value="{{ $formattedDate ?? '' }}"
       name="delivery_date"
       readonly
       required>

<div class="delivery_select_date delivery_select">
    <ul class="js-day-list"></ul>
</div>
                                        <span class="fbo_add_error_date text-danger"></span>
                                    </div>

                                    <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3">
                                        <label class="required label-style">
                                            {{ __('shop::app.fbo-detail.fbo-delivery-time') }}
                                        </label>
                                        
<input type="text"
       class="form-control form-control-lg js-time-select"
       value="{{ $order->delivery_time }}"
       name="delivery_time"
       readonly
       required>

<div class="delivery_select_time delivery_select">
    <ul class="js-time-list"></ul>
</div>
                                        <span class="fbo_add_error_time text-danger"></span>
                                    </div>
                                </div>

                                <button class="fbo-btn mt-3 m-auto fbo_detail_submit" type="submit">
                                    {{ __('shop::app.fbo-detail.fbo-update') }}
                                </button>
                            </form>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                {{-- fbo modal end --}}

                                {{-- billing address modal --}}
                <div class="modal fade" id="billingAddresscbcxbcvbcvbxcbxvbxcv" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header fbo-header">
                                <h5 class="fs24 fw6 pl-2">
                                    {{ __('shop::app.billing-address.page-head') }}
                                </h5>
                                <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body popup-content">
                                <div class="body col-12 border-0 p-3">
                                    <form class="Billingform" action="{{ route('order-view.update-billing-address') }}" method="post">
                                        {{ csrf_field() }}
                                        <input type="hidden" value="{{ $order->id }}" name="order_id">
                                        <div class="row mb-3">
                                            <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('Address') ? 'has-error' : '']">
                                                <label for="Address" class="required label-style mandatory">
                                                    {{ __('shop::app.billing-address.Address') }}
                                                </label>
                                                <input type="text" class="form-control form-control-lg" value="{{ isset($order->billing_address->address1) ? $order->billing_address->address1 : '' }}" v-validate="'required'" name='Address' required />
                                                <span class="control-error" v-if="errors.has('Address')" v-text="errors.first('Address')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('Address2') ? 'has-error' : '']">
                                                <label for="Address2" class="label-style">
                                                    {{ __('shop::app.billing-address.address_2') }}
                                                </label>
                                                <input type="text" class="form-control form-control-lg" value="{{ isset($order->billing_address->address2) ? $order->billing_address->address2 : '' }}" name='Address2' />
                                                <span class="control-error" v-if="errors.has('Address2')" v-text="errors.first('Address2')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('postCode') ? 'has-error' : '']">
                                                <label for="phone number" class="required label-style">
                                                    {{ __('shop::app.billing-address.postCode') }}
                                                </label>
                                                <input type="Number" class="form-control form-control-lg" value="{{ isset($order->billing_address->postcode) ? $order->billing_address->postcode : '' }}" name="postCode" v-validate="'required'" required />
                                                <span class="control-error" v-if="errors.has('postCode')" v-text="errors.first('postCode')"></span>
                                            </div>

                                            {{-- <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('select-country') ? 'has-error' : '']">
                                                    {{-- <label for="email" class="required label-style">
                                                        {{ __('shop::app.billing-address.Select-country') }}
                                            </label> --}}


                                            {{-- <div class="custom-dropdown">
                                                            <select class="form-control form-control-lg packagingsection"
                                                                name="select-country" v-validate="'required'">
                                                                <option value="" disabled>Select Country</option>
                                                                @foreach ($countries as $country)
                                                                <option value="{{ $country->code}}"
                                            {{ $country->code == 'Wrappers' ? 'selected' : '' }}>
                                            {{$country->name}}</option>
                                            @endforeach

                                            </select>
                                        </div> --}}
                                        {{-- <span class="control-error" v-if="errors.has('select-country')"
                                                            v-text="'The packaging section field is required'"></span> --}}


                                        {{-- </div> --}}
                                        <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('Select-State') ? 'has-error' : '']">
                                            <label for="tail number" class="required label-style">
                                                {{ __('shop::app.billing-address.Select-State') }}
                                            </label>

                                            <div class="custom-dropdown">
                                                <select class="form-control form-control-lg packagingsection" name="Select_State" v-validate="'required'">
                                                    <option value="" disabled>Select State</option>
                                                    @foreach ($states as $state)
                                                    <option value="{{ $state->code }}" {{ isset($order->billing_address->state) && $state->code == $order->billing_address->state ? 'selected' : '' }}>
                                                        {{ $state->default_name }}</option>
                                                    @endforeach

                                                </select>
                                            </div>
                                            <span class="control-error" v-if="errors.has('Select-State')" v-text="'The packaging section field is required'"></span>

                                        </div>


                                        {{-- <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('city') ? 'has-error' : '']">
                                            <label for="city" class="required label-style mandatory">
                                                {{ __('shop::app.billing-address.city') }}
                                            </label>
                                            <input type="text" required class="form-control form-control-lg" value="{{ isset($order->billing_address->city) ? $order->billing_address->city : '' }}" v-validate="'required'" name='city' />
                                            <span class="control-error" v-if="errors.has('city')" v-text="errors.first('city')"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('mobile') ? 'has-error' : '']">
                                            <label for="mobile" class="required label-style mandatory">
                                                {{ __('shop::app.billing-address.mobile') }}
                                            </label>
                                            <input type="text" required class="form-control form-control-lg usa_mobile_number" value="{{ isset($order->billing_address->phone) ? $order->billing_address->phone : '' }}" v-validate="'required'" name='mobile' id="BillingMobile" />
                                            <span class="" style="color:#FC6868;" id="billingMobile-error"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('vat') ? 'has-error' : '']">
                                            <label for="vat" class="label-style mandatory">
                                                {{ __('shop::app.billing-address.vat') }}
                                            </label>
                                            <input type="text" class="form-control form-control-lg" value="{{ isset($order->billing_address->vat_id) ? $order->billing_address->vat_id : '' }}" name='vat' />
                                            <span class="control-error" v-if="errors.has('vat')" v-text="errors.first('vat')"></span>
                                        </div> --}}

                                        {{-- <button class="fbo-btn mt-3 m-auto billing_submit_form" type="submit">
                                            {{ __('shop::app.fbo-detail.fbo-update') }}
                                        </button> --}}
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



            </div>

            <div class="modal fade" id="handlingAgent" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header fbo-header">
                            <h5 class="fs24 fw6 pl-2">
                                {{ __('shop::app.Handling-agent.page-head') }}
                            </h5>
                            <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body popup-content">
                            <div class="body col-12 border-0 p-3">
                                <form class="Agentform" action="{{ route('order-view.add-handler-agent') }}" method="post">
                                    {{ csrf_field() }}
                                    <input type="hidden" value="{{ $order->id }}" name="order_id">
                                    <div class="row mb-3">




                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('name') ? 'has-error' : '']">
                                            <label for="name" class="required label-style mandatory">
                                                {{ __('shop::app.Handling-agent.name') }}
                                            </label>
                                            <input type="text" required class="form-control form-control-lg" value="{{ isset($agent->Name) ? $agent->Name : '' }}" v-validate="'required'" name='name' />
                                            <span class="control-error" v-if="errors.has('name')" v-text="errors.first('name')"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('mobile') ? 'has-error' : '']">
                                            <label for="mobile" class="required label-style mandatory">
                                                {{ __('shop::app.Handling-agent.mobile') }}
                                            </label>
                                            <input type="text" required class="form-control form-control-lg usa_mobile_number" value="{{ isset($agent->Mobile) ? $agent->Mobile : '' }}" id="mobile" v-validate="'required'" name='mobile' />
                                            <span class="" style="color:#FC6868;" id="mobile-error"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('ppr_permit') ? 'has-error' : '']">
                                            <label for="ppr_permit" class="required label-style mandatory">
                                                {{ __('shop::app.Handling-agent.ppr-permit') }}
                                            </label>
                                            <input type="text" required class="form-control form-control-lg" value="{{ isset($agent->PPR_Permit) ? $agent->PPR_Permit : '' }}" v-validate="'required'" name='ppr_permit' />
                                            <span class="control-error" v-if="errors.has('ppr_permit')" v-text="errors.first('ppr_permit')"></span>
                                        </div>
                                        <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('Handling_charges') ? 'has-error' : '']">
                                            <label for="Handling_charges" class="required label-style mandatory">
                                                {{ __('shop::app.Handling-agent.Handling-charges') }}
                                            </label>
                                            <input type="number" required class="form-control form-control-lg" value="{{ isset($agent->Handling_charges) ? $agent->Handling_charges : '' }}" v-validate="'required'" name='handling_charges' step="any" />
                                            <span class="control-error" v-if="errors.has('Handling_charges')" v-text="errors.first('Handling_charges')"></span>
                                        </div>

                                        <button class="fbo-btn mt-3 m-auto handling_form_submit" type="submit">
                                            {{ __('shop::app.fbo-detail.fbo-update') }}
                                        </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="modal fade" id="Purchase_number" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header fbo-header">
                        <h5 class="fs24 fw6 pl-2">
                            {{ __('shop::app.Purchase_order_no.page-head') }}
                        </h5>
                        <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body popup-content">
                        <div class="body col-12 border-0 p-3">
                            <form action="{{ route('order-view.update-purchase-no') }}" method="post">
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $order->id }}" name="order_id">
                                <div class="row mb-3">
                                    <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('Purchase_order_no') ? 'has-error' : '']">
                                        <label for="Purchase_order_no" class="required label-style mandatory">
                                            {{ __('shop::app.Purchase_order_no.name') }}
                                        </label>
                                        <input type="text" class="form-control form-control-lg" value="{{ $order->purchase_order_no }}" v-validate="'required'" name='Purchase_order_no' />
                                        <span class="control-error" v-if="errors.has('Purchase_order_no')" v-text="errors.first('purchase')"></span>
                                    </div>
                                </div>

                                <button class="fbo-btn mt-3 m-auto" type="submit">
                                    {{ __('shop::app.Purchase_order_no.Purchase_order_no_button') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{-- enc billing address modal --}}
        {{-- fbo modal end --}}

        {{-- airport modal start --}}
        {{-- @dd($order->billing_address) --}}

        {{-- <form action="">
                @csrf --}}
        <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header address-header">

                        <h5>Select location</h5>

                        <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body address__body">
                        <div class="input-group d-block">
                            <div class="search-content">
                                <div class="row search_wrapper">
                                    <div class="col-lg-9 col-md-9 col-8  pr-0">
                                        <input type="text" id="auto_search" placeholder="Search Delivery Location" class="form-control" attr="{{ isset($airport_fbo) ? $airport_fbo->airport_id : '' }}" value="{{ isset($order->shipping_address->airport_name) ? $order->shipping_address->airport_name : '' }}">
                                        <div id="address-list" class="suggestion-list"></div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-4">
                                        <input type="hidden" id="airport_id" value="{{ isset($order->shipping_address) ? $order->shipping_address->airport_name : '' }}">

                                        <button class="btn btn-danger m-auto address-btn" disabled type="button" id="address_update">Search</button>
                                    </div>
                                </div>
                                {{-- 21-05-2024 || airport fbo detail dropdown from home page start --}}
                                <div class="row  airport__fbo__detail">
                                    <div class="search-content col-12 pr-0 pl-0 padding ">
                                        <div class="searchbar">
                                            <div class="search__wrapper">
                                                <input type="text" id="airport-fbo-input" class="form-control pr-2 pl-3 pointer" attr="{{ isset($airport_fbo) ? $airport_fbo->id : '' }}" placeholder="Aiport Fbo Detail" readonly @if (isset($airport_fbo)) value="{{ $airport_fbo->name }}" @endif>
                                                <img class="Navigation-image pointer pr-2" id="airport-fbo-input" src="{{ asset('themes/volantijetcatering/assets/images/home/down-arrow.svg') }}" alt="" height="20px" />
                                            </div>
                                            <input type="hidden" id="selected-fbo-id" name="selected_fbo_id" @if (isset($airport_fbo)) value="{{ $airport_fbo->id }}" @endif>
                                            <div id="airport-fbo-list" class="custom-dropdown-list text-justify d-none mx-3">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- 21-05-2024 || airport fbo detail dropdown from home page end --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal fade add_fbo_modal pl-0 pr-0" id="add_fbo_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-center" id="exampleModalCenterTitle">
                            <img class="Navigation-image" src="{{ asset('themes/volantijetcatering/assets/images/home/store.svg') }}" alt="" />
                            Add New Fbo
                        </h5>
                        <button type="button" class="fboClose" id="add_fbo_close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-container">
                            <div class="input_wrapper control-group">
                                <label for="fbo-name" class="required">Fbo
                                    Name</label>
                                <input type="text" class="control bg-transparent" id="fbo-name" name="name" v-validate="'required'" value="" />
                                <span class="control-error fbo-detail-error" id="name-error">
                                </span>

                            </div>
                            <div class="input_wrapper control-group">
                                <label for="fbo-address" class="required">Address</label>
                                <textarea v-validate="'required'" class="control bg-transparent" id="fbo-address" name="address" rows="5"></textarea>
                                <span class="control-error fbo-detail-error" id="address-error">
                                </span>
                            </div>
                            <div class="input_wrapper">
                                <label for="fbo-notes">Notes (Optional)</label>
                                <textarea class="control" id="fbo-notes" name="notes" rows="5"></textarea>
                                <span class="control-error fbo-detail-error" id="notes-error">
                                </span>
                            </div>


                            <button id="add-fbo-button">
                                <img class='suggestion-icon mr-2' src='/themes/volantijetcatering/assets/images/home/plus-circle1.svg'>
                                ADD</button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        {{-- </form> --}}

        {{-- airport modal end --}}

        @if (auth('admin')->user()->role_id == 1 && ($order->status === 'pending' || $order->status === 'accepted'))
        <div class="search__product mt-5">
            <div class="search__title d-md-flex">
                <h3>Products</h3>
                <div class="d-flex p-0">
                    <div class="product__search d-flex ml-md-3">
                        <div class="icon-wrapper product__search__icon ">
                            <span class="icon search-icon search-btn d-none d-lg-block"></span>
                        </div>
                        <input class="w-100" id="product_search" type="search" placeholder="Search product with name..." @if (isset($product)) value="{{ $product->name }}" @endif />
                    </div>
                    <button class="search_product_button ml-2" id="product_search_button">Search</button>
                </div>
            </div>
        </div>
        @endif


        {{-- Products modal start --}}

        <button type="button" class="btn btn-primary d-none" data-toggle="modal" data-target="#product-list" id="modal-open"></button>

        <div class="modal fade" id="product-list" tabindex="-1" role="dialog" aria-labelledby="productListTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered order_view_modal_dialog" role="document">
                <div class="modal-content product_modal">
                    <div class="modal-header">
                        <h4 class="text-dark">Add Products</h4>
                        <div class="action__button">
                            <button type="button" class="cancel" id='close' data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">Cancel</span>
                            </button>

                            <button type="button" class="save ml-2" id='save'>
                                <span aria-hidden="true">Add Order</span>
                                <span class="btn-ring-modal"></span>
                            </button>
                        </div>
                    </div>
                    <div class="errors" id="product_modal"></div>
                    <div class="modal-body " id="modal-contents">
                    </div>
                </div>
            </div>
        </div>

        {{-- Products modal end --}}

        <section class="order__summary mt-5">
            <h3>Shopping Cart</h3>


            @if ($order->total_item_count < 1) <div class="empty__item text-center my-4">
                <p class="my-3">Order item is empty, Please add products to show here!</p>
    </div>
    @else
    <div class="table" style="max-height: 500px; overflow:auto;">
        <div class="table-responsive">
            <table>

                <thead>
                    @if (auth('admin')->user()->role_id == 1)
                    <tr class="order_view_table_head">
                        <th>Item</th>
                        <th>Product</th>
                        <th>Special instructions</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Sub Total</th>
                        @if ($order->status === 'pending' || $order->status === 'accepted')
                        <th>Action</th>
                        @endif
                    </tr>
                    @else
                    <tr class="order_view_table_head">
                        <th>Item</th>
                        <th>Product</th>
                        <th>Special instructions</th>
                        {{-- <th>Price</th> --}}
                        <th>Qty</th>
                        {{-- <th>Sub Total</th> --}}
                        {{-- @if ($order->status === 'pending' || $order->status === 'accepted')
                                            <th>Action</th>
                                        @endif --}}

                    </tr>
                    @endif

                </thead>

                <tbody class="table__body">
                    @php
                    $orders = DB::table('order_items')
                    ->where('order_id', $order->id)
                    ->where('parent_id', null)
                    ->get();

                    @endphp
                    {{-- @dd($order->item) --}}
                    @foreach ($order->items as $item)
                    @php
                    $optionLabel = null;
                    $specialInstruction = null;
                    $notes = null;
                    if (isset($item->additional['attributes'])) {
                    $attributes = $item->additional['attributes'];

                    foreach ($attributes as $attribute) {
                    if (
                    isset($attribute['option_label']) &&
                    $attribute['option_label'] != ''
                    ) {
                    $optionLabel = $attribute['option_label'];
                    }
                    }
                    }

                    if (isset($item->additional['special_instruction'])) {
                    $specialInstruction = $item->additional['special_instruction'];
                    }
                    // dd($notes);
                    $notes = DB::table('order_items')
                    ->where('id', $item->id)
                    ->where('order_id', $order->increment_id)
                    ->value('additional_notes');

                    @endphp

                    <tr class="order_view_table_body">
                        <td style="
                                                min-width:110px">
                            {{-- sandeep delete image --}}
                            {{-- <div>
                                                    <img class="product__img"
                                                        src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                        alt="Product" style="height: 70px;width: 80px;" />
                                                </div> --}}

                            @if (isset($notes))
                            {{-- <p class="m-0 display__notes">{{ $notes }}</p> --}}
                            <p class="m-0 display__notes">{!! nl2br(e($notes)) !!}</p>
                            @endif

                            @if ($order->status === 'pending' || $order->status === 'accepted')
                            @if (isset($notes))
                            <p class="m-0 add__note mt-2" data-toggle="modal" data-target="#updateNote{{ $item->id }}">edit
                                Order
                                Notes
                            </p>
                            @else
                            <p class="m-0 add__note" data-toggle="modal" data-target="#addNote{{ $item->id }}">Add Order
                                Notes
                            </p>
                            @endif
                            @if (auth('admin')->user()->role_id == 1)
                            <p class="m-0 cursor-auto product__edits " data-toggle="modal" data-target="#product-edit{{ $item->id }}"><img class="ml-3" src="/themes/volantijetcatering/assets/images/pencil.png" height="10px" alt="">edit</p>
                            @endif
                            @endif

                            {{-- update note modal --}}

                            <div class="modal fade product__edit" id="updateNote{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title product__modal__title" id="myModalLabel">
                                                update order note
                                            </h5>

                                            <button id="order-add-note" type="button" class="">
                                                <span aria-hidden="true">update</span>
                                                <span class="btn-ring-modal"></span>
                                            </button>

                                        </div>
                                        <div class="modal-body d-flex edit__product" id="{{ $item->id }}" data="{{ $item->id }}">

                                            <textarea placeholder="Notes..." class="w-100 p-2" name="" id="add_note" cols="30" rows="10" style="height: 115px;">{{ isset($notes) ? $notes : '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            {{-- add note modal --}}

                            <div class="modal fade product__edit" id="addNote{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title product__modal__title" id="myModalLabel">
                                                Add order note
                                            </h5>

                                            <button id="order-add-note" type="button" class="">
                                                <span aria-hidden="true">add</span>
                                                <span class="btn-ring-modal"></span>
                                            </button>


                                        </div>
                                        <div class="modal-body d-flex edit__product" id="{{ $item->id }}">
                                            <textarea class="w-100 p-2" name="" id="add_note" cols="30" rows="10" style="height: 115px;" placeholder="Notes..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @php
                            $inventoryQty1 = 0;
                            $inventoryQty2 = 0;

                            if ($item->type === 'configurable') {
                            $optionId = DB::table('order_items')
                            ->select('product_id')
                            ->where('parent_id', $item->id)
                            ->first();

                            // Check if $optionId is not null before proceeding
                            if ($optionId) {
                            $optionInventory = DB::table('product_inventory_indices')
                            ->where('product_id', $optionId->product_id)
                            ->select('qty')
                            ->first();

                            // Use the quantity of the option if it exists
                            if ($optionInventory) {
                            $inventoryQty1 = $optionInventory->qty;
                            }
                            }
                            }

                            // If the product is not of type 'configurable' or no option quantity is found

                            if ($item->product_id) {
                            $productInventory = DB::table('product_inventory_indices')
                            ->where('product_id', $item->product_id)
                            ->select('qty')
                            ->first();

                            // Use the quantity of the product if it exists
                            if ($productInventory) {
                            $inventoryQty2 = $productInventory->qty;
                            }
                            }

                            $modalId = 'product-edit' . $item->id;
                            @endphp

                            <div class="modal fade product__edit" id="{{ $modalId }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content modal__note">
                                        <div class="modal-header">
                                            <h5 class="modal-title product__modal__title" id="myModalLabel">
                                                Edit Product Price
                                            </h5>

                                            <button type="button" class="" id="editSave">
                                                <span aria-hidden="true">save</span>
                                            </button>
                                        </div>

                                        <div class="displayErrors"></div>
                                        <div class="modal-body d-flex edit__product" id="{{ $item->product_id }}">
                                            <input type="hidden" id="editHiddenInput" name="{{ $item->type }}" quantity="{{ $item->qty_ordered }}" value="{{ $item->id }}" data="{{ $item->weight }}" totalQty="{{ $item->type === 'configurable' ? $inventoryQty1 : $inventoryQty2 }}">

                                            <!-- <img src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                                    alt="Product" style="height: 70px" /> -->

                                            <div class="w-100 pl-2">
                                                <p class="m-0 product__name">
                                                    {{ $item->name }}
                                                    @if ($optionLabel)
                                                    ({{ $optionLabel }})
                                                    @endif
                                                </p>
                                                <div class="group__input__field my-2">
                                                    <button class="border-0" id="editMinusBtn">-</button>
                                                    <input type="number" class="text-center w-25 border-0 bg-light p-1" value="{{ $item->qty_ordered }}" id="editQuantityInput">
                                                    <button class="border-0" id="editPlusBtn">+</button>
                                                </div>
                                                <div class="price">
                                                    @php
                                                    $price = number_format(
                                                    $item->base_price,
                                                    2,
                                                    '.',
                                                    '',
                                                    );
                                                    @endphp
                                                    <input type="number" id="editProductPrice" value="{{ $price }}" class="text-center w-25 border-0 bg-light">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                        <td style="min-width: 150px">
                            {{ $item->name }}
                            @if ($optionLabel)
                            ({{ $optionLabel }})
                            @endif
                        </td>

                        @if (isset($specialInstruction))
                        <td class="special-intruction" style="max-height: 100px;overflow:auto;min-width:110px">
                            <p style="color: inherit; max-height: 150px;">{{ $specialInstruction }}</p>
                        </td>
                        @else
                        <td class="special-intruction text-center">
                            <p style="color: inherit;"></p>
                        </td>
                        @endif

                        @if (auth('admin')->user()->role_id == 1)
                        <td>{{ core()->formatBasePrice($item->base_price) }}</td>
                        @endif

                        <td>
                            <span class="qty-row">
                                {{ $item->qty_ordered }}
                            </span>

                        </td>
                        @if (auth('admin')->user()->role_id == 1)
                        {{-- sandeep comment code --}}
                        {{-- <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }} --}}
                        <td>{{ core()->formatBasePrice($item->base_total - $item->base_discount_amount) }}
                        </td>
                        @endif

                        @if (in_array($order->status, ['pending', 'accepted']) && auth('admin')->user()->role_id == 1)
                        <td>
                            <div class="delete_order_item text-center">
                                <i data-toggle="modal" data-target="#remove-item{{ $item->id }}" class="remove__icon">
                                    <img src="/themes/volantijetcatering/assets/images/delete.png" style="height: 22px;" alt="">
                                </i>
                                <div class="modal fade " id="remove-item{{ $item->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content modal__note">
                                            <div class="modal-header">
                                                <h5 class="modal-title remove_item_modal_title" id="myModalLabel">
                                                    Remove item
                                                </h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body remove_modal_body">
                                                <p>Are you sure you want to delete the item
                                                </p>
                                                <div class="remove_item_buttons">

                                                    <button type="button" class="" data-dismiss="modal">Cancel</button>
                                                    <input type="hidden" id="{{ $item->id }}" value="{{ $item->product_id }}" name="{{ $item->type }}">
                                                    <a class="remove d-flex" href="{{ route('order-view.remove-order-product', ['order_id' => $order->id, 'id' => $item->id]) }}">Remove</a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </td>
                        @endif
                        @endforeach
                    </tr>
            </table>
        </div>
    </div>
    @endif
    <div class="row view__action__button mt-4 d-flex justify-content-start ml-auto">
        @if (!$order->total_item_count < 1) @if ($order->status === 'pending')
            <button type="button" data-toggle="modal" data-target="#accept" {{ $order->shipping_address->airport_name == '' || !isset($order->fbo_full_name) || $order->fbo_full_name == '' ? 'disabled' : '' }} class="order_view_accept">Accept</button>

            <button type="button" data-toggle="modal" data-target="#reject" class="order_view_reject ml-3">Reject</button>
            @endif
            @endif
            <!---------------------------accept modal------------------------------------------------>
            <div class="modal fade" id="accept" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content accept__modal w-75">
                        <div class="modal-body mb-5">
                            <div class="accept__icon text-center mb-2">
                                <img src="/themes/volantijetcatering/assets/images/accept.png" alt="">
                            </div>
                            <div class="accept__text text-center mb-4">
                                <h2>Are you sure?</h2>
                                <p>Do you really want to accept this order? <br>this process is cannot be
                                    undone.</p>
                            </div>


                            <div class="accept_buttons d-flex mt-5">
                                <button type="button" class="cancel__button d-flex" data-dismiss="modal">Cancel</button>

                                <a href="{{ route('order-view.order-accept', $order->id) }}" class="accept__button d-flex text-decoration-none">Accept</a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!----------------------------reject modal-------------------------------->

            <div class="modal fade" id="reject" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content reject__modal w-75">
                        <div class="modal-header">
                            <h5 class="modal-title reject__modal__title " id="myModalLabel">
                                Reject Order
                            </h5>
                            <div class="modal-header border-0 d-flex justify-content-end p-0 align-items-center">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>cancel</span></button>
                                <button id="reject-order" type="button" class="" action="reject">
                                    <span aria-hidden="true">reject</span>
                                    <span class="btn-ring-modal"></span>
                                </button>
                            </div>
                        </div>
                        <div class="modal-body reject_modal_body" id="{{ $order->id }}">

                            <textarea class="w-100 p-2" name="" id="reject_note" cols="30" placeholder="Notes..." rows="10" style="height: 115px;"></textarea>

                        </div>

                    </div>
                </div>
            </div>
    </div>
    </section>

    @if (auth('admin')->user()->role_id == 1)
    <div class="order_status_log table my-3">
        <h3>Order Status Log</h3>
        <div class="table-responsive">
            <table style="font-size: 15px;">
                <thead>
                    <tr class="order_view_table_head">
                        <th>Order Number</th>
                        <th>Updated By</th>
                        <th>Updated On</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody class="table__body">
                    @php
                    $status_log = DB::table('order_status_log as sl')
                    ->leftJoin('admins', 'sl.user_id', '=', 'admins.id')
                    ->leftJoin('order_status as os', 'sl.status_id', '=', 'os.id')
                    ->leftJoin('customers', 'sl.user_id', '=', 'customers.id')
                    ->where('sl.order_id', $order->id)
                    ->select(
                    'sl.order_id',
                    'sl.is_admin',
                    'admins.name',
                    'sl.email',
                    'sl.created_at',
                    'os.status',
                    'customers.first_name',
                    )
                    ->get();
                    @endphp
                    @foreach ($status_log as $status)
                    <tr>
                        <td>{{ $status->order_id }}</td>
                        <td>

                            @if ($status->name === null && $status->is_admin === 0)
                            {{ $status->first_name === '' ? $order->fbo_full_name : $status->first_name }}
                            @else
                            {{ $status->name }}
                            @endif
                        </td>
                        {{-- sandeep change date time formate --}}
                        <td>{{ date('m-d-Y h:i:s A', strtotime($status->created_at)) }}</td>
                        <td>
                            {{-- sandeep add code --}}
                            @if ($status->status == "cancel")
                            <span>canceled</span>
                            @else
                            <span>{{ $status->status }}</span>
                            @endif
                            @if ($status->status === 'invoice sent')
                            [{{ $status->email }}]
                            {{-- sandeep add code --}}
                            @elseif ($status->status === 'paid')
                            by
                            @if ($status->name === null && $status->is_admin === 0)
                            {{ $status->first_name === '' ? $order->fbo_full_name : $status->first_name }}
                            @else
                            {{ $status->name }}
                            @endif

                            @endif
                        </td>
                    </tr>
                    @endforeach

                </tbody>
            </table>
        </div>
    </div>
    @endif


</div>

<div class="col-sm-12 col-md-4 col-lg-4 order__view__right">
    {{-- @if ($order->status === 'canceled' || $order->status === 'rejected')
                {{ '' }}
    @else
    <button type="button" data-toggle="modal" class="order__cancel__button" data-target="#cancel" class="order_view_reject ml-3">cancel</button>
    @endif --}}
    {{-- sandeep add code  after piad not show order cancel button--}}
    @if (auth('admin')->user()->role_id == 1)
    @php
    $paidExists = $status_log->contains('status', 'paid');
    $excludedStatuses = ['pending', 'canceled', 'rejected'];
    if ($paidExists) {
    $excludedStatuses[] = 'paid';
    }
    @endphp
    @endif

    {{-- sandeep add deliverd ,paid status --}}
    @if (auth('admin')->check() &&
    auth('admin')->user()->role_id == 1 &&
    !in_array($order->status, ['canceled', 'rejected','delivered','paid']) && !in_array('paid', $excludedStatuses))
    <button type="button" data-toggle="modal" class="order__cancel__button" data-target="#cancel" class="order_view_reject ml-3">cancel</button>
    <div class="modal fade" id="cancel" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content reject__modal w-75">
                <div class="modal-header">
                    <h5 class="modal-title reject__modal__title " id="myModalLabel">
                        Cancel Order
                    </h5>
                    <div class="modal-header border-0 d-flex justify-content-end p-0 align-items-center">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span>close</span></button>
                        <button id="reject-order" type="button" class="" action="cancel">
                            <span aria-hidden="true">save</span>
                            <span class="btn-ring-modal"></span>
                        </button>
                    </div>
                </div>
                <div class="modal-body reject_modal_body" id="{{ $order->id }}">

                    <textarea class="w-100 p-2" name="" id="reject_note" placeholder="Notes..." cols="30" rows="10" style="height: 115px;"></textarea>
                </div>
            </div>
        </div>
    </div>
    @endif



    @if (auth('admin')->user()->role_id == 1)
    <div class="summary-comment-container ">
        <div class="comment-container">
            <form id="commentForm" action="{{ route('admin.sales.order.comments', $order->id) }}" method="post">
                @csrf()

                <div class="control-group" :class="[errors.has('comment') ? 'has-error' : '']">
                    <label for="comment" class="required mt-2">Note</label>
                    <div class="activity__text__area">
                        <textarea required v-validate="'required'" class="control order_view_note" id="comment" name="comment" data-vv-as="&quot;{{ __('admin::app.sales.orders.comment') }}&quot;" placeholder="Notes..."></textarea>
                    </div>
                    <span class="control-error" v-if="errors.has('comment')">@{{ errors.first('comment') }}</span>
                </div>

                <div class="control-group">
                    <span class="checkbox">
                        <input type="checkbox" name="customer_notified" id="customer-notified" name="checkbox[]">
                        <label class="checkbox-view" for="customer-notified"></label>
                        {{ __('admin::app.sales.orders.notify-customer') }}
                    </span>
                </div>

                <div class="d-flex justify-content-center w-100">
                    <button type="submit" class=" order_view_send_button mb-3" id="submitFormButton">
                        Save
                    </button>
                </div>
            </form>
            @php

            $commentsCount = OrderNotes::where('order_id', $order->id)->count();

            @endphp
            <div class="@if ($commentsCount > 0) d-block @else d-none @endif">
                <h5>Note Logs</h5>
                <div class="note_logs mt-2">
                    <ul class="comment_list m-0">
                        @foreach (OrderNotes::orderBy('id', 'desc')->where('order_id', $order->id)->get() as $comment)
                        <li class="d-flex pb-3" style="line-break: anywhere">
                            @if ($comment->is_admin === 1)
                            <div class="table m-0">
                                <tbody>
                                    <tr>
                                        <td><strong class="" style="color: #c6c6c6;">Support:</strong></td>
                                        <td><span class="" style="color: #9d9d9d;">{{ $comment->notes }}</span>
                                        </td>
                                        {{-- sandeep change date time formate --}}
                                        <td><span class="float-right" style="color: #9d9d9d;">({{ date('m-d-Y h:i:s A', strtotime($comment->created_at)) }})</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </div>
                            @else
                            <div class="table m-0">
                                <tbody>
                                    <tr>
                                        <td><strong class="" style="color: #c6c6c6;">Customer:</strong></td>
                                        <td><span class="" style="color: #9d9d9d;">{{ $comment->notes }}</span>
                                        </td>
                                        {{-- sandeep change date time formate --}}
                                        <td><span class="float-right" style="color: #9d9d9d;">({{ date('m-d-Y h:i:s A', strtotime($comment->created_at)) }})</span>
                                        </td>
                                    </tr>
                                </tbody>

                            </div>
                            @endif
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="order_view__payment mt-4">
            <h5>Payment</h5>
            <div class="card order__view__payment">
                <div class="row p-2 order__view__total">
                    <p class="col-7 cart_text">Cart Total</p>
                    {{-- sandeep chnage order grand total to item bas total --}}
                    <p class="col-5 total">{{ core()->formatBasePrice($order->sub_total) }}</p>
                    <p class="col-7 tax_text">Tax</p>
                    @if (isset($order->tax_amount))
                    <p class="col-5 tax">{{ core()->formatBasePrice($order->tax_amount) }}</p>
                    @else
                    <p class="col-5 total">{{ core()->formatBasePrice(0.0) }} </p>
                    @endif

                    <p class="col-7 tax_text">Agent Handling</p>
                    @if (isset($agent) && $agent->Handling_charges != null)
                    <p class="col-5 tax">{{ core()->formatBasePrice($agent->Handling_charges) }}</p>
                    @else
                    <p class="col-5 tax">{{ core()->formatBasePrice(0) }}</p>
                    @endif



                    <p class="col-7 cart_text">Order Total</p>

                    <p class="col-5 total">

                        @if (isset($agent->Handling_charges))
                        {{ core()->formatBasePrice($order->grand_total + $agent->Handling_charges) }}
                        @else
                        {{ core()->formatBasePrice($order->grand_total) }}
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- sandeep delete deliverd status  --}}
    @if (auth('admin')->user()->role_id == 1)
    <div class="row group_button">
        @if (
        !in_array($order->status, ['pending', 'canceled', 'rejected', 'paid']) &&
        !in_array('paid', $excludedStatuses))
        <div class="col-6 mt-3 payment_and_invoice">
            @php
            // $cards = collect();
            $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere([
            'customers_id' => $order->customer_id,
            ]);
            @endphp
            {{-- @dd($cards) --}}
            <button type="button" class="collect_payment_modal_button" data-toggle="modal" data-target="#collectPaymentModal">
                Collect Payment
            </button>
            <!-- Modal -->
            <div class="modal fade p-0" id="collectPaymentModal" tabindex="-1" role="dialog" aria-labelledby="collectPaymentTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="collectPaymentTitle">Collect Payment</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body text-dark">
                            <div class="add_new_card">
                                <button type="button" id="open-mpauthorizenet-modal" class="order_view_add_card_button mr-2">Add card</button>
                                <input type="hidden" id="order_order_id" value="{{ $order->id }}">
                                <input type="hidden" id="admin_id" value="{{ auth()->guard('admin')->id() }}">
                                <input type="hidden" id="order_customer_id" value="{{ $order->customer_id }}">
                                {{-- mpauthorizenet --}}
                                @include(
                                'mpauthorizenet::shop.volantijetcatering.checkout.card-script',
                                [
                                'orderId' => $order->id,
                                ]
                                )
                            </div>
                            {{-- sandeep add card error message --}}
                            <div class="card_erorr_message p-2 d-none" style="color:red;">
                                <span class="payment_error_message"></span>
                            </div>

                            @if (isset($cards) && count($cards) > 0)
                            <div class="strike-through text-center my-2">
                                <span>or use existing card</span>
                            </div>

                            <div class="existing_card">
                                @include(
                                'mpauthorizenet::shop.volantijetcatering.components.saved-cards',
                                [
                                'customerId' => $order->customer_id,
                                ]
                                )
                                {{-- delete payment modal --}}

                                <button class="payment-delete-model-btn d-none" data-toggle="modal" data-target="#payment_delete_model">delete
                                    model</button>
                                <div class="modal fade p-0" id="payment_delete_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header fbo-header">
                                                <h1 class="fs24 fw6 mt-1">
                                                    Delete Card
                                                </h1>
                                                <button type="button" class="close save-payment-close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body popup-content">
                                                <div class="body col-12 border-0 p-0">
                                                    <form action="" method="POST" @submit.prevent="onSubmit">
                                                        {{ csrf_field() }}
                                                        <div class="row mb-3 p-3">
                                                            <p class="px-3">Are you sure you want
                                                                to delete this card?
                                                                Confirming will permanently remove
                                                                the card from your account.
                                                            </p>
                                                            <div class="row w-100 delete__card">
                                                                <button type="button" class="btn btn-primary accept">Ok</button>
                                                                <button type="button" class="btn btn-primary cancel">Cancel</button>
                                                            </div>
                                                        </div>

                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- end delete payment modal --}}

                            </div>
                            @endif
                        </div>
                        <div class="modal-footer p-2">
                            <button type="button" class="collect_payment_close_button" data-dismiss="modal">Close</button>
                            <button type="button" class="order_view_pay_button pay_disable" id="collect_payment" disabled>Charge
                                @if (isset($agent->Handling_charges))
                                {{ core()->formatBasePrice($order->grand_total + $agent->Handling_charges) }}
                                @else
                                {{ core()->formatBasePrice($order->grand_total) }}
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- @if ($order->status !== 'pending')
                            <button type="button" data-toggle="modal" class="order_view_invoice_button"
                                data-target="#showInvoice">Show invoice</button>
                        @endif --}}
        </div>
        @endif

        {{-- sandeep add cancled and rejected status --}}
        <div class="col-6 mt-3 payment_and_invoice">
            @if (!in_array($order->status, ['pending', 'canceled', 'rejected']))
            <button type="button" data-toggle="modal" class="order_view_invoice_button" data-target="#showInvoice">Show invoice</button>
            @endif
        </div>

        @if ($order->quickbook_invoice_link && !in_array($order->status, ['pending', 'canceled', 'rejected', 'paid']) && !in_array('paid', $excludedStatuses))
        <div class="col-6 mt-3 payment_and_invoice">
            <a href="{{$order->quickbook_invoice_link}}" target="_blank" class="order_view_invoice_button">
                Quickbook Invoice
            </a>
        </div>
        @endif

        <div class="col-6 update_and_invoice">
            {{-- <button type="submit" class=" order_view_send_button" id="submitFormButton"> --}}
            {{-- {{ __('admin::app.sales.orders.submit-comment') }} --}}
            {{-- Send Updates --}}
            {{-- </button> --}}
            {{-- <button type="button" class=" order_view_send_button">
                                    Send Updates
                                </button> --}}

            @if (!in_array($order->status, ['pending', 'canceled', 'rejected', 'paid', 'shipped', 'delivered']))
            <button type="button" data-toggle="modal" class="order_view_invoice_button mt-3" data-target="#invoice">Send an invoice</button>
            @endif
            {{-- @if (!in_array($order->status, ['shipped', 'delivered', 'pending', 'accepted'])) --}}
            @if ($order->status == 'paid')
            <a href="{{ route('admin.sale.order.package-slip', $order->id) }}"><button type="button" class="order_view_invoice_button mt-3">Package Slip</button></a>
            @endif

            {{-- invoice modal --}}
            <div class="modal fade" id="invoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content reject__modal w-75">
                        <div class="modal-body mb-5">
                            <div class="reject__icon text-center my-3">
                                <img src="/themes/volantijetcatering/assets/images/invoice.png" alt="">
                            </div>
                            <div class="reject__text text-center mb-4">
                                <h2>Are you sure?</h2>
                                <p>Do you really want to send invoice of this order?</p>
                            </div>

                            <a href="{{ route('admin.sales.invoices.mail.create', $order->id) }}" class=" m-auto d-flex order__invoice__button">
                                Send Invoice
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- invoice modal start --}}
        {{-- @dd($order->shipping_address) --}}
        <div class="modal fade" id="showInvoice" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered invoice-dialog-centered" role="document">
                <div class="modal-content invoice__modal">
                    {{-- @if (isset($order->billing_address)) --}}
                    @include('paymentprofile::shop.volantijetcatering.invoices.mail.create')
                    {{-- @endif --}}
                </div>
            </div>
        </div>
        {{-- invoice modal end --}}

        {{-- @dd($order) --}}
        {!! view_render_event('sales.order.page_action.before', ['order' => $order]) !!}
        <div class="col-6 shipped_button mt-3 d-flex justify-content-center w-100">
            {{-- sandeep add canceled status --}}
            @if ($order->canShip() && !in_array($order->status, ['pending', 'delivered','canceled','rejected']))
            {{-- @if ($order->canShip()) --}}
            <a href="{{ route('admin.paymentprofile.shipments.create', $order->id) }}" class="order_view_invoice_button">{{ __('admin::app.sales.orders.shipment-btn-title') }}
            </a>
            @endif
            {{-- @if ($order->status === 'shipped' && $order->status !== 'paid')
                            <button id="order_view_shipped" class="order__shipped">Delivery <span
                                    class="btn-ring"></span></button>
                        @endif --}}
        </div>
        {!! view_render_event('sales.order.page_action.after', ['order' => $order]) !!}
    </div>
    @endif
</div>
</div>
</div>

{!! view_render_event('sales.order.tabs.after', ['order' => $order]) !!}
</div>


@stop

{{-- @if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1')
    <script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
@else
    <script type="text/javascript" src="https://js.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
@endif --}}
@push('scripts')
<script>
    // complete form validation code || sandeep
    // $(document).ready(function() {
    //     function validateInput(selector, errorSelector, errorMessage) {
    //         $(selector).on('input', function() {
    //             var value = $(this).val().replace(/\D/g, '').slice(0, 12);
    //             $(this).val(value);
    //             var isValid = value.length >= 10 && value.length <= 12;
    //             $(errorSelector).text(isValid ? '' : errorMessage);
    //             $(this).toggleClass('is-invalid', !isValid);
    //         });
    //     }

    //     validateInput('#BillingMobile', '#billingMobile-error', 'Mobile number must be 10-12 digits.');

    //     $('.Billingform').on('submit', function(e) {
    //         var isValid = true;

    //         $(this).find('input[required], select[required]').each(function() {
    //             var $input = $(this);
    //             if (!$input.val().trim()) {
    //                 $input.siblings('.control-error').text('This field is required.').end().addClass('is-invalid');
    //                 isValid = false;
    //             } else {
    //                 $input.siblings('.control-error').text('').end().removeClass('is-invalid');
    //             }
    //         });

    //         var mobileValue = $('#BillingMobile').val();
    //         if (mobileValue.length < 10 || mobileValue.length > 12) {
    //             $('#billingMobile-error').text('Mobile number must be 10-12 digits.');
    //             $('#BillingMobile').addClass('is-invalid');
    //             isValid = false;
    //         }

    //         if (!isValid) e.preventDefault();
    //     });
    // });



    //    // sandeep add mobile validation code
    //        $(function() {
    //        $('#mobile').on('input', function() {
    //                 var $input = $(this);
    //                 var value = $input.val().replace(/\D/g, '').slice(0, 12);
    //                 $input.val(value);
    //                 var isValid = value.length >= 10;
    //                 $('#mobile-error').text(isValid ? '' : 'Mobile number must be 10-12 digits.');
    //                 $input.toggleClass('is-invalid', !isValid);
    //             });

    //             $('.Agentform').on('submit', function(e) {
    //                 var mobileLength = $('#mobile').val().length;
    //                 if (mobileLength < 10 || mobileLength > 12) {
    //                     e.preventDefault();
    //                     $('#mobile-error').text('Mobile number must be 10-12 digits.');
    //                     $('#mobile').addClass('is-invalid');
    //                 }
    //             });
    // });

    $('body').on('click', '.fbo_detail_submit, .billing_submit_form, .handling_form_submit', function(event) {
        var self = this;
        var evt = event;

        setTimeout(function() {
            evt.preventDefault();
            var errorText = '';

            errorText = $('.control-group').find('.control-error').text();
            var customerMobileError = $('.control-group').find('#customermobile-error').text().trim();
            var billingMobileError = $('.control-group').find('#billingMobile-error').text().trim();
            var handlingMobileError = $('.control-group').find('#mobile-error').text().trim();

            if (errorText.trim() !== '' || customerMobileError !== '' || billingMobileError !== '' || handlingMobileError !== '') {
                return false;
            }

        }, 10);
    });

    // sandeep add code for mobile number shhow in usa formate
 $('body').on('input', '.usa_mobile_number', function() {

    var phone = $(this).val().replace(/\D/g, ''); // Only digits

    // Limit max digits
    phone = phone.substring(0, 14);

    let formatted = '';

    if (phone.length <= 3) {
        formatted = phone;
    }
    else if (phone.length <= 6) {
        formatted = '(' + phone.slice(0, 3) + ') ' + phone.slice(3);
    }
    else if (phone.length <= 10) {
        formatted = '(' + phone.slice(0, 3) + ') '
                    + phone.slice(3, 6) + '-' 
                    + phone.slice(6);
    }
    else {
        formatted = '(' + phone.slice(0, 3) + ') '
                    + phone.slice(3, 6) + '-' 
                    + phone.slice(6, 10) + '-' 
                    + phone.slice(10);
    }

    $(this).val(formatted);
});


    // sandeep || add mobile validation code

    $(function() {
        // Function to handle mobile input validation
        function validateMobileInput(inputSelector, errorSelector) {
            $(inputSelector).on('input', function() {
                var $input = $(this);
                var value = $input.val().replace(/\D/g, '').slice(0, 14);
                $input.val(value);
                var isValid = value.length >= 10;
                if (isValid) {
                    $(errorSelector).text('');
                } else {
                    $(errorSelector).text('Mobile number must be 10-14 digits.');
                }
            });
        }

        // Function to handle form submission validation
        function validateFormOnSubmit(formSelector, inputSelector, errorSelector) {
            $(formSelector).on('submit', function(e) {
                var valueLength = $(inputSelector).val().replace(/\D/g, '').length;
                if (valueLength < 10 || valueLength > 14) {
                    e.preventDefault();
                    $(errorSelector).text('Mobile number must be 10-14 digits.');
                } else {
                    $(errorSelector).text('');
                }
            });
        }

        // Apply validation for each mobile input and form
        validateMobileInput('#mobile', '#mobile-error');
        validateFormOnSubmit('.Agentform', '#mobile', '#mobile-error');
        validateMobileInput('#BillingMobile', '#billingMobile-error');
        validateFormOnSubmit('.Billingform', '#BillingMobile', '#billingMobile-error');
        validateMobileInput('#customer_mobile', '#customermobile-error'); // Corrected ID
        validateFormOnSubmit('.fbo_cutomer_form', '#customer_mobile', '#customermobile-error'); // Corrected ID
    });



    $(document).ready(function() {


        // sandeep add loader 
        $('body').on('click', '.order_view_pay_button, .accept__button,.order__invoice__button ', function() {
            $(this).html('<span class="btn-ring"></span>');
            $(this).find(".btn-ring").show();
            $(this).find('.btn-ring').css({
                'display': 'flex'
                , 'justify-content': 'center'
                , 'align-items': 'center'
            });
        });


        //         // sandeep || add validation in billing address model
        //         $('body').on('click', '.billing_address_fbo', function() {
        //             var hasError = false;

        //             // Clear previous errors
        //             $('.control-error').empty();
        //             $('.control-group').removeClass('has-error');

        //             // Validate required fields
        //             $('input[name="Address"], input[name="postCode"], input[name="city"], input[name="mobile"], select[name="Select_State"]').each(function() {
        //                 var $this = $(this);
        //                 if ($this.val().trim() === '') {
        //                     $this.closest('.control-group').addClass('has-error');
        //                     $this.closest('.control-group').find('.control-group').append('<span class="error-message">' + $this.attr('name') + ' is required.</span>'); 
        //                     hasError = true;
        //                 }
        //             });

        //             // Validate mobile number length
        //             var mobile = $('input[name="mobile"]').val();
        //             if (mobile.length < 10 || mobile.length > 14) {
        //                 $('input[name="mobile"]').closest('.control-group').addClass('has-error');
        //                 $('input[name="mobile"]').siblings('.control-error').text('Mobile number must be between 10 and 14 digits.');
        //                 hasError = true;
        //             }

        //             // Prevent form submission if there are errors
        //             if (hasError) {
        //                 return false;
        //             }
        // });



        // $('body').on('click', '.handling_agent_form', function() {
        //     var hasError = false;
        //     // Clear previous errors
        //     $('#handlingAgent .control-error').empty();
        //     $('#handlingAgent .control-group').removeClass('has-error');

        //     // Validate required fields
        //     $('#handlingAgent input[name="name"], #handlingAgent input[name="mobile"], #handlingAgent input[name="ppr_permit"], #handlingAgent input[name="handling_charges"]').each(function() {
        //         var $this = $(this);
        //         if ($this.val().trim() === '') {
        //             $this.closest('.control-group').addClass('has-error');
        //             $this.siblings('.control-error').text($this.attr('name') + ' is required.');
        //             hasError = true;
        //         }
        //     });

        //     // Validate mobile number length
        //     var mobile = $('#handlingAgent input[name="mobile"]').val();
        //     if (mobile.length < 10 || mobile.length > 14) {
        //         console.log('mobile erorr ');
        //         $('#handlingAgent input[name="mobile"]').closest('.control-group').addClass('has-error');
        //         $('#handlingAgent input[name="mobile"]').siblings('#mobile-error').text('Mobile number must be between 10 and 14 digits.');
        //         hasError = true;
        //     }
        // });


        // {{-- fbo modal start --}}
        //date and time validation start

        $('.fbo_detail_submit').click(() => {
            let errors = 0;

            if ($('#timeSlots').val() === '') {
                $('.fbo_add_error_time').text('Delivery time is required');
                errors++;
            } else {
                $('.fbo_add_error_time').text('');
            }

            if ($('#daySelect').val() === '') {
                $('.fbo_add_error_date').text('Delivery date is required');
                errors++;
            } else {
                $('.fbo_add_error_date').text('');
            }

            if (errors > 0) return;
            // Continue with the form submission or other actions
        });


        //date and time validation end







        // Trigger file input click when upload button is clicked
        $('#uploadTrigger').on('click', function() {
            $('#imageUpload').click();
        });

        // Enable the delivery button and show image preview when an image is selected
        $('#imageUpload').on('change', function() {
            console.log(this.files, 'hjdgfsgdf')
            if (this.files && this.files[0]) {
                // $('#order_view_shipped').prop('disabled', false);
                $('#order_view_shipped').prop('disabled', false).css('cursor', 'pointer');
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#imagePreview').attr('src', e.target.result).show();
                }
                reader.readAsDataURL(this.files[0]);
            } else {
                $('#order_view_shipped').prop('disabled', true);
                $('#imagePreview').hide();
            }
        });
    });


    jQuery(document).ready(function() {


            $('.close').click(function() {
                $('.modal-backdrop').removeClass('modal-backdrop');
            })
            jQuery('body').on('click', '#order_view_shipped', function() {

                $(this).prop('disabled', true);
                $(this).html('<span class="btn-ring"></span>');
                $(".btn-ring").show();
                // $(this).val('disabled');
                setTimeout(function() {
                    $(".btn-ring").hide();
                    $(this).prop('disabled', false);
                }, 20000);

                // debugger;

                $.ajax({
                    url: "{{ route('admin.order.status') }}"
                    , method: "GET"
                    , data: {
                        "order_id": orderid
                    , }
                    , success: function(result) {
                        location.reload();
                    }
                });
            });

            $('#expire').on('input', function() {
                // Remove non-numeric characters
                var value = $(this).val().replace(/\D/g, '');

                // Add '/' after two digits
                if (value.length > 2) {
                    value = value.slice(0, 2) + '/' + value.slice(2);
                }

                // Update the input value
                $(this).val(value);
            });



            jQuery('body').on('keydown', '#product_search', function(event) {

                if (event.keyCode === 13) {
                    event.preventDefault();
                    searchProducts();
                }
            });

            // Handle click event on #product_search_button
            jQuery('body').on('click', '#product_search_button', function() {
                searchProducts();
            });

            function searchProducts() {
                var name = jQuery('#product_search').val();
                var token = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: "{{ route('admin.sale.order.view.products') }}"
                    , type: 'GET'
                    , data: {
                        'name': name
                    , }
                    , success: function(result) {
                        // console.log(result);
                        // if (result) {
                        $('#modal-open').click();
                        jQuery('#modal-contents').html(result);
                        // }
                    }
                });
            }


            $('#formFboValidate').submit(function(event) {
                // Prevent form submission initially
                event.preventDefault();

                // Check if all inputs have values
                var allInputsFilled = true;
                var mobilevalid = true;
                // sandeep add mobile code
                var mobile = $(this).find('.control-group').find('#customer_mobile').val();
                var mobileDigits = mobile.replace(/\D/g, '');
                if (mobileDigits.length < 14 || mobileDigits.length > 18) {
                    mobilevalid = false;
                }
                $(this).find('input').each(function() {
                    if ($(this).val() === '') {
                        allInputsFilled = false;
                        return false;
                    }
                });

                // If all inputs have values, submit the form
                if (allInputsFilled && mobilevalid) {
                    this.submit();
                } else {

                    if ($('.validate_form').next('.alert.alert-danger').length === 0) {
                        $('.validate_form').after(
                            '<div class="alert alert-danger">Please fill in all required fields.</div>');
                    }

                }
            });
            //_______________________address view________________________________//



            // var islogin = '<?php echo $islogin; ?>';
            var customer_token = '<?php echo $guestToken; ?>';
            var customerArray = <?php echo json_encode($airportArr); ?>;


            //alert(customerArray);
            var auto_search = jQuery('#auto_search').val();
            var airport_fbo_value = jQuery('#airport-fbo-input').val();


            // if (auto_search == '' && airport_fbo_value == '' ) {
            //     jQuery('#address_update').prop('disabled', true);
            // }else{
            //     jQuery('#address_update').prop('disabled', false);
            // }

            jQuery('#address_update').prop('disabled', !auto_search || !airport_fbo_value);


            // sandeep ||add timeout code for airport list search
            let typingTimer;
            const typingDelay = 500;
            jQuery('body').on('keyup', '#auto_search', function() {
                // console.log('hfggh');
                clearTimeout(typingTimer); 
                jQuery('#airport-fbo-list').hide();
                var name = jQuery(this).val();
                console.log('ajs name',name);
                // here when ajax hit then show airport  

                if ($.inArray(name, customerArray) === -1) {

                    jQuery('#address_update').prop('disabled', true);
                }

                typingTimer = setTimeout(function() {
                $.ajax({
                    url: "{{ route('admin.sale.order.view.address') }}",

                    type: 'GET',
                    data: {
                        'name': name,
                        'type': 'address_search'
                    },
                    success: function(result) {
                        jQuery("#address-list").addClass('list_of_address');
                        jQuery("#address-list").html(result);
                    }
                });
                  }, typingDelay);

            })

            var airport_id = $('#auto_search').attr('attr') ?? null;
            jQuery('body').on('click', '#address-list li', function() {
                // sandeep || add fbo id code for disaled button
                $('#airport-fbo-input').val('');
                $('#selected-fbo-id').val('');
                var airport_fbo_value = jQuery('#airport-fbo-input').val();
                var fbo_id = $('#selected-fbo-id').val();
                if (airport_fbo_value && fbo_id != '' && fbo_id != '0') {
                    jQuery('#address_update').prop('disabled', false);
                }

                console.log('aiuehfd ',airport_fbo_value);
                jQuery("#address-list").removeClass('list_of_address');
                var name = jQuery(this).attr('data-attr');

                airport_id = jQuery(this).attr('attr');
                // airport_id = jQuery("#auto_search").attr("attr", airportId);
                jQuery("#auto_search").attr('attr', airport_id);

                var orderid = jQuery('#orderID').text();

                var input_val = jQuery("#auto_search").val(name);
                jQuery("#address-list").html("");

                // if (airport_id) {
                //     airport_fbo();
                // }

            });

            // console.log(jQuery('#airport-fbo-input').attr('attr'));
            // console.log(jQuery('#selected-fbo-id').val());
            // debugger
            jQuery('body').on('click', '#address_update', function() {
                var delivery_address = jQuery("#auto_search").val();
                if(delivery_address != '' && !jQuery('#selected-fbo-id').val() != ''){
                    return false;
                }
                var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
                // here when ajax hit then create airport or update
                $.ajax({
                    url: "{{ route('admin.sale.order.view.search-address') }}",
                    type: 'POST',
                    data: {
                        "_token": "{{ csrf_token() }}",
                        'delivery_address': delivery_address,
                        'airport_id': airport_id,
                        'update_airport_id': jQuery('#airport_id').val(),
                        'order_id': orderid,
                        'selected_fbo_id': jQuery('#selected-fbo-id').val(),
                    },
                    success: function(result) {
                        location.reload();
                    }
                });
            });


            jQuery('body').on('click', '#airport-fbo-input', function() {
                var airport_fbo_list = jQuery('#airport-fbo-list');
                console.log(airport_fbo_list.css('display'), 'uhggghgvhvjv')
                if (airport_id && airport_fbo_list.css('display') == 'none') {
                    airport_fbo();

                } else {
                    $('#airport-fbo-list').toggle();
                }
            });

            jQuery('body').on('click', '#add-fbo-button', function() {

                console.log('add click')
                let fboName = $('#fbo-name').val();
                let fboaddress = $('#fbo-address').val();
                let fboNotes = $('#fbo-notes').val();
                let airportId = jQuery("#auto_search").attr("attr");
        //    sandeep || add error message
            if (!airportId || !fboName || !fboaddress) {
                if (!fboName){ $('#name-error').text('The name field is required.').show();}
                if (!fboaddress){ $('#address-error').text('The address field is required.').show();}
                setTimeout(function() {
                    $('.fbo-detail-error').fadeOut();
                }, 3000);
                return; 
            }




                // if (airportId && fboName && fboaddress) {
                $.ajax({
                    url: "{{ route('admin.fbo-details.store') }}",
                    method: 'POST',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'name': fboName,
                        'address': fboaddress,
                        'notes': fboNotes,
                        'airport_id': airportId,
                        'customer_id': "{{ $order->customer_id }}",
                    },
                    //updated 
                    success: function(response) {
                        if (response.response) {
                            resetFormFields();
                            updateFboDetails(response.data);
                            // sandeep commnet code 
                            // if (airportId) {
                            //     airport_fbo();
                            // }
                        //    sandeep || add address update button enable 
                            if($('#auto_search').val() != '' && $('#airport-fbo-input').val() != '' && $('#selected-fbo-id').val() != ''){
                                $('#address_update').prop('disabled',false);
                            }
                        }
                    },
                    // error: function(xhr, status, error) {
                    //     console.error('AJAX Error:', status, error);
                    // }
                    error: function(xhr,status,error) {
                            if (xhr.status === 422) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]);
                                    $('#' + key + '-error').css('display','block');
                                });
                                setTimeout(function() {
                                    $('.fbo-detail-error').fadeOut();
                                }, 3000);
                            }
                        }
                });
                // }
            });

            // Function to reset form fields
            function resetFormFields() {
                // $('.fboClose').click();
                $('#fbo-name, #fbo-address, #fbo-notes').val('');
                // $('.click-edit-airport').click();
                // $('#add_fbo_modal').hide();
                $('#add_fbo_close').click();
                    setTimeout(function(){
                    $('body').addClass('modal-open');
                }, 500);

            }
            // sandeep || add new code 
            $(document).on('click', 'body, #add_fbo_close', function() {
                setTimeout(function(){
                    if ($('#exampleModal').hasClass('show')) {
                    $('body').addClass('modal-open');
                   }
                },300);
            });

            // Function to update FBO details with the response data
            function updateFboDetails(data) {
                $('#airport-fbo-input').val(data.name).data('selected-id', data.id);
                $('#selected-fbo-id').val(data.id);
            }

            $(document).on('click', '.custom-option', function() {
                var selectedText = $(this).find('.airport-name').text().trim();
                var selectedId = $(this).data('id');
                // Check if selectedId is "abc" and return early if it is
                if ($(this).attr('id') === 'option_id') {
                    $('#airport-fbo-list').hide();
                    return;
                }
                $('#airport-fbo-input').val(selectedText);
                $('#airport-fbo-input').data('selected-id', selectedId);
                $('#selected-fbo-id').val(selectedId); // Store the selected ID in the hidden input
                $('#airport-fbo-list').hide();
                // sandeep || add code
                if($('#selected-fbo-id').val() != ''){
                    jQuery('#address_update').prop('disabled', false);
                }
            });

            let airport_fbo = () => {
                $.ajax({
                    url: "{{ route('admin.sale.order.view.address') }}",

                    method: 'GET',
                    data: {
                        '_token': "{{ csrf_token() }}",
                        'airport_id': airport_id,
                        'airport_fbo_airport_id': jQuery('#airport_fbo_airport_id').val(),
                        'airport_fbo_customer_id': "{{ $order->customer_id }}",
                        'type': 'airport_fbo_detail'
                    },
                    success: function(response) {
                        if (response.options) {
                            $('#airport-fbo-list').removeClass('d-none')
                            $('#airport-fbo-list').show();
                            $("#airport-fbo-list").html(response.options);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            }

            //-----------------------------------Quantity----------------------------------------------------//

            // Add click event listener to plus button
            jQuery('body').on('click', '#plusBtn', function() {

                var parentContainer = $(this).parent().parent();
                var quantityInput = parentContainer.find('#quantityInput');
                var currentValue = parseInt(quantityInput.val(), 10);
                var newValue = currentValue + 1;

                quantityInput.val(newValue);

            });

            // Add click event listener to minus button
            jQuery('body').on('click', '#minusBtn', function() {
                var parentContainer = $(this).parent().parent();
                var quantityInput = parentContainer.find('#quantityInput');
                var currentValue = parseInt(quantityInput.val(), 10);
                if (currentValue > 1) {
                    var newValue = currentValue - 1;
                    quantityInput.val(newValue);
                }
                console.log(newValue)
            });

            //------------------------- checkbox ----------------------------------//

            var checkedCheckboxIds = [];
            var optionsMissing = false;
            var isErrorMessageDisplayed = false;
            var checkboxName = '';
            var orderid = jQuery('#orderID').text();
            var integerLength = 0;

            jQuery('body').on('click', '#save', function() {
                var limitExceeded = false;
                var limitExceededMessage = 'You have exceeded the available quantity for some products.';
                var QuantityValue = false;
                $('.checkboxName').each(function() {
                    var checkboxId = $(this).attr('id');
                    checkboxName = $(this).attr('name');
                    var hasOptions = $(this).closest('.search_product_list').find('.options input')
                        .length > 0;
                    var optionsId = $(this).closest('.search_product_list').find(
                        '.options input:checked').attr('id');
                    var quantityInput = $(this).closest('.search_product_list').find(
                        '#quantityInput');
                    var plusButton = $(this).closest('.row').find('.plusBtn');
                    var quantityLimitMessage = $(this).closest('.row').find(
                        '.quantity-limit-message');
                    var productQtyAvailable = $(this).closest('.row').find('#product_qty').val();
                    var quantityInputValue = parseInt(quantityInput.val(), 10) || 0;

                    var targetElement1 = $(this).closest('.search_product_list').find('.options');

                    function updateErrorMessage(targetElements) {
                        $.each(targetElements, function(index, targetElement) {
                            var errorMessageElement = $(targetElement).next(
                                '.error-message');
                            if (!errorMessageElement.length) {
                                errorMessageElement = $(
                                    '<span class="error-message"></span>').insertAfter(targetElement);
    }

    console.log(checkboxId)
    errorMessageElement.text(
    'Please select an option for the product' + ' ' + '(' +
    checkboxName + ')');

    });
    }
    if ($(this).prop('checked')) {
        // alert(quantityInputValue)
        if (quantityInputValue <= 0 || quantityInputValue === '') {

            QuantityValue = true;
        }

        var productPrice = $(this).closest('.row').find('#productPrice').val();
        var productExists = checkedCheckboxIds.some(item => item.product_id ===
            checkboxId);

        var integerValue = parseInt(productPrice); // Convert to integer
        integerLength = integerValue.toString()
            .length; // Get length of integer value as a string


        var optionQtyElement = $(this).closest('.search_product_list').find(
            '.options input:checked');
        var optionQty = optionQtyElement.data('qty');
        if (typeof optionQty === 'undefined' || isNaN(optionQty)) {
            optionQty = parseInt($('.option-quantities').val()) || 0;
        }

        if (hasOptions && !optionsId) {

            console.log('Please select an option for the product.');
            updateErrorMessage([targetElement1.get(0)]);
            optionsMissing = true;
            return;
        } else {
            optionsMissing = false;
        }

        if (!productExists) {
            var obj = {
                'product_id': checkboxId
                , 'qty': quantityInputValue
                , 'price': productPrice
                , 'option_id': optionsId
            , };
            checkedCheckboxIds.push(obj);


        }
    } else {
        // If unchecked, remove the checkbox id from the array
        var index = checkedCheckboxIds.findIndex(item => item.product_id ===
            checkboxId);
        if (index !== -1) {
            checkedCheckboxIds.splice(index, 1);
        }
    }
    });

    // ------------------------- ajax -------------------------------

    if (QuantityValue) {
        displayErrorMessage('Quantity cannot be less than 1');
    } else if (integerLength > 6) {
        displayErrorMessage('Product price digits cannot be more than 5');
    } else if (optionsMissing) {
        displayErrorMessage('Please select options for selected product', 'options');
    } else if (checkedCheckboxIds.length === 0) {
        displayErrorMessage('Please select at least one product.', 'checkbox');
    } else {
        $(this).prop('disabled', true);
        // $(this).css('cursor', 'not-allowed');
        // $(this).html('Loading...')
        $("body .cancel").hide();
        $(this).html('<span class="btn-ring-modal"></span>');
        $(".btn-ring-modal").show();
        setTimeout(function() {
            $(".btn-ring-modal").hide();
            $(this).prop('disabled', false);
        }, 20000);

        if (checkedCheckboxIds.length > 0) {
            $(this).prop('disabled', true);
            $.ajax({
                url: "{{ route('order-view.add-order') }}"
                , type: 'POST'
                , data: {
                    "_token": "{{ csrf_token() }}"
                    , 'product_info': checkedCheckboxIds
                    , 'order_id': orderid
                , }
                , success: function(response) {
                    console.log(response, 'response')
                    location.reload();
                }
            });
        }
    }

    function displayErrorMessage(message, customClass) {
        console.log('Displaying error message:', message);

        var classCheck = $('body').find('.errors' + ' ' + '.' + customClass);

        if (classCheck.length <= 0) {
            var alertElement = $(
                '<div class="alert alert-warning alert-dismissible fade show m-0 p-2 ' +
                customClass + '" role="alert">' +
                '<strong>Warning!</strong> ' + message +
                '<button type="button" class="close text-dark p-2" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span></button></div>');

            alertElement.appendTo('.errors');

            setTimeout(function() {
                alertElement.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);
        }
    }


    function updateErrorMessage(targetElements) {
        $.each(targetElements, function(index, targetElement) {
            var errorMessageElement = $(targetElement).next('.error-message');

            if (!errorMessageElement.length && optionsMissing) {
                errorMessageElement = $('<span class="error-message"></span>')
                    .insertAfter(targetElement);
            }

            errorMessageElement.text('Please select an option for the product' + ' (' +
                checkboxName + ')');
        });
    }
    });


    // -----------------------------------------Notes--------------------------------------------//


    $('body').on('click', '#order-add-note', function() {

        var notes = $(this).closest('.modal-content').find('#add_note').val();
        var productId = $(this).closest('.modal-content').find('.edit__product').attr('id')
        var itemId = $(this).closest('.modal__note').find('#noteHiddenInput').val();

        $(this).prop('disabled', true);
        $(this).html('<span class="btn-ring-modal"></span>');
        $(".btn-ring-modal").show();
        setTimeout(function() {
            $(".btn-ring-modal").hide();
            $(this).prop('disabled', false);
        }, 20000);


        console.log(productId, 'sdfhjvsg');
        // debugger
        $.ajax({
            url: "{{ route('order-view.add-note') }}"
            , type: "POST"
            , data: {
                "_token": "{{ csrf_token() }}"
                , "id": productId
                , "note": notes
                , "orderId": orderid
                , "itemId": itemId,

            }
            , success: function() {
                location.reload();
            }

        })
    });

    // -----------------------------------------reject order--------------------------------------------//

    $('body').on('click', '#reject-order', function() {
        console.log('clickkkkkkkkkk')
        var notes = $(this).closest('.reject__modal').find('#reject_note').val();

        var action = $(this).attr('action');

        $(this).prop('disabled', true);
        $('body .close').hide();
        $(this).html('<span class="btn-ring-modal"></span>');
        $(".btn-ring-modal").show();

        setTimeout(function() {
            $(".btn-ring-modal").hide();
            $(this).prop('disabled', false);
        }, 20000);

        $.ajax({
            url: "{{ route('order-view.order-reject') }}"
            , type: "POST"
            , data: {
                "_token": "{{ csrf_token() }}"
                , "note": notes
                , "orderId": orderid
                , "action": action,

            }
            , success: function() {
                location.reload();
            }
            , error: function() {
                $('#reject-order').prop('disabled', false).html('reject');
            },

        })
    });


    // ----------------------------Edit product--------------------------------------//


    // Add click event listener to plus button
    jQuery('body').on('click', '#editPlusBtn', function() {

        var parentContainer = $(this).parent().parent();
        var quantityInput = parentContainer.find('#editQuantityInput');
        var currentValue = parseInt(quantityInput.val(), 10);
        var newValue = currentValue + 1;
        quantityInput.val(newValue);


    });

    // Add click event listener to minus button
    jQuery('body').on('click', '#editMinusBtn', function() {
        var parentContainer = $(this).parent().parent();
        var quantityInput = parentContainer.find('#editQuantityInput');
        var currentValue = parseInt(quantityInput.val(), 10);
        if (currentValue > 1) {
            var newValue = currentValue - 1;
            quantityInput.val(newValue);
        }
    });

    var orderid = jQuery('#orderID').text();

    var updateProductInfo = [];

    jQuery('body').on('click', '#editSave', function() {
        var modalContent = $(this).closest('.modal-content');
        var itemId = modalContent.find('#editHiddenInput').val();
        var productId = modalContent.find('.edit__product').attr('id');
        var itemprice = modalContent.find('#editProductPrice').val();
        var itemType = modalContent.find('#editHiddenInput').attr('name');
        var itemWeight = modalContent.find('#editHiddenInput').attr('data');
        var modalContent = $(this).closest('.modal-content');
        var inventoryQty = modalContent.find('#editHiddenInput').attr('totalQty');
        var oldQuantity = modalContent.find('#editHiddenInput').attr('quantity');
        var quantity = modalContent.find('#editQuantityInput').val();

        console.log(oldQuantity, 'old')
        console.log(quantity, 'new')

        var newQty = quantity - oldQuantity;

        console.log(newQty, 'new-total');
        // debugger

        updateProductInfo.push({
            'productId': productId
            , 'quantity': quantity
            , 'newQty': newQty
            , 'itemId': itemId
            , 'itemprice': itemprice
            , 'itemType': itemType
            , 'itemWeight': itemWeight
        , })


        if (quantity <= 0 || quantity === '') {
            displayError('Quantity cannot be less than 1');
            return false;
        }
        // if (newQty > inventoryQty) {
        //     displayError('Quantity limit exceed');
        //     return false;
        // }

        setTimeout(function() {
            $.ajax({
                url: "{{ route('order-view.edit-order-product') }}"
                , type: "POST"
                , data: {
                    "_token": "{{ csrf_token() }}"
                    , "productInfo": updateProductInfo
                    , "orderID": orderid
                , }
                , success: function() {
                    location.reload();
                }
            });
        }, 500);
    });

    function displayError(message, customClass) {
        console.log('Displaying error message:', message);

        var classCheck = $('body').find('.errors' + ' ' + '.' + customClass);

        if (classCheck.length <= 0) {
            var alertElement = $(
                '<div class="alert alert-warning alert-dismissible fade show m-0 p-2 ' +
                customClass + '" role="alert">' +
                '<strong>Warning!</strong> ' + message +
                '<button type="button" class="close text-dark p-2" data-dismiss="alert" aria-label="Close">' +
                '<span aria-hidden="true">&times;</span></button></div>');

            alertElement.appendTo('.displayErrors');
            setTimeout(function() {
                alertElement.fadeOut('slow', function() {
                    $(this).remove();
                });
            }, 3000);
        }
    }

    });



    $(document).ready(function () {

    // --------- DATE LIST BANANA (DONO MODALS KE LIYE) --------- //

    var date = new Date(new Date().toLocaleString("en-US", { timeZone: "America/Los_Angeles" }));
    var days = [];

    var year  = date.getFullYear();
    var month = ('0' + (date.getMonth() + 1)).slice(-2);
    var day   = ('0' + date.getDate()).slice(-2);

    var formattedDate = year + '-' + month + '-' + day;

    for (var i = 0; i < 14; i++) {
        if (i === 0) {
            days.push({ text: "Today", value: formattedDate });
        } else {
            date.setDate(date.getDate() + 1);

            days.push({
                text: (i === 1
                    ? "Tomorrow"
                    : (date.toLocaleDateString('default', { weekday: 'long' }) +
                       " " + (date.getMonth() + 1) + "/" + date.getDate())
                ),
                value: date.toISOString().split('T')[0]
            });
        }
    }

    // sabhi .js-day-list (FBO + Billing) me same options daal do
    $('.js-day-list').each(function () {
        var $ul = $(this);
        $ul.empty();

        $.each(days, function (idx, d) {
            $ul.append(
                $('<li>', {
                    text: d.text,
                    'data-value': d.value
                })
            );
        });
    });


    // --------- CLICK HANDLERS (SCOPED PER MODAL) --------- //

    // Date input click → apne paas ka dropdown toggle
    $('body').on('click', '.js-day-select', function () {
        $(this)
            .closest('.control-group')
            .find('.delivery_select_date')
            .toggle();
    });

    // Date list se value choose
    $('body').on('click', '.js-day-list li', function () {
        var $group = $(this).closest('.control-group');
        var text   = $(this).text();

        $group.find('.js-day-select').val(text);
        $group.find('.delivery_select_date').hide();

        // is date ke hisab se usi row / modal ke time slots banao
        showTimeSlots($group.closest('.row'));
    });

    // Time input click → apne paas ka time dropdown toggle
    $('body').on('click', '.js-time-select', function () {
        $(this)
            .closest('.control-group')
            .find('.delivery_select_time')
            .toggle();
    });

    // Time list se value choose
    $('body').on('click', '.js-time-list li', function () {
        var $group = $(this).closest('.control-group');
        var text   = $(this).text();

        $group.find('.js-time-select').val(text);
        $group.find('.delivery_select_time').hide();
    });


    // Page load par jitne bhi date fields hain unke liye time slots generate karo
    $('.js-day-select').each(function () {
        showTimeSlots($(this).closest('.row'));
    });

});


// ---------- FUNCTIONS ---------- //

function showTimeSlots(scope) {
    // scope: wahi row jisme date + time dono controls hain (per modal)
    var dayText = scope.find('.js-day-select').val();
    var timeList = scope.find('.js-time-list');
    timeList.empty();

    var selectedDay;

    if (!dayText || dayText === 'Today') {
        selectedDay = new Date();
    } else if (dayText === 'Tomorrow') {
        var d = new Date();
        d.setDate(d.getDate() + 1);
        selectedDay = d;
    } else {
        selectedDay = parseCustomDate(dayText);
    }

    var options = {
        timeZone: 'America/Los_Angeles',
        hour12: true,
        year: 'numeric',
        month: 'numeric',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric'
    };

    var pstString     = selectedDay.toLocaleString('en-US', options);
    var selectedDayPST = new Date(pstString);

    var nowString = new Date().toLocaleString('en-US', options);
    var nowPST    = new Date(nowString);

    var current = new Date(selectedDayPST);

    // agar aaj ka din hai to current time se slots start karo, warna 12:00 am se
    if (selectedDayPST.toDateString() === nowPST.toDateString()) {
        var currentHour   = current.getHours();
        var currentMinute = current.getMinutes();
        var currentSlot   = Math.ceil(currentMinute / 15) * 15;
        current.setHours(currentHour, currentSlot, 0, 0);
    } else {
        current.setHours(0, 0, 0, 0);
    }

    var end = new Date(current);
    end.setHours(23, 59, 59, 999);

    while (current <= end) {
        var h       = current.getHours();
        var minutes = current.getMinutes().toString().padStart(2, '0');
        var amPm    = h >= 12 ? "PM" : "AM";
        h = h % 12;
        h = h ? h : 12; // 0 ko 12 banao

        var timeLabel = h + ":" + minutes + " " + amPm;

        timeList.append(
            $('<li>', {
                text: timeLabel
            })
        );

        current.setMinutes(current.getMinutes() + 30);
    }
}

function parseCustomDate(dateString) {
    // expected format: "Monday 3/26"
    var parts = dateString.split(' ');
    if (parts.length < 2) return new Date();

    var monthDay = parts[1].split('/');
    var month = parseInt(monthDay[0], 10) - 1;
    var day   = parseInt(monthDay[1], 10);
    var year  = new Date().getFullYear();

    return new Date(year, month, day);
}


</script>
@endpush
