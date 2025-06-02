<div class="order-summary fs16">
    <h3 class="fw6"><?php echo e(__('velocity::app.checkout.cart.cart-summary')); ?></h3>

    <div class="row">
        <span class="col-8"><?php echo e(__('velocity::app.checkout.sub-total')); ?></span>
        <span class="col-4 text-right"><?php echo e(core()->currency($cart->base_sub_total)); ?></span>
    </div>

    <?php if($cart->selected_shipping_rate): ?>
        <div class="row">
            <span class="col-8"><?php echo e(__('shop::app.checkout.total.delivery-charges')); ?></span>
            <span class="col-4 text-right"><?php echo e(core()->currency($cart->selected_shipping_rate->base_price)); ?></span>
        </div>
    <?php endif; ?>

    <?php if($cart->base_tax_total): ?>
        <?php $__currentLoopData = Webkul\Tax\Helpers\Tax::getTaxRatesWithAmount($cart, true); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $taxRate => $baseTaxAmount): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="row">
                <span class="col-8" id="taxrate-<?php echo e(core()->taxRateAsIdentifier($taxRate)); ?>"><?php echo e(__('shop::app.checkout.total.tax')); ?> <?php echo e($taxRate); ?> %</span>
                <span class="col-4 text-right" id="basetaxamount-<?php echo e(core()->taxRateAsIdentifier($taxRate)); ?>"><?php echo e(core()->currency($baseTaxAmount)); ?></span>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>

    <?php if(
        $cart->base_discount_amount
        && $cart->base_discount_amount > 0
    ): ?>
        <div
            id="discount-detail"
            class="row">

            <span class="col-8"><?php echo e(__('shop::app.checkout.total.disc-amount')); ?></span>
            <span class="col-4 text-right">
                -<?php echo e(core()->currency($cart->base_discount_amount)); ?>

            </span>
        </div>
    <?php endif; ?>

    <div class="payable-amount row" id="grand-total-detail">
        <span class="col-8"><?php echo e(__('shop::app.checkout.total.grand-total')); ?></span>
        <span class="col-4 text-right fw6" id="grand-total-amount-detail">
            <?php echo e(core()->currency($cart->base_grand_total)); ?>

        </span>
    </div>

    <div class="row">
        <?php
            $minimumOrderAmount = (float) core()->getConfigData('sales.orderSettings.minimum-order.minimum_order_amount') ?? 0;
        ?>

        <?php if(Cart::hasError()): ?>
            <button class="theme-btn text-uppercase col-12 remove-decoration fw6 text-center" disabled>
                <?php echo e(__('velocity::app.checkout.proceed')); ?>

            </button>
        <?php else: ?>
            <proceed-to-checkout
                href="<?php echo e(route('shop.checkout.onepage.show_fbo_detail')); ?>"
                add-class="theme-btn text-uppercase col-12 remove-decoration fw6 text-center"
                text="<?php echo e(__('velocity::app.checkout.proceed')); ?>"
                is-minimum-order-completed="<?php echo e($cart->checkMinimumOrder()); ?>"
                minimum-order-message="<?php echo e(__('shop::app.checkout.cart.minimum-order-message', ['amount' => core()->currency($minimumOrderAmount)])); ?>">
            </proceed-to-checkout>
        <?php endif; ?>
    </div>
</div><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/total/summary.blade.php ENDPATH**/ ?>