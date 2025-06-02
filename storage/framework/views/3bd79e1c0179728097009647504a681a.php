<?php
    $cart = cart()->getCart();
?>

<?php if($cart): ?>
    <?php
        $items = $cart->items;
    ?>

    <div class="dropdown-toggle">
        <a class="cart-link" href="<?php echo e(route('shop.checkout.cart.index')); ?>">
            <span class="icon cart-icon"></span>
        </a>

        <span class="name">
            <?php echo e(__('shop::app.header.cart')); ?>

            <span class="count"> (<?php echo e($cart->items->count()); ?>)</span>
        </span>

        <i class="icon arrow-down-icon"></i>
    </div>

    <div class="dropdown-list" style="display: none; top: 52px; right: 0px;">
        <div class="dropdown-container">
            <div class="dropdown-cart">
                <div class="dropdown-header">
                    <p class="heading">
                        <?php echo e(__('shop::app.checkout.cart.cart-subtotal')); ?> -

                        <?php echo view_render_event('bagisto.shop.checkout.cart-mini.subtotal.before', ['cart' => $cart]); ?>


                        <?php if(Webkul\Tax\Helpers\Tax::isTaxInclusive()): ?>
                            <b><?php echo e(core()->currency($cart->base_grand_total)); ?></b>
                        <?php else: ?>
                            <b><?php echo e(core()->currency($cart->base_sub_total)); ?></b>
                        <?php endif; ?>

                        <?php echo view_render_event('bagisto.shop.checkout.cart-mini.subtotal.after', ['cart' => $cart]); ?>

                    </p>
                </div>

                <div class="dropdown-content">
                    <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="item">
                            <div class="item-image" >
                                <?php
                                    $images = $item->product->getTypeInstance()->getBaseImage($item);
                                ?>

                                <a href="<?php echo e(route('shop.productOrCategory.index', $item->product->url_key)); ?>" title="<?php echo e($item->name); ?>">
                                    <img src="<?php echo e($images['small_image_url']); ?>"  alt=""/>
                                </a>
                            </div>

                            <div class="item-details">
                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.name.before', ['item' => $item]); ?>


                                <div class="item-name">
                                    <a href="<?php echo e(route('shop.productOrCategory.index', $item->product->url_key)); ?>" title="<?php echo e($item->name); ?>">
                                        <?php echo e($item->name); ?>

                                    </a>
                                </div>

                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.name.after', ['item' => $item]); ?>


                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.options.before', ['item' => $item]); ?>


                                <?php if(isset($item->additional['attributes'])): ?>
                                    <div class="item-options">
                                        <?php $__currentLoopData = $item->additional['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <b><?php echo e($attribute['attribute_name']); ?> : </b><?php echo e($attribute['option_label']); ?></br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                <?php endif; ?>

                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.options.after', ['item' => $item]); ?>


                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.price.before', ['item' => $item]); ?>


                                <div class="item-price">
                                    <?php if(Webkul\Tax\Helpers\Tax::isTaxInclusive()): ?>
                                        <b><?php echo e(core()->currency($item->base_total + $item->tax_amount)); ?></b>
                                    <?php else: ?>
                                        <b><?php echo e(core()->currency($item->base_total)); ?></b>
                                    <?php endif; ?>
                                </div>

                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.price.after', ['item' => $item]); ?>


                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.before', ['item' => $item]); ?>


                                <div class="item-qty">Quantity : <?php echo e($item->quantity); ?></div>

                                <?php echo view_render_event('bagisto.shop.checkout.cart-mini.item.quantity.after', ['item' => $item]); ?>


                                <div class="item-remove">
                                    <a href="<?php echo e(route('shop.checkout.cart.remove', $item->id)); ?>" onclick="removeLink('<?php echo e(__('shop::app.checkout.cart.cart-remove-action')); ?>')"><?php echo e(__('shop::app.checkout.cart.remove-link')); ?></a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="dropdown-footer">
                    <a href="<?php echo e(route('shop.checkout.cart.index')); ?>"><?php echo e(__('shop::app.minicart.view-cart')); ?></a>

                    <?php
                        $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;
                    ?>

                    <proceed-to-checkout
                        href="<?php echo e(route('shop.checkout.onepage.index')); ?>"
                        add-class="btn btn-primary btn-lg"
                        text="<?php echo e(__('shop::app.minicart.checkout')); ?>"
                        is-minimum-order-completed="<?php echo e($cart->checkMinimumOrder()); ?>"
                        minimum-order-message="<?php echo e(__('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)])); ?>"
                        style="color: white;">
                    </proceed-to-checkout>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="dropdown-toggle">
        <div style="display: inline-block; cursor: not-allowed">
            <span class="icon cart-icon"></span>
            <span class="name"><?php echo e(__('shop::app.minicart.cart')); ?><span class="count"> (<?php echo e(__('shop::app.minicart.zero')); ?>) </span></span>
        </div>
    </div>
<?php endif; ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/checkout/cart/mini-cart.blade.php ENDPATH**/ ?>