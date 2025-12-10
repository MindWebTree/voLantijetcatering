@extends('shop::layouts.master')
@section('content-wrapper')

<div class="container my-5 checkout_order_invoice p-4" style="max-width: 100% !important">
    <div class="head mb-4">
        <p>Please fill your email and fbo tail number to pay your order: <span>{{$order_detail->orderid}}</span></p>
    </div>
    
    <form action="{{route('order-invoice-view-detail',['orderid'=> $order_detail->orderid,'customerid'=> $order_detail->customerid])}}" method="POST" @submit.prevent="onSubmit">
        {{ csrf_field() }}
        <div class="row">
            <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('email') ? 'has-error' : '']">
                <label for="email" class="required label-style mandatory">
                    Email
                </label>
                <input type="email" class="form-control form-control-lg" 
                    v-validate="'required'" name='email' />
                <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
            </div>
            {{-- <input type="hidden" name="orderid" value="{{$order_detail->orderid}}"> --}}
            <div class="control-group col-sm-12 col-md-12 col-lg-12 mb-3" :class="[errors.has('tail_number') ? 'has-error' : '']">
                <label for="tail_number" class="required label-style">
                    Fbo tail number
                </label>
                <input type="text" class="form-control form-control-lg"
                    name="tail_number" v-validate="'required'" />
                <span class="control-error" v-if="errors.has('tail_number')" v-text="errors.first('tail_number')"></span>
            </div>
        </div>
        <button class="checkout_order_invoice_button my-3 m-auto" type="submit">
            Submit
        </button>
    </form>
</div>
@endsection