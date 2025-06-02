<div class="form-container review-checkout-conainer">
    <accordian :title="'<?php echo e(__('shop::app.checkout.onepage.summary')); ?>'" :active="true">
        <div class="form-header mb-30" slot="header">
            <h3 class="fw6 display-inbl">
                <?php echo e(__('shop::app.checkout.onepage.summary')); ?>

            </h3>
            <i class="rango-arrow"></i>
        </div>

        <div slot="body">
            <div class="address-summary row">
                <?php if($billingAddress = $cart->billing_address): ?>
                    <div class="billing-address col-lg-6 col-md-12">
                        <div class="card-title mb-20">
                            <b><?php echo e(__('shop::app.checkout.onepage.billing-address')); ?></b>
                        </div>

                        <div class="card-content">
                            <ul type="none">
                                <li>
                                    <?php echo e($billingAddress->company_name ?? ''); ?>

                                </li><br />
                                <li>
                                    <?php echo e($billingAddress->name); ?>

                                </li><br />
                                <li>
                                    <?php echo e($billingAddress->address1); ?>, <br />
                                </li><br />

                                <li>
                                    <?php echo e($billingAddress->postcode . " " . $billingAddress->city); ?>

                                </li><br />

                                <li>
                                    <?php echo e($billingAddress->state); ?>

                                </li><br />

                                <li>
                                    <?php echo e(core()->country_name($billingAddress->country)); ?>

                                </li><br />

                                <li>
                                    <?php echo e(__('shop::app.checkout.onepage.contact')); ?> : <?php echo e($billingAddress->phone); ?>

                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if(
                    $cart->haveStockableItems()
                    && $shippingAddress = $cart->shipping_address
                ): ?>
                    <div class="shipping-address col-6">
                        <div class="card-title mb-20">
                            <b><?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?></b>
                        </div>

                        <div class="card-content">
                            <ul>
                                <li>
                                    <?php echo e($shippingAddress->company_name ?? ''); ?>

                                </li><br/>
                                <li>
                                    <?php echo e($shippingAddress->name); ?>

                                </li><br/>
                                <li>
                                    <?php echo e($shippingAddress->address1); ?>,<br/>
                                </li><br/>

                                <li>
                                    <?php echo e($shippingAddress->postcode . " " . $shippingAddress->city); ?>

                                </li><br />

                                <li>
                                    <?php echo e($shippingAddress->state); ?>

                                </li><br />

                                <li>
                                    <?php echo e(core()->country_name($shippingAddress->country)); ?>

                                </li><br />

                                <li>
                                    <?php echo e(__('shop::app.checkout.onepage.contact')); ?> : <?php echo e($shippingAddress->phone); ?>

                                </li>
                            </ul>
                        </div>
                    </div>
                <?php endif; ?>

            </div>

            <div class="cart-item-list">
                <h4 class="fw6"><?php echo e(__('velocity::app.checkout.items')); ?></h4>

                <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $productBaseImage = $item->product->getTypeInstance()->getBaseImage($item);
                    ?>

                    <div class="row col-12 no-padding">
                        <div class="col-2 max-sm-img-dimension">
                            <img src="<?php echo e($productBaseImage['medium_image_url']); ?>" alt="" />
                        </div>

                        <div class="col-10 no-padding fs16">

                            <?php echo view_render_event('bagisto.shop.checkout.name.before', ['item' => $item]); ?>


                                <div class="row fs20">
                                    <span class="col-12 link-color fw6"><?php echo e($item->product->name); ?></span>
                                </div>

                            <?php echo view_render_event('bagisto.shop.checkout.name.after', ['item' => $item]); ?>


                            <div class="row col-12">
                                <?php echo view_render_event('bagisto.shop.checkout.price.before', ['item' => $item]); ?>

                                        <span class="value">
                                            
                                        </span>
                                <?php echo view_render_event('bagisto.shop.checkout.price.after', ['item' => $item]); ?>


                                <i class="rango-close text-down-4"></i>

                                <?php echo view_render_event('bagisto.shop.checkout.quantity.before', ['item' => $item]); ?>

                                    <span class="value">
                                        <?php echo e($item->quantity); ?> (<?php echo e(__('shop::app.checkout.onepage.quantity')); ?>)
                                    </span>
                                <?php echo view_render_event('bagisto.shop.checkout.quantity.after', ['item' => $item]); ?>

                            </div>

                            <div class="row col-12">
                                <b><?php echo e(core()->currency($item->base_total)); ?></b>
                            </div>

                            <?php echo view_render_event('bagisto.shop.checkout.options.before', ['item' => $item]); ?>


                                <?php if(isset($item->additional['attributes'])): ?>
                                    <div class="item-options">

                                        <?php $__currentLoopData = $item->additional['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <b><?php echo e($attribute['attribute_name']); ?> : </b><?php echo e($attribute['option_label']); ?></br>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    </div>
                                <?php endif; ?>

                            <?php echo view_render_event('bagisto.shop.checkout.options.after', ['item' => $item]); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="order-description row fs16 cart-details">
                <div class="col-lg-4 col-md-12">
                    <?php if($cart->haveStockableItems()): ?>
                        <div class="shipping mb20">
                            <div class="decorator">
                                <i class="icon shipping-icon"></i>
                            </div>

                            <div class="text">
                                <h4 class="fw6 fs18">
                                    
                                </h4>

                                <div class="info">
                                    
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="payment mb20">
                        <div class="decorator">
                            <i class="icon payment-icon"></i>
                        </div>

                        <div class="text">
                            <h4 class="fw6 fs18">
                                <?php echo e(core()->getConfigData('sales.paymentmethods.' . $cart->payment->method . '.title')); ?>

                            </h4>

                            <span><?php echo e(__('shop::app.customer.account.order.view.payment-method')); ?></span>
                        </div>
                    </div>

                    <slot name="place-order-btn"></slot>
                </div>

                <div class="col-lg-6 col-md-12 order-summary-container bottom pt0 offset-lg-2">
                    <slot name="summary-section"></slot>
                </div>
            </div>
        </div>
    </accordian>
</div>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/onepage/review.blade.php ENDPATH**/ ?>