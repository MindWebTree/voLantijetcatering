<?php
    $orderStatusMessages = [
        'pending' => trans('admin::app.notification.order-status-messages.pending'),
        'canceled' => trans('admin::app.notification.order-status-messages.canceled'),
        'closed' => trans('admin::app.notification.order-status-messages.closed'),
        'completed' => trans('admin::app.notification.order-status-messages.completed'),
        'processing' => trans('admin::app.notification.order-status-messages.processing'),
        'pending_payment' => trans('admin::app.notification.order-status-messages.pending_payment'),
    ];
    $allLocales = core()->getAllLocales()->pluck('name', 'code');
?>

<div class="navbar-top">
    <div class="navbar-top-left">
        <?php echo $__env->make('admin::layouts.mobile-nav', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        <div class="brand-logo">
            <a href="<?php echo e(route('admin.dashboard.index')); ?>">
                <?php if(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode())): ?>
                    <img src="/themes/velocity/assets/images/Desktop_image3.png">
                    <!-- <img src="<?php echo e(\Illuminate\Support\Facades\Storage::url(core()->getConfigData('general.design.admin_logo.logo_image', core()->getCurrentChannelCode()))); ?>" alt="<?php echo e(config('app.name')); ?>" style="height: 40px; width: 110px;"/> -->
                <?php else: ?>
                    <default-image light-theme-image-url="<?php echo e(asset('vendor/webkul/ui/assets/images/logo.png')); ?>"
                        dark-theme-image-url="<?php echo e(asset('vendor/webkul/ui/assets/images/logo_light.png')); ?>"></default-image>
                <?php endif; ?>
            </a>
        </div>
    </div>

    <div class="navbar-top-right">
        <div class="profile">
            <span class="avatar">
            </span>

            <div class="store">
                <div>
                    <a href="<?php echo e(route('shop.home.index')); ?>" target="_blank"
                        style="display: inline-block; vertical-align: middle;">
                        <span class="icon store-icon" data-toggle="tooltip" data-placement="bottom"
                            title="<?php echo e(__('admin::app.layouts.visit-shop')); ?>"></span>
                    </a>
                </div>
            </div>

            <notification notif-title="<?php echo e(__('admin::app.notification.notification-title', ['read' => 0])); ?>"
                get-notification-url="<?php echo e(route('admin.notification.get_notification')); ?>"
                view-all="<?php echo e(route('admin.notification.index')); ?>"
                order-view-url="<?php echo e(\URL::to('/')); ?>/<?php echo e(config('app.admin_url')); ?>/viewed-notifications/"
                pusher-key="<?php echo e(env('PUSHER_APP_KEY')); ?>" pusher-cluster="<?php echo e(env('PUSHER_APP_CLUSTER')); ?>"
                title="<?php echo e(__('admin::app.notification.title-plural')); ?>"
                view-all-title="<?php echo e(__('admin::app.notification.view-all')); ?>"
                get-read-all-url="<?php echo e(route('admin.notification.read_all')); ?>"
                order-status-messages="<?php echo e(json_encode($orderStatusMessages)); ?>"
                read-all-title="<?php echo e(__('admin::app.notification.read-all')); ?>"
                locale-code=<?php echo e(core()->getCurrentLocale()->code); ?>>

                <div class="notifications">
                    <div class="dropdown-toggle">
                        <i class="icon notification-icon active" style="margin-left: 0px;"></i>
                    </div>
                </div>

            </notification>

            <div class="profile-info">
                <div class="dropdown-toggle">
                    <div style="display: inline-block; vertical-align: middle;">
                        <div class="profile-info-div">
                            <?php if(auth()->guard('admin')->user()->image): ?>
                                <div class="profile-info-icon">
                                    <img src="<?php echo e(auth()->guard('admin')->user()->image_url); ?>" />
                                </div>
                            <?php else: ?>
                                <div class="profile-info-icon">
                                    <span><?php echo e(substr(auth()->guard('admin')->user()->name, 0, 1)); ?></span>
                                </div>
                            <?php endif; ?>


                            <div class="profile-info-desc">
                                <span class="name">
                                    <?php echo e(auth()->guard('admin')->user()->name); ?>

                                </span>

                                <span class="role">
                                    <?php echo e(auth()->guard('admin')->user()->role['name']); ?>

                                </span>
                            </div>
                        </div>
                    </div>
                    <i class="icon arrow-down-icon active"></i>
                </div>

                <div class="dropdown-list bottom-right">
                    <span
                        class="app-version"><?php echo e(__('admin::app.layouts.app-version', ['version' => 'v' . core()->version()])); ?></span>

                    <div class="dropdown-container">
                        <label><?php echo e(__('admin::app.layouts.account-title')); ?></label>
                        <ul>
                            <li>
                                <a
                                    href="<?php echo e(route('admin.account.edit')); ?>"><?php echo e(__('admin::app.layouts.my-account')); ?></a>
                            </li>
                            <li v-if="!isMobile()">
                                <div><?php echo e(__('admin::app.layouts.mode')); ?></div>
                                <dark></dark>
                            </li>
                            <li>
                                <a
                                    href="<?php echo e(route('admin.session.destroy')); ?>"><?php echo e(__('admin::app.layouts.logout')); ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Admin/src/Providers/../Resources/views/layouts/nav-top.blade.php ENDPATH**/ ?>