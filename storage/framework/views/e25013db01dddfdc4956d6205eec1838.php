<?php

    $tree = \Webkul\Core\Tree::create();

    foreach (config('core') as $item) {
        $tree->add($item);
    }

    $tree->items = core()->sortItems($tree->items);

    $config = $tree;

    $allLocales = core()->getAllLocales()->pluck('name', 'code');
?>

<div class="navbar-left" v-bind:class="{'open': isMenuOpen}">

    <ul class="menubar">
        <?php $__currentLoopData = $menu->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="menu-item <?php echo e($menu->getActive($menuItem)); ?>">
            <a class="menubar-anchor"  href="<?php echo e($menuItem['url']); ?>">
                <span class="icon-menu icon <?php echo e($menuItem['icon-class']); ?>"></span>

                <span class="menu-label"><?php echo e(trans($menuItem['name'])); ?></span>

                <?php if(count($menuItem['children']) || $menuItem['key'] == 'configuration' ): ?>
                    <span
                        class="icon arrow-icon <?php echo e($menu->getActive($menuItem) == 'active' ? 'rotate-arrow-icon' : ''); ?> <?php echo e(( core()->getCurrentLocale() && core()->getCurrentLocale()->direction == 'rtl' ) ? 'arrow-icon-right' :'arrow-icon-left'); ?>"
                        ></span>

                <?php endif; ?>
            </a>
            <?php if($menuItem['key'] != 'configuration'): ?>
                <?php if(count($menuItem['children'])): ?>
                    <ul class="sub-menubar">
                        <?php $__currentLoopData = $menuItem['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subMenuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li class="sub-menu-item <?php echo e($menu->getActive($subMenuItem)); ?>">
                                <a href="<?php echo e(count($subMenuItem['children']) ? current($subMenuItem['children'])['url'] : $subMenuItem['url']); ?>">
                                    <span class="menu-label"><?php echo e(trans($subMenuItem['name'])); ?></span>
                                </a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                <?php endif; ?>
            <?php else: ?>
                <ul class="sub-menubar">
                    <?php $__currentLoopData = $config->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="sub-menu-item <?php echo e($item['key'] == request()->route('slug') ? 'active' : ''); ?>">
                            <a href="<?php echo e(route('admin.configuration.index', $item['key'])); ?>">
                                <span class="menu-label"> <?php echo e(isset($item['name']) ? trans($item['name']) : ''); ?></span>
                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <nav-slide-button id="nav-expand-button" icon-class="accordian-right-icon"></nav-slide-button>
</div>

<?php $__env->startPush('scripts'); ?>

    <script>

        $(document).ready(function () {
            $(".menubar-anchor").click(function() {
                if ( $(this).parent().attr('class') == 'menu-item active' ) {
                    $(this).parent().removeClass('active');
                    $('.arrow-icon-left').removeClass('rotate-arrow-icon');
                    $('.arrow-icon-right').removeClass('rotate-arrow-icon');
                    $(".sub-menubar").hide();
                    event.preventDefault();
                }
            });
        });

    </script>

<?php $__env->stopPush(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Admin/src/Providers/../Resources/views/layouts/nav-left.blade.php ENDPATH**/ ?>