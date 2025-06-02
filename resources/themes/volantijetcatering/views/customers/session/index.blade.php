@extends('shop::layouts.master')

@section('page_title')
    {{ __('shop::app.customer.login-form.page-title') }}
@endsection

@section('content-wrapper')
    <div class="auth-content form-container">

        {!! view_render_event('bagisto.shop.customers.login.before') !!}

        <div class="container login-form">
            {{-- <div class="row my-4"> --}}
                <div class=" row mx-auto register-buttons justify-content-center w-100 mt-3">
                    <a href="{{ route('shop.customer.session.index') }}" class="btn-new-customer col-lg-5 p-0">
                        <button type="button" class="theme-btn btn rounded-0 btn-login light w-100 items-end border border-danger ">
                            {{ __('velocity::app.customer.signup-form.login') }}
                        </button>
                    </a>
                    <a href="{{ route('shop.customer.register.index') }}" class="btn-new-customer col-lg-5 p-0">
                        <button type="button" class="theme-btn btn rounded-0 light w-100 items-start">
                            {{ __('velocity::app.customer.login-form.sign-up') }}
                        </button>
                        
                    </a>
                </div>
            {{-- </div> --}}
            <div class="col-lg-10 col-md-12 offset-lg-1">
                <h1 class="fs24 fw6 text-center mt-5 login-head">
                        {{ __('velocity::app.customer.login-form.customer-login')}}
                    </h1>

                <div class="body col-12 border-0 p-0">
                    {{-- <div class="form-header">
                        <h3 class="fw6">
                            {{ __('velocity::app.customer.login-form.registered-user') }}
                        </h3>

                        <p class="fs16">
                            {{ __('velocity::app.customer.login-form.form-login-text') }}
                        </p>
                    </div> --}}

                    <form method="POST" action="{{ route('shop.customer.session.create') }}" @submit.prevent="onSubmit">

                        {{ csrf_field() }}

                        {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                        <div class="form-group" :class="[errors.has('email') ? 'has-error' : '']">
                            <label for="email" class="mandatory label-style">
                                {{ __('shop::app.customer.login-form.email') }}
                            </label>

                            <input type="text" class="form-control form-control-lg  email-field" name="email" v-validate="'required|email'"
                                value="{{ old('email') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.login-form.email') }}&quot;" />

                            <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                        </div>

                        <div class="form-group" :class="[errors.has('password') ? 'has-error' : '']">
                            <label for="password" class="mandatory label-style">
                                {{ __('shop::app.customer.login-form.password') }}
                            </label>

                            <input type="password" class="form-control form-control-lg password-field" name="password" id="password" v-validate="'required'"
                                value="{{ old('password') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.login-form.password') }}&quot;" />

                            <input type="checkbox" onclick="myFunction()" id="shoPassword" class="show-password" />

                            {{ __('shop::app.customer.login-form.show-password') }}


                            <div class="mt10">
                                <span class="control-error" v-if="errors.has('password')"
                                    v-text="errors.first('password')"></span>
                                @if (Cookie::has('enable-resend'))
                                    @if (Cookie::get('enable-resend') == true)
                                        <a
                                            href="{{ route('shop.customer.resend.verification_email', Cookie::get('email-for-resend')) }}">{{ __('shop::app.customer.login-form.resend-verification') }}</a>
                                    @endif
                                @endif
                            </div>
                        </div>

                        <div class="form-group">

                            {!! Captcha::render() !!}

                        </div>

                        {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}

                        <input class="theme-btn signIn-btn mx-auto " type="submit"
                            value="{{ __('shop::app.customer.login-form.button_title') }}">
                        <a href="{{ route('shop.customer.forgot_password.create') }}"
                            class=" forget-password">
                            {{ __('shop::app.customer.login-form.forgot_pass') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.customers.login.after') !!}
    </div>
@endsection

@push('scripts')
    {!! Captcha::renderJS() !!}

    <script>
        $(function() {
            $(":input[name=email]").focus();
        });

        function myFunction() {
            var x = document.getElementById("password");
            if (x.type === "password") {
                x.type = "text";
            } else {
                x.type = "password";
            }
        }
    </script>
@endpush
