

<?php
    // $cards = collect();
    if(auth()->guard('customer')->check()) {
        $customer_id = auth()->guard('customer')->user()->id;
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customer_id]);
    }
?>


<form data-vv-scope="payment-form" class="payment-form">
    <div class="form-container mt-3">
        <div class="form-header mb-30" slot="header">

            <h4 class="fw6 mb-4">
                
                Payments
            </h4>

            <i class="rango-arrow"></i>
        </div>

        <div class="payment-methods" slot="body">
            <?php $__currentLoopData = $paymentMethods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo view_render_event('bagisto.shop.checkout.payment-method.before', ['payment' => $payment]); ?>


                
                <div class="row col-12 mb-0 <?php echo e($payment['method'] === 'mpauthorizenet' ? 'authorze_payment_row' : ''); ?>" style="justify-content: space-between">
                
                <div class="radio d-none payment-saved">
                        <?php if($payment['method'] != 'mpauthorizenet'): ?>
                        <input type="radio" name="payment[method]" v-validate="'required'"
                            v-model="payment.method" checked @change="methodSelected()"  id="<?php echo e($payment['method']); ?>"
                            value="<?php echo e($payment['method']); ?>"                        
                            data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.payment-method')); ?>&quot;"/>

                        <label for="<?php echo e($payment['method']); ?>" class="radio-view"></label>
                        <?php else: ?>
                        <input type="radio" name="payment[method]" v-validate="'required'"
                        v-model="payment.method" @change="methodSelected()"  id="<?php echo e($payment['method']); ?>"
                        value="<?php echo e($payment['method']); ?>"    
                        class="authorze_payment_radio"                    
                        data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.payment-method')); ?>&quot;"/>

                    <label for="<?php echo e($payment['method']); ?>" class="radio-view"></label>
                    <?php endif; ?>
                    </div>

                    <div class="pl20 w-100 <?php echo e($payment['method'] === 'mpauthorizenet' ? 'authorize-text' : ''); ?>">
                        <div class="row pl-2">
                            <span class="payment-method method-label">                               
                                <?php if($payment['method_title']=='Authorize Net'): ?>                                     
                                    <label for="<?php echo e($payment['method']); ?>" class="radio-view"><b>Debit or Credit Card</b></label>                         
                                <?php else: ?>
                                <b><?php echo e($payment['method_title']); ?></b>                                
                                <?php endif; ?>  
                                
                            </span>
                            <div>
                                <img src="<?php echo e(asset('themes/volantijetcatering/assets/images/visa.png')); ?>" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="<?php echo e(asset('themes/volantijetcatering/assets/images/shopping.png')); ?>" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="<?php echo e(asset('themes/volantijetcatering/assets/images/discover.png')); ?>" alt="Authorize.net Logo" width="35px" class="mr-2" id="AuthorizeNet_image">
                                <img src="<?php echo e(asset('themes/volantijetcatering/assets/images/american-express.png')); ?>" alt="Authorize.net Logo" width="35px" id="AuthorizeNet_image">
                            </div>

                        </div>

                        <div class="row">
                            <?php if($payment['method_title']!='Authorize Net'): ?>
                            <span class="method-summary"><?php echo e($payment['description']); ?></span>
                            <?php endif; ?>
                        </div>

                        <?php $additionalDetails = \Webkul\Payment\Payment::getAdditionalDetails($payment['method']); ?>

                        <?php if(!empty($additionalDetails)): ?>
                            <div class="instructions" v-show="payment.method == '<?php echo e($payment['method']); ?>'">
                                <label><?php echo e($additionalDetails['title']); ?></label>
                                <p><?php echo e($additionalDetails['value']); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <?php echo view_render_event('bagisto.shop.checkout.payment-method.after', ['payment' => $payment]); ?>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <span class="control-error" v-if="errors.has('payment-form.payment[method]')"
                v-text="errors.first('payment-form.payment[method]')"></span>
        </div>

<span class="d-none card_success_message text-success"></span>



        
            
        
    </div>
</form>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/onepage/payment.blade.php ENDPATH**/ ?>