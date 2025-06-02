@extends('shop::layouts.master')

@section('page_title')
Customization Services | Volanti Jet Catering
@stop

@section('seo')
<meta name="title" content="Customization Services | Volanti Jet Catering" />
<meta name="description" content="Have questions or special requests? Use our easy-to-fill custom enquiry form to get in touch with volanti jet catering for menu inquiries, catering quotes, orders, and more. We’ll get back to you promptly!" />
<meta name="keywords" content="" />
<link rel="canonical" href="{{ url()->current() }}" />
@stop

@section('content-wrapper')
<div class="container mt-5 d-flex justify-content-center">
    <div class="card p-4 customization-card">
        <h1 class="text-center my-5">Custom Order Enquiry</h1>

        <div class="body">
            <form action="{{ route('store.inquery') }}" method="POST" enctype="multipart/form-data" id="customizationForm">
                @csrf

                <!-- Name Fields -->
                <div class="form-row inquiry-row-field">
                    <div class="form-group col-md-6 pr-3">
                        <label for="fname">First Name</label>
                        <input type="text" class="form-control fname-field @error('fname') is-invalid @enderror" id="fname" name="fname" required value="{{ old('fname') }}">
                        @error('fname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span class="text-danger fname-error control-error"></span>
                    </div>
                    <div class="form-group col-md-6 pl-3">
                        <label for="lname">Last Name</label>
                        <input type="text" class="form-control lname-field @error('lname') is-invalid @enderror" id="lname" name="lname" required value="{{ old('lname') }}">
                        @error('lname')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span class="text-danger lname-error control-error"></span>
                    </div>
                </div>

                <!-- Email and Phone Fields -->
                <div class="form-row inquiry-row-field">
                    <div class="form-group col-md-6 pr-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control email-field @error('email') is-invalid @enderror" id="email" name="email" required value="{{ old('email') }}">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span class="text-danger email-error control-error"></span>
                    </div>
                    <div class="form-group col-md-6 pl-3">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control phone-field @error('mobile_number') is-invalid @enderror" id="phone" name="mobile_number" required value="{{ old('mobile_number') }}">
                        @error('mobile_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <span class="text-danger phone-error control-error"></span>
                    </div>
                </div>

                <!-- Message Field -->
                <div class="form-group message">
                    <label for="message">Message</label>
                    <textarea class="form-control message-field @error('message') is-invalid @enderror" id="message" name="message" rows="4" required style="border-radius: 5px;">{{ old('message') }}</textarea>
                    @error('message')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <span class="text-danger message-error control-error"></span>
                </div>

                <!-- File Upload Field -->
                <div class="form-group">
                    <label for="file">Upload File</label>
                    <div class="file_upload_div">
                    <input type="file" class="form-control-file file-field @error('uploadfile.*') is-invalid @enderror" id="uploadfile" accept=".pdf, .xls, .xlsx, .doc, .docx" name="uploadfile[]" multiple required style="display: none">
                    <label class="file__input--label d-flex w-50" for="uploadfile">
                        <span class="label-text w-100 add_file_text d-flex mt-1 pl-2">Add file:</span>
                        <span class="upload-btn ml-auto upload_file_text d-flex align-items-center justify-content-center mt-1">Upload</span>
                    </label>
                    </div>
                    <div id="fileError" class="text-danger"></div>
                    @error('uploadfile')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <span class="text-danger file-error"></span>
                </div>

                 <div class="control-group">
                   <div id="recaptcha"></div> 
                    @error('g-recaptcha-response')
                    <div class="error-message control-error">
                        {{-- {{ $message }} --}}
                        Please select CAPTCHA
                    </div>
                @enderror

                </div>

                {{-- <div class="form-group">
                    <label>Upload File</label>
                    <div class="file">
                        <div class="file__input" id="file__input">
                            <input id="customFile" class="file__input--file @error('uploadfile.*') is-invalid @enderror" 
                                   type="file" multiple name="uploadfile[]" 
                                   accept=".pdf,.xls,.xlsx,.doc,.docx">
                            <label class="file__input--label" for="customFile" data-text-btn="Upload">Add file:</label>
                        </div>
                    </div>
                    <div id="fileError" class="text-danger"></div>
                    @error('uploadfile.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <span class="text-danger file-error"></span>
                </div> --}}

                <!-- Submit Button -->
                <div class="form-group mt-5">
                    <button type="submit" class="btn sendbutton">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>
@stop
@push('scripts')

{!! Captcha::renderJS() !!}
    <script>
       window.siteKey = "{{ core()->getConfigData('customer.captcha.credentials.site_key') }}";
      $(document).ready(function() {

 setTimeout(function() {
console.log(typeof grecaptcha !== "undefined" ? "reCAPTCHA Loaded" : "reCAPTCHA Not Loaded");
    }, 3000);
            // sandeep || add validation code 
            jQuery("body").on('click', '.sendbutton', function(e) {
                e.preventDefault();

 $('.error-message').remove();
                var fname = $('.fname-field').val(),
                    lname = $('.lname-field').val(),
                    email = $('.email-field').val(),
                    phoneNumber = $('.phone-field').val(),
                    message = $('.message-field').val(),
                    file = $('#uploadfile').val(),
recaptchaResponse = grecaptcha.getResponse(),
                    hasError = false;


                    $('.form-control').removeClass('is-invalid');
                    $('.invalid-feedback, .fname-error, .lname-error, .email-error, .phone-error, .message-error, .file-error, #fileError').empty();

                    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/,
                         phonePattern = /^\(\d{3}\) \d{3}-\d{4}$/;
var firstErrorField = null;

                    // Validation checks
                    if (!fname || fname.length > 25) { 
                        $('.fname-error').text(fname ? 'First name should not exceed 25 characters.' : 'First name is required.').fadeIn(); 
  if (!firstErrorField) firstErrorField = $('.fname-error');                       
 hasError = true; 
                    }
                    if (!lname || lname.length > 25) { 
                        $('.lname-error').text(lname ? 'Last name should not exceed 25 characters.' : 'Last name is required.').fadeIn(); 
 if (!firstErrorField) firstErrorField = $('.lname-error');                       
 hasError = true; 
                    }
                    if (!phoneNumber || !phonePattern.test(phoneNumber)) { 
                        $('.phone-error').text(phoneNumber ? 'Please enter a valid phone number (10 to 14 digits).' : 'Phone number is required.').fadeIn(); 
  if (!firstErrorField) firstErrorField = $('.phone-error');                       
 hasError = true; 
                    }
                    if (!email || !emailPattern.test(email)) { 
                        $('.email-error').text(email ? 'Please enter a valid email address.' : 'Email is required.').fadeIn(); 
                        if (!firstErrorField) firstErrorField = $('.email-error');                    
    hasError = true; 
                    }

                    if (!message) { $('.message-error').text('Message is required.').fadeIn();if (!firstErrorField) firstErrorField = $('.message-error'); hasError = true; }

                    if (!file) { $('.file-error').text('file is required.').fadeIn(); if (!firstErrorField) firstErrorField = $('.file-error'); hasError = true; }
  if (recaptchaResponse === '') {
                        $('#recaptcha').after('<span class="error-message text-danger">Please select CAPTCHA</span>');
                         if (!firstErrorField) firstErrorField = $('#recaptcha');
 hasError = true;                     
}

                    if (hasError && firstErrorField && firstErrorField.length) {
                        setTimeout(function() {
                            var scrollPosition = firstErrorField.offset().top - 200;
                            $('html, body').animate({
                                scrollTop: scrollPosition
                            }, 500);
                        }, 100);
                        return false;
                    }
                   
                    if(!hasError){
                    jQuery("#customizationForm").submit();
                    $(this).css('min-width', $(this).outerWidth());
                        $(this).html('<span class="btn-ring"></span>');
                        $(this).find(".btn-ring").show();
                        $(this).find('.btn-ring').css({
                            'display': 'flex',
                            'justify-content': 'center',
                            'align-items': 'center'
                    });
                }else{
                }
            });


            //  sandeep || remove error message click on input
        //     $('body').on('input', '.form-control, .form-control-file', function() {
        //     const field = $(this);
        //     const value = field.val();
        //     const fieldClass = field.attr('class').split(' ').find(cls => cls.endsWith('-field'));
            
        //     const errors = {
        //         'fname-field': value.length <= 25,
        //         'lname-field': value.length <= 25, 
        //         'phone-field': /^[0-9]{10,14}$/.test(value), 
        //         'email-field': /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value), 
        //         'message-field': value.length > 0, 
        //         'file-field': value.length > 0 
        //     };

        //     if (fieldClass && errors[fieldClass]) {
        //         field.siblings(`.${fieldClass.replace('-field', '-error')}`).fadeOut();
        //     }

        //     // Enable or disable send button based on visible errors
        //     const hasVisibleErrors = !!$('.control-error:visible').length;
        //     $('.sendbutton').prop('disabled', hasVisibleErrors);
        // });

// sandeep || clcik on input validation code

 $('body').on('input', '.form-control, .form-control-file', function() {
    const field = $(this);
    const value = field.val();
    const passwordField = field.closest('form').find('.password-field1');
    const passwordValue = passwordField.val();
    let isValid = true;

    // Validate only the current field
    if (field.hasClass('fname-field')) {
        if (value.length === 0) {
            field.siblings('.fname-error').text('First name is required.').fadeIn();
            isValid = false;
        } else if (value.length > 25) {
            field.siblings('.fname-error').text('First name should not exceed 25 characters.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.fname-error').fadeOut();
        }
    }

    if (field.hasClass('lname-field')) {
        if (value.length === 0) {
            field.siblings('.lname-error').text('Last name is required.').fadeIn();
            isValid = false;
        } else if (value.length > 25) {
            field.siblings('.lname-error').text('Last name should not exceed 25 characters.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.lname-error').fadeOut();
        }
    }

    if (field.hasClass('phone-field')) {
        if (value.length === 0) {
            field.siblings('.phone-error').text('Phone number is required.').fadeIn();
            isValid = false;
        } else if (!/^\(\d{3}\) \d{3}-\d{4}$/.test(value)) {
            field.siblings('.phone-error').text('Please enter a valid phone number (10 to 14 digits).').fadeIn();
            isValid = false;
        } else {
            field.siblings('.phone-error').fadeOut();
        }
    }

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

    if (field.hasClass('message-field')) {
        if (value.length === 0) {
            field.siblings('.message-error').text('Message is required.').fadeIn();
            isValid = false;
        } else {
            field.siblings('.message-error').fadeOut();
        }
    }

    if (field.hasClass('file-field')) {
        if (value.length === 0) {
            field.siblings('.file-error').text('file is required.').fadeIn();
            isValid = false;
        }else{
            field.siblings('.file-error').fadeOut();
        }
    }

    const fields = $(this).closest('form').find('.form-control, .form-control-file');
    fields.each(function() {
        const fieldValue = $(this).val();
        if (fieldValue.length === 0 || $(this).siblings('.error:visible').length > 0) {
            isValid = false;
        }
    });

});

let selectedFiles = [];
$('body').on('change', '#uploadfile', function() {
    $('.file-error').empty();
    var newFiles = $(this)[0].files;
    $('#fileError').hide().text('');

    var fileContainer = $('.file_upload_div');

    // Check if total files will exceed limit
    if (selectedFiles.length + newFiles.length > 5) {
        $('#fileError').text('Maximum 5 files can be uploaded.').show();
        return;
    }

    const dataTransfer = new DataTransfer();

    // Add already selected files to DataTransfer
    selectedFiles.forEach(file => dataTransfer.items.add(file));

    for (var i = 0; i < newFiles.length; i++) {
        var file = newFiles[i];

        // File size validation
        if (file.size > 2000 * 1024) {
            $('#fileError').text('Maximum file size allowed is 2 MB.').show();
            return;
        }

        // File type validation
        var fileType = file.type;
        if (!(fileType === "application/pdf" ||
              fileType === "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
              fileType === "application/vnd.ms-excel" ||
              fileType === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
        )) {
            $('#fileError').text('Only upload PDF, DOCX, and XLS files.').show();
            return;
        }

        // Duplicate file check
        if (selectedFiles.some(existingFile => existingFile.name === file.name)) {
            $('#fileError').text('File with same name already exists.').show();
            return;
        }

        // Add file to arrays and DataTransfer
        selectedFiles.push(file);
        dataTransfer.items.add(file);

        var fileHtml = `
            <div class='file__value d-flex justify-content-between align-items-center mt-2'>
                <div class='file__value--text'>${file.name}</div>
                <div class='file__value_remove text-danger pr-3' data-id='${file.name}' style='cursor: pointer;'>Remove</div>
            </div>
        `;
        fileContainer.append(fileHtml);
    }

    // Update input files
    $('#uploadfile')[0].files = dataTransfer.files;
});

// File remove functionality
$('body').on('click', '.file__value_remove', function() {
    const fileName = $(this).data('id');

    // Remove file from selectedFiles
    selectedFiles = selectedFiles.filter(file => file.name !== fileName);
    $(this).closest('.file__value').remove();

    // Update input with remaining files
    const dataTransfer = new DataTransfer();
    selectedFiles.forEach(file => dataTransfer.items.add(file));
    $('#uploadfile')[0].files = dataTransfer.files;

    if (selectedFiles.length === 0) {
        $('#fileError').text('Please upload at least one file.').show();
    } else {
        $('#fileError').hide();
    }
});

});

</script>

@endpush
