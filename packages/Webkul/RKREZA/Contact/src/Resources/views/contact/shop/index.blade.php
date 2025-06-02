@extends('shop::layouts.master')

@section('page_title')
    {{ __('contact_lang::app.shop.title') }}
@endsection

@section('seo')
<link rel="canonical" href="{{ url()->current() }}" />
@stop

@section('content-wrapper')
    <div class="contact-us-container container mt-5  pl-2 pr-2 pl-lg-5 pr-lg-5 pl-md-5 pr-md-5">
        <div class="row pl-3 pr-3 pl-lg-5 pr-lg-5">
            <!-- Left Section -->
            <div class="col-lg-8 mb-5 contact-us-form p-0">
                <span class="d-flex align-items-center section-title mb-2">
                <h1 class="p-2 pl-4 mb-0" style="font-size:32px">Send us a message</h1>
                <img src="{{ asset('themes/volantijetcatering/assets/images/message-icon.png') }}" alt="Email Icon" height="30px" width="30px" class="ml-auto mr-4"/>
            </span>
                <div class="send-message-section pl-4 pr-4">
                    <p class="section-description">
                        We are here to answer any questions you may have about our services.
                        Send us your question, and we'll respond as soon as we can.
                    </p>
                    
                    <form class="contact-form" id="contactForm" action="{{ route('shop.contact.send-message') }}" method="post">
                        {{ csrf_field() }}
                        
                    <div class="d-lg-flex d-md-flex">
                        <div class="form-group col-lg-6 col-md-6 pr-lg-3 pr-md-3 p-0">
                            <label for="name">Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}"  required>
                            <span></span>
                        </div>
                        
                        <div class="form-group col-lg-6 pl-lg-3 col-lmd-6 pl-md-3 p-0">
                            <label for="email">Email Address <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" id="email" value="{{ old('email') }}"  required>
                        </div>
                    </div>

                        <div class="form-group">
                            <label for="message_body">Message <span class="text-danger">*</span></label>
                            <textarea name="message_body" id="message_body" rows="5" class="form-control" required>{{ old('message_body') }}</textarea>
                        </div>


 <div class="form-group">
                         <div id="recaptcha"></div>
                         @error('g-recaptcha-response')
                         <div class="error-message control-error">
                             Please select CAPTCHA
                         </div>
                     @enderror
                         </div>

                        
                        <div class="d-flex justify-content-center">
                        <button type="submit" class="btn theme-btn btn-block mb-5 mt-5 pt-1 pb-1 send_message_button">
                            <i class="fa fa-paper-plane"></i> Send Message
                        </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Right Section -->
            <div class="col-lg-4 contact-email-details pl-lg-4 pr-lg-4 pl-1 pr-1 mb-5">
                <div class="contact-email-section">
                    <span class="d-flex align-items-center contact-details-section">
                        <h2 class="email-title p-2 mb-0 ms-2"style="font-size:32px">Contact Detail</h2>
                        <img src="{{ asset('themes/volantijetcatering/assets/images/call.png') }}" alt="Email Icon" height="25px" width="25px" class="ml-auto mr-3"/>
                    </span>
                <div class="email-section p-3">
                
                    <p class="email-address d-lg-flex d-md-flex">
                        <img src="{{ asset('themes/volantijetcatering/assets/images/email.png') }}" alt="Email Icon" height="20px" width="20px" />
                       <a href="mailto:jetcatering@volantiscottsdale.com" class="pl-2" style="color:black">jetcatering@volantiscottsdale.com</a>
                    </p>

                    
                    <p class="email-address d-lg-flex d-md-flex">
                        <img src="{{ asset('themes/volantijetcatering/assets/images/telephone.png') }}" alt="Email Icon" height="20px" width="20px" />
                      <a href="tel:+14806572426" class="pl-2" style="color:black">480.657.2426</a>
                    </p>

                    <p class="email-address d-lg-flex d-md-flex">
                        <img src="{{ asset('themes/volantijetcatering/assets/images/air-location.png') }}" alt="Email Icon" height="20px" width="20px" />
                     <a href="https://www.google.com/maps?q=15000+N.+Airport+Dr.+Scottsdale,+AZ+85260" class="pl-2" target="_blank" style="color: black;" rel="noopener noreferrer"> 15000 N. Airport Dr. Scottsdale, AZ 85260 </a>
                    </p>
                    
                </div>
            </d>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
{!! Captcha::renderJS() !!}
<script>
       window.siteKey = "{{ core()->getConfigData('customer.captcha.credentials.site_key') }}";

    $(document).ready(function() {
        console.log('Form submission started');
        $("body").on('click', '.send_message_button', function() {
            $('.error-message').remove();

            // Retrieve form values
            var name = $('#name').val().trim();
            var email = $('#email').val().trim();
            var message = $('#message_body').val().trim();
var recaptchaResponse = grecaptcha.getResponse();
            // Validation flags
            var isValid = true;
 var firstErrorField = null;
            // Validate Name
            if (name === '') {
                $('#name').after('<span class="error-message text-danger">Name is required.</span>');
 if (!firstErrorField) firstErrorField = $('#name');                
isValid = false;
            }

            // Validate Email
            var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email === '') {
                $('#email').after('<span class="error-message text-danger">Email is required.</span>');
 if (!firstErrorField) firstErrorField = $('#email');               
 isValid = false;
            } else if (!emailPattern.test(email)) {
                $('#email').after('<span class="error-message text-danger">Enter a valid email address.</span>');
 if (!firstErrorField) firstErrorField = $('#email');               
 isValid = false;
            }

            // Validate Message
            if (message === '') {
                $('#message_body').after('<span class="error-message text-danger">Message is required.</span>');
 if (!firstErrorField) firstErrorField = $('#message_body');                
isValid = false;
            }

 if (recaptchaResponse === '') {
                $('#recaptcha').after('<span class="error-message text-danger">Please select CAPTCHA</span>');
                               if (!firstErrorField) firstErrorField = $('#recaptcha');
                isValid = false;
            }

            setTimeout(function() {
                $('.error-message').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);

            if (!isValid && firstErrorField && firstErrorField.length) {
                        setTimeout(function() {
                            var scrollPosition = firstErrorField.offset().top - 200;
                            $('html, body').animate({
                                scrollTop: scrollPosition
                            }, 500);
                        }, 100);
                        return false;
                    }

            if (isValid) {
                $('#contactForm').submit();
                $(this).html('<span class="btn-ring"></span>');
                $(".btn-ring").show();
                $(this).val('disabled');
                $(this).find('.btn-ring').css({
                'display': 'flex',
                'justify-content': 'center',
                'align-items': 'center'
            });
            }
});

    });


</script>

@endpush
