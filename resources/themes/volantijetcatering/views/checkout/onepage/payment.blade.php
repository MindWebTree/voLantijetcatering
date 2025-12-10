{{-- sandeep add code --}}

@php
    // $cards = collect();
    if(auth()->guard('customer')->check()) {
        $customer_id = auth()->guard('customer')->user()->id;
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customer_id]);
    }
@endphp


<form data-vv-scope="payment-form" class="payment-form">
    <div class="form-container mt-3">
        <div class="form-header mb-30" slot="header">

            <h4 class="fw6 mb-4">
                {{-- {{ __('shop::app.checkout.onepage.payment-methods') }} --}}
                Payments
            </h4>

            <i class="rango-arrow"></i>
        </div>

        <div class="payment-methods" slot="body">
            @foreach ($paymentMethods as $payment)
                {!! view_render_event('bagisto.shop.checkout.payment-method.before', ['payment' => $payment]) !!}

                
                <div class="row col-12 mb-0 {{ $payment['method'] === 'mpauthorizenet' ? 'authorze_payment_row' : '' }}" style="justify-content: space-between">
                {{-- sandeep add code --}}
                <div class="radio d-none payment-saved">
                        @if($payment['method'] != 'mpauthorizenet')
                        <input type="radio" name="payment[method]" v-validate="'required'"
                            v-model="payment.method" checked @change="methodSelected()"  id="{{ $payment['method'] }}"
                            value="{{ $payment['method'] }}"                        
                            data-vv-as="&quot;{{ __('shop::app.checkout.onepage.payment-method') }}&quot;"/>

                        <label for="{{ $payment['method'] }}" class="radio-view"></label>
                        @else
                        <input type="radio" name="payment[method]" v-validate="'required'"
                        v-model="payment.method" @change="methodSelected()"  id="{{ $payment['method'] }}"
                        value="{{ $payment['method'] }}"    
                        class="authorze_payment_radio"                    
                        data-vv-as="&quot;{{ __('shop::app.checkout.onepage.payment-method') }}&quot;"/>

                    <label for="{{ $payment['method'] }}" class="radio-view"></label>
                    @endif
                    </div>

                    <div class="pl20 w-100 {{ $payment['method'] === 'mpauthorizenet' ? 'authorize-text' : '' }}">
                        <div class="row pl-2">
                            <span class="payment-method method-label">                               
                                @if($payment['method_title']=='Authorize Net')                                     
                                    <label for="{{ $payment['method'] }}" class="radio-view"><b>Debit or Credit Card</b></label>                         
                                @else
                                <b>{{ $payment['method_title'] }}</b>                                
                                @endif  
                                {{-- <span class="authorizedotnet-image d-inline-flex align-items-center">
                                    <label for="mpauthorizenet">
                                    <img src="{{ asset('themes/volantijetcatering/assets/images/authorize.net-logo.png') }}" alt="Authorize.net Logo" id="AuthorizeNet_image">
                                </label>
                                </span>    --}}
                            </span>
                            <div>
                                <img src="{{ asset('themes/volantijetcatering/assets/images/visa.png') }}" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="{{ asset('themes/volantijetcatering/assets/images/shopping.png') }}" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="{{ asset('themes/volantijetcatering/assets/images/discover.png') }}" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="{{ asset('themes/volantijetcatering/assets/images/american-express.png') }}" alt="Authorize.net Logo" width="35px" id="AuthorizeNet_image">
                            </div>

                        </div>

                        <div class="row">
                            @if($payment['method_title']!='Authorize Net')
                            <span class="method-summary">{{ $payment['description'] }}</span>
                            @endif
                        </div>

                        @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($payment['method']); @endphp

                        @if (!empty($additionalDetails))
                            <div class="instructions" v-show="payment.method == '{{ $payment['method'] }}'">
                                <label>{{ $additionalDetails['title'] }}</label>
                                <p>{{ $additionalDetails['value'] }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {!! view_render_event('bagisto.shop.checkout.payment-method.after', ['payment' => $payment]) !!}
            @endforeach

            <span class="control-error" v-if="errors.has('payment-form.payment[method]')"
                v-text="errors.first('payment-form.payment[method]')"></span>
        </div>
{{-- sandeep add credit card success message --}}
<span class="d-none card_success_message text-success"></span>



        {{-- <accordian :title="'{{ __('shop::app.checkout.payment-methods') }}'" :active="true"> --}}
            {{-- <div class="form-header mb-30" slot="header">

                <h3 class="fw6 display-inbl">
                    {{ __('shop::app.checkout.onepage.payment-methods') }}
                </h3>

                <i class="rango-arrow"></i>
            </div>

            <div class="payment-methods" slot="body">
                @foreach ($paymentMethods as $payment)
                    {!! view_render_event('bagisto.shop.checkout.payment-method.before', ['payment' => $payment]) !!}

                    <div class="row col-12">
                        <div class="radio">
                            <input type="radio" name="payment[method]" v-validate="'required'"
                                v-model="payment.method" @change="methodSelected()" id="{{ $payment['method'] }}"
                                value="{{ $payment['method'] }}"
                                data-vv-as="&quot;{{ __('shop::app.checkout.onepage.payment-method') }}&quot;" />

                            <label for="{{ $payment['method'] }}" class="radio-view"></label>
                        </div>

                        <div class="pl20">
                            <div class="row">
                                <span class="payment-method method-label">
                                    <b>{{ $payment['method_title'] }}</b>
                                </span>
                            </div>

                            <div class="row">
                                <span class="method-summary">{{ $payment['description'] }}</span>
                            </div>

                            @php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($payment['method']); @endphp

                            @if (!empty($additionalDetails))
                                <div class="instructions" v-show="payment.method == '{{ $payment['method'] }}'">
                                    <label>{{ $additionalDetails['title'] }}</label>
                                    <p>{{ $additionalDetails['value'] }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {!! view_render_event('bagisto.shop.checkout.payment-method.after', ['payment' => $payment]) !!}
                @endforeach

                <span class="control-error" v-if="errors.has('payment-form.payment[method]')"
                    v-text="errors.first('payment-form.payment[method]')"></span>
            </div> --}}
        {{-- </accordian> --}}
    </div>
</form>
