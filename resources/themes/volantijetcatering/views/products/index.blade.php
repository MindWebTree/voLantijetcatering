<!--  include search fields  -->
@php
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
$guestToken = Session::token();
@endphp
<!-- include search fields  -->
@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')
@inject ('productRepository', 'Webkul\Product\Repositories\ProductRepository')
@extends('shop::layouts.master')
@section('page_title')
{{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}
@stop

@section('seo')
<meta name="title" content="{{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}" />
<meta name="description" content="{{ $category->meta_description }}" />
<meta name="keywords" content="{{ $category->meta_keywords }}" />
<link rel="canonical" href="{{ url()->current() }}" />
@if (core()->getConfigData('catalog.rich_snippets.categories.enable'))
<script type="application/ld+json">
    {
        !!app('Webkul\Product\Helpers\SEO') - > getCategoryJsonLd($category) !!
    }

</script>
@endif
@stop
@push('css')
<style type="text/css">
    .product-price span:first-child,
    .product-price span:last-child {
        font-size: 18px;
        font-weight: 600;
    }

    @media only screen and (max-width: 992px) {
        .main-content-wrapper .vc-header {
            box-shadow: unset;
        }
    }

</style>
@endpush
@php
$isProductsDisplayMode = in_array(
$category->display_mode, [
null,
'products_only',
'products_and_description'
]
);
$isDescriptionDisplayMode = in_array(
$category->display_mode, [
null,
'description_only',
'products_and_description'
]
);

@endphp
@section('content-wrapper')
<category-component></category-component>
@stop
@push('scripts')
<script type="text/x-template" id="category-template">
    <section class="row col-12 velocity-divide-page category-page-wrapper">  
   <div class="listing-overlay w-100 d-flex" style="min-height:190px; align-items:center">
                   <div class="container ">
                   @php 
                   $customer = auth()->guard('customer')->user();                           
                     
                       if(Auth::check())
                       {
                           $islogin = 1;
                           $address = Db::table('addresses')->where('customer_id',$customer->id)->orderBy('created_at', 'desc')->first(); 			
                       }
                       else{
                           $islogin = 0;
                           $address = Db::table('addresses')->where('customer_token',$guestToken)->first();
                       }
                
                    @endphp
                    @if($address!='')
                    
                       <div class=" listing-banner-contant py-3">
                           <h2 class="listing-banner-heading">{{$address->airport_name}}</h2>
                           <p class="listing-paragraph-1">{{$address->address1}}, </p>
                           <p class="listing-paragraph-2">{{$address->state}} {{$address->postcode }},{{$address->country }}</p>        
                       </div>
                       @else
                       <div class="listing-banner-choose-contant py-3">
                        <h1 class="listing-banner-choose-heading"><a href="{{ route('shop.home.index') }}">Choose Location</a></h1>
                    </div>
                    @endif
                       
                       {{-- <div class="listing-searchbar">
                           <div class="category-search-icon">
                           <img src="/themes/volantijetcatering/assets/images/black-search-icon.png">
                           </div>
                           
                           
                           <input type="text" class="form-control" placeholder="Search the menu...">  
                       </div> --}}
                   </div>
               </div>

       {!! view_render_event('bagisto.shop.productOrCategory.index.before', ['category' => $category]) !!}
   
       @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
           @include ('shop::products.list.layered-navigation')
       @endif
       <div class="col-12 no-padding">
                   <div class="hero-image">
                       @if (!is_null($category->category_banner))
                       <!-- here i change the img class logo to image banner -->
                           <img class="image banner" src="{{ $category->banner_url }}" alt="" width="100%" height="350px" />
                       @endif
                   </div>
               </div>
   
        <!-- i change this div class" name category-container "right to container  -->
   
        <div class="container col-lg-9 col-12 mb-3" id="ProductsContainer" style="padding:0;">
           {{ Breadcrumbs::render('shop.productOrCategory.index',$category) }}
       @php                 
        if(count($childCategory)>0){                       
        @endphp
       <div class=" listing-menu-section">
       {{-- <div class="listing-suggestion-list">
       <ul class="listing-menu-contant">


   @foreach ($childCategory as $index => $childSubCategory)
   @if(count($childSubCategory->products)>0)
   <li><a class="text-black " href="#{{$childSubCategory->slug}}">{{ $childSubCategory->name }}</a></li>     
   @endif       
   @endforeach
   
   </ul>
   
   </div> --}}
   </div>
   @php   
}
    @endphp   
           <div class="row remove-padding-margin">
               <div class="pl0 col-12">
                   <!-- <h2 class="fw6 mb10">{{ $category->name }}</h2> -->
   
                   <!-- @if ($isDescriptionDisplayMode)
                       @if ($category->description)
                           <div class="category-description">
                               {!! $category->description !!}
                           </div>
                       @endif
                   @endif -->
               </div>
               </div>
   
     {{-- sandeep add search code --}}
               <div class="search-product ml-3 ml-md-3 ml-lg-3 mr-3 mr-md-3 mr-lg-0">
                <input id="tnb-google-search-input" class="search_product" type="text" placeholder="Search for dishes" name = "product_search">
            </div> 


                   <div class="search_not_found_message pt-5"></div>
               <div class='col-md-12' id="products_header">


                   <div class='carousel-products sub-category'>
                       <!-- <carousel-component
                           :slides-per-page="slidesPerPage"
                           pagination-enabled="hide"
                           :slides-count="{{count($childCategory)}}">
   
                           {{-- @foreach ($childCategory as $index => $childSubCategory)
                               <slide slot="slide-{{ $index }}">
                                   <div class='childSubCategory'>
                                       <a href='{{ url($childSubCategory->url_path) }}'>
                                           <div>
                                               <img src='{{ $childSubCategory->getImageUrlAttribute()?? url("/vendor/webkul/ui/assets/images/product/small-product-placeholder.png") }}'>
                                               <label>{{ $childSubCategory->name }}</label>
                                           
   
   
                                           </div>
                                       </a>
                                   </div>
                               </slide>
                           @endforeach --}}
                       </carousel-component> -->
                        @php
                            if(count($childCategory)>0){
                        @endphp
                        @php
                            $category_ids = $category->id;
                        @endphp
                        @if(count($category->products))
                         <div class="category-block" >
                           
                        @if ($category->display_mode == 'description_only')
                            style="width: 100%"
                        @endif

                        {{-- sandeep delete div --}}
                        {{-- <div class="centered-word center-heading">  --}}
                            <h1  id="categoryheading" class="mt-4"> {{ $category->name }}</h1>   
                        {{-- </div> --}}
                        
                        <div class="container listing-title-section breakfast-image-1 custom-image-background d-none" id="{{$category->slug}}">
                           {{-- sandeep delete image --}}
                            {{-- <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="left-image"> --}}
        
            
                {{-- sandeep delete image --}}
                {{-- <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="right-image"> --}}
        
                </div>
                <div class="row column-title">

                </div> 

                @endif

                {{-- sandeep filter  category data and send category page --}}

                @php
                $products = collect($getCategorydetail['products']);
                
                $categories = collect($getCategorydetail['product_category']);
                $desiredCategoryId = $category->id; // Keep as object property access
            
                // Filter categories based on the desired category ID
                $filteredCategories = $categories->filter(function ($cat) use ($desiredCategoryId) {
                    return $cat->category_id == $desiredCategoryId; // Use object property access
                });
            
                // Extract product IDs from the filtered categories
                $filteredProductIds = $filteredCategories->pluck('product_id');
            
                // Filter products based on the product IDs from the filtered categories
                $categoryproducts = $products->filter(function ($product) use ($filteredProductIds) {
                    return $filteredProductIds->contains($product['id']); // Use object property access
                
                });
                
            @endphp

                        <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>
                        <template v-else-if="products.length > 0">
                            @if ($toolbarHelper->getCurrentMode() == 'grid')    
                                <div class="row col-12 remove-padding-margin custom-product-card ">
                                    {{-- sandeep delete vue.js file --}}
                                    {{-- <custom-product-card
                                        :key="index"
                                        :product="product"
                                        v-for="(product, index) 
                                        in 
                                        products.filter(p=>
                                        product_category.filter(pc=>pc.category_id=={{$category->id}})
                                        .find(_p=>_p.product_id==p.id
                                        ))">
                                    </custom-product-card> --}}

                                    {{-- sandeep send data  --}}
                                    @include ('shop::products.category-products.category-product-list',['categoryproducts'=>$categoryproducts,'cate_id' => $category->id])   
                            

                                </div>
                            @else
                                <div class="product-list">
                                    <custom-product-card
                                        list=true
                                        :key="index"
                                        :product="product"
                                        v-for="(product, index) in products">
                                    </custom-product-card>
                                </div>
                            @endif
        
                            {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]) !!}
        
                                <div class="bottom-toolbar" v-html="paginationHTML"></div>
        
                            {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]) !!}
                        </template> 

                    
            
                       @foreach($childCategory as $index => $childSubCategory)
                      
                 
                     
                       @php
                        $category_ids .= ','.$childSubCategory->id;
                       @endphp
                       @if(count($childSubCategory->products)>0)

                       {{-- sandeep delete code --}}
                       {{-- <div class="container listing-title-section breakfast-image-1 custom-image-background" id="{{$childSubCategory->slug}}">
                       <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="left-image">
    --}}
   
            {{-- <div class="centered-word center-heading">  --}}
                <h1 class ="childCategoryheading mt-4"> {{ $childSubCategory->name }}</h1>   
            {{-- </div> --}}
        {{-- <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="right-image"> --}}
   
