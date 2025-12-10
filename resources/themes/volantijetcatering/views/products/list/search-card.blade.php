@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('toolbarHelper', 'Webkul\Product\Helpers\Toolbar')

@php
    use Jenssegers\Agent\Agent;


    $list = $toolbarHelper->getCurrentMode() == 'list' ? true : false;

    $productBaseImage = product_image()->getProductBaseImage($product);

    $totalReviews = $reviewHelper->getTotalReviews($product);

    $avgRatings = ceil($reviewHelper->getAverageRating($product));

    $agent = new Agent();
    $isDesktop = $agent->isDesktop();
    $isTablet = $agent->isTablet();


@endphp

{!! view_render_event('bagisto.shop.products.list.card.before', ['product' => $product]) !!}
@if (!empty($list))

    <div class="col-12 lg-card-container list-card product-card row">
        <div class="product-image">
            <a title="{{ $product->name }}" href="{{$product->url_key }}">
                <img src="{{ $productBaseImage['medium_image_url'] }}"
                    :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`"
                    alt=""/>

                <div class="quick-view-in-list">
                    <product-quick-view-btn
                        :quick-view-details="{{ json_encode($velocityHelper->formatProduct($product)) }}"></product-quick-view-btn>
                </div>
            </a>
        </div>
        <div class="product-information">
            <div>
                <div class="product-name">
                    <a href="{{$product->url_key }}" title="{{ $product->name }}"
                        class="unset">

                        <span class="fs16">{{ $product->name }}</span>
                    </a>

                    @if (!empty($additionalAttributes))
                        @if (isset($item->additional['attributes']))
                            <div class="item-options">

                                @foreach ($item->additional['attributes'] as $attribute)
                                    <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                @endforeach

                            </div>
                        @endif
                    @endif
                </div>

                <div class="product-price">
                    @include ('shop::products.price', ['product' => $product])
                </div>

                @if ($totalReviews)
                    <div class="product-rating">
                        <star-ratings ratings="{{ $avgRatings }}"></star-ratings>

                        <span>{{ $totalReviews }} Ratings</span>
                    </div>
                @endif

                <div class="cart-wish-wrap mt5">
                    @include ('shop::products.add-to-cart', [
                        'addWishlistClass' => 'pl10',
                        'product' => $product,
                        'addToCartBtnClass' => 'medium-padding',
                        'showCompare' => (bool) core()->getConfigData('general.content.shop.compare_option'),
                    ])
                </div>
            </div>
        </div>
    </div>
@else
    {{-- <div class="container"> --}}
    
        <div class="card grid-card product-card-new search-product-card col-12 m-0">

            <div class="row my-4 ml-0 @if (($isDesktop || $isTablet) || (!$isDesktop && !$isTablet && $product['type'] != 'simple')) align-items-center @endif">
                
                {{-- sandeep delete code  --}}
            {{-- <div class="product-content">
                <a href="{{$product->url_key }}" title="{{ $product->name }}" --}}
                    {{-- class="{{ $cardClass ?? 'product-image-container' }}" --}}
                    {{-- class="product-image-container" --}}
                    {{-- > --}}
    
                    {{-- <img loading="lazy" class="card-img-top" alt="{{ $product->name }}"
                        src="{{ $productBaseImage['large_image_url'] }}" class="card-img-top lzy_img"
                        :onerror="`this.src='${this.$root.baseUrl}/vendor/webkul/ui/assets/images/product/large-product-placeholder.png'`" />
     --}}
                    {{-- <product-quick-view-btn :quick-view-details="{{ json_encode($velocityHelper->formatProduct($product)) }}"></product-quick-view-btn> --}}
                {{-- </a>
            </div> --}}
            {{-- @if (!$product->getTypeInstance()->haveDiscount() && $product->new)
                <div class="sticker new">
                    {{ __('shop::app.products.new') }}
                </div>
            @endif --}}
    
            <div class="card-body col-10 p-md-0 p-lg-0 mt-lg-3 mt-md-3">
                <div class="product-name no-padding search-product-name w-100">
                        <span class="fs16 my-1">{{ $product->name }}</span>
                        {{-- @if (!empty($additionalAttributes))
                            @if (isset($item->additional['attributes']))
                                <div class="item-options">
    
                                    @foreach ($item->additional['attributes'] as $attribute)
                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                    @endforeach
    
                                </div>
                            @endif
                        @endif --}}
                 
                    <br>
                    <p class="text-left">{{ $product->description }}</p>
                    
                    @if ($isDesktop || $isTablet)
                    @if ($product->isSaleable() && $product['type'] == 'simple')
                    <a id="category_instructions" data-toggle="collapse" class="m-0"
                    href="#category_instructions_Div{{ $product['id'] }}" role="button" aria-expanded="true"
                    aria-controls="category_instructions_Div">Special Instructions
                    (optional)
                    <span class="toggle-icon">-</span></a>
                <div class="collapse multi-collapse category_instructions_Div mt-3 mb-3 show in" id="category_instructions_Div{{ $product['id'] }}">
                    <div id="category_instructions_Div" class="">
                        <textarea id="textarea-customize" name="special_instruction" class="p-2"  placeholder="Add your special instructions here..."></textarea>          
                    </div>
                </div>
                    @endif
                    @endif
                </div>
                {{-- sandeep delete code --}}
                {{-- <div class="search-plus-img"> --}}
                    {{-- <a href="{{$product->url_key }}">
                        <img class="plus-img" src="./../themes/volantijetcatering/assets/images/plus.png" alt="" />
                    </a> --}}
                {{-- </div> --}}
    
                {{-- <div class="product-price fs16">
                    @include ('shop::products.price', ['product' => $product])
                </div> --}}
    
                {{-- @if ($totalReviews)
                    <div class="product-rating col-12 no-padding">
                        <star-ratings ratings="{{ $avgRatings }}"></star-ratings>
                        <span class="align-top">
                            {{ __('velocity::app.products.ratings', ['totalRatings' => $totalReviews]) }}
                        </span>
                    </div>
                @else
                    <div class="product-rating col-12 no-padding">
                        <span class="fs14">{{ __('velocity::app.products.be-first-review') }}</span>
                    </div>
                @endif --}}
    
                {{-- <div class="cart-wish-wrap no-padding ml0">
                        @include ('shop::products.add-to-cart', [
                            'product'           => $product,
                            'btnText'           => $btnText ?? null,
                            'moveToCart'        => $moveToCart ?? null,
                            'wishlistMoveRoute' => $wishlistMoveRoute ?? null,
                            'reloadPage'        => $reloadPage ?? null,
                            'addToCartForm'     => $addToCartForm ?? false,
                            'addToCartBtnClass' => $addToCartBtnClass ?? '',
                            'showCompare'       => (bool) core()->getConfigData('general.content.shop.compare_option'),
                        ])
                    </div> --}}
            </div>
{{-- 
            <div class="AddToCartButton col-2 m-auto ">
                <div class="AddButton text-center">
                    <button type="submit" class="add_button" id="AddToCartButton">Add</button>
                </div>
            </div> --}}



            <div class="AddToCartButton col-2 my-4 p-md-0 p-lg-0" id="AddToCartButton_searchpage">
                <input type="hidden" name="product_id" value="{{ $product['id'] }}" id="ProductId">
                @if ($product->isSaleable())
                    @if ($product['type'] == 'simple')
                    <quantity-changer
                    :product-id="{{ $product['id'] }}"
                    :quantity-id="'quantity_' + {{ $product['id'] }}"
                    quantity-text="{{ __('shop::app.products.quantity') }}">
                  </quantity-changer>
                  
                     <span id="quantityError_{{ $product['id'] }}_{{$product['category_id']}}" class="text-danger" style="color: red"></span>

                        <div class="AddButton text-center mt-2">
                             <button 
                                type="submit" 
                                class="add_button" 
                                id="AddToCartButton" 
                                data-category-slug="{{ $product->category_names }}" 
                                data="{{ $product['type'] }}" 
                                attr="{{ $product['category_id'] }}"
                            >
                                Add
                            </button>
                            <span id="successMessage_{{ $product['id'] }}_{{$product['category_id']}}" class="text-success successMessage"></span>
                        </div>
                    @else
                        <div class="configurable_product">
                            <div class="AddButton text-center">
                                <input type="hidden" id="slug" value="{{$product['url_key']}}">
                                <button type="button" data-toggle="modal" data-target="#exampleModal{{ $product['id']}}_{{$product['category_id']}}" class="OptionsAddButton"
                                    id="AddToCartButtonpopup" data-category-slug="{{ $product->category_names }}" >Add</button>
                                <span class="customisable">Customisable</span>
                                <br>
                                {{-- @dd($cate_id); --}}
                                <span id="successMessage_{{ $product['id'] }}_{{$product['category_id']}}" class="text-success successMessage" style="display: none;"></span>
                                
                            </div>
                            <!-- Modal -->
                            <div class="modal custom_modal fade p-0" id="exampleModal{{ $product['id']}}_{{$product['category_id']}}" data="{{ $product['id']}}" tabindex="-1" role="dialog"
                                aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog  modal-dialog-centered " role="document">
                                    <div class="modal-content pb-3">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add To Cart</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <span class="fs16 ProductName" id = "ProductName">{{ $product['name'] }}</span>
                                            <br />
                                            <p class="description">{{ $product['description'] }}</p>
                                            <quantity-changer
                                            :product-id="{{ $product['id'] }}"
                                            :quantity-id="'quantity_' + {{ $product['id'] }}"
                                            quantity-text="{{ __('shop::app.products.quantity') }}">
                                          </quantity-changer>
                                          
                                            {{-- <quantity-changer quantity-text="{{ __('shop::app.products.quantity') }}"></quantity-changer> --}}
                                            <span id="quantityError_{{ $product['id'] }}_{{$product['category_id']}}" class="text-danger" style="color: red"></span>
                                            <div class="variant__option"></div>
                                        </div>

                                        
                                            <button type="submit" class="add_button OptionsAddButton m-auto" data="{{$product['type']}}" id="Add_Button_Popop" attr="{{$product['category_id']}}">Add</button>
                                            {{-- <span id="successMessage" class="text-success" style="display: none;"></span> --}}

                                    </div>
                                </div> 
                            </div>
                        </div>
                    @endif
                @else
                    <div class="AddButton text-center">
                        <button type="submit" class="stockoutButton" disabled>Out of stock</button>
                    </div>
                @endif

            </div>
            @if (!$isDesktop && !$isTablet)
            @if ($product->isSaleable() && $product['type'] == 'simple')
            <div class="category-instructions-div col-12 p-md-0 p-lg-0">
            <a id="category_instructions" data-toggle="collapse" class="m-0"
            href="#category_instructions_Div{{ $product['id'] }}" role="button" aria-expanded="true"
            aria-controls="category_instructions_Div">Special Instructions
            (optional)
            <span class="toggle-icon">-</span></a>
        <div class="collapse multi-collapse category_instructions_Div mt-3 mb-3 show in" id="category_instructions_Div{{ $product['id'] }}">
            <div id="category_instructions_Div" class="">
                <textarea id="textarea-customize" name="special_instruction" class="p-2"  placeholder="Add your special instructions here..."></textarea>          
            </div>
        </div>
        </div>
            @endif
            @endif
        </div>
        </div>
    {{-- </div> --}}
@endif

{!! view_render_event('bagisto.shop.products.list.card.after', ['product' => $product]) !!}
