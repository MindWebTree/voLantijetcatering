@extends('shop::customers.account.index')

{{-- @section('page_title')
    {{ __('shop::app.customer.account.profile.index.title') }}
@endsection --}}

@section('page_title')
    {{-- {{ __('shop::app.customer.account.profile.index.title') }} --}}
    Account | Volanti Jet Catering
@endsection


@section('seo')
<meta name="title" content="Account | Volanti Jet Catering" />
<meta name="description" content="Account | Volanti Jet Catering" />
<meta name="keywords" content="" />
@stop

@section('page-detail-wrapper')
    <div class="account-head mb-15">
        <span class="account-heading">{{ __('shop::app.customer.account.profile.index.title') }}</span>

        <span></span>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.edit.before', ['customer' => $customer]) !!}

    <form
        method="POST"
        @submit.prevent="onSubmit"
        action="{{ route('shop.customer.profile.store') }}"
        enctype="multipart/form-data">

        <div class="account-table-content">
            @csrf
            <div class="row edit-profile">

            {!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.before', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div :class="`row ${errors.has('fullname') ? 'has-error' : ''}`">
                    <label class="col-12 mandatory">
                        {{-- {{ __('shop::app.customer.account.profile.fname') }} --}}
                        Full Name
                    </label>

                    <div class="col-12">
                        <input value="{{ $customer->first_name }} {{ $customer->last_name }}" name="fullname" type="text" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('shop::app.customer.account.profile.fname') }}&quot;" />
                        <span class="control-error" v-if="errors.has('fullname')" v-text="errors.first('fullname')"></span>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.edit.first_name.after', ['customer' => $customer]) !!}
            {{-- <div class="col-6 mt-3 user-profile-input">
                <div :class="`row ${errors.has('last_name') ? 'has-error' : ''}`">
                    <label class="col-12 mandatory">
                        {{ __('shop::app.customer.account.profile.lname') }}
                    </label>

                    <div class="col-12">
                        <input value="{{ $customer->last_name }}" name="last_name" type="text" class="control" v-validate="'required'" data-vv-as="&quot;{{ __('shop::app.customer.account.profile.lname') }}&quot;" />
                        <span class="control-error" v-if="errors.has('last_name')" v-text="errors.first('last_name')"></span>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.edit.last_name.after', ['customer' => $customer]) !!} --}}
            <div class="col-6 mt-3 user-profile-input">
                <div :class="`row ${errors.has('gender') ? 'has-error' : ''}`">
                    <label class="col-12 mandatory">
                        {{ __('shop::app.customer.account.profile.gender') }}
                    </label>

                    <div class="col-12">
                        <select
                            name="gender"
                            v-validate="'required'"
                            class="control styled-select"
                            data-vv-as="&quot;{{ __('shop::app.customer.account.profile.gender') }}&quot;">

                            <option value=""
                                @if (old('gender',$customer->gender) == "")
                                    selected="selected"
                                @endif>
                                {{ __('admin::app.customers.customers.select-gender') }}
                            </option>

                            <option value="Other"
                                @if (old('gender', $customer->gender) == "Other")
                                    selected="selected"
                                @endif>
                                {{ __('velocity::app.shop.gender.other') }}
                            </option>

                            <option
                                value="Male"
                                @if (old('gender', $customer->gender) == "Male")
                                    selected="selected"
                                @endif>
                                {{ __('velocity::app.shop.gender.male') }}
                            </option>

                            <option
                                value="Female"
                                @if (old('gender', $customer->gender) == "Female")
                                    selected="selected"
                                @endif>
                                {{ __('velocity::app.shop.gender.female') }}
                            </option>
                        </select>

                        <div class="select-icon-container">
                            <span class="select-icon rango-arrow-down"></span>
                        </div>

                        <span class="control-error" v-if="errors.has('gender')" v-text="errors.first('gender')"></span>
                    </div>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.gender.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div :class="`row ${errors.has('date_of_birth') ? 'has-error' : ''}`">
                    <label class="col-12">
                        {{ __('shop::app.customer.account.profile.dob') }}
                    </label>

                    <div class="col-12">
                        <date id="date-of-birth">
                            <input
                                type="date"
                                name="date_of_birth"
                                id="date-of-birth" 
                                placeholder="yyyy/mm/dd"
                                value="{{ old('date_of_birth') ?? $customer->date_of_birth }}"
                                v-validate="" data-vv-as="&quot;{{ __('shop::app.customer.account.profile.dob') }}&quot;" />
                        </date>

                        <span class="control-error" v-if="errors.has('date_of_birth')" v-text="errors.first('date_of_birth')"></span>
                    </div>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.date_of_birth.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div class="row">
                    <label class="col-12 mandatory">
                        {{ __('shop::app.customer.account.profile.email') }}
                    </label>

                    <div class="col-12">
                        <input value="{{ $customer->email }}" name="email" class="control" type="text" v-validate="'required'" />
                        <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                    </div>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.email.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div class="row">
                    <label class="col-12">
                        {{ __('shop::app.customer.account.profile.phone') }}
                    </label>

                    <div class="col-12">
                        <input value="{{ old('phone') ?? $customer->phone }}" name="phone"  id="phone" type="text" v-validate="'required|min:14'"/>
                        <span class="control-error" v-if="errors.has('phone')" v-text="errors.first('phone')?'Please enter a valid 10-14 digit phone number.':''"></span>
                    </div>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.phone.after', ['customer' => $customer]) !!}
            <div class="col-12 mt-3">
                <div class="row image-container {!! $errors->has('image.*') ? 'has-error' : '' !!}">
                    <label class="col-12">
                        {{ __('admin::app.catalog.categories.image') }}
                    </label>

                    <div class="col-12">
                        <image-wrapper :button-label="'{{ __('admin::app.catalog.products.add-image-btn-title') }}'" input-name="image" :multiple="false" :images='"{{ $customer->image_url }}"'></image-wrapper>

                        <span class="control-error" v-if="{!! $errors->has('image.*') !!}">
                            @foreach ($errors->get('image.*') as $key => $message)
                                @php echo str_replace($key, 'Image', $message[0]); @endphp
                            @endforeach
                        </span>
                    </div>
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit.image.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div class="row">
                    <label class="col-12">
                        {{ __('velocity::app.shop.general.enter-current-password') }}
                    </label>

                    <div :class="`col-12 ${errors.has('oldpassword') ? 'has-error' : ''}`">
                        <input value="" name="oldpassword" type="password" />
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.edit.oldpassword.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div class="row">
                    <label class="col-12">
                        {{ __('velocity::app.shop.general.new-password') }}
                    </label>

                    <div :class="`col-12 ${errors.has('password') ? 'has-error' : ''}`">
                        <input
                            value=""
                            name="password"
                            ref="password"
                            type="password"
                            v-validate="'min:6'" />

                        <span class="control-error" v-if="errors.has('password')" v-text="errors.first('password')"></span>
                    </div>
                </div>
            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.edit.password.after', ['customer' => $customer]) !!}
            <div class="col-6 mt-3 user-profile-input">
                <div class="row">
                    <label class="col-12">
                        {{ __('velocity::app.shop.general.confirm-new-password') }}
                    </label>

                    <div :class="`col-12 ${errors.has('password_confirmation') ? 'has-error' : ''}`">
                        <input value="" name="password_confirmation" type="password"
                        v-validate="'min:6|confirmed:password'" data-vv-as="confirm password" />

                        <span class="control-error" v-if="errors.has('password_confirmation')" v-text="errors.first('password_confirmation')"></span>
                    </div>
                </div>
            </div>

            @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                <div class="control-group">
                    <input type="checkbox" id="checkbox2" name="subscribed_to_news_letter" @if (isset($customer->subscription)) value="{{ $customer->subscription->is_subscribed }}" {{ $customer->subscription->is_subscribed ? 'checked' : ''}} @endif  style="width: auto;">
                    <span>{{ __('shop::app.customer.signup-form.subscribe-to-newsletter') }}</span>
                </div>
            @endif

            {!! view_render_event('bagisto.shop.customers.account.profile.edit_form_controls.after', ['customer' => $customer]) !!}
            <div class="col-12 mt-5 d-flex justify-content-center">
                <button
                    type="submit"
                    class="theme-btn mb20 profile_update_button" style="width:200px">
                    {{ __('velocity::app.shop.general.update') }}
                </button>
            </div>
            </div>
        </div>
    </form>

    {!! view_render_event('bagisto.shop.customers.account.profile.edit.after', ['customer' => $customer]) !!}
@endsection


@push('scripts')
<script>
// $(document).ready(function() {
//     // Optional: When a user selects a date, update the hidden input
//     $("body").on("click","#date-of-birth", function() {
//         console.log('sandeep jakhar');
//         var selectedDate = $(this).attr("aria-label");
//         $('.flatpickr-calendar').hide();
//         if (selectedDate) {
//             $("#date-of-birth input").val(selectedDate);
//         }
//     });
// });



$(document).ready(function() {
    function disableFutureDates() {
        console.log('call disableFutureDates function');
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth();

        // Disable future days
        $(".flatpickr-day").each(function() {
            const dayDate = new Date($(this).attr("aria-label"));
            console.log('dayDate',dayDate);
            if (dayDate > today) {
                console.log('disbled days');
                $(this).addClass('disabled').css({ "pointer-events": "none", "opacity": "0.3" });
            }
        });

        // Disable future months
        $(".flatpickr-monthDropdown-month").each(function() {
            var inputYear = $('.numInput').val();
            if(inputYear >= currentYear){
            if (parseInt($(this).val()) > currentMonth) $(this).prop('disabled', true);
            }
        });


        $(".numInput.cur-year").on('input', function() {
            const inputYear = parseInt($(this).val());
            console.log('inputYear',inputYear);
            if (inputYear > currentYear) $(this).val(currentYear);
        });

        // Show/hide arrow based on year input
        const currentInputYear = parseInt($('.numInput').val());
        if (currentInputYear < currentYear) $('.arrowUp').show();
        else $('.arrowUp').hide();
    }

    $("body").on("click keyup input", "#date-of-birth", function() {
        var today = new Date();
        const currentYear = today.getFullYear();
        const currentMonth = today.getMonth() + 1;
        const currentDay = today.getDate();

        var inputDate = $(this).val().trim();

        if (inputDate.match(/^(\d{4})-(\d{2})-(\d{2})$/)) {
            var [inputYear, inputMonth, inputDay] = inputDate.split('-').map(Number);

            if (inputYear > currentYear) {
                inputYear = currentYear;
            }

            if (inputYear === currentYear && inputMonth > currentMonth) {
                inputMonth = currentMonth;
            }

            if (inputYear === currentYear && inputMonth === currentMonth && inputDay > currentDay) {
                inputDay = currentDay;
            }

            $(this).val(
                `${inputYear}-${String(inputMonth).padStart(2, '0')}-${String(inputDay).padStart(2, '0')}`
            );
        }
        $('.flatpickr-next-month').hide();
        disableFutureDates();
});


$("body").on("click", ".arrowUp, .flatpickr-monthDropdown-month, .flatpickr-prev-month, .arrowDown", function() {
    console.log('Clicked on arrowUp, monthDropdown, or prev-month');
    disableFutureDates();
});


$("body").on("change input", ".flatpickr-monthDropdown-months, .numInput", function() {
    console.log('Month changed');
    disableFutureDates();
});


    // Initialize on page load
    disableFutureDates();
});
</script>
@endpush