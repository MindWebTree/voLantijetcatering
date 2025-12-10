@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

@extends('shop::layouts.master')

@section('page_title')
{{-- {{ __('shop::app.search.page-title') }} --}}
{{ app('request')->input('term') ? app('request')->input('term') . ' | Volanti Jet Catering' : '| Volanti Jet Catering' }}
@endsection

@section('seo')
<meta name="title" content="{{ app('request')->input('term') ? app('request')->input('term') . ' | Volanti Jet Catering' : '| Volanti Jet Catering' }}" />
<meta name="description" content="Find your favorite dishes and ingredients with ease! Use our search to explore a world of delicious options tailored to your cravings." />
<meta name="keywords" content="" />
<link rel="canonical" href="{{ url()->current() }}" />

@stop

@push('css')
    <style type="text/css">
        .category-container {
            min-height: unset;
        }

        .toolbar-wrapper .col-4:first-child {
            display: none !important;
        }

        .toolbar-wrapper .col-4:last-child {
            right: 0;
            position: absolute;
        }

        @media only screen and (max-width: 992px) {
            .main-content-wrapper .vc-header {
                box-shadow: unset;
            }

            .toolbar-wrapper .col-4:last-child {
                left: 175px;
            }

            .toolbar-wrapper .sorter {
                left: 35px;
                position: relative;
            }

            .quick-view-btn-container,
            .rango-zoom-plus,
            .quick-view-in-list {
                display: none;
            }
        }
    </style>
@endpush
@php
    

 $categoryIds = collect($getCategorydetail['product_category'] ?? [])
    ->pluck('category_id')
    ->filter()
    ->toArray();

   
$categorySlugs = [];

if (!empty($categoryIds)) {
    $categorySlugs = DB::table('category_translations')
        ->whereIn('category_id', $categoryIds)
        ->where('locale', app()->getLocale())
        ->pluck('slug')
        ->filter()
        ->map(fn($slug) => trim($slug))
        ->toArray();
}
if (empty($categorySlugs)) {
    $categorySlugs = ['not-available'];
}

// Convert array to comma-separated string
$categorySlugsString = implode(',', $categorySlugs);

// dd($categorySlugsString);
if (Auth::check()) {
    $date_of_birth = auth()->user()->date_of_birth;
} else {
    $guestToken = Session::token();
    $guestDob = DB::table('customers')
        ->where('token', $guestToken)
        ->value('date_of_birth');

    $date_of_birth = $guestDob;
}
       
@endphp
<input type="hidden" id="category_slug" value="{{ $categorySlugsString }}">

<input type="hidden" 
    id="userAge"    
        value="{{ $date_of_birth ? \Carbon\Carbon::parse($date_of_birth)->age : '' }}"
   
>
@section('content-wrapper')
    <div class="container category-page-wrapper">
        <search-component></search-component>
    </div>
@endsection

