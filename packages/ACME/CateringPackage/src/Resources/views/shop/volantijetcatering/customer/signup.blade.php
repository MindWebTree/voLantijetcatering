@php
    // sandeep || get previous url 
       $previousUrl = url()->previous();
        if(isset($previousUrl) && (str_ends_with($previousUrl, 'signIn') || 
        str_ends_with($previousUrl, 'signIn?form=register'))){
            $previousUrl = url()->previous();
          
        }

         if($previousUrl && isset($previousUrl)){
          $Url = $previousUrl;
          session()->put('previous_url',$previousUrl);
        }
        
@endphp

@extends('shop::layouts.master')

{{-- sandeep change title and add seo --}}
@section('page_title')
    {{-- {{ __('shop::app.customer.login-form.page-title') }} --}}
    Account Login | Volanti Jet Catering
@endsection

@section('seo')
<meta name="title" content="Account Login | Volanti Jet Catering" />
<meta name="description" content="Join us for a delightful journey of flavors! Sign up to explore exclusive menus, personalized offers, and a seamless food ordering experience." />
<meta name="keywords" content="" />
<link rel="canonical" href="{{ url()->current() }}" />

@stop

@push('scripts')
@endpush
@section('content-wrapper') 
    <div class=" row mx-auto register-buttons justify-content-center  mt-5" style="margin-top: 50px !important;">
        <span class="btn-new-customer col-lg-3 col-md-6 col-6 p-0" id="button-1">
            <button href="#rrr" type="button" class="theme-btn btn rounded-0 light w-100 button-1 btn-login">
                {{ __('velocity::app.customer.signup-form.login') }}
            </button>
        </span>
        <span class="btn-new-customer col-lg-3 col-md-6 col-6 p-0" id="button-2">
            <button href="#ddd" type="button" class="theme-btn btn rounded-0 light w-100 button-2">
                {{ __('velocity::app.customer.login-form.sign-up') }}
            </button>
        </span>
    </div>
    
    <div class="auth-content form-container login-form" id="rrr">
        {!! view_render_event('bagisto.shop.customers.login.before') !!}

        <div class="container">
            {{-- <div class=" row mx-auto register-buttons justify-content-center w-100 mt-3">
                <a href="{{ route('shop.customer.session.index') }}" class="btn-new-customer col-lg-5 p-0">
                    <button type="button"
                        class="theme-btn btn rounded-0 btn-login light w-100 items-end border border-danger ">
                        {{ __('velocity::app.customer.signup-form.login') }}
                    </button>
                </a>
                <a href="{{ route('shop.customer.register.index') }}" class="btn-new-customer col-lg-5 p-0">
                    <button type="button" class="theme-btn btn rounded-0 light w-100 items-start">
                        {{ __('velocity::app.customer.login-form.sign-up') }}
                    </button>

                </a>
            </div> --}}
            <div class="col-lg-8 col-md-12 mx-auto login-form" id="ddd">
                <h1 class="fs24 fw6 text-center my-5 login-head">
                    {{ __('velocity::app.customer.login-form.customer-login') }}
                </h1>

                <div class="body col-12 border-0 p-0" id="aaa">
                    {{-- <div class="form-header">
                        <h3 class="fw6">
                            {{ __('velocity::app.customer.login-form.registered-user') }}
                        </h3>

                        <p class="fs16">
                            {{ __('velocity::app.customer.login-form.form-login-text') }}
                        </p>
                    </div> --}}


                    <form method="POST" action="{{ route('shop.customer.session.create') }}" id="form1">
                        {{ csrf_field() }}

                        @php
                            // $form_type = 'login'
                        @endphp

                        <input type="hidden" name="form_type" value="login">


                        {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                        <div class="form-group  col-lg-12 col-md-12 col-12"
                            :class="[errors.has('email') && '{{ old('form_type', '') }}'
                                === 'login' ? 'has-error' : ''
                            ]">
                            <label for="email" class="mandatory label-style">
                                {{ __('shop::app.customer.login-form.email') }}
                            </label>

                            <input type="email" class="form-control form-control-lg  email-field"  name="email"
                                v-validate="'required|email'" value="{{ old('email') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.login-form.email') }}&quot;" />

                            {{-- <span class="control-error email_field"
                                v-if="errors.has('email') && '{{ old('form_type', '') }}' === 'login' "
                                v-text="errors.first('email')"></span> --}}
                                <span class="text-danger email-error"></span>
                        </div>

                        <div class="form-group col-md-12 col-12 col-lg-12"
                            :class="[errors.has('password') && '{{ old('form_type', '') }}'
                                === 'login' ? 'has-error' : ''
                            ]">
                            <label for="password" class="mandatory label-style">
                                {{ __('shop::app.customer.login-form.password') }}
                            </label>

                            <input type="password" class="form-control form-control-lg password-field" name="password"
                                id="password" v-validate="'required'" value="{{ old('password') }}"
                                data-vv-as="&quot;{{ __('shop::app.customer.login-form.password') }}&quot;" />

                                {{-- sandeep add hidden input for previous url --}}
                                <input type="text" name="previous_url" value="{{ isset($Url) ? $Url : '' }}" hidden>

                            <input type="checkbox" onclick="myFunction()" id="shoPassword" class="show-password mr-0" />

                            {{-- sandeep comment show passsword code and add code --}}
                          
                           <label for="shoPassword" class="">{{ __('shop::app.customer.login-form.show-password') }}</label>

                            <div class="mt10">
                                {{-- <span class="control-error password_field"
                                    v-if="errors.has('password')  && '{{ old('form_type', '') }}' === 'login' "
                                    v-text="errors.first('password')"></span> --}}
                                    <span class="text-danger password-error"></span>
                                @if (Cookie::has('enable-resend'))
                                    @if (Cookie::get('enable-resend') == true)
                                        <a
                                            href="{{ route('shop.customer.resend.verification_email', Cookie::get('email-for-resend')) }}">{{ __('shop::app.customer.login-form.resend-verification') }}</a>
                                    @endif
                                @endif
                            </div>
                        </div>

   <div class="form-group col-md-12 col-12 col-lg-12">
                                <div id="recaptcha"></div> 
                                @error('g-recaptcha-response')
                                <div class="error-message control-error">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}

                        {{-- <input class="theme-btn signIn-btn mx-auto" type="submit"
                            value="{{ __('shop::app.customer.login-form.button_title') }}"><br> --}}

                        <button class="theme-btn signIn-btn mx-auto " type="submit"
                            id="submit-form1">{{ __('shop::app.customer.login-form.button_title') }}</button>

                        <a href="{{ route('shop.customer.forgot_password.create') }}" class=" forget-password mt-2">
                            {{ __('shop::app.customer.login-form.forgot_pass') }}
                        </a>
                    </form>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.customers.login.after') !!}
    </div>

    <div class="auth-content form-container register-form display-none">
        <div class="container ">
            {{-- <div class=" row mx-auto register-buttons justify-content-center w-100 mt-3">
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
                </div> --}}
            <div class="col-lg-10 col-md-12 offset-lg-1" style="padding-left: 15px; padding-right:15px">
                <h2 class="fs24 fw6 text-center register-head my-5">
                    {{ __('velocity::app.customer.signup-form.user-registration') }}
                </h2>
                <div class="body col-12 border-0 p-0">
                    {{-- <h3 class="fw6">
                        {{ __('velocity::app.customer.signup-form.become-user') }}
                    </h3>

                    <p class="fs16">
                        {{ __('velocity::app.customer.signup-form.form-signup-text') }}
                    </p> --}}

                    {!! view_render_event('bagisto.shop.customers.signup.before') !!}



                    <form method="POST" id="form2" action="{{ route('shop.customer.register.create') }}">

                        {{ csrf_field() }}

                        <input type="hidden" name="form_type" value="register" id="formTypeInput">

                        <div id="my-element" data-form-type="{{ old('form_type') }}"></div>



                        <div class="row">
                            {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                         <div class="control-group col-12 col-lg-6 col-md-6 mb-3"
                                :class="[errors.has('fullname') ? 'has-error' : '']">
                                <label for="fullname" class="required label-style">
                                    {{-- {{ __('shop::app.customer.signup-form.firstname') }} --}}
                                    Full Name
                                </label>

                                <input type="text" class="form-control form-control-lg fname-field " name="fullname"
                                    v-validate="'required'" value="{{ old('fullname') }}"
                                    data-vv-as="&quot;{{ __('shop::app.customer.signup-form.firstname') }}&quot;" />

                                {{-- <span class="control-error" v-if="errors.has('first_name')"
                                    v-text="errors.first('first_name')"></span> --}}
                                <span class="text-danger fname-error control-error"></span>
                            </div>
                            {!! view_render_event('bagisto.shop.customers.signup_form_controls.firstname.after') !!}

                            <div class="control-group col-12 col-lg-6 col-md-6 mb-3"
                                :class="[errors.has('email') && '{{ old('form_type', '') }}'
                                    === 'register' ? 'has-error' : ''
                                ]">
                                <label for="email" class="required label-style">
                                    {{ __('shop::app.customer.signup-form.email') }}
                                </label>

                                <input type="email" class="form-control form-control-lg email-field1" name="email"
                                    v-validate="'required|email'" value="{{ old('email') }}"
                                    data-vv-as="&quot;{{ __('shop::app.customer.signup-form.email') }}&quot;" />

                                <span class="control-error email_error1"
                                    v-if="errors.has('email')  && '{{ old('form_type', '') }}' === 'register' "
                                    v-text="errors.first('email')"></span>
                                    <span class="text-danger email-error1 control-error"></span>
                            </div>

                            {{-- Phone field added start --}}

                            <div class="control-group col-12 col-lg-6 col-md-6 mb-3"
                                :class="[errors.has('phone') ? 'has-error' : '']">
                                <label for="phone" class="required label-style">
                                    {{ __('shop::app.customer.signup-form.phonenumber') }}
                                </label>
                                <input type="text" class="control form-control form-control-lg phone-field" id="phone"
                                    name="phone" value="{{ old('phone') }}"
                                    v-validate="'required'"
                                    data-vv-as="&quot;{{ __('admin::app.customers.customers.phone') }}&quot;">
                                {{-- <span class="control-error" v-if="errors.has('phone')"
                                    v-text="errors.first('phone')"></span> --}}
                                    <span class="text-danger phone-error control-error"></span>
                            </div>


                            {{-- Phone field added end --}}

                            {!! view_render_event('bagisto.shop.customers.signup_form_controls.email.after') !!}

                            <div class="control-group col-12 col-lg-6 col-md-6 mb-3"
                                :class="[errors.has('password') && '{{ old('form_type', '') }}'
                                    === 'register' ? 'has-error' : ''
                                ]">
                                <label for="password" class="required label-style">
                                    {{ __('shop::app.customer.signup-form.password') }}
                                </label>

                                <input type="password" class="form-control form-control-lg password-field1 " name="password"
                                    v-validate="'required|min:6'" ref="password" value="{{ old('password') }}"
                                    data-vv-as="&quot;{{ __('shop::app.customer.signup-form.password') }}&quot;" />

                                {{-- <span class="control-error"
                                    v-if="errors.has('password')  && '{{ old('form_type', '') }}' === 'register' "
                                    v-text="errors.first('password')"></span> --}}
                                    <span class="text-danger password-error1 control-error"></span>
                            </div>

                            {!! view_render_event('bagisto.shop.customers.signup_form_controls.password.after') !!}

                            <div class="control-group col-12 col-lg-6 col-md-6 mb-3"
                                :class="[errors.has('password') && '{{ old('form_type', '') }}'
                                    === 'register' ? 'has-error' : ''
                                ]">
                                <label for="password_confirmation" class="required label-style">
                                    {{ __('shop::app.customer.signup-form.confirm_pass') }}
                                </label>

                                <input type="password" class="form-control form-control-lg cpassword-field" name="password_confirmation"
                                    v-validate="'required|min:6|confirmed:password'"
                                    data-vv-as="&quot;{{ __('shop::app.customer.signup-form.confirm_pass') }}&quot;" />

                                {{-- <span class="control-error"
                                    v-if="errors.has('password')  && '{{ old('form_type', '') }}' === 'register'"
                                    v-text="errors.first('password')"></span> --}}
                                <span class="text-danger confirm-password-error control-error"></span>
                                   
                            </div>
                              {{-- sandeep add hidden input for previous url --}}
                              <input type="text" name="previous_url" value="{{ isset($Url) ? $Url : '' }}" hidden>


                            {!! view_render_event('bagisto.shop.customers.signup_form_controls.password_confirmation.after') !!}
                        </div>

  <div class="control-group">
                            <div id="recaptcha-register"></div> 
                            @error('g-recaptcha-response')
                            <div class="error-message control-error">
                                {{ $message }}
                            </div>
                        @enderror

                        </div>

                        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                            <div class="control-group">
                                <input type="checkbox" id="checkbox2" name="is_subscribed">
                                <span>{{ __('shop::app.customer.signup-form.subscribe-to-newsletter') }}</span>
                            </div>
                        @endif

                        {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}

                        <button class="theme-btn register-btn" type="submit" id="submit-form2">
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
   {!! Captcha::renderJS() !!}

    <script>
       window.siteKey = "{{ core()->getConfigData('customer.captcha.credentials.site_key') }}";
        document.addEventListener("DOMContentLoaded", function() {
            var formType = document.getElementById("my-element").getAttribute("data-form-type");

            if (formType === 'register') {
                // Your JavaScript code here
                // For example, triggering a click event on an element with class "button-2"
                var button2 = document.querySelector('.button-2');
                if (button2) {
                    button2.click();
                }
            }

        });

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
        $(function() {
            $(":input[name=first_name]").focus();
        });


        // sandeep || signin and signup error handle code

    jQuery(document).ready(function() {

      $('body').on('click', '#submit-form1, #submit-form2', function(e) {
        e.preventDefault();
 $('.error-message').remove();
        const isForm1 = $(this).is('#submit-form1');
        const form = isForm1 ? '#form1' : '#form2';
        const email = $('.email-field').val();
        const password = $('.password-field').val();
        const fname = $('.fname-field').val();
        // const lname = $('.lname-field').val();
        const signemail = $('.email-field1').val();
        const phoneNumber = $('.phone-field').val();
        const signpassword = $('.password-field1').val();
        const confirmPassword = $('.cpassword-field').val();
        var recaptchaResponse = grecaptcha.getResponse();
        var recaptchaRegsiterResponse = window.recaptchaRegsiterResponse ?? '';
        let hasError = false;
 let firstErrorElement = null;
console.log('recaptchaRegsiterResponse',recaptchaRegsiterResponse);
        // Clear previous errors
        $('.control-error, .email-error, .password-error, .fname-error, .lname-error,.email-error1, .phone-error, .password-error1, .confirm-password-error').empty();

        // Patterns
        const emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
        const phonePattern = /^\(\d{3}\) \d{3}-\d{4}$/;
        let phoneDigits = phoneNumber.replace(/\D/g, '').length;
        

        // Validation
        if (isForm1) {
            if (email === '') {
                    $('.email-error').text('Email is required.').fadeIn();
 if (!firstErrorElement) firstErrorElement = $('.email-error');                   
 hasError = true;
                } else if (!emailPattern.test(email)) {
                    $('.email-error').text('Please enter a valid email address.').fadeIn();
 if (!firstErrorElement) firstErrorElement = $('.email-error');                   
 hasError = true;
                }
                if (password === '') {
                    $('.password-error').text('The password field is required.').fadeIn();
  if (!firstErrorElement) firstErrorElement = $('.password-error');                   
 hasError = true;
                }
  if (recaptchaResponse === '') {
                $('#recaptcha').after('<span class="error-message text-danger">Please select CAPTCHA</span>');
               if (!firstErrorElement) firstErrorElement = $('#recaptcha');               

 hasError = true;
           
 }

        
        } else {
            if (!fname) { $('.fname-error').text('First name is required.').fadeIn();if (!firstErrorElement) firstErrorElement = $('.fname-error'); hasError = true; }
                    if (phoneDigits < 10 || !phoneNumber || !phonePattern.test(phoneNumber)) { 
                        $('.phone-error').text(phoneNumber ? 'Please enter a valid 10-14 digit phone number.' : 'Phone number is required.').fadeIn(); 
 if (!firstErrorElement) firstErrorElement = $('.phone-error');                       
 hasError = true; 
                    }
                    if (!signemail || !emailPattern.test(signemail)) { 
                        $('.email-error1').text(email ? 'Please enter a valid email address.' : 'Email is required.').fadeIn(); 
  if (!firstErrorElement) firstErrorElement = $('.email-error1');                       
 hasError = true; 
                    }
                    if (!signpassword) { $('.password-error1').text('The password field is required.').fadeIn();if (!firstErrorElement) firstErrorElement = $('.password-error1'); hasError = true; }
                    if (!confirmPassword || signpassword !== confirmPassword) { 
                        $('.confirm-password-error').text(confirmPassword ? 'Passwords do not match.' : 'The confirm password field is required.').fadeIn(); 
 if (!firstErrorElement) firstErrorElement = $('.confirm-password-error');                       
 hasError = true; 
                    }
     if (recaptchaRegsiterResponse === '') {
console.log('empty captcah');
                    $('#recaptcha-register').after('<span class="error-message text-danger">Please select CAPTCHA</span>');
 if (!firstErrorElement) firstErrorElement = $('#recaptcha-register');                   
 hasError = true; 
                                   }
          }

console.log('empty captcah3');
                
                // sandeep add code click on login button then remove register url
                if($(this).is('#submit-form1')){
                    let url = window.location.href;
                    if (url.endsWith('?form=register')) {
                        url = url.slice(0, -'?form=register'.length);
                        history.replaceState(null, '', url);
                    }
                 }
                if (hasError && firstErrorElement.length) {
                    $('html, body').animate({
                        scrollTop: firstErrorElement.offset().top - 200
                    }, 500);
                }

                 if (!hasError) {
console.log('empty captcah4');
                    $(this).html('<span class="btn-ring"></span>').find('.btn-ring').show().css({
                        'display': 'flex',
                        'justify-content': 'center',
                        'align-items': 'center'
                    });
                    $(form).submit();
                }
    });


    //sandeep || check on click input validation code 
    $('body').on('input', '.form-control', function() {
    const field = $(this);
    const value = field.val();
    const passwordField = field.closest('form').find('.password-field1');
    const passwordValue = passwordField.val();
    let isValid = true;

    // Validate only the current field
    if (field.hasClass('email-field')) {
        if (value.length === 0) {
            field.siblings('.email-error').text('Email is required.').fadeIn();
            isValid = false;
        } else if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value)) {
            field.siblings('.email-error').text('Please enter a valid email address.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.email-error').fadeOut();
        }
    }

    if (field.hasClass('password-field')) {
        if (value.length === 0) {
            field.closest('.form-group').find('.password-error').text('The password field is required.').fadeIn();
            isValid = false;
        } else {
            field.closest('.form-group').find('.password-error').fadeOut();
        }
    }

    if (field.hasClass('fname-field')) {
        if (value.length === 0) {
            field.siblings('.fname-error').text('First name is required.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.fname-error').fadeOut();
        }
    }

  //  if (field.hasClass('lname-field')) {
    //    if (value.length === 0) {
      //      field.siblings('.lname-error').text('Last name is required.').fadeIn();
        //    isValid = false;
     //   } else if (value.length > 25) {
       //     field.siblings('.lname-error').text('Last name must be 25 characters or less.').fadeIn();
         //   isValid = false;
      //  } else {
        //    field.siblings('.lname-error').fadeOut();
     //   }
 //   }

    if (field.hasClass('phone-field')) {
        if (value.length === 0) {
            field.siblings('.phone-error').text('Phone number is required.').fadeIn();
            isValid = false;
        } else if (value.replace(/\D/g, '').length < 10 || !/^\(\d{3}\) \d{3}-\d{4}$/.test(value)) {
            field.siblings('.phone-error').text('Please enter a valid 10-14 digit phone number.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.phone-error').fadeOut();
        }
    }

    if (field.hasClass('email-field1')) {

        field.siblings('.email_error1').hide(); 
        if (value.length === 0) {
            field.siblings('.email-error1').text('Email is required.').fadeIn();
            isValid = false;
        } else if (!/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value)) {
            field.siblings('.email-error1').text('Please enter a valid email address.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.email-error1').fadeOut();
        }
    }

    if (field.hasClass('password-field1')) {
        if (value.length === 0) {
            field.siblings('.password-error1').text('The password field is required.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.password-error1').fadeOut();
        }
    }

    if (field.hasClass('cpassword-field')) {
        if (value.length === 0) {
            field.siblings('.confirm-password-error').text('The confirm password field is required.').fadeIn();
            isValid = false;
        } else if (value !== passwordValue) {
            field.siblings('.confirm-password-error').text('Passwords do not match.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.confirm-password-error').fadeOut();
        }
    }

    // Run a final check on all fields before enabling/disabling the submit button
    const fields = $(this).closest('form').find('.form-control');
    fields.each(function() {
        const fieldValue = $(this).val();
        if (fieldValue.length === 0 || $(this).siblings('.error:visible').length > 0) {
            isValid = false;
        }
    });

});

            var formType = getParameterByName('form');
            if (formType == 'register') {
                // If it is, trigger a click on the register form
                $('#button-2').click();
            }
        });


        function getParameterByName(name, url) {
            if (!url) url = window.location.href;
            name = name.replace(/[\[\]]/g, "\\$&");
            var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
                results = regex.exec(url);
                console.log('result',results);
            if (!results) return null;
            if (!results[2]) return '';
            return decodeURIComponent(results[2].replace(/\+/g, " "));
        }
    </script>
@endpush
