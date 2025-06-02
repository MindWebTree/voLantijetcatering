<?php echo view_render_event('bagisto.shop.products.add_to_cart.before', ['product' => $product]); ?>




<div class="mt-4 mx-0"> 
    <?php if(
        isset($showCompare)
        && $showCompare
    ): ?>
        <compare-component
            <?php if(auth()->guard('customer')->check()): ?>
                customer="true"
            <?php endif; ?>

            <?php if(auth()->guard('customer')->guest()): ?>
                customer="false"
            <?php endif; ?>

            slug="<?php echo e($product->url_key); ?>"
            product-id="<?php echo e($product->id); ?>"
            add-tooltip="<?php echo e(__('velocity::app.customer.compare.add-tooltip')); ?>"
        ></compare-component>
    <?php endif; ?>

    <div class="add-to-cart-btn pl0 add-to-cart-button-alignment single-product-page-quantity-button">
        <?php if(
            isset($form)
            && ! $form
        ): ?>
            <button
                type="submit"
                <?php echo e(! $product->isSaleable() ? 'disabled' : ''); ?>

                class="theme-btn <?php echo e($addToCartBtnClass ?? ''); ?> add__to__cart">

                <?php if(
                    ! (isset($showCartIcon)
                    && ! $showCartIcon)
                ): ?>
                    <i class="material-icons text-down-3">shopping_cart</i>
                <?php endif; ?>
                <img src="/../themes/volantijetcatering/assets/images/shopping-bag-icon.png" alt="">
                <?php echo e(__('shop::app.products.add-to-cart')); ?>

            </button>
        <?php elseif(isset($addToCartForm) && ! $addToCartForm): ?>
            <form
                method="POST"
                action="<?php echo e(route('shop.cart.add', $product->id)); ?>">

                <?php echo e(csrf_field()); ?>


                <input type="hidden" name="product_id" value="<?php echo e($product->id); ?>">
                <input type="hidden" name="quantity" value="1">
                <button
                    type="submit"
                    <?php echo e(! $product->isSaleable() ? 'disabled' : ''); ?>

                    class="btn btn-add-to-cart <?php echo e($addToCartBtnClass ?? ''); ?>">
                    <?php if(empty($showCartIcon)): ?>
                        <i class="material-icons text-down-3">shopping_cart</i>
                    <?php endif; ?>

                    <span class="fs14 fw6 text-uppercase text-up-4">
                        <?php echo e(($product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart')); ?>

                    </span>
                </button>
            </form>
        <?php else: ?>
            <add-to-cart
                form="true"
                csrf-token='<?php echo e(csrf_token()); ?>'
                product-id="<?php echo e($product->id); ?>"
                reload-page="<?php echo e($reloadPage ?? false); ?>"
                move-to-cart="<?php echo e($moveToCart ?? false); ?>"
                wishlist-move-route="<?php echo e($wishlistMoveRoute ?? false); ?>"
                add-class-to-btn="<?php echo e($addToCartBtnClass ?? ''); ?>"
                is-enable=<?php echo e(! $product->isSaleable() ? 'false' : 'true'); ?>

                show-cart-icon=<?php echo e(empty($showCartIcon)); ?>

                btn-text="<?php echo e((! isset($moveToCart) && $product->type == 'booking') ?  __('shop::app.products.book-now') : $btnText ?? __('shop::app.products.add-to-cart')); ?>">
            </add-to-cart>
        <?php endif; ?>
    </div>

        <?php if(
        ! (
            isset($showWishlist)
            && ! $showWishlist
        )
        && (bool) core()->getConfigData('general.content.shop.wishlist_option')
    ): ?>
        <?php echo $__env->make('shop::products.wishlist', [
            'addClass' => $addWishlistClass ?? '',
            'showText' => request()->routeIs([
                'velocity.product.compare',
                'velocity.product.details',
            ])
        ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php endif; ?>
</div>

<?php echo view_render_event('bagisto.shop.products.add_to_cart.after', ['product' => $product]); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/products/add-to-cart.blade.php ENDPATH**/ ?>