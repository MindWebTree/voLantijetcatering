<?php $__env->startSection('page_title'); ?>
    <?php echo e(__('shop::app.checkout.success.title')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('seo'); ?>
<meta name="title" content="<?php echo e(__('shop::app.checkout.success.title')); ?>" />
<meta name="description" content="Order successfully placed" />
<meta name="keywords" content="" />
<link rel="canonical" href="<?php echo e(url()->current()); ?>" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    






    <div class="container my-5 thank__you">
        <div class="thank__you text-center">
            <img class="mr-5 success_tick" src="./../themes/volantijetcatering/assets/images/tick.png" alt="">
            <h1 class="fw-6 text-center my-4"><?php echo e(__('shop::app.checkout.success.thanks')); ?></h1>

        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-lg-5 col-md-6 col-12 thank__left">

                <div class="col-12 border my-3 thank__address thank__order">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/air-location.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Ordering From</h4>
                        </div>
                    </div>

                    <strong class="m-0 airport__name"><?php echo e($orderDetails[0]->airport_name); ?></strong>
                    <p>
                        <?php echo e($orderDetails[0]->address1); ?>

                    </p>
                </div>
                <div class="col-12 border my-3 thank__fbo thank__order pb-3">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/fbo.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Fbo Detail</h4>
                        </div>
                    </div>
                    
                    <h5 class="my-2 fs23 fw6"><?php echo e(__('shop::app.fbo-detail.client-info')); ?></h5>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_full_name); ?> </p>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_phone_number); ?> </p>
                    <p> <?php echo e($orderDetails[0]->fbo_email_address); ?> </p>
                    <h5 class="my-2 fs23 fw6"><?php echo e(__('shop::app.fbo-detail.aircraft-info')); ?></h5>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_tail_number); ?> </p>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_packaging); ?> </p>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_service_packaging); ?> </p>
               <?php if(isset($fboDetails)): ?>
                    <?php if(!empty($fboDetails->delivery_date) || !empty($fboDetails->delivery_time)): ?>
                    <p class="m-0">
                        <?php echo e(date('m-d-Y', strtotime($fboDetails->delivery_date)) ?? ''); ?> <?php echo e($fboDetails->delivery_time ?? ''); ?>

                    </p>
                <?php endif; ?>
                    <?php endif; ?>
                    <h5 class="my-2 fs23 fw6">Airport Fbo Detail</h5>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_airport_name); ?> </p>
                    <p class="m-0"> <?php echo e($orderDetails[0]->fbo_airport_address); ?> </p>
                </div>
                <?php if(!auth()->guard('customer')->check()): ?>
                    <div class="col-12 border my-3 thank__create p-3 thank__order">
  <h5 class="fw-bold text-dark">Unlock Exclusive Benefits!</h5>
                        <p>Create an account to track your orders, save FBO details, securely manage payments, and enjoy a seamless checkout experience.
                        </p>

                        <a href="<?php echo e(route('shop.customer.session.create')); ?>?form=register">
                            <button class="btn-lg  bg-light">Create account</button>
                        </a>
                    </div>
                <?php else: ?>
                    <?php echo e(''); ?>

                <?php endif; ?>

            </div>
            <div class="col-lg-5 col-md-6 col-12 thank__right ">
                <div class="col-12 border my-3 thank__summary thank__order">
                    <div class="d-flex gap">
                        <div class="img mt-3">
                            <img src="./../themes/volantijetcatering/assets/images/order-bag.png" alt="">
                        </div>
                        <div class="title">
                            <h4 class="fw-6 text-center mt-3">Order Summary</h4>
                        </div>
                    </div>
                    <ol class="summary-body pl-4 pl-lg-5">
                        <?php $__currentLoopData = $orderDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $orderDetail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <div class='row'>
                                    <div class='col-8'>
                                        <h6 class="items m-0"><?php echo e($orderDetail->name); ?></h6>
                                        

                                        <?php
                                        $optionLabel = null;
                                        $additionalData = json_decode($orderDetail->additional);
                                            if (isset($additionalData->attributes)) {
                                                $attributes = $additionalData->attributes;

                                                foreach ($attributes as $attribute) {
                                                    if (isset($attribute->option_label) && $attribute->option_label != '') {
                                                        $optionLabel = $attribute->option_label;
                                                    }
                                                }
                                            }

                                        ?>
                                        <?php if(isset($optionLabel)): ?>
                                            <p><strong>Preference:</strong> <?php echo e($optionLabel); ?></p>
                                        <?php endif; ?>
                                    </div>
                                    <div class='col-4'>
                                        <p><strong>Qty: </strong><?php echo e($orderDetail->qty_ordered); ?></p>
                                    </div>
                                    <div class='col-8' style='margin-top: -6px'>
                                        <?php
                                            $additionalData = json_decode($orderDetail->additional, true); // Decode as an associative array
                                            $specialInstruction = isset($additionalData['special_instruction']) ? $additionalData['special_instruction'] : null;
                                        ?>

                                        <?php if($specialInstruction): ?>
                                            <p class="special-intruction" style="margin-top: -10px;overflow: auto;"><strong>Special
                                                    Instruction:
                                                </strong><?php echo e($specialInstruction); ?></p>
                                            
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ol>

                    <div class="col-12 border-top my-3 thank__orderId thank__order p-0">
                        <div class="d-flex gap">
                            <div class="img mt-3">
                                <img src="./../themes/volantijetcatering/assets/images/list.png" alt="">
                            </div>
                            <div class="title">
                                <h4 class="fw-6 text-center mt-3">Additional Detail</h4>
                            </div>
                        </div>
                        <div class="row">

                            <?php if(auth()->guard('customer')->user()): ?>
                                <div class="col-8 pl-4">
                                    <strong>Order Number: </strong>
                                </div>
                                <div class="col-4 pl-4">
                                    <a
                                        href=<?php echo e(route('shop.customer.orders.view', $order['id'])); ?>><strong>#<?php echo e($order['id']); ?></strong></a>
                                </div>
                            <?php else: ?>
                                <div class="col-8">
                                    <strong>Order Number: </strong>
                                </div>
                                <div class="col-4 pl-4">
                                    <p>#<?php echo e($order['id']); ?></p>
                                </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/success.blade.php ENDPATH**/ ?>