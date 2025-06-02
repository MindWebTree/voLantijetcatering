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
@if (! request()->is('/'))
    <meta name="title" content="Menu | Volanti Jet Catering"/>
    <meta name="description" content="Explore our diverse food menu, packed with flavors to suit every craving. From classic favorites to exciting new dishes, find the perfect meal for any occasion!"/>
    <meta name="keywords" content="Online Food Menu" />
    <link rel="canonical" href="{{ url()->current() }}" />
@endif
@stop

@section('content-wrapper')

@php 
$customer = auth()->guard('customer')->user();                           
if(Auth::check())
{
$islogin = 1;
$address = Db::table('addresses')->where('address_type','customer')->where('customer_id',$customer->id)->orderBy('created_at', 'desc')->first(); 
// dd($address);			
}
else{
$islogin = 0;
$address = Db::table('addresses')->where('customer_token',$guestToken)->first();
}

@endphp



    <div class="listing-overlay w-100 d-flex" style="min-height: 210px; align-items: center;">
        <div class="container p-4">
        
            @if($address!='')
            <div class=" listing-banner-contant border-0">
                <h1 class="listing-banner-heading">{{$address->airport_name}}</h1>
                <p class="listing-paragraph-1">{{$address->address1}}, </p>
                <p class="listing-paragraph-2">{{$address->state}} {{$address->postcode }},{{$address->country }}</p>
            </div>
           @else
            <div class="listing-banner-choose-contant border-0">
                <h1 class="listing-banner-choose-heading"><a href="{{ route('shop.home.index') }}">Choose Location</a></h1>
            </div>
           @endif
        

            {{-- <div class="listing-searchbar">
                <div class="category-search-icon">
                    <img src="/themes/volantijetcatering/assets/images/black-search-icon.png">
                </div>
                <input type="text" class="form-control" id="category-search" placeholder="Search the menu...">
            </div> --}}
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

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    jQuery(document).ready(function() {
        var categories = @json($categories);

        var category_name='';
        jQuery("body").on("keyup", '#category-search',function() {
            var searchTerm = jQuery(this).val().toLowerCase();


            jQuery.each( categories, function( key, value ) {
                return  category_name = value.name;
            });


            var filteredCategories = category_name.filter(function(category) {
                return category.toLowerCase().includes(searchTerm);
            });

            updateCategoryList(filteredCategories);
        });

        function updateCategoryList(filteredCategories) {
            jQuery(".subcategories").empty();

            jQuery.each(filteredCategories, function(index, category) {
                jQuery(".subcategories").append('<div class="col-sm-12 col-md-4 col-lg-3"><div class="card-block text-center mt-5"><a href="' + category.slug + '"><span class="text-center category-name">' + category.name + '</span></a></div></div>');
            });
        }


        // jQuery('body').on('keyup', '#category-search', function() {
        //         // console.log('hfggh');
        //         var name = jQuery(this).val();
        //         // $('#address_update').prop('disabled', false);
        //         $.ajax({
        //             url: "{{ route('shop.home.index') }}",

        //             type: 'GET',
        //             data: {
        //                 'name': name
        //             },
        //             success: function(result) {
        //                 console.log(result);
        //                 jQuery("#address-list").html(result);
        //             }
        //         });

        //     })
    });
</script>
@endpush