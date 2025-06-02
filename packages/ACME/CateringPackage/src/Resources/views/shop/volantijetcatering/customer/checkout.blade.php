@php

    use Carbon\Carbon;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;

    
    if (isset($fboDetails->delivery_date)) {
        $date = $fboDetails->delivery_date;

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

    $guestToken = Session::token();
    $airportArr = Db::table('delivery_location_airports')->pluck('name')->toArray();

    // Retrieve the guest session ID
    $guestSessionId = Session::getId();
    $cartItems = Session::get('cart');
    //echo session()->get('cart')->id;

    $customer = auth()->guard('customer')->user();

    if (Auth::check()) {
        $islogin = 1;
        $address = Db::table('addresses')
            ->where('customer_id', $customer->id)
            ->where('address_type', 'customer')
            ->orderBy('id', 'desc')
            ->first();
    } else {
        $islogin = 0;
        $address = Db::table('addresses')
            ->where('customer_token', $guestToken)
            ->where('address_type', 'customer')
            ->first();
    }

    // sandeep add code || get airport id
    if (isset($address->airport_name) && $address->airport_name != '') {
        $airport_id = DB::table('delivery_location_airports')
            ->where('name', $address->airport_name)
            ->first();
    }

    if ($address) {
        $airpport = DB::table('delivery_location_airports as al')
            ->join('airport_fbo_details as af', 'af.airport_id', '=', 'al.id')
            ->where('al.name', $address->airport_name)
            ->where('af.id', $address->airport_fbo_id)
            ->select('af.name as fbo_name', 'al.id as airport_id')
            ->first();
        // dd($airpport);
    }

    // $orderId = DB::table('orders')
    // ->join('addresses', 'addresses.order_id', '=', 'orders.id')
    // ->where('orders.customer_id',$customer->id)
    // ->orderBy('orders.id','desc')
    // ->first();
    // dd($orderId);

    if ($customer) {
        $defaultAddress = DB::table('addresses')
            ->where('address_type', 'customer')
            ->where('customer_id', $customer->id)
            ->where('default_address', '1')
            ->select('id')
            ->first();
    }
    //    dd($defaultAddress);

    if (Auth::check()) {
        $addresses = Auth::guard('customer')
            ->user()
            ->addresses()
            ->join('airport_fbo_details', 'airport_fbo_details.id', '=', 'addresses.airport_fbo_id')
            ->select(
                'addresses.*',
                'airport_fbo_details.id as fbo_id',
                'airport_fbo_details.name as fbo_name',
                'airport_fbo_details.address as fbo_address',
            )
            ->get();
    }

@endphp
@extends('shop::layouts.master')

{{-- sandeep add title and seo --}}
@section('page_title')
    Checkout
@stop

@section('seo')
    <meta name="title" content="Checkout" />
    <meta name="description" content="" />
    <meta name="keywords" content="" />
    <link rel="canonical" href="{{ url()->current() }}" />
    
@stop

{{-- @dd($fboDetails) --}}
@if (
    !$fboDetails ||
        ($fboDetails->delivery_date === null ||
            $fboDetails->delivery_time === null ||
            ($fboDetails->full_name == '' && $fboDetails->phone_number == '')))
    {{-- @dd($fboDetails) --}}

    @section('content-wrapper')
        <div class="container fbo-detail">
            <h1 class="fs24 fw6 text-center  fbo-head">{{ __('shop::app.fbo-detail.client-info') }}</h1>
            <div class="col-lg-10 col-md-12 offset-lg-1 mt-5">
                <div class="body col-12 border-0 p-0">
                    <form action="{{ route('cateringpackage.shop.customer.add-fbo') }}" method="POST"
                        @submit.prevent="onSubmit">
                        {{ csrf_field() }}

                        <div class="row">

                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('fullname') ? 'has-error' : '']">
                                <label for="fullname" class="required label-style mandatory">
                                    {{-- {{ __('shop::app.fbo-detail.fullname') }} --}}
                                    Full Name
                                </label>

                                {{-- <input type="text" class="form-control form-control-lg"
                                    value="{{ isset($fboDetails->full_name) ? $fboDetails->full_name : auth('customer')->user()->first_name . auth('customer')->user()->last_name }}"
                                    v-validate="'required'" name='fullname' /> --}}
                                <input type="text" class="form-control form-control-lg"
                                    value="{{ isset($fboDetails->full_name) && $fboDetails->full_name ? $fboDetails->full_name : (auth('customer')->check() ? trim(auth('customer')->user()->first_name . ' ' . auth('customer')->user()->last_name) : '') }}"
                                    v-validate="'required'" name='fullname' />
                                <span class="control-error" v-if="errors.has('fullname')"
                                    v-text="'The full name field is required'"></span>
                            </div>


                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('phonenumber') ? 'has-error' : '']">
                                <label for="phone number" class="required label-style">
                                    {{ __('shop::app.fbo-detail.phone-number') }}
                                </label>

                                <input type="text" class="form-control form-control-lg" id="phone"
                                    value="{{ isset($fboDetails->phone_number) && $fboDetails->phone_number ? $fboDetails->phone_number : (auth('customer')->check() && auth('customer')->user()->phone ? auth('customer')->user()->phone : '') }}"
                                    name="phonenumber" v-validate="'required|min:14'" />
                                <span class="control-error" v-if="errors.has('phonenumber')"
                                    v-text="errors.first('phonenumber')"></span>
                            </div>


                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('email') ? 'has-error' : '']">
                                <label for="email" class="required label-style">
                                    {{ __('shop::app.fbo-detail.email-address') }}
                                </label>
                                <input type="email" class="form-control form-control-lg"
                                    value="{{ isset($fboDetails->email_address) && $fboDetails->email_address ? $fboDetails->email_address : (auth('customer')->check() && auth('customer')->user()->email ? auth('customer')->user()->email : '') }}"
                                    name="email" v-validate="'required'" />
                                <span class="control-error" v-if="errors.has('email')"
                                    v-text="errors.first('email')"></span>
                            </div>

                        </div>
                        <h1 class="fs24 fw6 text-center  my-5">{{ __('shop::app.fbo-detail.aircraft-info') }}</h1>
                        <div class="row">
                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('tailnumber') ? 'has-error' : '']">
                                <label for="tail number" class="required label-style">
                                    {{ __('shop::app.fbo-detail.tail-number') }}
                                </label>
                                <input type="text" class="control form-control form-control-lg"
                                    value="{{ isset($fboDetails->tail_number) ? $fboDetails->tail_number : '' }}"
                                    name="tailnumber" v-validate="'required'">
                                <span class="control-error" v-if="errors.has('tailnumber')"
                                    v-text="' The tail number field is required'"></span>
                            </div>

                            <div class="control-group col-sm-12 col-md-6 mb-3 packagingsection"
                                :class="[errors.has('packagingsection') ? 'has-error' : '']">

                                <label for="packaging section" class="required label-style">
                                    {{ __('shop::app.fbo-detail.packaging-section') }}
                                </label>

                                <div class="custom-dropdown ">
                                    <select class="form-control form-control-lg" name="packagingsection"
                                        v-validate="'required'">
                                        <option value="" disabled>Select Packaging</option>
                                        <option value="Microwave"
                                            {{ isset($fboDetails->packaging_section) && $fboDetails->packaging_section == 'Microwave' ? 'selected' : '' }}>
                                            Microwave</option>
                                        <option value="Oven"
                                            {{ isset($fboDetails->packaging_section) && $fboDetails->packaging_section == 'Oven' ? 'selected' : '' }}>
                                            Oven</option>
                                        <option value="Both"
                                            {{ isset($fboDetails->packaging_section) && $fboDetails->packaging_section == 'Both' ? 'selected' : '' }}>
                                            Both</option>
                                    </select>
                                </div>
                                <span class="control-error" v-if="errors.has('packagingsection')"
                                    v-text="'The packaging section field is required'"></span>
                            </div>
                            {{-- service_packaging --}}
                            <div class="control-group col-sm-12 col-md-6 mb-3 servicepackaging"
                                :class="[errors.has('servicepackaging') ? 'has-error' : '']">

                                <label for="packaging section" class="required label-style">
                                    Service Packaging
                                </label>

                                <div class="custom-dropdown ">
                                    <select class="form-control form-control-lg" name="servicepackaging"
                                        v-validate="'required'">
                                        <option value="" disabled>Select Service Packaging</option>
                                        <option value="Bulk Packaging"
                                            {{ isset($fboDetails->service_packaging) && $fboDetails->service_packaging == 'Bulk Packaging' ? 'selected' : '' }}>
                                            Bulk Packaging</option>
                                        <option value="Ready For Services"
                                            {{ isset($fboDetails->service_packaging) && $fboDetails->service_packaging == 'Ready For Services' ? 'selected' : '' }}>
                                            Ready For Services</option>
                                    </select>
                                </div>
                                <span class="control-error" v-if="errors.has('servicepackaging')"
                                    v-text="'The packaging section field is required'"></span>
                            </div>
                        </div>
                        @if (isset($fboDetails))
                            @if (!isset($fboDetails->delivery_date) && !isset($fboDetails->delivery_time))
                                <h1 class="text-center fs24 fw6 my-5 margin-top-2">Delivery Time</h1>
                            @else
                                <h1 class="text-center fs24 fw6 my-5 margin-top-2"> Delivery Time</h1>
                            @endif
                        @else
                            <h1 class="text-center fs24 fw6 my-5 margin-top-2"> Delivery Time</h1>
                        @endif
                        <div class="row">
                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('delivery_date') ? 'has-error' : '']">
                                <label for="delivery_date" class="required label-style mandatory text-left">
                                    {{ __('shop::app.fbo-detail.delivery_date') }}
                                </label>

                                <input type="text" required readonly class="form-control form-control-lg" id="daySelect"
                                    value="{{ isset($formattedDate) ? $formattedDate : '' }}" v-validate="'required'"
                                    name='delivery_date' required />
                                <div class="delivery_select delivery_select_date">
                                    <ul id="dayList"></ul>
                                </div>
                                <span class="control-error" v-if="errors.has('delivery_date')"
                                    v-text="'The Date field is required'"></span>
                            </div>

                            <div class="control-group col-sm-12 col-md-6 mb-3"
                                :class="[errors.has('delivery_time') ? 'has-error' : '']">
                                <label for="delivery_time" class="required label-style mandatory text-left">
                                    {{ __('shop::app.fbo-detail.delivery_time') }}
                                </label>

                                <input type="text" required readonly id="timeSlots"
                                    class="form-control form-control-lg"
                                    value="{{ isset($fboDetails->delivery_time) ? $fboDetails->delivery_time : '' }}"
                                    v-validate="'required'" name='delivery_time' required />
                                <div class="delivery_select delivery_select_time">
                                    <ul id="timeSlotsList"></ul>
                                </div>
                                <span class="control-error" v-if="errors.has('delivery_time')"
                                    v-text="'The Time field is required'"></span>
                            </div>
                        </div>

                        {{-- @if (!Auth::check()) --}}
                        {{-- <button class="theme-btn fbo-guest-btn mx-auto mt-4" type="submit">
                    {{ __('shop::app.fbo-detail.fbo-guest-button') }}
                </button> --}}
                        {{-- <p class="text-center mt-3"> want to pay faster next time?</p>
                <div class="col-12  mb-5 thank__create d-flex justify-content-center">
                    <a href="{{ route('shop.customer.session.create') }}">
                        <button class="btn-lg  bg-light">Create account</button>
                    </a>
                </div>
                @else --}}
                        <button class="theme-btn fbo-btn mx-auto my-4 fbo_button fbo_detail_button" id ="fbo_button"
                            style="width:200px" type="submit">
                            {{ __('shop::app.fbo-detail.fbo-button') }}
                        </button>
                        {{-- @endif --}}

                    </form>
                    @if (!Auth::check())
                        {{-- <button class="theme-btn fbo-guest-btn mx-auto mt-4" type="submit">
                {{ __('shop::app.fbo-detail.fbo-guest-button') }}
            </button> --}}
                        <p class="text-center mt-3"> Make your next checkout faster! <br>Create an account today.</p>
                        <div class="col-12  mb-5 thank__create d-flex justify-content-center">
                            <a href="{{ route('shop.customer.session.create') }}?form=register">
                                <button class="btn-lg  bg-light create_account_button">Sign Up & Save Time</button>
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        </div>
    @endsection

    {{-- @push('scripts')
<script>
    $(document).ready(function() {
                $(".custom-dropdown select").hover(function() {
                    $(this).find("option").css("background-color", "");
                });
            });
</script>
@endpush --}}
@else
    @section('content-wrapper')
       <div id="checkout-second-section" data-second-section="true"></div> 

