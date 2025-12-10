<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    {{-- title --}}
    <title>@yield('page_title')</title>

    {{-- meta data --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="content-language" content="{{ app()->getLocale() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url()->to('/') }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!---------added by umesh 14-07-2023--------->

    <link rel="stylesheet" href="{{ asset('themes/velocity/assets/css/style.css') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@700&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Oswald&display=swap" rel="stylesheet">


    <!---------end by umesh 14-07-2023--------->

    <link rel="stylesheet" href="{{ asset('themes/velocity/assets/css/jquery-ui.min.css') }}" />

    <!---------end by shyam 01-08-2023--------->

    {!! view_render_event('bagisto.shop.layout.head') !!}

    {{-- for extra head data --}}
    @yield('head')

    {{-- seo meta data --}}
    @yield('seo')

    {{-- fav icon --}}
    @if ($favicon = core()->getCurrentChannel()->favicon_url)
        <link rel="icon" sizes="16x16" href="{{ $favicon }}" />
    @else
        <link rel="icon" sizes="16x16"
            href="{{ asset('/themes/volantijetcatering/assets/images/static/v-icon.png') }}" />
    @endif

    {{-- all styles --}}
    @include('shop::layouts.styles')
</head>

<body @if (url()->current() == 'http://127.0.0.1:8000') class="home-page" @else class="" @endif
    @if (core()->getCurrentLocale() && core()->getCurrentLocale()->direction === 'rtl') class="rtl" @endif>
    {!! view_render_event('bagisto.shop.layout.body.before') !!}

    {{-- main app --}}
    <div id="app">
        <product-quick-view v-if="$root.quickView"></product-quick-view>

        <div class="main-container-wrapper">

            @section('body-header')
                {{-- top nav which contains currency, locale and login header --}}

                {{-- comment by umesh 14-07-2023
                    @include('shop::layouts.top-nav.index') --}}

                {!! view_render_event('bagisto.shop.layout.header.before') !!}

                {{-- primary header after top nav --}}
                @include('shop::layouts.header.index')

                {!! view_render_event('bagisto.shop.layout.header.after') !!}

                <div class="main-content-wrapper col-12 no-padding shop-by-category">

                    {{-- secondary header --}}
                    <header class="row velocity-divide-page vc-header header-shadow active">

                        {{-- mobile header --}}
                        <div class="vc-small-screen container header-background " v-if='$root.currentScreen <= 992'>
                            @include('shop::layouts.header.mobile')
                        </div>

                        {{-- desktop header --}}
                        {{-- commented by umesh 17-07-2023
                            @include('shop::layouts.header.desktop')
                          --}}

                    </header>

                    <div class="">
                        <div class="row col-12 remove-padding-margin ">
                            <sidebar-component main-sidebar=true id="sidebar-level-0" url="{{ url()->to('/') }}"
                                category-count="{{ $velocityMetaData ? $velocityMetaData->sidebar_category_count : 10 }}"
                                add-class="category-list-container pt10">
                            </sidebar-component>

                            <div class="col-12 no-padding content  row-display-none" id="home-right-bar-container">
                                <div class="container-right row no-margin col-12 no-padding">
                                    {!! view_render_event('bagisto.shop.layout.content.before') !!}

                                    @yield('content-wrapper')

                                    {!! view_render_event('bagisto.shop.layout.content.after') !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @show

            <div class="container-fuild">
                {!! view_render_event('bagisto.shop.layout.full-content.before') !!}

                @yield('full-content-wrapper')

                {!! view_render_event('bagisto.shop.layout.full-content.after') !!}
            </div>
        </div>

        {{-- overlay loader --}}
        <velocity-overlay-loader></velocity-overlay-loader>

        <go-top bg-color="#26A37C"></go-top>
    </div>

    {{-- footer --}}
    @section('footer')
        {!! view_render_event('bagisto.shop.layout.footer.before') !!}

        @include('shop::layouts.footer.index')

        {!! view_render_event('bagisto.shop.layout.footer.after') !!}
    @show

    {!! view_render_event('bagisto.shop.layout.body.after') !!}

    {{-- alert container --}}
    <div id="alert-container"></div>

    {{-- all scripts --}}
    @include('shop::layouts.scripts')
</body>

</html>
