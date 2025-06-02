<?php $__env->startSection('page_title'); ?>
    <?php echo e(__('shop::app.customer.forgot-password.page_title')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    <div class="auth-content form-container">
        <div class="container">
            <div class="col-lg-10 col-md-12 offset-lg-1">
                <div class="heading d-flex align-items-center mb-5">
                    <h2 class="fs24 fw6 w-100 m-0">
                        <?php echo e(__('velocity::app.customer.forget-password.forgot-password')); ?>

                    </h2>

                    <a href="<?php echo e(route('shop.customer.session.index')); ?>" class="btn-new-customer m-0 text-right">
                        <button type="button" class="theme-btn light">
                            <?php echo e(__('velocity::app.customer.signup-form.login')); ?>

                        </button>
                    </a>
                </div>

                <div class="body col-12">
                    <h3 class="fw6">
                        <?php echo e(__('velocity::app.customer.forget-password.recover-password')); ?>

                    </h3>

                    <p class="fs16">
                        <?php echo e(__('velocity::app.customer.forget-password.recover-password-text')); ?>

                    </p>

                    <?php echo view_render_event('bagisto.shop.customers.forget_password.before'); ?>


                    <form
                        method="post"
                        action="<?php echo e(route('shop.customer.forgot_password.store')); ?>"
                        @submit.prevent="onSubmit" id="forgot-password-form">

                        <?php echo e(csrf_field()); ?>


                        <?php echo view_render_event('bagisto.shop.customers.forget_password_form_controls.before'); ?>


                        <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                            <label for="email" class="mandatory label-style">
                                <?php echo e(__('shop::app.customer.forgot-password.email')); ?>

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-style"
 value="<?php echo e(old('email')); ?>"
                                v-validate="'required|email'" />

                            <span class="control-error" v-if="errors.has('email')" v-text="errors.first('email')"></span>
                        </div>

                          <div class="control-group">
                            <div id="recaptcha"></div>
                            <?php $__errorArgs = ['g-recaptcha-response'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="error-message control-error">
                                <?php echo e($message); ?>

                            </div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <?php echo view_render_event('bagisto.shop.customers.forget_password_form_controls.after'); ?>


                        <button class="theme-btn forget_password_button" type="submit">
                            <?php echo e(__('shop::app.customer.forgot-password.submit')); ?>

                        </button>
                    </form>

                    <?php echo view_render_event('bagisto.shop.customers.forget_password.after'); ?>

                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>

<?php echo Captcha::renderJS(); ?>


<script>
    window.siteKey = "<?php echo e(core()->getConfigData('customer.captcha.credentials.site_key')); ?>";
    $(document).ready(function() {
        // Handle form submission
        jQuery("body").on('click', '.forget_password_button', function(e) {
            e.preventDefault();

            var recaptchaResponse = grecaptcha.getResponse();
            console.log(recaptchaResponse);
            $('.error-message').remove();

            if (recaptchaResponse === '') {
                console.log('captcha error ');
                $('#recaptcha').after('<span class="error-message text-danger">Please select CAPTCHA</span>');
            } else {
                console.log('captcha not error ');
                document.getElementById('forgot-password-form').submit();
            }
        });
    });

        </script>

<?php $__env->stopPush(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/customers/signup/forgot-password.blade.php ENDPATH**/ ?>