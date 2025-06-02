<?php $__env->startSection('page_title'); ?>
    <?php echo e(__('admin::app.error.404.page-title')); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
<div class="d-flex flex-column justify-content-center align-items-center pt-5">
    <div class="error_page">
        <div class="error_page_heading text-center">
            <h1 class="">Page Not Found</h1>
        </div>
        <div class="error_page_body pt-5">
            <div class="col-12 text-start">
                <p class="font-weight-bold">The page you requested was not found, and we have a fine guess why.</p>
                <ul class="pt-4 pl-3">
                    <li>If you typed the URL directly, please make sure the spelling is correct.</li>
                    <li>The page no longer exists. In this case, we profusely apologize for the inconvenience and for any damage this may cause.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/errors/404.blade.php ENDPATH**/ ?>