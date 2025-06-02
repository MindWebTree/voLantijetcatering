<form data-vv-scope="shipping-form" class="shipping-form">
    <div class="form-container">
        <accordian :title="'<?php echo e(__('shop::app.checkout.onepage.shipping-method')); ?>'" :active="true">
            <div class="form-header" slot="header">
                <h3 class="fw6 display-inbl">
                    <?php echo e(__('shop::app.checkout.onepage.shipping-method')); ?>

                </h3>
                <i class="rango-arrow"></i>
            </div>

            <div :class="`shipping-methods ${errors.has('shipping-form.shipping_method') ? 'has-error' : ''}`"
                slot="body">

                <?php $__currentLoopData = $shippingRateGroups; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rateGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo view_render_event('bagisto.shop.checkout.shipping-method.before', ['rateGroup' => $rateGroup]); ?>

                    <?php $__currentLoopData = $rateGroup['rates']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rate): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="row col-12">
                            <div class="radio">
                                <input type="radio" v-validate="'required'" name="shipping_method"
                                    id="<?php echo e($rate->method); ?>" value="<?php echo e($rate->method); ?>" @change="methodSelected()"
                                    v-model="selected_shipping_method"
                                    data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.shipping-method')); ?>&quot;" />

                                <label for="<?php echo e($rate->method); ?>" class="radio-view"></label>
                            </div>

                            <div class="pl20">
                                <div class="row">
                                    <b><?php echo e(core()->currency($rate->base_price)); ?></b>
                                </div>

                                <div class="row">
                                    <b><?php echo e($rate->method_title); ?></b> - <?php echo e(__($rate->method_description)); ?>

                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                    <?php echo view_render_event('bagisto.shop.checkout.shipping-method.after', ['rateGroup' => $rateGroup]); ?>

                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <span class="control-error" v-if="errors.has('shipping-form.shipping_method')"
                    v-text="errors.first('shipping-form.shipping_method')">
                </span>
            </div>
        </accordian>
    </div>
</form>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/onepage/shipping.blade.php ENDPATH**/ ?>