</div>
</div>
   <div class=" row column-title">
    

   </div>                            
   @endif

               {{-- sandeep filter child category data and send category page --}}
                
                @php

                $desiredCategoryId = $childSubCategory->id; 

                // Filter products based on the desired category ID
                $filteredProducts = $categories->filter(function ($category) use ($desiredCategoryId) {
                    return $category->category_id == $desiredCategoryId;
                });

                $filteredProductIds = $filteredProducts->pluck('product_id');
        
                // Filter products based on the product IDs from the filtered categories
                $categoryproducts = $products->filter(function ($product) use ($filteredProductIds) {
                    return $filteredProductIds->contains($product['id']);
                });

            @endphp
           <div class="child_category" id="child-category">
           <div class="category-block"
                   @if ($category->display_mode == 'description_only')
                       style="width: 100%"
                   @endif>
   
                   <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>

                <template v-else-if="products.length > 0">
                    @if ($toolbarHelper->getCurrentMode() == 'grid')    
                        <div class="row col-12 remove-padding-margin custom-product-card">

                            {{-- sandeep comment code  --}}
                            {{-- <custom-product-card
                                :key="index"
                                :product="product"
                                v-for="(product, index) 
                                in 
                                products.filter(p=>
                                product_category.filter(pc=>pc.category_id=={{$childSubCategory->id}})
                                .find(_p=>_p.product_id==p.id
                                ))">
                            </custom-product-card> --}}

                            {{-- sandeep send data --}}
                            @include ('shop::products.category-products.category-product-list',['categoryproducts'=>$categoryproducts,'cate_id' => $childSubCategory->id])    
                            @if (!empty($getCategorydetail['paginationHTML']))
                            <div class="pagination-wrapper">
                                {!! $getCategorydetail['paginationHTML'] !!}
                            </div>
                            @endif
                        </div>
                    @else
                        <div class="product-list">
                            <custom-product-card
                                list=true
                                :key="index"
                                :product="product"
                                v-for="(product, index) in products">
                            </custom-product-card>
                        </div>
                    @endif

                    {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]) !!}

                        <div class="bottom-toolbar" v-html="paginationHTML"></div>
                    {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]) !!}
                </template>
                
                
                    <div class="product-list empty" v-else>
                        <h2>{{ __('shop::app.products.whoops') }}</h2>
                        <p>{{ __('shop::app.products.empty') }}</p>
                    </div>
                    </div>
                    </div>   
                    @foreach($childSubCategory as $index => $thirdChildSubCategory)

                     
                 
                    @endforeach
                    
                @push('scripts')                        
            <script>

   Vue.component('category-component', {
       template: '#category-template',
   
       data: function () {
           return {
               'products': [],
               'isLoading': true,
               'paginationHTML': '',
               'currentScreen': window.innerWidth,
               'slidesPerPage': 5,
           }
       },
   
       created: function () {                 
           this.getCategoryProducts();
           this.setSlidesPerPage(this.currentScreen);
       },
   
           methods: {                    
            'getCategoryProducts': function() {
                        this.$http.get(
                                `${this.$root.baseUrl}/category-products/{{ $category_ids }}${window.location.search}`
                            )
                            .then(response => {
                                
                                this.isLoading = false;
                                this.products = response.data.products;
                                // this.paginationHTML = response.data.paginationHTML;
                                this.product_category = response.data
                                    .product_category; //category-id and product-id
                            })
                            .catch(error => {
                                this.isLoading = false;
                                
                            })
                    },
   
           setSlidesPerPage: function (width) {
               
               if (width >= 1200) {
                   this.slidesPerPage = 5;
               } else if (width < 1200 && width >= 626) {
                   this.slidesPerPage = 3;
               } else if (width < 626 && width >= 400) {
                   this.slidesPerPage = 2;
               } else {
                   this.slidesPerPage = 1;
               }
           }
       }
   })
   
   
