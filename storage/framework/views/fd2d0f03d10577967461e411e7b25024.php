<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">

<head>

    <title><?php echo $__env->yieldContent('page_title'); ?></title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base-url" content="<?php echo e(url()->to('/')); ?>">
    <meta http-equiv="content-language" content="<?php echo e(app()->getLocale()); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/webkul/ui/assets/css/ui.css')); ?>">

    <link rel="stylesheet" href="<?php echo e(bagisto_asset('css/shop.css')); ?>">
    

    <?php if($favicon = core()->getCurrentChannel()->favicon_url): ?>
        <link rel="icon" sizes="16x16" href="<?php echo e($favicon); ?>" />
    <?php else: ?>
        <link rel="icon" sizes="16x16" href="<?php echo e(bagisto_asset('images/favicon.ico')); ?>" />
    <?php endif; ?>

    <?php echo $__env->yieldContent('head'); ?>

    <?php $__env->startSection('seo'); ?>
        <?php if(! request()->is('/')): ?>
            <meta name="description" content="<?php echo e(core()->getCurrentChannel()->description); ?>"/>
        <?php endif; ?>
    <?php echo $__env->yieldSection(); ?>

    <?php echo $__env->yieldPushContent('css'); ?>

    <?php echo view_render_event('bagisto.shop.layout.head'); ?>


    <style>
        <?php echo core()->getConfigData('general.content.custom_scripts.custom_css'); ?>

    </style>

</head>


<body <?php if(core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl'): ?> class="rtl" <?php endif; ?> style="scroll-behavior: smooth;">

    <?php echo view_render_event('bagisto.shop.layout.body.before'); ?>


    <div id="app">
        <flash-wrapper ref='flashes'></flash-wrapper>

        <div class="main-container-wrapper">

            <?php echo view_render_event('bagisto.shop.layout.header.before'); ?>


            <?php echo $__env->make('shop::layouts.header.index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

            <?php echo view_render_event('bagisto.shop.layout.header.after'); ?>


            <?php echo $__env->yieldContent('slider'); ?>

            <main class="content-container">

                <?php echo view_render_event('bagisto.shop.layout.content.before'); ?>


                <?php echo $__env->yieldContent('content-wrapper'); ?>

                <?php echo view_render_event('bagisto.shop.layout.content.after'); ?>


            </main>

        </div>

        <?php echo view_render_event('bagisto.shop.layout.footer.before'); ?>


        <?php echo $__env->make('shop::layouts.footer.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo view_render_event('bagisto.shop.layout.footer.after'); ?>


        <?php if(core()->getConfigData('general.content.footer.footer_toggle')): ?>
            <div class="footer">
                <p style="text-align: center;">
                    <?php if(core()->getConfigData('general.content.footer.footer_content')): ?>
                        <?php echo e(core()->getConfigData('general.content.footer.footer_content')); ?>

                    <?php else: ?>
                        <?php echo trans('admin::app.footer.copy-right'); ?>

                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>

        <overlay-loader :is-open="show_loader"></overlay-loader>

        <go-top bg-color="#0041ff"></go-top>
    </div>

    <script type="text/javascript">
        window.flashMessages = [];

        <?php if($success = session('success')): ?>
            window.flashMessages = [{'type': 'alert-success', 'message': "<?php echo e($success); ?>" }];
        <?php elseif($warning = session('warning')): ?>
            window.flashMessages = [{'type': 'alert-warning', 'message': "<?php echo e($warning); ?>" }];
        <?php elseif($error = session('error')): ?>
            window.flashMessages = [{'type': 'alert-error', 'message': "<?php echo e($error); ?>" }];
        <?php elseif($info = session('info')): ?>
            window.flashMessages = [{'type': 'alert-info', 'message': "<?php echo e($info); ?>" }];
        <?php endif; ?>

        window.serverErrors = [];

        <?php if(isset($errors)): ?>
            <?php if(count($errors)): ?>
                window.serverErrors = <?php echo json_encode($errors->getMessages(), 15, 512) ?>;
            <?php endif; ?>
        <?php endif; ?>
    </script>

    <script type="text/javascript" src="<?php echo e(bagisto_asset('js/shop.js')); ?>" ></script>
    <script type="text/javascript" src="<?php echo e(asset('vendor/webkul/ui/assets/js/ui.js')); ?>"></script>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php echo view_render_event('bagisto.shop.layout.body.after'); ?>


    <div class="modal-overlay"></div>

    <script>
        <?php echo core()->getConfigData('general.content.custom_scripts.custom_javascript'); ?>

    </script>

</body>

</html><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/layouts/master.blade.php ENDPATH**/ ?>