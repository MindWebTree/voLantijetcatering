@php
    use Jenssegers\Agent\Agent;

    $agent = new Agent();
    $isDesktop = $agent->isDesktop();
    $isTablet = $agent->isTablet();
    
@endphp


@foreach ($getCategorydetail['products'] as $product)

    <div class="container product-card-new product-custom-class product-item"
        data-name="{{ strtolower($product['name']) }}">
        {{-- @include('ui::datagrid.pagination') --}}
        <div class="row my-4 ml-0 @if (($isDesktop || $isTablet) || (!$isDesktop && !$isTablet && $product['type'] != 'simple')) align-items-center @endif"> 
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="col-10 p-md-0 p-lg-0 pr-0">
                <div class="product-name no-padding custom-product-name">
                    <span class="fs16" id = "ProductName">{{ $product['name'] }}</span>
                    <br />
                    <p>{{ $product['description'] }}</p>
                    @if ($isDesktop || $isTablet)
                    @if ($product['isSaleable'])
                    @if ($product['type'] == 'simple')
                    <a id="category_instructions" data-toggle="collapse" class="m-0"
                        href="#category_instructions_Div{{ $product['id'] }}" role="button" aria-expanded="true"
                        aria-controls="category_instructions_Div">Special Instructions
                        (optional)
                        <span class="toggle-icon">-</span></a>
                    <div class="collapse multi-collapse mt-2 mb-2 show in" id="category_instructions_Div{{ $product['id'] }}">
                        <div id="category_instructions_Div" class="">
                            <textarea id="textarea-customize" name="special_instruction" placeholder="Add your special instructions here..."></textarea>
                        </div>
                    </div>
                    @endif
                    @endif
                    @endif
                </div>
            </div>

            <div class="AddToCartButton col-2 mt-0 mt-lg-2 mt-md-2 p-md-0 p-lg-0 pt-2">
                <input type="hidden" name="product_id" value="{{ $product['id'] }}" id="ProductId">
                @if ($product['isSaleable'])
                    @if ($product['type'] == 'simple')
                        <quantity-changer :product-id="{{ $product['id'] }}"
                            :quantity-id="'quantity_' + {{ $product['id'] }}"
                            quantity-text="{{ __('shop::app.products.quantity') }}">
                        </quantity-changer> 
                        <div id="quantityError_{{ $product['id'] }}_{{ $cate_id }}" class="text-danger quantityError_message"
                            style="color: red"></div>

                        <div class="AddButton text-center">
                            <button type="submit" class="add_button" id="AddToCartButton"
                                data="{{ $product['type'] }}" attr="{{ $cate_id }}">Add</button>
                            <span id="successMessage_{{ $product['id'] }}_{{ $cate_id }}"
                                class="text-success successMessage"></span>
                        </div>
                    @else
                        <div class="configurable_product">
                            <div class="AddButton text-center">
                                <input type="hidden" id="slug" value="{{ $product['slug'] }}">
                                <button type="button" data-toggle="modal"
                                    data-target="#exampleModal{{ $product['id'] }}_{{ $cate_id }}"
                                    class="OptionsAddButton" id="AddToCartButtonpopup">Add</button>
                                <span class="customisable">Customizable</span>
                                <br>
                                <span id="successMessage_{{ $product['id'] }}_{{ $cate_id }}"
                                    class="text-success successMessage"></span>

                            </div>  
                            <!-- Modal -->
                            <div class="modal custom_modal fade"
                                id="exampleModal{{ $product['id'] }}_{{ $cate_id }}" data="{{ $product['id'] }}" tabindex="-1"
                                role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog  modal-dialog-centered " role="document">
                                    <div class="modal-content pb-3">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Add To Cart</h5>
                                            <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body p-3">
                                            <span class="fs16 ProductName"
                                                id = "ProductName">{{ $product['name'] }}</span>
                                            <br />
                                            <p class="description">{{ $product['description'] }}</p>
                                            <quantity-changer :product-id="{{ $product['id'] }}"
                                                :quantity-id="'quantity_' + {{ $product['id'] }}"
                                                quantity-text="{{ __('shop::app.products.quantity') }}">
                                            </quantity-changer>
                                            {{-- <quantity-changer quantity-text="{{ __('shop::app.products.quantity') }}"></quantity-changer> --}}
                                            <div id="quantityError_{{ $product['id'] }}_{{ $cate_id }}"
                                                class="text-danger quantityError_message" style="color: red"></div>
                                            <div class="variant__option"></div>
                                        </div>


                                        <button type="submit" class="add_button mx-auto"
                                            data="{{ $product['type'] }}" id="Add_Button_Popop"
                                            attr="{{ $cate_id }}">Add</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="AddButton text-center p-md-0 p-lg-0">
                        <button type="submit" class="stockoutButton" disabled>Out of stock</button>
                    </div>
                @endif  

            </div>
            @if (!$isDesktop && !$isTablet)
            @if ($product['isSaleable'])
            @if ($product['type'] == 'simple')
            <div class="category-instructions-div col-12 p-md-0 p-lg-0">
                <a id="category_instructions" data-toggle="collapse" class="m-0"
                    href="#category_instructions_Div{{ $product['id'] }}" role="button"
                    aria-expanded="false" aria-controls="category_instructions_Div">Special Instructions
                    (optional)
                    <span class="toggle-icon">-</span></a>
                <div class="collapse multi-collapse mb-2 mt-2 show in"
                    id="category_instructions_Div{{ $product['id'] }}">
                    <div id="category_instructions_Div" class="">
                        <textarea id="textarea-customize" name="special_instruction" placeholder="Add your special instructions here..."></textarea>
                    </div>
                </div>
            </div>
            @endif
        @endif
        @endif
        </div>

    </div>
@endforeach

<div class="pagination-wrapper">
    {!! $getCategorydetail['paginationHTML'] !!}
</div>
