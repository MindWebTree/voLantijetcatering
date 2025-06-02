@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.checkout.success.title') }}
@stop

@section('seo')
<meta name="title" content="{{ __('shop::app.checkout.success.title') }}" />
<meta name="description" content="Order successfully placed" />
<meta name="keywords" content="" />
<link rel="canonical" href="{{ url()->current() }}" />
@stop

@section('content-wrapper')
    {{-- <div class="container">
        <div class="order-success-content row col-12 offset-1">
            <h1 class="row col-12">{{ __('shop::app.checkout.success.thanks') }}</h1>

            <p class="row col-12">
                @if (auth()->guard('customer')->user())
                    {!! __('shop::app.checkout.success.order-id-info', [
                        'order_id' => '<a href="' . route('shop.customer.orders.view', $order->id) . '">' . $order->increment_id . '</a>',
                    ]) !!}
                @else
                    {{ __('shop::app.checkout.success.order-id-info', ['order_id' => $order->increment_id]) }}
                @endif
            </p>

            <p class="row col-12">
                {{ __('shop::app.checkout.success.info') }}
            </p>

            {{ view_render_event('bagisto.shop.checkout.continue-shopping.before', ['order' => $order]) }}
            <div class="row col-12 mt15">
                <span class="mb30 mr10">
                    <a href="{{ route('shop.home.index') }}" class="theme-btn remove-decoration">
                        {{ __('shop::app.checkout.cart.continue-shopping') }}
                    </a>
                </span>

                @guest('customer')
                    <span class="">
                        <a href="{{ route('shop.customer.register.index') }}" class="theme-btn remove-decoration">
                            {{ __('shop::app.checkout.cart.continue-registration') }}
                        </a>
                    </span>
                @endguest
            </div>
            {{ view_render_event('bagisto.shop.checkout.continue-shopping.after', ['order' => $order]) }}
        </div>
    </div> --}}






    <div class="container my-5 thank__you">
        <div class="thank__you text-center">
            <img class="mr-5 success_tick" src="./../themes/volantijetcatering/assets/images/tick.png" alt="">
            <h1 class="fw-6 text-center my-4">{{ __('shop::app.checkout.success.thanks') }}</h1>

        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-lg-5 col-md-6 col-12 thank__left">

                <div class="col-12 border my-3 thank__address thank__order">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/air-location.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Ordering From</h4>
                        </div>
                    </div>

                    <strong class="m-0 airport__name">{{ $orderDetails[0]->airport_name }}</strong>
                    <p>
                        {{ $orderDetails[0]->address1 }}
                    </p>
                </div>
                <div class="col-12 border my-3 thank__fbo thank__order pb-3">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/fbo.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Fbo Detail</h4>
                        </div>
                    </div>
                    {{-- @dd($orderDetails[0]) --}}
                    <h5 class="my-2 fs23 fw6">{{__('shop::app.fbo-detail.client-info')}}</h5>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_full_name }} </p>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_phone_number }} </p>
                    <p> {{ $orderDetails[0]->fbo_email_address }} </p>
                    <h5 class="my-2 fs23 fw6">{{__('shop::app.fbo-detail.aircraft-info')}}</h5>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_tail_number }} </p>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_packaging }} </p>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_service_packaging }} </p>
               @if(isset($fboDetails))
                    @if(!empty($fboDetails->delivery_date) || !empty($fboDetails->delivery_time))
                    <p class="m-0">
                        {{ date('m-d-Y', strtotime($fboDetails->delivery_date)) ?? '' }} {{ $fboDetails->delivery_time ?? '' }}
                    </p>
                @endif
                    @endif
                    <h5 class="my-2 fs23 fw6">Airport Fbo Detail</h5>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_airport_name }} </p>
                    <p class="m-0"> {{ $orderDetails[0]->fbo_airport_address }} </p>
                </div>
                @if (!auth()->guard('customer')->check())
                    <div class="col-12 border my-3 thank__create p-3 thank__order">
  <h5 class="fw-bold text-dark">Unlock Exclusive Benefits!</h5>
                        <p>Create an account to track your orders, save FBO details, securely manage payments, and enjoy a seamless checkout experience.
                        </p>

                        <a href="{{ route('shop.customer.session.create') }}?form=register">
                            <button class="btn-lg  bg-light">Create account</button>
                        </a>
                    </div>
                @else
                    {{ '' }}
                @endif

            </div>
            <div class="col-lg-5 col-md-6 col-12 thank__right ">
                <div class="col-12 border my-3 thank__summary thank__order">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/order-bag.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Order Summary</h4>
                        </div>
                    </div>
                    <ol class="summary-body pl-4 pl-lg-5">
                        @foreach ($orderDetails as $orderDetail)
                            <li>
                                <div class='row'>
                                    <div class='col-8'>
                                        <h6 class="items m-0">{{ $orderDetail->name }}</h6>
                                        {{-- @php
                                            $additionalData = json_decode($orderDetail->additional);
                                            dd($additionalData);
                                            $optionLabel = isset($additionalData->attributes->options->option_label) ? $additionalData->attributes->options->option_label : null;
                                        @endphp --}}

                                        @php
                                        $optionLabel = null;
                                        $additionalData = json_decode($orderDetail->additional);
                                            if (isset($additionalData->attributes)) {
                                                $attributes = $additionalData->attributes;

                                                foreach ($attributes as $attribute) {
                                                    if (isset($attribute->option_label) && $attribute->option_label != '') {
                                                        $optionLabel = $attribute->option_label;
                                                    }
                                                }
                                            }

                                        @endphp
                                        @if (isset($optionLabel))
                                            <p><strong>Preference:</strong> {{ $optionLabel }}</p>
                                        @endif
                                    </div>
                                    <div class='col-4'>
                                        <p><strong>Qty: </strong>{{ $orderDetail->qty_ordered }}</p>
                                    </div>
                                    <div class='col-8' style='margin-top: -6px'>
                                        @php
                                            $additionalData = json_decode($orderDetail->additional, true); // Decode as an associative array
                                            $specialInstruction = isset($additionalData['special_instruction']) ? $additionalData['special_instruction'] : null;
                                        @endphp

                                        @if ($specialInstruction)
                                            <p class="special-intruction" style="margin-top: -10px;overflow: auto;"><strong>Special
                                                    Instruction:
                                                </strong>{{ $specialInstruction }}</p>
                                            {{-- @else
                                            {{ '' }} --}}
                                        @endif
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="col-12 border-top my-3 thank__orderId thank__order p-0">
                        <div class="d-flex gap">
                            <div class="img mt-3">
                                <img src="./../themes/volantijetcatering/assets/images/list.png" alt="">
                            </div>
                            <div class="title">
                                <h4 class="fw-6 text-center mt-3">Additional Detail</h4>
                            </div>
                        </div>
                        <div class="row">

                            @if (auth()->guard('customer')->user())
                                <div class="col-8 pl-4">
                                    <strong>Order Number: </strong>
                                </div>
                                <div class="col-4 pl-4">
                                    <a
                                        href={{ route('shop.customer.orders.view', $order['id']) }}><strong>#{{ $order['id'] }}</strong></a>
                                </div>
                            @else
                                <div class="col-8">
                                    <strong>Order Number: </strong>
                                </div>
                                <div class="col-4 pl-4">
                                    <p>#{{ $order['id'] }}</p>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
