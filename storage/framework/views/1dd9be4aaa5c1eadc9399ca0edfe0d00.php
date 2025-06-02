<!DOCTYPE html>
<html lang="<?php echo e(config('app.locale')); ?>">

<head>
    <title><?php echo $__env->yieldContent('page_title'); ?></title>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="base-url" content="<?php echo e(url()->to('/')); ?>">

    <?php if($favicon = core()->getConfigData('general.design.admin_logo.favicon', core()->getCurrentChannelCode())): ?>
        <link rel="icon" sizes="16x16" href="<?php echo e(\Illuminate\Support\Facades\Storage::url($favicon)); ?>" />
    <?php else: ?>
        <link rel="icon" sizes="16x16" href="<?php echo e(asset('vendor/webkul/ui/assets/images/favicon.ico')); ?>" />
    <?php endif; ?>

    <link rel="stylesheet" href="<?php echo e(asset('vendor/webkul/ui/assets/css/ui.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('vendor/webkul/admin/assets/css/admin.css')); ?>">
    <link rel="stylesheet" href="<?php echo e(asset('themes/volantijetcatering/assets/css/admin.css')); ?>">

    <?php echo $__env->yieldContent('head'); ?>

    <?php echo $__env->yieldPushContent('css'); ?>

    <?php echo view_render_event('bagisto.admin.layout.head'); ?>

</head>

<body <?php if(core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl'): ?> class="rtl" <?php endif; ?> style="scroll-behavior: smooth;">

    <?php echo view_render_event('bagisto.admin.layout.body.before'); ?>


    <div id="app">
        <flash-wrapper ref='flashes'></flash-wrapper>

        <?php echo view_render_event('bagisto.admin.layout.nav-top.before'); ?>


        <?php echo $__env->make('admin::layouts.nav-top', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo view_render_event('bagisto.admin.layout.nav-top.after'); ?>


        <?php echo view_render_event('bagisto.admin.layout.nav-left.before'); ?>


        <?php echo $__env->make('admin::layouts.nav-left', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <?php echo view_render_event('bagisto.admin.layout.nav-left.after'); ?>


        <div class="content-container"
            :class="isMenuOpen ? 'padding-container-navbar-expand' : 'padding-container-navbar-not-expand'">

            <?php echo view_render_event('bagisto.admin.layout.content.before'); ?>


            <?php echo $__env->yieldContent('content-wrapper'); ?>

            <?php echo view_render_event('bagisto.admin.layout.content.after'); ?>


        </div>
    </div>

    <script type="text/javascript">
        window.flashMessages = [];

        if (localStorage.getItem('dark-mode') == 'true') {
            document.body.classList.toggle("dark-mode");
        }

        <?php $__currentLoopData = ['success', 'warning', 'error', 'info']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($value = session($key)): ?>
                window.flashMessages.push({
                    'type': 'alert-<?php echo e($key); ?>',
                    'message': "<?php echo e($value); ?>"
                });
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        window.serverErrors = [];
        <?php if(isset($errors)): ?>
            <?php if(count($errors)): ?>
                window.serverErrors = <?php echo json_encode($errors->getMessages(), 15, 512) ?>;
            <?php endif; ?>
        <?php endif; ?>
    </script>


    <script type="text/javascript" src="<?php echo e(asset('vendor/webkul/admin/assets/js/admin.js')); ?>"></script>

    <script type="text/javascript" src="<?php echo e(asset('vendor/webkul/ui/assets/js/ui.js')); ?>"></script>

    <script type="text/javascript">
        window.addEventListener('DOMContentLoaded', function() {
            moveDown = 60;
            moveUp = -60;
            count = 0;
            countKeyUp = 0;
            pageDown = 60;
            pageUp = -60;
            scroll = 0;

            listLastElement = $('.menubar li:last-child').offset();

            if (listLastElement) {
                lastElementOfNavBar = listLastElement.top;
            }

            navbarTop = $('.navbar-left').css("top");
            menuTopValue = $('.navbar-left').css('top');
            menubarTopValue = menuTopValue;

            documentHeight = $(document).height();
            menubarHeight = $('ul.menubar').height();
            navbarHeight = $('.navbar-left').height();
            windowHeight = $(window).height();
            contentHeight = $('.content').height();
            innerSectionHeight = $('.inner-section').height();
            gridHeight = $('.grid-container').height();
            pageContentHeight = $('.page-content').height();

            if (menubarHeight <= windowHeight) {
                differenceInHeight = windowHeight - menubarHeight;
            } else {
                differenceInHeight = menubarHeight - windowHeight;
            }

            if (menubarHeight > windowHeight) {
                document.addEventListener("keydown", function(event) {
                    if ((event.keyCode == 38) && count <= 0) {
                        count = count + moveDown;

                        $('.navbar-left').css("top", count + "px");
                    } else if ((event.keyCode == 40) && count >= -differenceInHeight) {
                        count = count + moveUp;

                        $('.navbar-left').css("top", count + "px");
                    } else if ((event.keyCode == 33) && countKeyUp <= 0) {
                        countKeyUp = countKeyUp + pageDown;

                        $('.navbar-left').css("top", countKeyUp + "px");
                    } else if ((event.keyCode == 34) && countKeyUp >= -differenceInHeight) {
                        countKeyUp = countKeyUp + pageUp;

                        $('.navbar-left').css("top", countKeyUp + "px");
                    }
                });

                $("body").css({
                    minHeight: $(".menubar").outerHeight() + 100 + "px"
                });

                window.addEventListener('scroll', function() {
                    documentScrollWhenScrolled = $(document).scrollTop();

                    if (documentScrollWhenScrolled <= differenceInHeight + 200) {
                        $('.navbar-left').css('top', -documentScrollWhenScrolled + 60 + 'px');
                        scrollTopValueWhenNavBarFixed = $(document).scrollTop();
                    }
                });
            }
        });
    </script>
    





    <?php echo $__env->yieldPushContent('scripts'); ?>

    <?php echo view_render_event('bagisto.admin.layout.body.after'); ?>


    <div class="modal-overlay"></div>

</body>

</html>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Admin/src/Providers/../Resources/views/layouts/master.blade.php ENDPATH**/ ?>