{{-- @dd($fboDetails) --}}
        <checkout></checkout>
    @endsection

    @push('scripts')
        <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

        {{-- @include('shop::checkout.cart.coupon') --}}

        <script type="text/x-template" id="checkout-template">
    <div class="container">
        <div id="checkout" class="checkout-process row offset-lg-1 col-lg-11 col-md-12">
            <h1 class="col-12 text-center my-4">{{ __('velocity::app.checkout.checkout') }}</h1>
            <div class="col-lg-6 col-md-6 col-12 border checkout-left">
                <div class="step-content information" id="address-section" tabindex="-1">
                    @include('shop::checkout.onepage.customer-info')
                </div>


                

                <!-- customer info -->
                <input type='hidden' id='input_airport_id'/>
                <div class="container custom-checkout">
                    <div class="row border-top">
                            <div class="border-0" style="width: 100%;">
                                <div class="card-body fbo-body"> 

                                    <div class='row mt-4'>
                                        <div class='col-9'><h4 class="card-title fw6">{{__('shop::app.fbo-detail.client-info')}}</h4></div>
                                        <div class='col-3 fbo-edit text-right'><span class="text-danger pointer" data-toggle="modal" data-target="#fboModal">edit</span></div>
                                            <div class='fbo-body px-3'>
                                                <h6 class="card-subtitle mb-2">{{$fboDetails->full_name}} </h6>
                                                <h6>{{$fboDetails->email_address}}</h6>
                                                <h6>{{$fboDetails->phone_number}}</h6>
                                                {{-- <h6 class="card-subtitle mb-2"><b>Date:</b> {{$formattedDate}} </h6>
                                                <h6 class="card-subtitle mb-2"><b>Time:</b> {{$fboDetails->delivery_time}} </h6> --}}
                                            </div>
                                    </div>   
                                    <div class='row mt-2'>
                                        <div class='col-9'><h5 class="card-title child-card-title fw6">{{__('shop::app.fbo-detail.aircraft-info')}}</h5></div>
                                            <div class='fbo-body px-3'>
                                                <h6>{{$fboDetails->tail_number}}</h6>
                                                <h6>{{$fboDetails->packaging_section}}</h6>
                                                <h6>{{$fboDetails->service_packaging}}</h6>
                                            </div>
                                     </div>
                                    <div class='row mt-2'>
                                        <div class='col-9'><h5 class="card-title child-card-title fw6">Delivery Time</h5></div>
                                            <div class='fbo-body px-3'>
                                                {{-- sandeep change date formate to usa date formate  --}}
                                                <h6>{{date('m-d-Y', strtotime( $fboDetails->delivery_date))}}</h6>
                                                <h6>{{$fboDetails->delivery_time}}</h6>
                                            </div>
                                    </div>   
                                </div>   
                            </div>
                    </div> 


                    <!-- Modal -->
                
                <div class="modal fade" id="fboModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header fbo-header align-items-center">
                            <h1 class="fs24 fw6 pl-2 m-0">
                                {{__('shop::app.fbo-detail.aircraft-info')}}
                            </h1>
                            <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" class="align-items-center">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body popup-content">
                                <div class="body col-12 border-0 p-3">
                                    <form action="{{ route('cateringpackage.shop.customer.update-fbo') }}" method="POST"
                                        >
                                        {{ csrf_field() }}
                                        <div class="row mb-3">
                                            <div class="col-12 text-center">
                                                <h1 class="fs24 fw6">{{__('shop::app.fbo-detail.client-info')}}</h1>
                                            </div>
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('fullname') ? 'has-error' : '']">
                                                <label for="fullname" class="required label-style mandatory">
                                                  Full Name
                                                </label>
                                                <input type="text" class="form-control form-control-lg" value="{{ $fboDetails->full_name }}"
                                                    v-validate="'required'" name='fullname' />
                                                <span class="control-error" v-if="errors.has('fullname')" v-text="errors.first('fullname')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('phonenumber') ? 'has-error' : '']">
                                                <label for="phone number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.phone-number') }}
                                                </label>
                                                <input type="text" class="form-control form-control-lg" id="phone" value="{{ $fboDetails->phone_number }}"
                                                    name="phonenumber" v-validate="'required|min:14'" />
                                                <span class="control-error" v-if="errors.has('phonenumber')" v-text="errors.first('phonenumber')"></span>
                                            </div>

                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('email') ? 'has-error' : '']">
                                                <label for="email" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.email-address') }}
                                                </label>
                                                <input type="email" class="form-control form-control-lg" value="{{ $fboDetails->email_address }}"
                                                    name="email" v-validate="'required'" />
                                                <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                                            </div>
                                        </div>   

                                        <div class="row mb-3">
                                            <div class="col-12 text-center">
                                                <h1 class="fs24 fw6">{{__('shop::app.fbo-detail.aircraft-info')}}</h1>
                                            </div>
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('tailnumber') ? 'has-error' : '']">
                                                <label for="tail number" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.tail-number') }}
                                                </label>
                                                <input type="text" class="control form-control form-control-lg" value="{{ $fboDetails->tail_number }}" name="tailnumber"
                                                    v-validate="'required'">
                                                <span class="control-error" v-if="errors.has('tailnumber')"
                                                    v-text="errors.first('tailnumber')"></span>
                                            </div>
                    
                                            {{-- <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                :class="[errors.has('packagingsection') ? 'has-error' : '']">
                                                <label for="packaging section" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.packaging-section') }}
                                                </label>
                    
                                                <input type="text" class="form-control form-control-lg" value={{$fboDetails->packaging_section}} name="packagingsection"
                                                    v-validate="'required'" />
                                                <span class="control-error" v-if="errors.has('packagingsection')"
                                                    v-text="errors.first('packagingsection')"></span>
                                            </div> --}}

                                            <div class="control-group col-sm-12 col-md-6 mb-3 packagingsection"
                                :class="[errors.has('packagingsection') ? 'has-error' : '']">

                                <label for="packaging section" class="required label-style">
                                    {{ __('shop::app.fbo-detail.packaging-section') }}
                                </label>

                                <div class="custom-dropdown">
                                    <select class="form-control form-control-lg" name="packagingsection" v-validate="'required'">
                                        <option value="" disabled>Select Packaging</option>
                                        <option value="Microwave" {{ $fboDetails->packaging_section == 'Microwave' ? 'selected' : '' }}>Microwave</option>
                                        <option value="Oven" {{ $fboDetails->packaging_section == 'Oven' ? 'selected' : '' }}>Oven</option>
                                        <option value="Both" {{ $fboDetails->packaging_section == 'Both' ? 'selected' : '' }}>Both</option>
                                    </select>
                                </div>
                                <span class="control-error" v-if="errors.has('packagingsection')"
                                    v-text="'The packaging section field is required'"></span>
                                
                            </div>
                            {{-- SERVICE PACKAGING --}}
                                            <div class="control-group col-sm-12 col-md-6 mb-3 servicepackaging"
                                                :class="[errors.has('servicepackaging') ? 'has-error' : '']">

                                                <label for="packaging section" class="required label-style">
                                                    Service Packaging
                                                </label>

                                                <div class="custom-dropdown">
                                                    <select class="form-control form-control-lg" name="servicepackaging" v-validate="'required'">
                                                        <option value="" disabled>Select Packaging</option>
                                                        <option value="Bulk Packaging" {{ $fboDetails->service_packaging == 'Bulk Packaging' ? 'selected' : '' }}>Bulk Packaging</option>
                                                        <option value="Ready For Services" {{ $fboDetails->service_packaging == 'Ready For Services' ? 'selected' : '' }}>Ready For Services</option>
                                                    </select>
                                                </div>
                                                <span class="control-error" v-if="errors.has('servicepackaging')"
                                                    v-text="'The packaging section field is required'"></span>
                                
                                            </div>
                                        </div>
                                        <h1 class="text-center fs24 fw6 mt-1 margin-top-2">Delivery Time</h1>
                                        <div class="row">
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('delivery_date') ? 'has-error' : '']">
                                                <label for="Delivery Date" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.fbo-delivery-date') }}
                                                </label>
                                                <input type="text" readonly id="daySelect" class="control form-control form-control-lg" value="{{$formattedDate}}" name="delivery_date"
                                                    v-validate="'required'">
                                                    <div class="delivery_select  delivery_select_date">
                                                        <ul id="dayList"></ul>
                                                    </div>
                                                <span class="control-error" v-if="errors.has('delivery_date')"
                                                    v-text="errors.first('delivery_date')"></span>
                                            </div>
                                            <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3" :class="[errors.has('delivery_date') ? 'has-error' : '']">
                                                <label for="Delivery Date" class="required label-style">
                                                    {{ __('shop::app.fbo-detail.fbo-delivery-time') }}
                                                </label>
                                                <input type="text"  id="timeSlots" readonly class="control form-control form-control-lg" value="{{ $fboDetails->delivery_time }}" name="delivery_time"
                                                    v-validate="'required'">
                                                    <div class="delivery_select delivery_select_time">
                                                        <ul id="timeSlotsList"></ul>
                                                    </div>
                                                <span class="control-error" v-if="errors.has('delivery_time')"
                                                    v-text="errors.first('delivery_time')"></span>
                                            </div>
                                        </div>

                                        <button class="fbo-btn mt-3 m-auto fbo_Info fbo_detail_button" type="submit">
                                            {{ __('shop::app.fbo-detail.fbo-update') }}
                                        </button>
                                    </form>
                                </div>
                        </div>
                        </div>
                    </div>
                </div>
{{-- @dd(auth()->guard('customer')->user()->id) --}}
                {{-- payment modal --}}
                <button class="payment-model-btn" data-toggle="modal" data-target="#payment_model" >payment model</button>
                <div class="modal fade" id="payment_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header fbo-header">
                            <h1 class="fs24 fw6 mt-1">
                              Save Card
                            </h1>
                            <button type="button" class="close save-payment-close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body popup-content">
                                <div class="body col-12 border-0 p-0">
                                    <form action="" method="POST"
                                        @submit.prevent="onSubmit">
                                        {{ csrf_field() }}
                                        <div class="row mb-3">
                                            <p class="px-3">Save cards securely for future use with our convenient feature. Enjoy hassle-free transactions  and seamless experiences by storing your card details for future transactions</p>
                                            <div class="row w-100">
                                                <div class="col">
                                                    <button type="button" class="btn btn-primary accept">Ok</button>
                                                </div>
                                                <div class="col">
                                                    <button type="button" class="btn btn-primary cancel">Cancel</button>
                                                </div>
                                        </div>
                                    </div>

                                    </form>
                                </div>
                        </div>
                        </div>
                    </div>
                </div>

                {{-- end payment modal --}}

                {{-- delete payment modal --}}
                <button class="payment-delete-model-btn" data-toggle="modal" data-target="#payment_delete_model" >delete model</button>
                <div class="modal fade" id="payment_delete_model" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <form action="" method="POST"
                                        @submit.prevent="onSubmit">
                                        {{ csrf_field() }}
                                        <div class="row mb-3">
                                            <p class="px-3">Are you sure you want to delete this card? Confirming will permanently remove the card from your account.</p>
                                            <div class="row w-100">
                                                <div class="col">
                                                    <button type="button" class="btn btn-primary accept">Ok</button>
                                                </div>
                                                <div class="col">
                                                    <button type="button" class="btn btn-primary cancel">Cancel</button>
                                                </div>
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
                <!-- end customer info -->
            

                <div class="row checkout-summary add_mt_to_summary border-top">
                    <div class="border-0 order-summary w-100">
                        <div class="card-body  fw6">
                            <div class="summery_heading col-12 p-0 d-flex justify-content-between">
                            <h4 class="card-title order-summary fw6">Order Summary</h4> 
                            <span class=""><a href="{{ route('shop.product.parentcat', ['summaryedit']) }}" class="text-danger pointer" id="OrderSumarry_edit">edit</a></span>
                            </div>
                            <ol class='summary-body'>
                                @foreach($cart->items as $cartItem)
                                @php
                                $optionLabel = null;
                                
                                if(isset($cartItem->additional['attributes'])){
                                $attributes = $cartItem->additional['attributes'];

                                foreach ($attributes as $attribute) {
                                if(isset($attribute['option_label']) && $attribute['option_label']!=''){
                                $optionLabel = $attribute['option_label'];
                                }
                            }
                            }
                            @endphp
                                <li>
                                    <div class='row mx-1'>
                                        <div class='col-8'>
                                            <h6 class="items">{{$cartItem->name}}</h6>
                                            @if (isset($optionLabel) )
                                            <p class="checkout__options" style="font-size:13px"><strong>Preference: </strong><span>{{$optionLabel}}</span> </p>
                                            @endif
                                        </div>
                                        <div class='col-4 text-right'>
                                            <p><strong>Qty: </strong>{{$cartItem->quantity}}</p>
                                        </div>
                                        <div class='col-12' style='margin-top: -10px;font-size:13px'>
                                            @if (isset($cartItem->additional['special_instruction']) && $cartItem->additional['special_instruction']!='' )
                                            <p style='margin-top: -17px' class="special-intruction"><strong>Special Instruction: </strong> <span>{{$cartItem->additional['special_instruction']}}</span></p>
                                            @endif
                                        </div>
                                    </div>
                                </li>  
                                @endforeach 
                            </ol>
                        </div>
                    </div>
                </div>

                <div
                    class="step-content shipping"
                    id="shipping-section"
                    v-if="showShippingSection"
                >
                <shipping-section 
                style='display : none' 
                type='hidden'
                :methods="allShippingMethods"
                :key="shippingComponentKey"
                @onShippingMethodSelected="shippingMethodSelected($event)">
                </shipping-section> 
            </div>

            

                <!-- <div
                    class="step-content review"
                    id="summary-section"
                    v-if="showSummarySection">
                    <review-section :key="reviewComponentKey">
                        <div slot="summary-section">
                            <summary-section
                            style='display : none' 
                                discount="1"
                                :key="summaryComponentKey"
                                @onApplyCoupon="getOrderSummary"
                                @onRemoveCoupon="getOrderSummary"
                            ></summary-section>
                        </div>
                        
                        <div slot="place-order-btn">
                            <div class="mb20"> 
                            </div>
                        </div>
                    </review-section>
                </div>  -->

            </div>
            <div class='col-lg-6 col-md-6 col-12 border checkout-right'>
            <div
                    class="step-content payment"
                    id="payment-section"
                    v-if="showPaymentSection">
                    <payment-section
                        @onPaymentMethodSelected="paymentMethodSelected($event)">
                    </payment-section> 

                    <!-- <coupon-component
                        @onApplyCoupon="getOrderSummary"
                        @onRemoveCoupon="getOrderSummary">
                    </coupon-component> - -->
                </div>


                






                <!-- <shipping-section  
                {{-- style='display : none'  --}}
                type='hidden'
                :methods="allShippingMethods"
                :key="shippingComponentKey"
                @onShippingMethodSelected="shippingMethodSelected($event)">
                </shipping-section>  -->

                <div
                    class="step-content payment"
                    id="payment-section"
                    v-if="showPaymentSection">
                    <!-- <payment-section
                        @onPaymentMethodSelected="paymentMethodSelected($event)">
                    </payment-section> -->

                    {{-- <coupon-component
                        @onApplyCoupon="getOrderSummary"
                        @onRemoveCoupon="getOrderSummary">
                    </coupon-component> --}}
                </div>
                {{-- sandeep add card error message --}}
                <div class="card_erorr_message p-2 d-none" style="color:red;">
                <span class="payment_error_message"></span>
                </div>
                
                <div class="acknowledge_checkbox d-flex pt-3">
                    <input type="checkbox" id="acknowledge_checkbox" class="mt-1"/>
                    <p>I acknowledge that this is an order request. 
                        This order will reviewed and confirmed by the Volanti team. If your order is within 24 hours we suggest you follow up with a phone call after submission.</p>
                </div>

                <div class='place-order-btn'>
                    <button
                    type="button"
                    class="theme-btn"   
                    {{-- :disabled="!isPlaceOrderEnabled" --}}
                    @click="placeOrder()"                    
                    v-if="selected_payment_method.method != 'paypal_smart_button'"
                    id="checkout-place-order-button">
                    {{ __('shop::app.checkout.onepage.place-order') }}
                    <span class="btn-ring"></span>
                    </button>
                </div>
            </div>


            <!-- hide cart summary -->
            <!-- <div class="col-lg-12 col-md-12 offset-lg-1 order-summary-container top pt0">
                <summary-section :key="summaryComponentKey"></summary-section>

                <div class="paypal-button-container mt10"></div>--> 

            
    
            </div> 

        </div>
    </div>
