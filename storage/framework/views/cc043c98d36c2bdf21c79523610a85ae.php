<a class="mini-cart-btn">
    <mini-cart
        is-tax-inclusive="<?php echo e(Webkul\Tax\Helpers\Tax::isTaxInclusive()); ?>"
        view-cart-route="<?php echo e(route('shop.checkout.cart.index')); ?>"
        checkout-route="<?php echo e(route('shop.checkout.onepage.show_fbo_detail')); ?>"
        check-minimum-order-route="<?php echo e(route('shop.checkout.check_minimum_order')); ?>"
        cart-text="<?php echo e(__('shop::app.minicart.cart')); ?>"
        view-cart-text="<?php echo e(__('shop::app.minicart.view-cart')); ?>"
        checkout-text="<?php echo e(__('shop::app.minicart.checkout')); ?>"
        subtotal-text="<?php echo e(__('shop::app.checkout.cart.cart-subtotal')); ?>">
    </mini-cart>
</a><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/cart/mini-cart.blade.php ENDPATH**/ ?>