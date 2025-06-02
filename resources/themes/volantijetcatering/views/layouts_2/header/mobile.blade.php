


@php
    $cart = cart()->getCart();
    $cartItemsCount = $cart ? $cart->items->count() : trans('shop::app.minicart.zero');
@endphp

<mobile-header is-customer="{{ auth()->guard('customer')->check()? 'true': 'false' }}"
    heading= "{{ __('velocity::app.menu-navbar.text-category') }}"
    :header-content="{{ json_encode(app('Webkul\Velocity\Repositories\ContentRepository')->getAllContents()) }}"
    category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 10 }}"
    cart-items-count="{{ $cartItemsCount }}" cart-route="{{ route('shop.checkout.cart.index') }}"
    :locale="{{ json_encode(core()->getCurrentLocale()) }}"
    :all-locales="{{ json_encode(core()->getCurrentChannel()->locales()->orderBy('name')->get()) }}"
    :currency="{{ json_encode(core()->getCurrentCurrency()) }}"
    :all-currencies="{{ json_encode(core()->getCurrentChannel()->currencies) }}">

    {{-- this is default content if js is not loaded --}}
    <div class="row mobile-header-view ">

        <div class="hamburger-wrapper">
            <i class="rango-toggle hamburger"></i>
        </div>
        <div class="header-centered">
            <a class="left mobile-logo" href="{{ route('shop.home.index') }}" aria-label="Logo">
                @if (Request()->is('/'))
                    <img class="logo"
                        src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-only.png') }}"
                        alt="" />
                @else
                    <img class="logo"
                        src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-volanti-dark.png') }}"
                        alt="" />
                @endif
            </a>
        </div>


        <div class="right-vc-header">
            <a class="unset cursor-pointer">
                <i class="material-icons">search</i>
            </a>
            <a href="{{ route('shop.checkout.cart.index') }}" class="unset">
                {{-- <i class="material-icons text-down-3">shopping_cart</i> --}}
                <i class="material-icons text-down-3 mobile-bag-icon"></i>
                {{-- <div class="badge-wrapper">
                    <span class="badge">{{ $cartItemsCount }}</span>
                </div> --}}
            </a>
        </div>
    </div>

    <template v-slot:greetings>
        @guest('customer')
            <a class="unset" href="{{ route('shop.customer.session.index') }}">
                {{ __('velocity::app.responsive.header.greeting-for-guest') }}
            </a>
        @endguest

        @auth('customer')
            <a class="unset" href="{{ route('shop.customer.profile.index') }}">
                {{ __('velocity::app.responsive.header.greeting', ['customer' => auth()->guard('customer')->user()->first_name]) }}
            </a>
        @endauth
    </template>

    <template v-slot:customer-navigation>
        @auth('customer')
            <ul type="none" class="vc-customer-options">
                <li>
                    <a href="{{ route('shop.customer.profile.index') }}" class="unset">
                        <i class="icon profile text-down-3"></i>
                        <span>{{ __('shop::app.header.profile') }}</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('shop.customer.addresses.index') }}" class="unset">
                        <i class="icon address text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.addresses') }}</span>
                    </a>
                </li>

                <!-- here remove the download product,wishlist and review on 26-07-23 by shyam -->


                <!-- <li>
                        <a href="{{ route('shop.customer.reviews.index') }}" class="unset">
                            <i class="icon reviews text-down-3"></i>
                            <span>{{ __('velocity::app.shop.general.reviews') }}</span>
                        </a>
                    </li> -->

                @if (core()->getConfigData('general.content.shop.wishlist_option'))
                    <!-- <li>
                            <a href="{{ route('shop.customer.wishlist.index') }}" class="unset">
                                <i class="icon wishlist text-down-3"></i>
                                <span>{{ __('shop::app.header.wishlist') }}</span>
                            </a>
                        </li> -->
                @endif

                @if (core()->getConfigData('general.content.shop.compare_option'))
                    <li>
                        <a href="{{ route('velocity.customer.product.compare') }}" class="unset">
                            <i class="icon compare text-down-3"></i>
                            <span>{{ __('shop::app.customer.compare.text') }}</span>
                        </a>
                    </li>
                @endif

                <li>
                    <a href="{{ route('shop.customer.orders.index') }}" class="unset">
                        <i class="icon orders text-down-3"></i>
                        <span>{{ __('velocity::app.shop.general.orders') }}</span>
                    </a>
                </li>
                <!--
                    <li>
                        <a href="{{ route('shop.customer.downloadable_products.index') }}" class="unset">
                            <i class="icon downloadables text-down-3"></i>
                            <span>{{ __('velocity::app.shop.general.downloadables') }}</span>
                        </a>
                    </li> -->
            </ul>
        @endauth
    </template>

    <template v-slot:extra-navigation>
        <li>
            @auth('customer')
                <form id="customerLogout" action="{{ route('shop.customer.session.destroy') }}" method="POST">
                    @csrf

                    @method('DELETE')
                </form>

                <a class="unset" href="{{ route('shop.customer.session.destroy') }}"
                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                    {{ __('shop::app.header.logout') }}
                </a>
            @endauth

            @guest('customer')
                {{-- <div class="row"> --}}

                <a class="unset side_nav_anchor" href="{{ route('shop.customer.session.index') }}">
                    <div class="">
                        <img src="/../themes/volantijetcatering/assets/images/profile-user.png" alt=""
                        class="small_device_icon">
                    </div>
                    <span>{{ __('shop::app.customer.login-form.title') }}</span>
                </a>
                {{-- </div> --}}
            @endguest
        </li>

        <!--<li>-->
        <!--    @guest('customer')
    -->
            <!--        <a-->
            <!--            class="unset"-->
            <!--            href="{{ route('shop.customer.register.index') }}">-->
            <!--            <span>{{ __('shop::app.header.sign-up') }}</span>-->
            <!--        </a>-->
            <!--