</script>
@endpush
@endforeach
@php
}else{
@endphp

<!-- work when only product show-->
@if ($isProductsDisplayMode)

{{-- sandeep delte code --}}
{{-- <div class="container listing-title-section breakfast-image-1 custom-image-background" id="{{$category->slug}}">
    <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="left-image">
    
    
        <div class="centered-word center-heading">
                <h1> {{$category->name}}</h1>
                    </div>
                        <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="right-image">
                        
                        </div> --}}

                        {{-- sandeep add search code --}}
                        <h1  id="categoryheading" class="mt-4"> {{ $category->name }}</h1>  
                        
                        {{-- <div class="search-product">
                            <input id="tnb-google-search-input" class="search_product" type="text" placeholder="Search for dishes" name = "product_search">
                        </div> --}}


<div class="row column-title">

</div>
<div class="category-block" @if ($category->display_mode == 'description_only')
    style="width: 100%"
    @endif>

    <shimmer-component v-if="isLoading" shimmer-count="4"></shimmer-component>

    <template v-else-if="products.length > 0">
        @if ($toolbarHelper->getCurrentMode() == 'grid')

        <div class="row col-12 remove-padding-margin custom-product-card">
            {{-- sandeep delete code  --}}
            {{-- <custom-product-card :key="index" :product="product" v-for="(product, index) in products">
            </custom-product-card> --}}

            {{-- sandeep send data to single-category-products page --}}
            @include ('shop::products.category-products.single-category-products',['cate_id' => $category->id])
        </div>
        @else
        <div class="product-list">
            {{-- <custom-product-card list=true :key="index" :product="product" v-for="(product, index) in products">
            </custom-product-card> --}}
            @include ('shop::products.category-products.single-category-products',['cate_id' => $category->id])
        </div>
        @endif

        {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.before', ['category' => $category]) !!}

        <div class="bottom-toolbar" v-html="paginationHTML"></div>

        {!! view_render_event('bagisto.shop.productOrCategory.index.pagination.after', ['category' => $category]) !!}
    </template>



    <div class="product-list empty" v-else>
        <h2>{{ __('shop::app.products.whoops') }}</h2>
        <p>{{ __('shop::app.products.empty') }}</p>
    </div>
</div>
@endif
@push('scripts')
<script>
    
    Vue.component('category-component', {
        template: '#category-template'
        , data: function() {
            return {
                'products': []
                , 'isLoading': true
                , 'paginationHTML': ''
                , 'currentScreen': window.innerWidth
                , 'slidesPerPage': 5
            , }
        },

        created: function() {
            this.getCategoryProducts();
            this.setSlidesPerPage(this.currentScreen);
        },

        methods: {
            'getCategoryProducts': function() {
                this.$http.get(`${this.$root.baseUrl}/category-products/{{$category->id}}?limit=48${window.location.search}`)
                    .then(response => {
                       this.isLoading = false;
                        this.products = response.data.products;

                        // this.paginationHTML = response.data.paginationHTML;
                    })
                    .catch(error => {
                        this.isLoading = false;
                    })
            },

            setSlidesPerPage: function(width) {

                if (width >= 1200) {
                    this.slidesPerPage = 5;
                } else if (width < 1200 && width >= 626) {
                    this.slidesPerPage = 3;
                } else if (width < 626 && width >= 400) {
                    this.slidesPerPage = 2;
                } else {
                    this.slidesPerPage = 1;
                }
            }
        }
    })

</script>
@endpush
@php

}
@endphp
</div>
</div>
@if ($isProductsDisplayMode)
<div class="filters-container">
    <template v-if="products.length >= 0">
        {{-- @include ('shop::products.list.toolbar') --}}
    </template>
</div>
</div>
@endif
{!! view_render_event('bagisto.shop.productOrCategory.index.after', ['category' => $category]) !!}
</section>
</script>

@endpush



@push('scripts')

@endpush