@push('scripts')
    <script type="text/x-template" id="image-search-result-component-template">
        <div class="image-search-result">
            <div class="searched-image">
                <img :src="searchedImageUrl" alt=""/>
            </div>

            <div class="searched-terms">
                <h3 class="fw6 fs20 mb-4">
                    {{ __('shop::app.search.analysed-keywords') }}
                </h3>

                <div class="term-list">
                    <a v-for="term in searched_terms" :href="'{{ route('shop.search.index') }}?term=' + term.slug">
                        @{{ term.name }}
                    </a>
                </div>
            </div>
        </div>
    </script>

    <script type="text/x-template" id="seach-component-template">
        
        <section class="search-container row category-container m-auto">
            
            @if (request('image-search'))
                <image-search-result-component></image-search-result-component>
            @endif

             <!-- Sandeep || Display an error message if the search term is less than 3 characters -->

             @if (!empty($errorMessage))
                   <div class="search_error_message text-center">
                    <h2 class="fw6 col-12 text-center" style="font-size:27px">We're Sorry, We Could Not Find  Any Results For "{{app('request')->input('term')}}"</h2>
                    <span class="col-12">{{ $errorMessage }}</span>
                    <div class="container category-page mb-5 pt-4">
                        <h3 class="pt-3 m-0 text-center" style="font-size: 24px;">Explore More from Our Menu Collection</h3>
                        <div class="mx-auto my-3" style="width: 60px; height: 2px; background-color: #9a7b4f;"></div>
                        <div class="row subcategories">
                            @foreach ($categories as $category)
                                <div class="col-sm-12 col-md-4 col-lg-4">
                                    <a href={{ $category->slug }}>
                                    <div class="card-block text-center mt-5">
                                            <span class="text-center">{{ $category->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                  </div>
             @elseif (!$results)
             <div>
                    <h2 class="fw6 col-12 text-center pt-3"  style="font-size:27px">No Results Found For  "{{app('request')->input('term')}}"</h2>
                    <p class="text-center" style="color: #666;">
                        We couldn't find what you were looking for. Please try another search term or browse our categories.
                    </p>
                    <div class="container category-page mb-5 pt-4">
                        <h3 class="pt-3 m-0 text-center" style="font-size: 24px;">Explore More from Our Menu Collection</h3>
                        <div class="mx-auto my-3" style="width: 60px; height: 2px; background-color: #9a7b4f;"></div>
                        <div class="row subcategories">
                            @foreach ($categories as $category)
                                <div class="col-sm-12 col-md-4 col-lg-4">
                                    <a href={{ $category->slug }}>
                                    <div class="card-block text-center mt-5">
                                            <span class="text-center">{{ $category->name }}</span>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
              @else
                @if ($results->isEmpty())
                <div class="no-result-found">$

                    <div>
                    <h2 class="fw6 col-12">{{ __('shop::app.products.whoops') }}</h2>
                    <span class="col-12">{{ __('shop::app.search.no-results') }}</span>
                    </div>
                </div>

                @else
                {{-- sandeep commnet code --}}
                    {{-- @if ($results->total() == 1)
                        <h5 class="fw6 col-12 mb20 text-center">
                            {{ $results->total() }} {{ __('shop::app.search.found-result') }}
                        </h5>
                    @else --}}
                   

            <div class="container listing-title-section breakfast-image-1 custom-image-background mb-3">
                <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="left-image search-left-image">
    
    
            <div class="centered-word  center-heading" > 
                <h1 style='font-size: 40px;' class='fw-15 f-5 col-12 mb20 p-0 m-auto pl-2'>{{ $results->total() }} {{ __('shop::app.search.found-results') }} for "{{app('request')->input('term')}}" </h1>
            </div>
            <img src="{{ asset('themes/velocity/assets/images/Food-Icon.png') }}" class="right-image search-right-image">
    
            </div>
            @if (
                $results
                && $results->count()
            )
                <div class="filters-container col-12" style="
                    margin-top: 20px;
                    padding-left: 0px !important;
                    padding-bottom: 10px !important;
                ">
                    {{-- @include ('shop::products.list.toolbar') --}}
                </div>

            @endif
                    {{-- @endif --}}
                    
                    <div id="search-item-list" class="search-item col-12">
                         
                    @foreach ($results as $productFlat)
                        @if ($toolbarHelper->getCurrentMode() == 'grid')
                            @include('shop::products.list.search-card', [
                                'cardClass' => 'category-product-image-container',
                                'product' => $productFlat->product,
                            ])
                           
                        @else
                            @include('shop::products.list.search-card', [
                                'list' => true,
                                'product' => $productFlat->product,
                            ])
                        @endif
                    @endforeach
                    @include ('shop::search.agemodel')
                    @include('ui::datagrid.pagination')
                    </div>
                    
                @endif
            @endif
        </section>

    </script>


    <script>
        Vue.component('search-component', {
            template: '#seach-component-template',
        });

        Vue.component('image-search-result-component', {
            template: '#image-search-result-component-template',

            data: function() {
                return {
                    searched_terms: [],
                    searchedImageUrl: localStorage.searchedImageUrl,
                }
            },

            created: function() {
                if (localStorage.searched_terms && localStorage.searched_terms != '') {
                    this.searched_terms = localStorage.searched_terms.split('_');

                    this.searched_terms = this.searched_terms.map(term => {
                        return {
                            name: term,
                            slug: term.split(' ').join('+'),
                        }
                    });
                }
            }
        });
    </script>
@endpush