@endguest-->
        <!--</li>-->
    </template>

    <template v-slot:logo>
        
        <a class="left" href="{{ route('shop.home.index') }}" aria-label="Logo">
            @if (request()->is('/'))
                <img class="logo"
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-only.png') }}"
                    alt="" />
            @else
                <img class="logo"
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-volanti-dark.png') }}"
                    alt="" />
            @endif
        </a>
       
        {{-- sandeep add search icon --}}
        {{-- <div class="search_icon" id="search-icon">
            <i class="fs16 fw6 rango-search d-flex" style="font-size: 21px;padding: 0 10px 0 5px;"></i>
        </div> --}}

        <div>


    </template>

    <template v-slot:search-icon>
        <div class="search_icon search_icon_mobile mt-2" id="search-icon">
            <i class="fs16 fw6 rango-search d-flex" style="font-size: 21px;padding: 0 10px 0 5px;" id="dropdownMenuButton"></i>
        </div>

    </template>

    {{-- sandeep add search --}}
    <template v-slot:searchbar> 
        <div class="search-products-header">
        @include('shop::layouts.particals.search-bar')

        <img class="mt-0 ml-2 header_searchbar_close_button" id="close_button_mobile"
            src="{{ asset('themes/volantijetcatering/assets/images/close.png') }}" alt=""
            style="height: 20px; width: 40px; padding: 0px 16px 0px 5px;display:none;" />
    
        </div>
    </template>



    <template v-slot:search-bar>
        <div class="row">
            <div class="col-md-12">
                @include('velocity::shop.layouts.particals.search-bar')
            </div>
        </div>
    </template>
</mobile-header>


@push('scripts')
<script>

    //  sandeep || add searchbar in mobile 
    $(document).ready(function() {

        $('body').on('click','.search_icon_mobile',function(){

            $('.search-products').toggleClass('d-none');
            $('.mobile-screen-header-col-1 .mobile-small-icon').addClass('d-none');


        });

       $('body').on('click','#close_button_mobile',function() {
            $('.search-products').addClass('d-none');
            // $('#home-right-bar-container').css('margin-top', 'auto');
            $('.mobile-screen-header-col-1 .mobile-small-icon').removeClass('d-none');

        });

    });

</script>

@endpush
