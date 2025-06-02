<?php $__env->startSection('page_title'); ?>
    <?php echo e($page->page_title); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('head'); ?>
    <?php if(isset($page->meta_title)): ?>
        <meta name="title" content="<?php echo e($page->meta_title); ?>" />
    <?php endif; ?>

    <?php if(isset($page->meta_description)): ?>
        <meta name="description" content="<?php echo e($page->meta_description); ?>" />
    <?php endif; ?>

    <?php if(isset($page->meta_keywords)): ?>
        <meta name="keywords" content="<?php echo e($page->meta_keywords); ?>" />
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content-wrapper'); ?>
    <div class="cms-page-container p-2">
        <?php echo Blade::render($page->html_content); ?>

    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('shop::layouts.master', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/cms/page.blade.php ENDPATH**/ ?>