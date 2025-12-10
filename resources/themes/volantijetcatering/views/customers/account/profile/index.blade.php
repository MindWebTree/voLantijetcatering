@extends('shop::customers.account.index')

@section('page_title')
    {{-- {{ __('shop::app.customer.account.profile.index.title') }} --}}
    Account | Volanti Jet Catering
@endsection


@section('seo')
<meta name="title" content="Account | Volanti Jet Catering" />
<meta name="description" content="Manage your Volanti Jet Catering account — view past orders, update personal details." />
<meta name="keywords" content="" />
@stop

@push('css')
    <style>
        .account-head {
            height: 50px;
        }
    </style>
@endpush


@section('page-detail-wrapper')
    <div class="profile-header my-4">
        <h3 class="account-heading mt-3 pl-0">
            {{ __('shop::app.customer.account.profile.index.title') }}
        </h3>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.view.before', ['customer' => $customer]) !!}

    <div class="account-table-content profile-page-content border">

        <div class="row mt-3">
            {!! view_render_event('bagisto.shop.customers.account.profile.view.table.before', ['customer' => $customer]) !!}
            <div class="col-sm-12 col-md-6 col-lg-6 profile-left mt-2">
                <img src="/../themes/volantijetcatering/assets/images/catering_img.jpg " alt="">
            </div>

            <div class="col-sm-12 col-md-6 col-lg-6 d-block profile-right">
                {{-- <h6>{{ __('shop::app.customer.account.profile.fname') }}</h6> --}}
                <div class="profilename d-flex flex-wrap">
                <h1>{{ $customer->first_name }}</h1>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.first_name.after', [
                    'customer' => $customer,
                ]) !!}

                {{-- <h6>{{ __('shop::app.customer.account.profile.lname') }}</h6> --}}
                <h1>{{ $customer->last_name }}</h1>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.last_name.after', [
                    'customer' => $customer,
                ]) !!}
                </div>
                <div class="mt-3">
                @if($customer->date_of_birth!='')
                <div class="profile-info ">
                    {{-- <h6>{{ __('shop::app.customer.account.profile.dob') }}</h6> --}}
                    <img src="/../themes/volantijetcatering/assets/images/calendar.png" alt="">
                    <p>{{ $customer->date_of_birth ?? '-' }}</p>
                    {!! view_render_event('bagisto.shop.customers.account.profile.view.table.date_of_birth.after', [
                        'customer' => $customer,
                    ]) !!}
                </div>
                @endif
                @if($customer->phone!='')
                <div class="profile-info">
                    {{-- <h6>{{ __('shop::app.customer.account.profile.phone') }}</h6> --}}
                    <img src="/../themes/volantijetcatering/assets/images/telephone.png " alt="">
                    <p>{{ $customer->phone ?? '-' }}</p>
                    {!! view_render_event('bagisto.shop.customers.account.profile.view.table.phone.after', [
                        'customer' => $customer,
                    ]) !!}
                </div>
            @endif

                <div class="profile-info">
                    {{-- <h6>{{ __('shop::app.customer.account.profile.email') }}</h6> --}}
                    <img src="/../themes/volantijetcatering/assets/images/email.png " alt="">
                    <p>{{ $customer->email }}</p>
                </div>
                @if($customer->gender!='')
                <div class="profile-info">
                    {{-- <h6>{{ __('shop::app.customer.account.profile.gender') }}</h6> --}}
                    <img src="/../themes/volantijetcatering/assets/images/sex.png " alt="">
                    <p>{{ $customer->gender ?? '-' }}</p>
                    {!! view_render_event('bagisto.shop.customers.account.profile.view.table.gender.after', [
                        'customer' => $customer,
                    ]) !!}
                </div>
                @endif
            </div>
                {{-- button (Edit and Delete) --}}
                <div class="row mt-1">
                    <div class="col-7">
                        <span class="account-action d-flex">
                            <a href="{{ route('shop.customer.profile.edit') }}"
                                class="theme-btn light unset profile-edit text-center text-white float-right ">
                                {{ __('shop::app.customer.account.profile.index.edit') }}
                            </a>
                        </span>
                    </div>
                    {{-- <div class="col-6">
                <button type="submit" class="theme-btn mb20 profile-delete" onclick="window.showDeleteProfileModal();">
                    {{ __('shop::app.customer.account.address.index.delete') }}
                </button>
            </div> --}}
                </div>

            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.view.table.after', ['customer' => $customer]) !!}
        </div>



        {{-- <div class="row profile-details border p-3">
            {!! view_render_event('bagisto.shop.customers.account.profile.view.table.before', ['customer' => $customer]) !!}
            <div class="col-lg-6 col-md-6 col-sm-12 m-auto profile-detail">

                <h6>{{ __('shop::app.customer.account.profile.fname') }}</h6>
                <p>{{ $customer->first_name }}</p>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.first_name.after', [
                    'customer' => $customer,
                ]) !!}

                <h6>{{ __('shop::app.customer.account.profile.email') }}</h6>
                <p>{{ $customer->email }}</p>

                <h6>{{ __('shop::app.customer.account.profile.gender') }}</h6>
                <p>{{ $customer->gender ?? '-' }}</p>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.gender.after', [
                    'customer' => $customer,
                ]) !!}

            </div>
            <div class="col-lg-6 col-md-6 col-sm-12  m-auto profile-detail">

                <h6>{{ __('shop::app.customer.account.profile.lname') }}</h6>
                <p>{{ $customer->last_name }}</p>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.last_name.after', [
                    'customer' => $customer,
                ]) !!}

                <h6>{{ __('shop::app.customer.account.profile.phone') }}</h6>
                <p>{{ $customer->phone ?? '-' }}</p>
                <h6>{{ __('shop::app.customer.account.profile.dob') }}</h6>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.phone.after', [
                    'customer' => $customer,
                ]) !!}


                <p>{{ $customer->date_of_birth ?? '-' }}</p>
                {!! view_render_event('bagisto.shop.customers.account.profile.view.table.date_of_birth.after', [
                    'customer' => $customer,
                ]) !!}
            </div>
            {!! view_render_event('bagisto.shop.customers.account.profile.view.table.after', ['customer' => $customer]) !!}
        </div> --}}
        {{-- button (Edit and Delete) --}}
        {{-- <div class="row mt-1">
            <div class="col-7 pr-5">
                <span class="account-action">
                    <a href="{{ route('shop.customer.profile.edit') }}"
                        class="theme-btn light unset profile-edit text-center text-white float-right ">
                        {{ __('shop::app.customer.account.profile.index.edit') }}
                    </a>
                </span>
            </div> --}}
        {{-- <div class="col-6">
                <button type="submit" class="theme-btn mb20 profile-delete" onclick="window.showDeleteProfileModal();">
                    {{ __('shop::app.customer.account.address.index.delete') }}
                </button>
            </div> --}}
        {{-- </div> --}}















        {{-- <div class="table">
                <table>
                    <tbody>
                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.before', ['customer' => $customer]) !!}

                        <tr>
                            <td>{{ __('shop::app.customer.account.profile.fname') }}</td>

                            <td>{{ $customer->first_name }}</td>
                        </tr>

                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.first_name.after', ['customer' => $customer]) !!}

                        <tr>
                            <td>{{ __('shop::app.customer.account.profile.lname') }}</td>

                            <td>{{ $customer->last_name }}</td>
                        </tr>

                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.last_name.after', ['customer' => $customer]) !!}

                        <tr>
                            <td>{{ __('shop::app.customer.account.profile.gender') }}</td>

                            <td>{{ $customer->gender ?? '-' }}</td>
                        </tr>

                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.gender.after', ['customer' => $customer]) !!}

                        <tr>
                            <td>{{ __('shop::app.customer.account.profile.dob') }}</td>

                            <td>{{ $customer->date_of_birth ?? '-' }}</td>
                        </tr>

                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.date_of_birth.after', ['customer' => $customer]) !!}

                        <tr>
                            <td>{{ __('shop::app.customer.account.profile.email') }}</td>

                            <td>{{ $customer->email }}</td>
                        </tr>

                        {!! view_render_event('bagisto.shop.customers.account.profile.view.table.after', ['customer' => $customer]) !!}
                    </tbody>
                </table>
            </div> --}}



        <div id="deleteProfileForm" class="d-none">
            <form method="POST" action="{{ route('shop.customer.profile.destroy') }}" @submit.prevent="onSubmit">
                @csrf

                <modal id="deleteProfile" :is-open="modalIds.deleteProfile">
                    <h3 slot="header">
                        {{ __('shop::app.customer.account.address.index.enter-password') }}
                    </h3>

                    <i class="rango-close"></i>

                    <div slot="body">
                        <div class="control-group" :class="[errors.has('password') ? 'has-error' : '']">
                            <label for="password" class="required">{{ __('admin::app.users.users.password') }}</label>

                            <input type="password" v-validate="'required|min:6'" class="control" id="password"
                                name="password" data-vv-as="&quot;{{ __('admin::app.users.users.password') }}&quot;" />

                            <span class="control-error" v-if="errors.has('password')"
                                v-text="errors.first('password')"></span>
                        </div>

                        <div class="page-action">
                            <button type="submit" class="theme-btn mb20">
                                {{ __('shop::app.customer.account.address.index.delete') }}
                            </button>
                        </div>
                    </div>
                </modal>
            </form>
        </div>
    </div>

    {!! view_render_event('bagisto.shop.customers.account.profile.view.after', ['customer' => $customer]) !!}
@endsection

@push('scripts')
    <script>
        /**
         * Show delete profile modal.
         */
        function showDeleteProfileModal() {
            document.getElementById('deleteProfileForm').classList.remove('d-none');

            window.app.showModal('deleteProfile');
        }
    </script>
@endpush
