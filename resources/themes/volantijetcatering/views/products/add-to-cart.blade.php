{!! view_render_event('bagisto.shop.products.add_to_cart.before', ['product' => $product]) !!}


{{-- removed--no-padding --}}
<div class="mt-4 mx-0"> 
    @if (
        isset($showCompare)
        && $showCompare
    )
        <compare-component
            @auth('customer')
                customer="true"
            @endif

            @guest('customer')
                customer="false"
            @endif

            slug="{{ $product->url_key }}"
            product-id="{{ $product->id }}"
            add-tooltip="{{ __('velocity::app.customer.compare.add-tooltip') }}"
        ></compare-component>
    @endif

    <div class="add-to-cart-btn pl0 add-to-cart-button-alignment single-product-page-quantity-button">
        @if (
            isset($form)
            && ! $form
        )
            <button
                type="submit"
                {{ ! $product->isSaleable() ? 'disabled' : '' }}
                class="theme-btn {{ $addToCartBtnClass ?? '' }} add__to__cart">

                @if (
                    ! (isset($showCartIcon)
                    && ! $showCartIcon)
                )
                    <i class="material-icons text-down-3">shopping_cart</i>
                @endif
                <img src="/../themes/volantijetcatering/assets/images/shopping-bag-icon.png" alt="">
                {{ __('shop::app.products.add-to-cart') }}
            </button>
        @elseif(isset($addToCartForm) && ! $addToCartForm)
            <form
                method="POST"
                action="{{ route('shop.cart.add', $product->id) }}">

                {{ csrf_field() }}

                <input type="hidden" name="product_id" value="{{ $product->id }}">
                <input type="hidden" name="quantity" value="1">
                <button
                    type="submit"
                    {{ ! $product->isSaleable() ? 'disabled' : '' }}
                    class="btn btn-add-to-cart {{ $addToCartBtnClass ?? '' }}">
                    @if (empty($showCartIcon))
                        <i class="material-icons text-down-3">shopping_cart</i>
                    @endif

                    <span class="fs14 fw6 text-uppercase text-up-4">
                        {{ ($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart') }}
                    </span>
                </button>
            </form>
        @else
            <add-to-cart
                form="true"
                csrf-token='{{ csrf_token() }}'
                product-id="{{ $product->id }}"
                reload-page="{{ $reloadPage ?? false }}"
                move-to-cart="{{ $moveToCart ?? false }}"
                wishlist-move-route="{{ $wishlistMoveRoute ?? false }}"
                add-class-to-btn="{{ $addToCartBtnClass ?? '' }}"
                is-enable={{ ! $product->isSaleable() ? 'false' : 'true' }}
                show-cart-icon={{ empty($showCartIcon) }}
                btn-text="{{ (! isset($moveToCart) && $product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart') }}">
            </add-to-cart>
        @endif
    </div>

        @if (
        ! (
            isset($showWishlist)
            && ! $showWishlist
        )
        && (bool) core()->getConfigData('general.content.shop.wishlist_option')
    )
        @include('shop::products.wishlist', [
            'addClass' => $addWishlistClass ?? '',
            'showText' => request()->routeIs([
                'velocity.product.compare',
                'velocity.product.details',
            ])
        ])
    @endif
</div>

{!! view_render_event('bagisto.shop.products.add_to_cart.after', ['product' => $product]) !!}