</script>

        <script type="text/javascript">
            var paymentsaved = false;
            var checkPlacedOrder = '';
            var isAiportFboSelected = $('#airport-fbo-input').val() != '' ? true : false;
            (() => {

                    var reviewHtml = '';
                    var paymentHtml = '';
                    var summaryHtml = '';
                    var shippingHtml = '';
                    var paymentMethods = '';
                    var customerAddress = '';
                    var shippingMethods = '';

                    var reviewTemplateRenderFns = [];
                    var paymentTemplateRenderFns = [];
                    var summaryTemplateRenderFns = [];
                    var shippingTemplateRenderFns = [];

                    @auth('customer')

                        @if (auth('customer')->user()->addresses)
                            customerAddress = @json($addresses);
                            customerAddress.email = "{{ auth('customer')->user()->email }}";
                            customerAddress.first_name = "{{ auth('customer')->user()->first_name }}";
                            customerAddress.last_name = "{{ auth('customer')->user()->last_name }}";
                        @endif
                    @endauth

                    Vue.component('checkout', {
                            template: '#checkout-template',
                            inject: ['$validator'],

                            data: function() {
                                return {
                                    allAddress: {},
                                    current_step: 1,
                                    completed_step: 0,
                                    isCheckPayment: true,
                                    is_customer_exist: 0,
                                    disable_button: true,
                                    shippingComponentKey: 0,
                                    reviewComponentKey: 0,
                                    summaryComponentKey: 0,
                                    showPaymentSection: false,
                                    showSummarySection: false,
                                    isPlaceOrderEnabled: true,
                                    new_billing_address: false,
                                    showShippingSection: false,
                                    new_shipping_address: false,
                                    selected_payment_method: '',
                                    selected_shipping_method: '',
                                    isShippingMethod: false,
                                    countries: [],
                                    countryStates: [],
                                    allShippingMethods: [],

                                    step_numbers: {
                                        'information': 1,
                                        'shipping': 2,
                                        'payment': 3,
                                        'review': 4
                                    },

                                    address: {
                                        billing: {
                                            address1: [''],
                                            save_as_address: false,
                                            use_for_shipping: true,
                                            country: '',
                                        },

                                        shipping: {
                                            address1: [''],
                                            country: '',
                                        },
                                    },
                                }
                            },

                            created: function() {

                                this.saveShipping();
                                this.fetchCountries();

                                this.fetchCountryStates();

                                this.getOrderSummary();

                                //console.log('customer address', customerAddress);
                                if (!customerAddress) {
                                    this.new_shipping_address = true;
                                    this.new_billing_address = true;
                                } else {
                                    this.address.billing.first_name = this.address.shipping.first_name =
                                        customerAddress
                                        .first_name;
                                    this.address.billing.last_name = this.address.shipping.last_name =
                                        customerAddress
                                        .last_name;
                                    this.address.billing.email = this.address.shipping.email = customerAddress
                                        .email;

                                    if (customerAddress.length < 1) {
                                        this.new_shipping_address = true;
                                        this.new_billing_address = true;
                                    } else {
                                        this.allAddress = customerAddress;

                                        for (let country in this.countries) {
                                            for (let code in this.allAddress) {
                                                if (this.allAddress[code].country) {
                                                    if (this.allAddress[code].country == this.countries[country]
                                                        .code) {
                                                        this.allAddress[code]['country'] = this.countries[country]
                                                            .name;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            },

                            methods: {

                                navigateToStep: function(step) {



                                    if (step <= this.completed_step) {
                                        this.current_step = step;
                                        this.completed_step = step - 1;
                                    }
                                },

                                fetchCountries: function() {

                                    let countriesEndPoint = '{{ route('shop.countries') }}';

                                    this.$http.get(countriesEndPoint)
                                        .then(response => {
                                            this.countries = response.data.data;
                                        })
                                        .catch(function(error) {});
                                },

                                fetchCountryStates: function() {
                                    let countryStateEndPoint = '{{ route('shop.countries.states') }}';

                                    this.$http.get(countryStateEndPoint)
                                        .then(response => {
                                            this.countryStates = response.data.data;
                                        })
                                        .catch(function(error) {});
                                },

                                haveStates: function(addressType) {
                                    if (this.countryStates[this.address[addressType].country] && this.countryStates[
                                            this
                                            .address[addressType].country].length)
                                        return true;

                                    return false;
                                },

                                validateForm: async function(scope) {

                                    var isManualValidationFail = false;

                                    if (scope == 'address-form') {
                                        isManualValidationFail = this.validateAddressForm();

                                    }


                                    await this.$validator.validateAll(scope)

                                        .then(result => {
                                            if (result) {

                                                //             if(scope=='saved-payment-form'){
                                                //     this.savePayment();
                                                // }
                                                switch (scope) {
                                                    case 'address-form':

                                                        /* loader will activate only when save as address is clicked */
                                                        if (this.address.billing.save_as_address) {
                                                            this.$root.showLoader();
                                                        }

                                                        /* this is outside because save as address also calling for
                                                           saving the address in the order only */

                                                        this.saveAddress();
                                                        break;

                                                    case 'shipping-form':
                                                        if (this.showShippingSection) {
                                                            this.$root.showLoader();
                                                            // this.saveShipping();
                                                            break;
                                                        }

                                                    case 'payment-form':
                                                        this.$root.showLoader();
                                                        this.savePayment();

                                                        break;

                                                    default:
                                                        break;
                                                }

                                            } else {
                                                this.isPlaceOrderEnabled = false;
                                            }
                                        });

                                },

                                validateAddressForm: function() {
                                    var isManualValidationFail = false;

                                    let form = $(document).find('form[data-vv-scope=address-form]');

                                    // validate that if all the field contains some value
                                    if (form) {
                                        form.find(':input').each((index, element) => {
                                            let value = $(element).val();
                                            let elementId = element.id;

                                            if (value == "" &&
                                                element.id != 'sign-btn' &&
                                                element.id != 'billing[company_name]' &&
                                                element.id != 'billing[country]' &&
                                                element.id != 'billing[state]' &&
                                                element.id != 'billing[postcode]' &&
                                                element.id != 'shipping[company_name]' &&
                                                element.id != 'shipping[country]' &&
                                                element.id != 'shipping[state]' &&
                                                element.id != 'shipping[postcode]'
                                            ) {
                                                // check for multiple line address
                                                if (elementId.match('billing_address_') ||
                                                    elementId.match('shipping_address_')
                                                ) {
                                                    // only first line address is required
                                                    if (elementId == 'billing_address_0' ||
                                                        elementId == 'shipping_address_0'
                                                    ) {
                                                        isManualValidationFail = true;
                                                    }
                                                } else {
                                                    isManualValidationFail = true;
                                                }
                                            }
                                        });
                                    }

                                    // validate that if customer wants to use different shipping address
                                    if (!this.address.billing.use_for_shipping) {
                                        if (!this.address.shipping.address_id && !this.new_shipping_address) {
                                            isManualValidationFail = true;
                                        }
                                    }

                                    return isManualValidationFail;
                                },


                                saveShipping: async function() {

                                    this.disable_button = true;

                                    this.$http.post("{{ route('shop.checkout.save_shipping') }}", {
                                            'shipping_method': this.selected_shipping_method
                                        })
                                        .then(response => {

                                            this.$root.hideLoader();
                                            this.disable_button = false;
                                            this.showPaymentSection = true;

                                            paymentHtml = Vue.compile(response.data.html)

                                            this.completed_step = this.step_numbers[response.data
                                                    .jump_to_section] +
                                                1;

                                            this.current_step = this.step_numbers[response.data
                                                .jump_to_section];

                                            paymentMethods = response.data.paymentMethods;
                                            // paymentMethods = "mpauthorizenet"; //tanish



                                            if (this.selected_payment_method) {
                                                this.savePayment();
                                            }

                                            this.getOrderSummary();
                                        })
                                        .catch(error => {
                                            this.disable_button = false;
                                            this.$root.hideLoader();
                                            this.handleErrorResponse(error.response, 'shipping-form')
                                        })
                                },



                                isCustomerExist: function() {
                                    this.$validator.attach('address-form.billing[email]', 'required|email');

                                    this.$validator.validate('address-form.billing[email]', this.address.billing
                                            .email)
                                        .then(isValid => {
                                            if (!isValid)
                                                return;

                                            this.$http.post("{{ route('shop.customer.checkout.exist') }}", {
                                                    email: this.address.billing.email
                                                })
                                                .then(response => {
                                                    this.is_customer_exist = response.data ? 1 : 0;

                                                    if (response.data)
                                                        this.$root.hideLoader();
                                                })
                                                .catch(function(error) {})
                                        })
                                        .catch(error => {})
                                },

                                loginCustomer: function() {
                                    this.$http.post("{{ route('shop.customer.checkout.login') }}", {
                                            email: this.address.billing.email,
                                            password: this.address.billing.password
                                        })
                                        .then(response => {
                                            if (response.data.success) {
                                                window.location.href =
                                                    "{{ route('shop.checkout.onepage.show_fbo_detail') }}";
                                            } else {
                                                window.showAlert(`alert-danger`, this.__(
                                                    'shop.general.alert.danger'), response.data.error);
                                            }
                                        })
                                        .catch(function(error) {})
                                },

                                getOrderSummary: function() {
                                    this.$http.get("{{ route('shop.checkout.summary') }}")
                                        .then(response => {
                                            summaryHtml = Vue.compile(response.data.html)

                                            this.summaryComponentKey++;
                                            this.reviewComponentKey++;
                                        })
                                        .catch(function(error) {})
                                },

                                saveAddress: async function() {
                                    if (this.showPaymentSection || this.showSummarySection) {
                                        //   this.showPaymentSection = false;
                                        if ($('#mpauthorizenet').is(':checked')) {
                                            jQuery('.mpauthorizenet-add-card').css('display', 'block');
                                            jQuery('#saved-cards').css('display', 'block');
                                        }

                                        this.showSummarySection = false;
                                    }

                                    this.disable_button = true;

                                    if (this.$refs.billingSaveAsAddress && this.$refs.billingSaveAsAddress
                                        .checked) {
                                        this.$refs.billingSaveAsAddress.setAttribute('disabled', 'disabled');
                                    }

                                    if (this.allAddress.length > 0) {
                                        let address = this.allAddress.forEach(address => {
                                            //console.log(address);

                                            if (address.id == this.address.billing.address_id) {

                                                this.address.billing.address1 = [address.address1];
                                                this.address.billing.airport_name = [address.airport_name];

                                                if (address.email) {
                                                    this.address.billing.email = address.email;
                                                }

                                                if (address.first_name) {
                                                    this.address.billing.first_name = address.first_name;
                                                }

                                                if (address.last_name) {
                                                    this.address.billing.last_name = address.last_name;
                                                }

                                                if (address.country) {
                                                    this.address.billing.country = address.country;
                                                }
                                            }

                                            if (address.id == this.address.shipping.address_id) {
                                                this.address.shipping.address1 = [address.address1];
                                                this.address.shipping.airport_name = [address.airport_name];

                                                if (address.email) {
                                                    this.address.shipping.email = address.email;
                                                }

                                                if (address.first_name) {
                                                    this.address.shipping.first_name = address.first_name;
                                                }

                                                if (address.last_name) {
                                                    this.address.shipping.last_name = address.last_name;
                                                }

                                                if (address.country) {
                                                    this.address.shipping.country = address.country;
                                                }
                                            }
                                        });
                                    }

                                    this.$http.post("{{ route('shop.checkout.save_address') }}", this.address)
                                        .then(response => {

                                            // sandeep delete  && $('#airport-fbo-input').val() != ''
                                            if ($('.authorze_payment_row input[type="radio"]').prop('checked')) {
                                                this.disable_button = false;
                                                this.isPlaceOrderEnabled = true;
                                                // $('#checkout-place-order-button').prop('disabled', false);
                                            }

                                            if (this.step_numbers[response.data.jump_to_section] == 2) {
                                                this.showShippingSection = true;
                                                shippingHtml = Vue.compile(response.data.html);
                                            } else {
                                                paymentHtml = Vue.compile(response.data.html)
                                            }
                                            if (response.data.airport_id) {
                                                $('#input_airport_id').val(response.data.airport_id);
                                                $('.airport__fbo__detail_wrapper').removeClass('d-none');
                                                // $('#airport-fbo-input').val('');
                                                $('#checkout_airport-fbo-list').hide();
                                                // $('.add_mt_to_summary').addClass('mt-0');

                                            }

                                            this.completed_step = this.step_numbers[response.data
                                                    .jump_to_section] +
                                                1;
                                            this.current_step = this.step_numbers[response.data
                                                .jump_to_section];

                                            if (response.data.jump_to_section == "payment") {
                                                this.showPaymentSection = true;
                                                paymentMethods = response.data.paymentMethods;
                                            }

                                            shippingMethods = response.data.shippingMethods;

                                            this.allShippingMethods = shippingMethods;

                                            this.shippingComponentKey++;

                                            this.getOrderSummary();

                                            this.$root.hideLoader();
                                        })
                                        .catch(error => {
                                            this.disable_button = false;
                                            this.$root.hideLoader();

                                            this.handleErrorResponse(error.response, 'address-form')
                                        })
                                },



                                savePayment: async function() {

                                    this.disable_button = true;

                                    if (this.isCheckPayment) {
                                        this.isCheckPayment = false;
                                        //console.log(this.selected_payment_method, '===');
                                        // 'payment': this.selected_payment_method
                                        this.$http.post("{{ route('shop.checkout.save_payment') }}", {
                                                'payment': this.selected_payment_method
                                            })
                                            .then(response => {

                                                this.isCheckPayment = true;

                                                // this.isPlaceOrderEnabled=false

                                                var address_checkbox = $('.address-container input[type="radio"]');

                                                if (response.config.data && shippingMethods !== '') {
                                                    // this.disable_button = false;                                
                                                    //     this.isPlaceOrderEnabled = true;
                                                } else {
                                                    // this.disable_button = true;                                
                                                    //     this.isPlaceOrderEnabled = false;
                                                }
                                                // this.disable_button = true;                                
                                                //         this.isPlaceOrderEnabled = true;

                                                //      address_checkbox.each(function() {                             
                                                //     if ($(this).prop('checked') ) {
                                                //         //console.log($(this).prop('checked'),'----===');
                                                //         this.disable_button = false;                                
                                                //         this.isPlaceOrderEnabled = true;

                                                //         //console.log('addres checksdbdfbsdfdf'); 

                                                //     }

                                                // })
                                                this.showSummarySection = true;
                                                this.$root.hideLoader();

                                                reviewHtml = Vue.compile(response.data.html)
                                                this.completed_step = this.step_numbers[response.data
                                                    .jump_to_section] + 1;
                                                this.current_step = this.step_numbers[response.data
                                                    .jump_to_section];

                                                document.body.style.cursor = 'auto';

                                                this.getOrderSummary();
                                            })
                                            .catch(error => {
                                                this.disable_button = false;
                                                this.$root.hideLoader();
                                                this.handleErrorResponse(error.response, 'payment-form')
                                            });
                                    }
                                },

                                placeOrder: async function() {
                                    // sandeep|| add code for when payment not save then open payment popop
                                    var rediocheked = $('input[name="saved-card"]:checked');

                                // sandeep add code || check redio buttons and airport name
                                var address_checkbox = $('.address-container input[type="radio"]:checked');
                                var selected_airport_id = $('#selected_airport_id').val();
                                var acknowledge_checkbox = $('#acknowledge_checkbox').is(':checked');
                                var fbo_name = $('#airport_fbo_details').find('#AirportFbo_Name').text().trim();
                                if (!address_checkbox || !fbo_name) {
                                    let button = document.querySelector('.add-address-btn');
                                    if(!button){
                                        button = document.querySelector('#address-section');
                                    }
                                    if (button) {
                                    button.classList.add('checkout_error_button_message');
                                    button.scrollIntoView({ behavior: 'smooth', block: 'center' });

                                    setTimeout(() => {
                                        button.classList.remove('checkout_error_button_message'); 
                                    }, 3000); 
                                }
                                    return;
                                }
                                if (!paymentsaved) {
                                    let button = document.querySelector('.payment-methods');
                                    if (button) {
                                    button.classList.add('checkout_error_button_message');
                                    button.scrollIntoView({ behavior: 'smooth', block: 'center' });

                                    setTimeout(() => {
                                        button.classList.remove('checkout_error_button_message'); 
                                    }, 3000); 
                                }
                                return;
                                }

                                if(!acknowledge_checkbox){
                                    let button = document.querySelector('#acknowledge_checkbox');
                                    if (button) {
                                    button.classList.add('checkout_error_button_message');
                                    button.scrollIntoView({ behavior: 'smooth', block: 'center' });

                                    setTimeout(() => {
                                        button.classList.remove('checkout_error_button_message'); 
                                    }, 3000); 
                                }
                                    return;
                                }

                                var checkout_button = $('#checkout-place-order-button');
                                if (!acknowledge_checkbox || address_checkbox.length === 0 || !paymentsaved ||
                                    fbo_name === "") {
                                    return;
                                }

                                $(checkout_button).html('<span class="btn-ring"></span>');
                                $(".btn-ring").show();

                                // if (this.isPlaceOrderEnabled) {

                                    this.disable_button = false;
                                    this.isPlaceOrderEnabled = true;
                                    // $('.address-container #checked-radio input').each(function() {
                                    //     if ($(this).prop('checked')) {
                                    //         checkedCount++;
                                    //     }
                                    // });
                                    // if (checkedCount > 0) {
                                    //     this.isPlaceOrderEnabled = false;
                                    // } else {
                                    //     this.isPlaceOrderEnabled = true;
                                    // }

                                    this.$root.showLoader();
                                    //console.log('345465');

                                    this.$http.post("{{ route('shop.checkout.save_order') }}", {
                                            '_token': "{{ csrf_token() }}"
                                        })
                                        .then(response => {
                                            if (response.data.success) {
                                                if (response.data.redirect_url) {
                                                    this.$root.hideLoader();
                                                    window.location.href = response.data.redirect_url;
                                                } else {
                                                    this.$root.hideLoader();
                                                    window.location.href =
                                                        "{{ route('shop.checkout.success') }}";
                                                }
                                            }
                                        })
                                        .catch(error => {
                                            this.disable_button = true;
                                            this.$root.hideLoader();

                                            // sandeep add code || add button original html
                                            $('#checkout-place-order-button').replaceWith(`
                                            <button type="button" id="checkout-place-order-button" class="theme-btn">
                                                Place Order
                                                <span class="btn-ring"></span>
                                            </button>
                                        `);
                                            window.showAlert(`alert-danger`, this.__(
                                                    'shop.general.alert.danger'), error.response.data
                                                .message ? error.response.data.message :
                                                "{{ __('shop::app.common.error') }}");
                                        })
                                // } else {
                                //     this.disable_button = true;
                                // }
                            },

                            handleErrorResponse: function(response, scope) {
                                // sandeep add code
                                if (xhr.status === 419) {
                                    window.location.href = '/';
                                }
                                if (response.status == 422) {
                                    serverErrors = response.data.errors;
                                    this.$root.addServerErrors(scope)
                                } else if (response.status == 403) {
                                    if (response.data.redirect_url) {
                                        window.location.href = response.data.redirect_url;
                                    }
                                }
                            },

                            shippingMethodSelected: function(shippingMethod) {
                                this.selected_shipping_method = shippingMethod;
                            },

                            paymentMethodSelected: function(paymentMethod) {
                                // //console.log(paymentMethod)
                                this.selected_payment_method = paymentMethod;
                            },

                            newBillingAddress: function() {
                                this.new_billing_address = true;
                                this.isPlaceOrderEnabled = true;
                                this.address.billing.address_id = null;

                                setTimeout(() => {
                                    if (
                                        this.$refs.billingSaveAsAddress &&
                                        this.$refs.billingSaveAsAddress.checked
                                    ) {
                                        this.$refs.billingSaveAsAddress.setAttribute('disabled',
                                            'disabled');
                                    }
                                }, 0);
                            },

                            newShippingAddress: function() {
                                this.new_shipping_address = true;
                                this.isPlaceOrderEnabled = true;
                                this.address.shipping.address_id = null;
                            },

                            backToSavedBillingAddress: function() {
                                this.new_billing_address = false;
                                this.validateFormAfterAction()
                            },

                            backToSavedShippingAddress: function() {
                                this.new_shipping_address = false;
                                this.validateFormAfterAction()
                            },

                            validateFormAfterAction: function() {
                                setTimeout(() => {
                                    this.validateForm('address-form');
                                }, 0);
                            }
                        },

                        watch: {
                            address: {
                                handler: function(v) {
                                    if (
                                        this.$refs.billingSaveAsAddress &&
                                        this.$refs.billingSaveAsAddress.hasAttribute('disabled')
                                    ) {
                                        this.$refs.billingSaveAsAddress.removeAttribute('disabled');
                                        this.$refs.billingSaveAsAddress.checked = false;
                                    }
                                },
                                deep: true
                            }
                        },
                    });

                Vue.component('shipping-section', {
                    inject: ['$validator'],

                    props: {
                        methods: {
                            type: Object,
                            default: {}
                        },
                    },

                    data: function() {
                        return {
                            templateRender: null,

                            selected_shipping_method: '',

                            first_iteration: true,
                        }
                    },

                    staticRenderFns: shippingTemplateRenderFns,

                    mounted: function() {
                        this.templateRender = shippingHtml.render;

                        for (let i in shippingHtml.staticRenderFns) {
                            shippingTemplateRenderFns.push(shippingHtml.staticRenderFns[i]);
                        }

                        eventBus.$emit('after-checkout-shipping-section-added');
                    },

                    render: function(h) {
                        return h('div', [
                            (this.templateRender ?
                                this.templateRender() :
                                '')
                        ]);
                    },

                    created: function() {
                        if (Object.keys(this.methods).length == 1) {
                            let firstMethod = Object.keys(this.methods)[0];

                            let methodRateObject = this.methods[firstMethod]['rates'][0];
                            this.selected_shipping_method = methodRateObject.method;
                            this.methodSelected();
                        }
                    },

                    methods: {
                        methodSelected: function() {
                            //   this.$parent.validateForm('shipping-form');

                            this.$emit('onShippingMethodSelected', this.selected_shipping_method)

                            eventBus.$emit('after-shipping-method-selected', this.selected_shipping_method);
                        }
                    }
                })

                Vue.component('payment-section', {
                    inject: ['$validator'],

                    data: function() {
                        return {
                            templateRender: null,

                            payment: {
                                method: ""
                            },

                            first_iteration: true,
                        }
                    },

                    staticRenderFns: paymentTemplateRenderFns,

                    mounted: function() {
                        this.templateRender = paymentHtml.render;

                        for (var i in paymentHtml.staticRenderFns) {
                            paymentTemplateRenderFns.push(paymentHtml.staticRenderFns[i]);
                        }

                        eventBus.$emit('after-checkout-payment-section-added');
                    },

                    render: function(h) {
                        return h('div', [
                            (this.templateRender ?
                                this.templateRender() :
                                '')
                        ]);
                    },

                    methods: {
                        methodSelected: function() {


                            this.$parent.validateForm('payment-form');
                            this.$parent.validateForm('saved-payment-form');

                            this.$emit('onPaymentMethodSelected', this.payment)

                            eventBus.$emit('after-payment-method-selected', this.payment);
                        }
                    }
                })

                Vue.component('review-section', {
                    data: function() {
                        return {
                            error_message: '',
                            templateRender: null,
                        }
                    },

                    staticRenderFns: reviewTemplateRenderFns,

                    render: function(h) {
                        return h(
                            'div', [
                                this.templateRender ? this.templateRender() : ''
                            ]
                        );
                    },

                    mounted: function() {
                        this.templateRender = reviewHtml.render;

                        for (var i in reviewHtml.staticRenderFns) {
                            reviewTemplateRenderFns[i] = reviewHtml.staticRenderFns[i];
                        }

                        this.$forceUpdate();
                    }
                });

                Vue.component('summary-section', {
                    inject: ['$validator'],

                    staticRenderFns: summaryTemplateRenderFns,

                    props: {
                        discount: {
                            default: 0,
                            type: [String, Number],
                        }
                    },

                    data: function() {
                        return {
                            changeCount: 0,
                            coupon_code: null,
                            error_message: null,
                            templateRender: null,
                            couponChanged: false,
                        }
                    },

                    mounted: function() {
                        this.templateRender = summaryHtml.render;

                        for (var i in summaryHtml.staticRenderFns) {
                            summaryTemplateRenderFns[i] = summaryHtml.staticRenderFns[i];
                        }

                        this.$forceUpdate();
                    },

                    render: function(h) {
                        return h('div', [
                            (this.templateRender ?
                                this.templateRender() :
                                '')
                        ]);
                    },

                    methods: {
                        onSubmit: function() {
                            var this_this = this;
                            const emptyCouponErrorText = "Please enter a coupon code";
                        },

                        changeCoupon: function() {
                            if (this.couponChanged == true && this.changeCount == 0) {
                                this.changeCount++;

                                this.error_message = null;

                                this.couponChanged = false;
                            } else {
                                this.changeCount = 0;
                            }
                        },
                    }
                });

            })();
        </script>
    @endpush
@endif
