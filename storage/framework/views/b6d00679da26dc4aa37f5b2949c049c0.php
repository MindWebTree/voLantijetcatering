<div class="col-12 form-field" id="password" v-if="is_customer_exist">
    <label for="password"><?php echo e(__('shop::app.checkout.onepage.password')); ?></label>

    <input
        id="password"
        type="password"
        class="control"
        name="password"
        v-model="address.billing.password" />

    <div class="forgot-password-link mt-4 mb-4">
        <a href="<?php echo e(route('shop.customer.forgot_password.create')); ?>"><?php echo e(__('shop::app.customer.login-form.forgot_pass')); ?></a>

        <div class="mt-10">
            <?php if(
                Cookie::has('enable-resend')
                && Cookie::get('enable-resend') == true
            ): ?>
                <a href="<?php echo e(route('shop.customer.resend.verification_email', Cookie::get('email-for-resend'))); ?>"><?php echo e(__('shop::app.customer.login-form.resend-verification')); ?></a>
            <?php endif; ?>
        </div>
    </div>

    <button type='button' id="" class="theme-btn" @click="loginCustomer">
        <?php echo e(__('shop::app.customer.login-form.button_title')); ?>

    </button>
</div><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/onepage/customer-checkout.blade.php ENDPATH**/ ?>