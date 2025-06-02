@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.signup-form.page-title') }}
@endsection

@section('content-wrapper')
    <div class="auth-content form-container">
        <div class="container register-form">
              {{-- <div class="row my-4"> --}}
                <div class=" row mx-auto register-buttons justify-content-center w-100 mt-3">
                    <a href="{{ route('shop.customer.session.index') }}" class="btn-new-customer col-lg-5 p-0">
                        <button type="button" class="theme-btn btn rounded-0 light w-100 items-start">
                            {{ __('velocity::app.customer.signup-form.login') }}
                        </button>
                    </a>
                    <a href="{{ route('shop.customer.register.index') }}" class="btn-new-customer col-lg-5 p-0">
                        <button type="button" class="theme-btn btn rounded-0 btn-login light w-100 items-end border border-danger ">
                            {{ __('velocity::app.customer.login-form.sign-up') }}
                        </button>
                        
                    </a>
                </div>
            {{-- </div> --}}
            <div class="col-lg-10 col-md-12 offset-lg-1">
                <h2 class="fs24 fw6 text-center register-head my-5">
                    {{ __('velocity::app.customer.signup-form.user-registration')}}
                </h2>
                <div class="body col-12 border-0 p-0">
                    {{-- <h3 class="fw6">
                        {{ __('velocity::app.customer.signup-form.become-user')}}
                    </h3>

                    <p class="fs16">
                        {{ __('velocity::app.customer.signup-form.form-signup-text')}}
                    </p> --}}

                    {!! view_render_event('bagisto.shop.customers.signup.before') !!}

                    <form
                        method="post"
                        action="{{ route('shop.customer.register.create') }}"
                        @submit.prevent="onSubmit">

                        {{ csrf_field() }}

                        <div class="row">
                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                        <div class="control-group col-6 mb-3" :class="[errors.has('first_name') ? 'has-error' : '']">
                            <label for="first_name" class="required label-style">
                                {{ __('shop::app.customer.signup-form.firstname') }}
                            </label>

                            <input
                                type="text"
                                class="form-control form-control-lg  "
                                name="first_name"
                                v-validate="'required'"
                                value="{{ old('first_name') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.firstname') }}&quot;" />

                            <span class="control-error" v-if="errors.has('first_name')" v-text="errors.first('first_name')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.firstname.after') !!}

                        <div class="control-group col-6 mb-3" :class="[errors.has('last_name') ? 'has-error' : '']">
                            <label for="last_name" class="required label-style">
                                {{ __('shop::app.customer.signup-form.lastname') }}
                            </label>

                            <input
                                type="text"
                                class="form-control form-control-lg  "
                                name="last_name"
                                v-validate="'required'"
                                value="{{ old('last_name') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.lastname') }}&quot;" />

                            <span class="control-error" v-if="errors.has('last_name')" v-text="errors.first('last_name')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.lastname.after') !!}

                        <div class="control-group col-6 mb-3" :class="[errors.has('email') ? 'has-error' : '']">
                            <label for="email" class="required label-style">
                                {{ __('shop::app.customer.signup-form.email') }}
                            </label>

                            <input
                                type="email"
                                class="form-control form-control-lg  "
                                name="email"
                                v-validate="'required|email'"
                                value="{{ old('email') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.email') }}&quot;" />

                            <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                        </div>
                        {{-- <div class="control-group col-6 mb-3" :class="[errors.has('Phone') ? 'has-error' : '']">
                            <label for="Phone Number" class="required label-style"> --}}
                                {{-- {{ __('shop::app.customer.signup-form.email') }} --}}  {{-- Phone Number --}}
                            {{-- </label>

                            <input
                                type="number"
                                class="form-control form-control-lg  "
                                name="Phone"
                                v-validate="'required|email'"
                                value="{{ old('email') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.email') }}&quot;" />

                            <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                        </div> --}}

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.email.after') !!}

                        <div class="control-group col-6 mb-3" :class="[errors.has('password') ? 'has-error' : '']">
                            <label for="password" class="required label-style">
                                {{ __('shop::app.customer.signup-form.password') }}
                            </label>

                            <input
                                type="password"
                                class="form-control form-control-lg  "
                                name="password"
                                v-validate="'required|min:6'"
                                ref="password"
                                value="{{ old('password') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.password') }}&quot;" />

                            <span class="control-error" v-if="errors.has('password')" v-text="errors.first('password')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.password.after') !!}

                        <div class="control-group col-6 mb-3" :class="[errors.has('password_confirmation') ? 'has-error' : '']">
                            <label for="password_confirmation" class="required label-style">
                                {{ __('shop::app.customer.signup-form.confirm_pass') }}
                            </label>

                            <input
                                type="password"
                                class="form-control form-control-lg  "
                                name="password_confirmation"
                                v-validate="'required|min:6|confirmed:password'"
                                data-vv-as="&quot;{{ __('shop::app.customer.signup-form.confirm_pass') }}&quot;" />

                            <span class="control-error" v-if="errors.has('password_confirmation')" v-text="errors.first('password_confirmation')"></span>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.password_confirmation.after') !!}
                        </div>
                        <div class="control-group">

                            {!! Captcha::render() !!}

                        </div>

                        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                            <div class="control-group">
                                <input type="checkbox" id="checkbox2" name="is_subscribed">
                                <span>{{ __('shop::app.customer.signup-form.subscribe-to-newsletter') }}</span>
                            </div>
                        @endif

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}

                        <button class="theme-btn register-btn" type="submit">
                            {{ __('shop::app.customer.signup-form.title') }}
                        </button>
                    </form>

                    {!! view_render_event('bagisto.shop.customers.signup.after') !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function(){
            $(":input[name=first_name]").focus();
        });
    </script>

{!! Captcha::renderJS() !!}

@endpush