<?php

    $tree = \Webkul\Core\Tree::create();

    foreach (config('core') as $item) {
        $tree->add($item);
    }

    $tree->items = core()->sortItems($tree->items);

    $config = $tree;
?>

<mobile-nav></mobile-nav>

<?php $__env->startPush('scripts'); ?>
    <script type="text/x-template" id="mobile-nav-template">
        <div class="nav-container">

            <div class="nav-toggle"></div>

            <div class="overlay"></div>

            <div class="nav-top">
                <div class="pro-info">
                    <div class="profile-info-icon">
                        <span style=""><?php echo e(substr(auth()->guard('admin')->user()->name, 0, 1)); ?></span>
                    </div>

                    <div class="profile-info-desc">
                        <div class="name">
                            <?php echo e(auth()->guard('admin')->user()->name); ?>

                        </div>

                        <div class="role">
                            <?php echo e(auth()->guard('admin')->user()->role['name']); ?>

                        </div>
                    </div>
                    <div style="display:inline-block" @click="closeNavBar">
                        <span class="close"></span>
                    </div>
                </div>
            </div>

            <div class="nav-items">
                <?php $__currentLoopData = $menu->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="nav-item <?php echo e($menu->getActive($menuItem)); ?>">
                        <a class="nav-tab-name" href="<?php echo e($menuItem['key'] == 'dashboard' ? $menuItem['url'] : '#'); ?>">
                            <span class="icon-menu icon <?php echo e($menuItem['icon-class']); ?>"
                            style="margin-right:10px; display: inline-block;vertical-align: middle;transform: scale(0.8);"></span>

                            <span class="menu-label"><?php echo e(trans($menuItem['name'])); ?></span>
                            <?php if(count($menuItem['children']) || $menuItem['key'] == 'configuration' ): ?>
                            <span class="icon arrow-icon"></span>
                            <?php endif; ?>
                        </a>
                        <?php if($menuItem['key'] != 'configuration'): ?>
                            <?php if(count($menuItem['children'])): ?>
                            <ul>
                                <?php $__currentLoopData = $menuItem['children']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subMenuItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="navbar-child <?php echo e($menu->getActive($subMenuItem)); ?>">
                                    <a href="<?php echo e(count($subMenuItem['children']) ? current($subMenuItem['children'])['url'] : $subMenuItem['url']); ?>">
                                            <span style="margin-left:47px"><?php echo e(trans($subMenuItem['name'])); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                            <?php endif; ?>
                        <?php else: ?>
                            <ul>
                                <?php $__currentLoopData = $config->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="navbar-child <?php echo e($item['key'] == request()->route('slug') ? 'active' : ''); ?>">
                                        <a href="<?php echo e(route('admin.configuration.index', $item['key'])); ?>">
                                            <span style="margin-left:47px"><?php echo e(trans($item['name'] ?? '')); ?></span>
                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <div class="nav-item">
                    <a class="nav-tab-name">
                        <span class="icon-menu icon accounts-icon"
                            style="margin-right:10px; display: inline-block;vertical-align: middle;transform: scale(0.8);"></span>
                        <span class="menu-label"><?php echo e(__('admin::app.layouts.account-title')); ?></span>
                        <span class="icon arrow-icon"></span>
                    </a>
                    <ul>
                        <li class="navbar-child">
                            <a>
                                <span style="display:flex;justify-content:space-between;height:20px">
                                    <div style="margin-top:12px;margin-left:47px">
                                        <span><?php echo e(__('admin::app.layouts.mode')); ?></span>
                                    </div>
                                    <dark style="margin-left:13%"></dark>
                                </span>
                            </a>

                        </li>
                        <li  class="navbar-child">
                            <a href="<?php echo e(route('admin.account.edit')); ?>">
                                <span style="margin-left:47px"><?php echo e(__('admin::app.layouts.my-account')); ?></span>
                            </a>
                        </li>
                        <li  class="navbar-child">
                            <a href="<?php echo e(route('admin.session.destroy')); ?>">
                                <span style="margin-left:47px"><?php echo e(__('admin::app.layouts.logout')); ?></span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </script>

    <script>
    Vue.component('mobile-nav', {

        template: '#mobile-nav-template',

        data: function() {
            return {
               openProfileNav: 0,
               openLocaleNav:0
            }
        },

        mounted(){
            const nav = document.querySelector(".nav-container");

            if (nav) {
                const toggle = nav.querySelector(".nav-toggle");

                if (toggle) {
                    toggle.addEventListener("click", () => {
                        if (nav.classList.contains("is-active")) {
                            nav.classList.remove("is-active");
                        } else {
                            nav.classList.add("is-active");
                        }
                    });

                    nav.addEventListener("blur", () => {
                        nav.classList.remove("is-active");
                    });
                }
            }

            document.querySelectorAll('.nav-tab-name').forEach(function(navItem) {
                navItem.addEventListener('click', function(item) {
                    var tabname = item.target.innerText;
                    if (! navItem.parentElement.classList.contains("pro-info") && tabname != 'Dashboard') {
                        navItem.parentElement.classList.toggle("active");
                        navItem.parentElement.children[1].classList.toggle("display-block");
                        navItem.children[2].classList.toggle("rotate-arrow-icon");
                    }

                });
            });
        },

        methods: {
           closeNavBar: function(){
                $('.nav-toggle').click();
           }
        }
    });
</script>
<?php $__env->stopPush(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Admin/src/Providers/../Resources/views/layouts/mobile-nav.blade.php ENDPATH**/ ?>