


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
                Hi,there
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
                        {{-- <i class="icon profile text-down-3"></i> --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="23" fill="currentColor" class="bi bi-person" viewBox="0 2 22 16">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                          </svg>
                        <span>{{ __('shop::app.header.profile') }}</span>
                    </a>
                </li>

                {{-- sandeep commnet fbo details code --}}
                {{-- <li>
                    <a href="{{ route('shop.customer.addresses.index') }}" class="unset">
                        <i class="icon address text-down-3"></i>
                        <span>Fbo Detail</span>
                    </a>
                </li> --}}

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
                        {{-- <i class="icon orders text-down-3"></i> --}}
                        {{-- sandeep add svg  --}}
                          <svg version="1.1" id="Layer_1" height="20px" width="23px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 149 140.88" style="enable-background:new 0 0 115.35 122.88;" xml:space="preserve"><g><path d="M25.27,86.92c-1.81,0-3.26-1.46-3.26-3.26s1.47-3.26,3.26-3.26h21.49c1.81,0,3.26,1.46,3.26,3.26s-1.46,3.26-3.26,3.26 H25.27L25.27,86.92L25.27,86.92z M61.1,77.47c-0.96,0-1.78-0.82-1.78-1.82c0-0.96,0.82-1.78,1.78-1.78h4.65c0.04,0,0.14,0,0.18,0 c1.64,0.04,3.1,0.36,4.33,1.14c1.37,0.87,2.37,2.19,2.92,4.15c0,0.04,0,0.09,0.05,0.14l0.46,1.82h39.89c1,0,1.78,0.82,1.78,1.78 c0,0.18-0.05,0.36-0.09,0.55l-4.65,18.74c-0.18,0.82-0.91,1.37-1.73,1.37l0,0l-29.18,0c0.64,2.37,1.28,3.65,2.14,4.24 c1.05,0.68,2.87,0.73,5.93,0.68h0.04l0,0h20.61c1,0,1.78,0.82,1.78,1.78c0,1-0.82,1.78-1.78,1.78H87.81l0,0 c-3.79,0.04-6.11-0.05-7.98-1.28c-1.92-1.28-2.92-3.46-3.92-7.43l0,0L69.8,80.2c0-0.05,0-0.05-0.04-0.09 c-0.27-1-0.73-1.69-1.37-2.05c-0.64-0.41-1.5-0.59-2.51-0.59c-0.05,0-0.09,0-0.14,0H61.1L61.1,77.47L61.1,77.47z M103.09,114.13 c2.42,0,4.38,1.96,4.38,4.38s-1.96,4.38-4.38,4.38s-4.38-1.96-4.38-4.38S100.67,114.13,103.09,114.13L103.09,114.13L103.09,114.13z M83.89,114.13c2.42,0,4.38,1.96,4.38,4.38s-1.96,4.38-4.38,4.38c-2.42,0-4.38-1.96-4.38-4.38S81.48,114.13,83.89,114.13 L83.89,114.13L83.89,114.13z M25.27,33.58c-1.81,0-3.26-1.47-3.26-3.26c0-1.8,1.47-3.26,3.26-3.26h50.52 c1.81,0,3.26,1.46,3.26,3.26c0,1.8-1.46,3.26-3.26,3.26H25.27L25.27,33.58L25.27,33.58z M7.57,0h85.63c2.09,0,3.99,0.85,5.35,2.21 s2.21,3.26,2.21,5.35v59.98h-6.5V7.59c0-0.29-0.12-0.56-0.31-0.76c-0.2-0.19-0.47-0.31-0.76-0.31l0,0H7.57 c-0.29,0-0.56,0.12-0.76,0.31S6.51,7.3,6.51,7.59v98.67c0,0.29,0.12,0.56,0.31,0.76s0.46,0.31,0.76,0.31h55.05 c0.61,2.39,1.3,4.48,2.23,6.47H7.57c-2.09,0-3.99-0.85-5.35-2.21C0.85,110.24,0,108.34,0,106.25V7.57c0-2.09,0.85-4,2.21-5.36 S5.48,0,7.57,0L7.57,0L7.57,0z M25.27,60.25c-1.81,0-3.26-1.46-3.26-3.26s1.47-3.26,3.26-3.26h50.52c1.81,0,3.26,1.46,3.26,3.26 s-1.46,3.26-3.26,3.26H25.27L25.27,60.25L25.27,60.25z"></path></g></svg>
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
                {{-- sandeep add icon --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 0 16 16">
                    <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                    <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                  </svg>
                {{-- <img src="{{ asset('themes/volantijetcatering/assets/images/logout.png') }}" alt="" style="width: 15px !important; height: 15px !important" class="m-0"> --}}
                <a class="unset" href="{{ route('shop.customer.session.destroy') }}"
                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                    {{ __('shop::app.header.logout') }}
                </a>
            @endauth

            @guest('customer')
                {{-- <div class="row"> --}}

                <a class="unset side_nav_anchor" href="{{ route('shop.customer.session.index') }}">
                    <div class="">
                        <img src="{{ asset('themes/volantijetcatering/assets/images/profile-user.png') }}" alt=""
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

        $('body').on('click', '.search_icon_mobile', function() {
        $('.mobile-screen-header-col-1 .mobile-small-icon').addClass('hiding');
        setTimeout(function() {
            $('.search-products').removeClass('d-none');
            setTimeout(function() {
                $('.search-products').addClass('show');
                $('.mobile-screen-header-col-1 .mobile-small-icon').addClass('d-none');
            }, 10);
        }, 300);
    });
    
    $('body').on('click', '#close_button_mobile', function() {
        $('.search-products').removeClass('show');
        setTimeout(function() {
            $('.search-products').addClass('d-none');
            $('.mobile-screen-header-col-1 .mobile-small-icon').removeClass('d-none');

            setTimeout(function() {
                $('.mobile-screen-header-col-1 .mobile-small-icon').removeClass('hiding');
            }, 10);
        }, 300);
    });

    });

</script>
@endpush
