<?php $reviewHelper = app('Webkul\Product\Helpers\Review'); ?>



<?php $__env->startSection('page_title'); ?>
    
    Shopping Cart | Volanti Jet Catering
<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<meta name="title" content="Shopping Cart | Volanti Jet Catering" />
<meta name="description" content="Shopping Cart | Volanti Jet Catering" />
<meta name="keywords" content="" />
<link rel="canonical" href="<?php echo e(url()->current()); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    <cart-component></cart-component>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('css'); ?>
    <style type="text/css">
        @media only screen and (max-width: 600px) {
            .rango-delete {
                margin-left: -10px !important;
            }
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
    <?php echo $__env->make('shop::checkout.cart.coupon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script type="text/x-template" id="cart-template">
        <div class="container">
            <section class="cart-details row no-margin col-12">
                <?php if($cart): ?>
                    <h2 class="fw6 col-12"><?php echo e(__('shop::app.checkout.cart.title')); ?></h2>
                <?php endif; ?>
                    <?php echo e(''); ?>

                <?php if($cart): ?>
                    <div class="cart-details-header col-lg-7 col-md-12">
                        <div class="row cart-header col-12 no-padding">
                            <span class="col-8 fw6 fs16 pr0">
                                <?php echo e(__('velocity::app.checkout.items')); ?>

                            </span>

                            <span class="col-2 fw6 fs16 no-padding text-right">
                                <?php echo e(__('velocity::app.checkout.qty')); ?>

                            </span>

                            <span class="col-2 fw6 fs16 text-right pr0">
                                <?php echo e(__('velocity::app.checkout.subtotal')); ?>

                            </span>
                        </div>

                        <div class="cart-content col-12">
                            <form
                                method="POST"
                                @submit.prevent="onSubmit"
                                action="<?php echo e(route('shop.checkout.cart.update')); ?>">

                                <div class="cart-item-list">
                                    <?php echo csrf_field(); ?>

                                    <?php
                                        $showUpdateCartButton = false;
                                    ?>

                                    <?php $__currentLoopData = $cart->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php
                                            $productBaseImage = $item->product->getTypeInstance()->getBaseImage($item);

                                            $product = $item->product;

                                            $productPrice = $product->getTypeInstance()->getProductPrices();

                                            if ($product->getTypeInstance()->showQuantityBox()) {
                                                $showUpdateCartButton = true;
                                            }
                                                
                                            if (is_null ($product->url_key)) {
                                                if (! is_null($product->parent)) {
                                                    $url_key = $product->parent->url_key;
                                                }
                                            } else {
                                                $url_key = $product->url_key;
                                            }
                                        ?>

                                        <div class="row col-12">
                                            
                                            

                                            <div class="product-details-content col-6 pr0">
                                                <div class="row item-title no-margin">
                                                    <a
                                                        href="<?php echo e(route('shop.productOrCategory.index', $url_key)); ?>"
                                                        title="<?php echo e($product->name); ?>"
                                                        class="unset col-12 no-padding">

                                                        <span class="fs20 fw6 link-color"><?php echo e($product->name); ?></span>
                                                    </a>
                                                </div>

                                                <?php if(isset($item->additional['attributes'])): ?>
                                                    <?php $__currentLoopData = $item->additional['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        <div class="row col-12 no-padding no-margin display-block item-attribute">
                                                            <label class="no-margin">
                                                                <?php echo e($attribute['attribute_name']); ?>:
                                                            </label>
                                                            <span>
                                                                <?php echo e($attribute['option_label']); ?>

                                                            </span>
                                                        </div>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                <?php endif; ?>

                                                <div class="row col-12 no-padding no-margin item-price">
                                                    <div class="product-price">
                                                        <span><?php echo e(core()->currency($item->base_price)); ?></span>
                                                    </div>
                                                </div>

                                                <?php
                                                    $moveToWishlist = trans('shop::app.checkout.cart.move-to-wishlist');
                                                ?>

                                                <div class="no-padding cursor-pointer fs16 item-actions">
                                                    <?php if(auth()->guard('customer')->check()): ?>
                                                       <?php if((bool) core()->getConfigData('general.content.shop.wishlist_option')): ?>
                                                            <?php if(
                                                                $item->parent_id != 'null'
                                                                || $item->parent_id != null
                                                            ): ?> 
                                                                <div class="d-inline-block">
                                                                    <?php echo $__env->make('shop::products.wishlist', [
                                                                        'route' => route('shop.move_to_wishlist', $item->id),
                                                                        'text' => "<span class='align-vertical-super'>$moveToWishlist</span>"
                                                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                                </div>
                                                            <?php else: ?>
                                                                <div class="d-inline-block">
                                                                    <?php echo $__env->make('shop::products.wishlist', [
                                                                        'route' => route('shop.move_to_wishlist', $item->child->id),
                                                                        'text' => "<span class='align-vertical-super'>$moveToWishlist</span>"
                                                                    ], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                                                                </div>
                                                            <?php endif; ?>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <div class="d-inline-block">
                                                        <a
                                                            class="unset"
                                                            href="<?php echo e(route('shop.checkout.cart.remove', ['id' => $item->id])); ?>"
                                                            @click="removeLink('<?php echo e(__('shop::app.checkout.cart.cart-remove-action')); ?>')">

                                                            <span class="rango-delete mt-0 fs24"></span>
                                                            <span class="align-vertical-super"><?php echo e(__('shop::app.checkout.cart.remove')); ?></span>
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="row col-12">
                                                    <?php if(isset($item->additional['special_instruction'])): ?>
                                                    <div class="special-instruction">
                                                        <strong >Special Instructions:</strong>
                                                        <?php echo e($item->additional['special_instruction']); ?>

                                                    </div>
                                                    <?php endif; ?> 
                                                </div>
                                                <div class="row col-12">
                                                    <?php if(isset($item->additional['made-for'])): ?>
                                                    <div class="special-instruction">
                                                        <strong>Instructions:</strong>
                                                        <?php echo e($item->additional['made_for']); ?>

                                                    </div>
                                                    <?php endif; ?> 
                                                </div>
                                            </div>

                                            <div class="product-quantity col-3 no-padding">
                                                <?php if($item->product->getTypeInstance()->showQuantityBox() === true): ?>
                                                    <quantity-changer
                                                        :control-name="'qty[<?php echo e($item->id); ?>]'"
                                                        quantity="<?php echo e($item->quantity); ?>"
                                                        quantity-text="<?php echo e(__('shop::app.products.quantity')); ?>">
                                                    </quantity-changer>
                                                <?php else: ?>
                                                    <p class="fw6 fs16 no-padding text-center ml15">--</p>
                                                <?php endif; ?>
                                            </div>

                                            <div class="product-price fs18 col-1">
                                                <span class="card-current-price fw6 mr10">
                                                    <?php echo e(core()->currency( $item->base_total)); ?>

                                                </span>
                                            </div>

                                            <?php if(! cart()->isItemHaveQuantity($item)): ?>
                                                <div class="control-error mt-4 fs16 fw6">
                                                    * <?php echo e(__('shop::app.checkout.cart.quantity-error')); ?>

                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>

                                <?php echo view_render_event('bagisto.shop.checkout.cart.controls.after', ['cart' => $cart]); ?>

                                <div class="misc">
                                    <a
                                        class="theme-btn light fs16 text-center"
                                        href="<?php echo e(route('shop.home.index')); ?>">
                                        <?php echo e(__('shop::app.checkout.cart.continue-shopping')); ?>

                                    </a>

                                    <form
                                        method="POST"
                                        @submit.prevent="onSubmit"
                                        action="<?php echo e(route('velocity.cart.remove.all.items')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <button
                                            type="submit"
                                            onclick="return confirm('<?php echo e(__('shop::app.checkout.cart.confirm-action')); ?>')"
                                            class="theme-btn light unset">

                                            <?php echo e(__('shop::app.checkout.cart.remove-all-items')); ?>

                                        </button>
                                    </form>

                                    <?php if($showUpdateCartButton): ?>
                                        <button
                                            type="submit"
                                            class="theme-btn light unset">

                                            <?php echo e(__('shop::app.checkout.cart.update-cart')); ?>

                                        </button>
                                    <?php endif; ?>
                                </div>

                                <?php echo view_render_event('bagisto.shop.checkout.cart.controls.after', ['cart' => $cart]); ?>

                            </form>
                        </div>

                        <?php echo $__env->make('shop::products.view.cross-sells', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    </div>
                <?php endif; ?>

                <?php echo view_render_event('bagisto.shop.checkout.cart.summary.after', ['cart' => $cart]); ?>


                    <?php if($cart): ?>
                        <div class="col-lg-4 col-md-12 offset-lg-1 row order-summary-container">
                            <?php echo $__env->make('shop::checkout.total.summary', ['cart' => $cart], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                            <coupon-component></coupon-component>
                        </div>
                    <?php else: ?>
                        <div class="row text-center w-100 empty__cart my-5">
                            <div class='image w-100'>
                                <img src="<?php echo e(asset('themes/volantijetcatering/assets/images/empty-cart.png')); ?>" class="img-fluid rounded-top" alt="">
                            </div>
                            <div class="fs16 col-12 empty-cart-message mt-4">
                            
                                <h1 class="mb-3">Your Shopping Cart is<span> Empty! </span> </h1>
                            </div>

                            <a class="fs16 mt15 col-12 remove-decoration continue-shopping"
                                href="<?php echo e(route('shop.product.parentcat')); ?>">

                                <button type="button" class="theme-btn remove-decoration">
                                    <?php echo e(__('shop::app.checkout.cart.continue-shopping')); ?>

                                </button>
                            </a>
                        </div>
                    <?php endif; ?>

                <?php echo view_render_event('bagisto.shop.checkout.cart.summary.after', ['cart' => $cart]); ?>

            </section>
        </div>
    </script>

    <script type="text/javascript" id="cart-template">
        (() => {
            Vue.component('cart-component', {
                template: '#cart-template',

                data: function() {
                    return {
                        isMobileDevice: this.isMobile(),
                    }
                },

                methods: {
                    removeLink(message) {
                        if (!confirm(message)) {
                            event.preventDefault();
                        }
                    }
                }
            })
        })();
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/cart/index.blade.php ENDPATH**/ ?>