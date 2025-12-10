<!-- shyam html added 05-07-2023-->
<header class="sticky-header">
    <div class="row remove-padding-margin velocity-divide-page custom-header">

        <a class="left navbar-brand" href="{{ route('shop.home.index') }}" aria-label="Logo">
            @if (request()->is('/'))
                <img class="logo ml-5"
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-only.png') }}"
                    alt="" />
            @else
                <img class="logo"
                    src="{{ core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-volanti-dark.png') }}">
            @endif
        </a>
        <div class="right searchbar">
            <div class="row sticky-horizontal">

                {{-- 20-05-2024 || design changes --}}
                <div class="col-lg-12 d-flex search__container">
                    <div class="volanti_site_button px-3">
                        <a href="https://www.volantijetcatering.com/">menu</a>
                    </div>
                    @include('shop::layouts.particals.search-bar')

                    <div class="signin-button-wrapper">

                        <div class="search_icon" id="search-icon">
                            <i class="fs16 fw6 rango-search d-flex" style="font-size: 21px;padding: 0 16px 0 5px;"></i>
                        </div>
                        <img class="header_searchbar_close_button" src="{{ asset('themes/volantijetcatering/assets/images/close.png') }}" alt="" style="height: 20px; width: 40px; padding: 0px 16px 0px 5px;display:none;"/>
                        <div class="icon-wrapper">


                            <!------------ copy login-section tap nav and added by umesh 06-07-2023----------->

                            {!! view_render_event('bagisto.shop.layout.header.account-item.before') !!}

                            <div id="account">
                                <div class="d-inline-block welcome-content dropdown-toggle">
                                    @if (auth()->guard('customer')->user())
                                        @if (auth()->guard('customer')->user()->image)
                                            <i class="align-vertical-top"><img class= "profile-small-icon"
                                                    src="{{ auth('customer')->user()->image_url }}"
                                                    alt="" /></i>
                                        @else
                                            <div class="customer-name col-12 text-uppercase">
                                                {{ substr(auth('customer')->user()->first_name, 0, 1) }}
                                            </div>
                                        @endif
                                    @else
                                        <img src="{{ asset('/../themes/volantijetcatering/assets/images/profile-user.png') }}"
                                            alt="" height="25px" />
                                    @endif



                                    <span class="text-center">


                                        @php
                                            $customerName = auth()->guard('customer')->user()
                                                ? auth()->guard('customer')->user()->first_name
                                                : '<a href="' .
                                                    route('shop.customer.session.index') .
                                                    '">' .
                                                    trans('shop::app.header.sign-in') .
                                                    '</a>';
                                        @endphp



                                        @if (auth()->guard('customer')->user())
                                            {!! __('velocity::app.header.welcome-message', [
                                                'customer_name' => auth()->guard('customer')->user()
                                                    ? '<span class="truncate">' . Str::limit(auth()->guard('customer')->user()->first_name, 10) . '</span>'
                                                    : '<a href="' . route('shop.customer.session.index') . '">' . trans('shop::app.header.sign-in') . '</a>',
                                            ]) !!}
                                        @else
                                            {!! auth()->guard('customer')->user()
                                                ? auth()->guard('customer')->user()->first_name
                                                : '<a href="' . route('shop.customer.session.index') . '">' . trans('shop::app.header.sign-in') . '</a>' !!}
                                        @endif

                                    </span>

                                    @if (auth()->guard('customer')->user())
                                        <span class="rango-arrow-down"> </span>
                                    @endif
                                </div>

                                @guest('customer')
                                @endguest

                                @auth('customer')
                                    <div class="dropdown-list">
                                        <div class="dropdown-label  user__name">

                                            {{ auth()->guard('customer')->user()->first_name }}
                                        </div>

                                        <div class="dropdown-container profile__dropdown">
                                            <ul type="none">
                                                <li>
                                                    <img src="{{ asset('themes/volantijetcatering/assets/images/profile-users.png') }}"
                                                        alt="">
                                                    <a href="{{ route('shop.customer.profile.index') }}"
                                                        class="unset p-2">{{ __('shop::app.header.profile') }}</a>
                                                </li>

                                                <li>
                                                    <img src="{{ asset('themes/volantijetcatering/assets/images/orders.png') }}"
                                                        alt="">
                                                    <a href="{{ route('shop.customer.orders.index') }}"
                                                        class="unset p-2">{{ __('velocity::app.shop.general.orders') }}</a>
                                                </li>

                                                @if ((bool) core()->getConfigData('general.content.shop.wishlist_option'))
                                                    <li>
                                                        <a href="{{ route('shop.customer.wishlist.index') }}"
                                                            class="unset p-2">{{ __('shop::app.header.wishlist') }}</a>
                                                    </li>
                                                @endif

                                                @if ((bool) core()->getConfigData('general.content.shop.compare_option'))
                                                    <li>
                                                        <a href="{{ route('velocity.customer.product.compare') }}"
                                                            class="unset p-2">{{ __('velocity::app.customer.compare.text') }}</a>
                                                    </li>
                                                @endif

                                                <li>
                                                    <form id="customerLogout"
                                                        action="{{ route('shop.customer.session.destroy') }}"
                                                        method="POST">
                                                        @csrf

                                                        @method('DELETE')
                                                    </form>
                                                    <img src="{{ asset('themes/volantijetcatering/assets/images/logout.png') }}"
                                                        alt="">
                                                    <a class="unset p-2"
                                                        href="{{ route('shop.customer.session.destroy') }}"
                                                        onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                                        {{ __('shop::app.header.logout') }}
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                @endauth
                            </div>

                            {!! view_render_event('bagisto.shop.layout.header.account-item.after') !!}

                            <!------------ended by umesh 06-07-2023----------->

                        </div>
                        <!--  <div class="cart-wrapper">
                                        <img src="{{ asset('themes/velocity/assets/images/shopping-bag-icon.png') }}" alt="" />
                                    </div> -->


                        <div class="left-wrapper">
                            {!! view_render_event('bagisto.shop.layout.header.wishlist.before') !!}

                            @include('velocity::shop.layouts.particals.header-compts', ['isText' => true])

                            {!! view_render_event('bagisto.shop.layout.header.cart-item.after') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>
<!-- shyam html end  05-07-2023-->
@push('scripts')
    <script type="text/javascript">
        (() => {
            document.addEventListener('scroll', e => {
                scrollPosition = Math.round(window.scrollY);

                if (scrollPosition > 50) {
                    document.querySelector('header').classList.add('header-shadow');
                } else {
                    document.querySelector('header').classList.remove('header-shadow');
                }
            });
        })();
    </script>
@endpush
