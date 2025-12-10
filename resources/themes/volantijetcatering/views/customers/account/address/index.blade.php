@extends('shop::customers.account.index')

@section('page_title')
    {{-- {{ __('shop::app.fbo-detail.page-title') }} --}}
    Addresses | Volanti Jet Catering
@endsection

@section('seo')
<meta name="title" content="Addresses | Volanti Jet Catering" />
<meta name="description" content="" />
<meta name="keywords" content="" />
@stop

@section('page-detail-wrapper')
    {{-- @if ($addresses->isEmpty())
        <a href="{{ route('shop.customer.addresses.create') }}" class="theme-btn light unset address-button">
            {{ __('shop::app.customer.account.address.index.add') }}
        </a>
    @endif --}}
        
    <div class="profile-header account-head d-flex justify-content-center mt-3">
        <h3 class="account-heading">
            {{ __('shop::app.fbo-detail.fbo-head') }}
        </h3>

        {{-- @if (!$addresses->isEmpty())
            <span class="account-action">
                <a href="{{ route('shop.customer.addresses.create') }}" class="theme-btn light unset float-right">
                    {{ __('shop::app.customer.account.address.index.add') }}
                </a>
            </span>
        @endif --}}
    </div>

    {{-- {!! view_render_event('bagisto.shop.customers.account.address.list.before', ['addresses' => $addresses]) !!} --}}

    <div class="container custom-checkout">
        @if ($fboDetails)
            <div class="card row border-top col-sm-12 col-md-5">
                <div class="border-0" style="width: 100%;">
                    <div class="card-body fbo-body">
                        <div class='row'>
                            <div class='col-9'>
                                <h4 class="card-title fw6">{{ __('shop::app.fbo-detail.client-info') }}</h4>
                            </div>
                            <div class='col-3 fbo-edit text-right'><span class="text-danger pointer" data-toggle="modal"
                                data-target="#fboModal">edit</span></div>
                        </div>
                        <div class='fbo-body'>
                            {{-- sandeep add logic --}}
                        @if (empty($fboDetails->full_name))
                            <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                            <h6 class="card-subtitle mb-2">{{ $fboDetails->full_name }} </h6>
                        @endif

                        @if (empty($fboDetails->phone_number))
                              <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                              <h6>{{ $fboDetails->phone_number }}</h6>
                        @endif

                        @if (empty($fboDetails->email_address))
                              <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                              <h6>{{ $fboDetails->email_address }}</h6>
                        @endif

                        </div>
                        <div class='row'>
                            <div class='col-12'>
                                <h4 class="card-title fw6">{{ __('shop::app.fbo-detail.aircraft-info') }}</h4>
                            </div>
                        </div>
                        <div class='fbo-body'>

                        @if (empty($fboDetails->tail_number))
                             <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                             <h6 class="card-subtitle mb-2">{{ $fboDetails->tail_number }} </h6>
                        @endif

                        @if (empty($fboDetails->packaging_section))
                             <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                             <h6>{{ $fboDetails->packaging_section }}</h6>
                        @endif

                        @if (empty($fboDetails->service_packaging))
                             <p class="" style="color:#f84661;font-size:15px">N/A</p>
                        @else
                             <h6>{{ $fboDetails->service_packaging }}</h6>
                        @endif

                        </div>
                    </div>
                </div>
            </div>




            <!-- Modal -->
            <div class="modal fade" id="fboModal" tabindex="-1" role="dialog" aria-labelledby="FboModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header fbo-header">
                            <h1 class="fs24 fw6">
                                {{ __('shop::app.fbo-detail.fbo-head') }}
                            </h1>
                            <button type="button" class="close fbo-close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body popup-content">

                            <div class="body col-12 border-0 p-3">
                                <form action="{{ route('cateringpackage.shop.customer.update-fbo-profile') }}"
                                    method="POST" @submit.prevent="onSubmit">
                                    {{ csrf_field() }}
                                    <div class="row mb-3">
                                        <div class="col-12 text-center">
                                            <h1 class="fs24 fw6">{{ __('shop::app.fbo-detail.client-info') }}</h1>
                                        </div>
                                        <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                            :class="[errors.has('fullname') ? 'has-error' : '']">
                                            <label for="fullname" class="required label-style mandatory">
                                                {{ __('shop::app.fbo-detail.fullname') }}
                                            </label>
                                            <input type="text" class="form-control form-control-lg"
                                                value="{{ $fboDetails->full_name }}" v-validate="'required'"
                                                name='fullname' />
                                            <span class="control-error" v-if="errors.has('fullname')"
                                                v-text="errors.first('fullname')"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                            :class="[errors.has('phonenumber') ? 'has-error' : '']">
                                            <label for="phone number" class="required label-style">
                                                {{ __('shop::app.fbo-detail.phone-number') }}
                                            </label>
                                            <input type="Number" class="form-control form-control-lg"
                                                value="{{ $fboDetails->phone_number }}" name="phonenumber"
                                                v-validate="'required|numeric|min:10|max:12'" />
                                            <span class="control-error" v-if="errors.has('phonenumber')"
                                                v-text="errors.first('phonenumber')"></span>
                                        </div>

                                        <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                            :class="[errors.has('email') ? 'has-error' : '']">
                                            <label for="email" class="required label-style">
                                                {{ __('shop::app.fbo-detail.email-address') }}
                                            </label>
                                            <input type="email" class="form-control form-control-lg"
                                                value="{{ $fboDetails->email_address }}" name="email"
                                                v-validate="'required'" />
                                            <span class="control-error" v-if="errors.has('email')"
                                                v-text="errors.first('email')"></span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-12 text-center">
                                            <h1 class="fs24 fw6">{{ __('shop::app.fbo-detail.aircraft-info') }}</h1>
                                        </div>
                                        <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                            :class="[errors.has('tailnumber') ? 'has-error' : '']">
                                            <label for="tail number" class="required label-style">
                                                {{ __('shop::app.fbo-detail.tail-number') }}
                                            </label>
                                            <input type="text" class="control form-control form-control-lg"
                                                value="{{ $fboDetails->tail_number }}" name="tailnumber"
                                                v-validate="'required'">
                                            <span class="control-error" v-if="errors.has('tailnumber')"
                                                v-text="errors.first('tailnumber')"></span>
                                        </div>


                                        <div class="control-group col-sm-12 col-md-6 mb-3 packagingsection"
                                            :class="[errors.has('packagingsection') ? 'has-error' : '']">

                                            <label for="packaging section" class="required label-style">
                                                {{ __('shop::app.fbo-detail.packaging-section') }}
                                            </label>

                                            <div class="custom-dropdown">
                                                <select class="form-control form-control-lg" name="packagingsection"
                                                    v-validate="'required'">
                                                    <option value="" disabled>Select Packaging</option>
                                                    <option value="Microwave"
                                                        {{ $fboDetails->packaging_section == 'Microwave' ? 'selected' : '' }}>
                                                        Microwave</option>
                                                    <option value="Oven"
                                                        {{ $fboDetails->packaging_section == 'Oven' ? 'selected' : '' }}>
                                                        Oven</option>
                                                    <option value="Both"
                                                        {{ $fboDetails->packaging_section == 'Both' ? 'selected' : '' }}>
                                                        Both</option>
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
                                                <select class="form-control form-control-lg" name="servicepackaging"
                                                    v-validate="'required'">
                                                    <option value="" disabled>Select Packaging</option>
                                                    <option value="Bulk Packaging"
                                                        {{ $fboDetails->service_packaging == 'Bulk Packaging' ? 'selected' : '' }}>
                                                        Bulk Packaging</option>
                                                    <option value="Ready For Services"
                                                        {{ $fboDetails->service_packaging == 'Ready For Services' ? 'selected' : '' }}>
                                                        Ready For Services</option>
                                                </select>
                                            </div>
                                            <span class="control-error" v-if="errors.has('servicepackaging')"
                                                v-text="'The packaging section field is required'"></span>

                                        </div>
                                    </div>

                                    <button class="fbo-btn mx-auto fbo_button fbo_detail_button" type="submit">
                                        {{ __('shop::app.fbo-detail.fbo-update') }}
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="card row border-top col-sm-12 col-md-4 fbo-profile">
                <div class="border-0 " style="width: 100%;">
                    <div class="card-body fbo-body">
                        <p class="text-center add-profile-fbo" data-toggle="modal" data-target="#add-FboModal">+ Add Fbo
                            Detail</p>
                    </div>
                    <!-- Modal -->
                    <div class="modal fade" id="add-FboModal" tabindex="-1" role="dialog"
                        aria-labelledby="FboModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                                <div class="modal-header fbo-header">
                                    <h1 class="fs24 fw6">
                                        {{ __('shop::app.fbo-detail.fbo-head') }}
                                    </h1>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body popup-content">

                                    <div class="body col-12 border-0 p-3">
                                        <form action="{{ route('cateringpackage.shop.customer.add_profile_fbo') }}"
                                            method="POST" @submit.prevent="onSubmit">
                                            {{ csrf_field() }}
                                            <div class="row mb-3">
                                                <div class="col-12 text-center">
                                                    <h1 class="fs24 fw6">{{ __('shop::app.fbo-detail.client-info') }}</h1>
                                                </div>
                                                <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('fullname') ? 'has-error' : '']">
                                                    <label for="fullname" class="required label-style mandatory">
                                                        {{ __('shop::app.fbo-detail.fullname') }}
                                                    </label>
                                                    <input type="text" class="form-control form-control-lg"
                                                        v-validate="'required'" name='fullname' />
                                                    <span class="control-error" v-if="errors.has('fullname')"
                                                        v-text="errors.first('fullname')"></span>
                                                </div>

                                                <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('phonenumber') ? 'has-error' : '']">
                                                    <label for="phone number" class="required label-style">
                                                        {{ __('shop::app.fbo-detail.phone-number') }}
                                                    </label>
                                                    <input type="text" class="form-control form-control-lg"
                                                        name="phonenumber" v-validate="'required'" />
                                                    <span class="control-error" v-if="errors.has('phonenumber')"
                                                        v-text="errors.first('phonenumber')"></span>
                                                </div>

                                                <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('email') ? 'has-error' : '']">
                                                    <label for="email" class="required label-style">
                                                        {{ __('shop::app.fbo-detail.email-address') }}
                                                    </label>
                                                    <input type="email" class="form-control form-control-lg"
                                                        name="email" v-validate="'required'" />
                                                    <span class="control-error" v-if="errors.has('email')"
                                                        v-text="errors.first('email')"></span>
                                                </div>
                                            </div>


                                            <div class="row mb-3">
                                                <div class="col-12 text-center">
                                                    <h1 class="fs24 fw6">{{ __('shop::app.fbo-detail.aircraft-info') }}
                                                    </h1>
                                                </div>
                                                <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('tailnumber') ? 'has-error' : '']">
                                                    <label for="tail number" class="required label-style">
                                                        {{ __('shop::app.fbo-detail.tail-number') }}
                                                    </label>
                                                    <input type="text" class="control form-control form-control-lg"
                                                        name="tailnumber" v-validate="'required'">
                                                    <span class="control-error" v-if="errors.has('tailnumber')"
                                                        v-text="errors.first('tailnumber')"></span>
                                                </div>

                                                <div class="control-group col-sm-12 col-md-6 col-lg-6 mb-3"
                                                    :class="[errors.has('packagingsection') ? 'has-error' : '']">
                                                    <label for="packaging section" class="required label-style">
                                                        {{ __('shop::app.fbo-detail.packaging-section') }}
                                                    </label>

                                                    <div class="custom-dropdown">
                                                        <select class="form-control form-control-lg"
                                                            name="packagingsection" v-validate="'required'">
                                                            <option value="" disabled>Select Packaging</option>
                                                            <option value="Microwave">Microwave</option>
                                                            <option value="Oven">Oven</option>
                                                            <option value="Both">Both</option>
                                                        </select>
                                                    </div>
                                                    <span class="control-error" v-if="errors.has('packagingsection')"
                                                        v-text="errors.first('packagingsection')"></span>
                                                </div>

                                                {{-- service_packaging --}}
                                                <div class="control-group col-sm-12 col-md-6 mb-3 servicepackaging"
                                                    :class="[errors.has('servicepackaging') ? 'has-error' : '']">

                                                    <label for="packaging section" class="required label-style">
                                                        Service Packaging
                                                    </label>

                                                    <div class="custom-dropdown ">
                                                        <select class="form-control form-control-lg"
                                                            name="servicepackaging" v-validate="'required'">
                                                            <option value="" disabled>Select Service Packaging
                                                            </option>
                                                            <option value="Bulk Packaging">Bulk Packaging</option>
                                                            <option value="Ready For Services">Ready For Services</option>
                                                        </select>
                                                    </div>
                                                    <span class="control-error" v-if="errors.has('servicepackaging')"
                                                        v-text="'The packaging section field is required'"></span>
                                                </div>
                                            </div>

                                            <button class="fbo-btn mt-5 m-auto fbo_button fbo_detail_button" id ="fbo_button" type="submit">
                                                {{-- {{ __('shop::app.fbo-detail.fbo-update') }} --}}
                                                Submit
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>


    {{-- <div class="account-table-content">
            @if ($addresses->isEmpty())
                <div>{{ __('shop::app.customer.account.address.index.empty') }}</div>
            @else
                <div class="row address-holder no-padding">
                    @foreach ($addresses as $address)
                        <div class="col-lg-4 col-md-6 col-xs-12">
                            <div class="card m-1">
                                <div class="card-body">
                                    <h5 class="card-title fw6">{{ $address->first_name }} {{ $address->last_name }}</h5>

                                    <ul type="none">
                                        <li>{{ $address->address1 }}</li>
                                        <li>{{ $address->city }}</li>
                                        <li>{{ $address->state }}</li>
                                        <li>{{ core()->country_name($address->country) }} {{ $address->postcode }}</li>
                                        <li>
                                            {{ __('shop::app.customer.account.address.index.contact') }} : {{ $address->phone }}
                                        </li>
                                    </ul>

                                    <a class="card-link" href="{{ route('shop.customer.addresses.edit', $address->id) }}">
                                        {{ __('shop::app.customer.account.address.index.edit') }}
                                    </a>

                                    <a class="card-link" href="javascript:void(0);" onclick="deleteAddress('{{ __('shop::app.customer.account.address.index.confirm-delete') }}', '{{ $address->id }}')">
                                        {{ __('shop::app.customer.account.address.index.delete') }}
                                    </a>

                                    <form id="deleteAddressForm{{ $address->id }}" action="{{ route('shop.customer.addresses.delete', $address->id) }}" method="post">
                                        @method('delete')

                                        @csrf
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div> --}}

    {{-- {!! view_render_event('bagisto.shop.customers.account.address.list.after', ['addresses' => $addresses]) !!} --}}
@endsection

@push('scripts')
    <script>
        function deleteAddress(message, addressId) {
            if (!confirm(message)) {
                return;
            }

            $(`#deleteAddressForm${addressId}`).submit();
        }
    </script>
@endpush

{{-- @if ($addresses->isEmpty())
    <style>
        a#add-address-button {
            position: absolute;
            margin-top: 92px;
        }

        .address-button {
            position: absolute;
            z-index: 1 !important;
            margin-top: 110px !important;
        }
    </style>
@endif --}}
