@php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    $guestToken = Session::token();
@endphp
@extends('shop::layouts.master')
@section('page_title')
    Menu | Volanti Jet Catering
@stop

@section('seo')
    @if (!request()->is('/'))
        <meta name="title" content="Menu | Volanti Jet Catering" />
        <meta name="description"
            content="Explore our diverse food menu, packed with flavors to suit every craving. From classic favorites to exciting new dishes, find the perfect meal for any occasion!" />
        <meta name="keywords" content="Online Food Menu" />
        <link rel="canonical" href="{{ url()->current() }}" />
    @endif
@stop

@section('content-wrapper')

    @php
        $customer = auth()->guard('customer')->user();
        if (Auth::check()) {
            $islogin = 1;
            $address = Db::table('addresses')
                ->where('address_type', 'customer')
                ->where('customer_id', $customer->id)
                ->orderBy('created_at', 'desc')
                ->first();
            // dd($address);
        } else {
            $islogin = 0;
            $address = Db::table('addresses')->where('customer_token', $guestToken)->first();
        }

    @endphp



    <div class="listing-overlay w-100 d-flex" style="min-height: 210px; align-items: center;">
        <div class="container p-4">

            <div class="listing-banner-contant border-0" id="airport-section" class="hidden-div">
                <h1 class="listing-banner-heading" id="airport_name"></h1>
                <p class="listing-paragraph-1" id="airport_address1"></p>
                <p class="listing-paragraph-2" id="airport_location"></p>
            </div>

            <div class="listing-banner-choose-contant border-0" id="choose-section" class="hidden-div">
                <h1 class="listing-banner-choose-heading">
                    <a href="{{ route('shop.home.index') }}">Choose Location</a>
                </h1>
            </div>

        </div>
    </div>
    <div class="container category-page mb-5">
        {{ Breadcrumbs::render('shop.product.parentcat') }}
        <div class="row subcategories">
            @foreach ($categories as $category)
                <div class="col-sm-12 col-md-4 col-lg-3">
                    <a href={{ $category->slug }}>
                        <div class="card-block text-center mt-5">
                            <span class="text-center">{{ $category->name }}</span>
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
    </div>
@endsection
<style>
    .hidden-div {
        display: none !important;
    }

    .visible-div {
        display: block !important;
    }
</style>

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $.get("{{ route('ajax.get-user-address') }}", function(address) {

                console.log("API Response:", address);

                if (address && Object.keys(address).length > 0) {

                    $("#airport_name").text(address.airport_name);
                    $("#airport_address1").text(address.address1 + ",");
                    $("#airport_location").text(address.state + " " + address.postcode + ", " + address
                        .country);

                    $("#choose-section").removeClass("visible-div").addClass("hidden-div");
                    $("#airport-section").removeClass("hidden-div").addClass("visible-div");

                } else {

                    $("#airport-section").removeClass("visible-div").addClass("hidden-div");
                    $("#choose-section").removeClass("hidden-div").addClass("visible-div");

                }
            });




            var categories = @json($categories);

            var category_name = '';
            jQuery("body").on("keyup", '#category-search', function() {
                var searchTerm = jQuery(this).val().toLowerCase();


                jQuery.each(categories, function(key, value) {
                    return category_name = value.name;
                });


                var filteredCategories = category_name.filter(function(category) {
                    return category.toLowerCase().includes(searchTerm);
                });

                updateCategoryList(filteredCategories);
            });

            function updateCategoryList(filteredCategories) {
                jQuery(".subcategories").empty();

                jQuery.each(filteredCategories, function(index, category) {
                    jQuery(".subcategories").append(
                        '<div class="col-sm-12 col-md-4 col-lg-3"><div class="card-block text-center mt-5"><a href="' +
                        category.slug + '"><span class="text-center category-name">' + category.name +
                        '</span></a></div></div>');
                });
            }

        });
    </script>
@endpush
