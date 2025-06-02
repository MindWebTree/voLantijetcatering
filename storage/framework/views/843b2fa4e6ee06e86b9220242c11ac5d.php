<!-- shyam html added 05-07-2023-->
<header class="sticky-header">
    <div class="row remove-padding-margin velocity-divide-page custom-header">

        <a class="left navbar-brand" href="<?php echo e(route('shop.home.index')); ?>" aria-label="Logo">
            <?php if(request()->is('/')): ?>
                <img class="logo ml-5"
                    src="<?php echo e(core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-only.png')); ?>"
                    alt="volanti logo" />
            <?php else: ?>
                <img class="logo"
                    src="<?php echo e(core()->getCurrentChannel()->logo_url ?? asset('themes/volantijetcatering/assets/images/logo-volanti-dark.png')); ?>" alt="volanti logo" />
            <?php endif; ?>
        </a>
        <div class="right searchbar">
            <div class="row sticky-horizontal">
                
                <div class="col-lg-12 d-flex search__container">
                    <?php echo $__env->make('shop::layouts.particals.search-bar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                    <div class="signin-button-wrapper">
                        <div class="search_icon" id="search-icon">
                            <i class="fs16 fw6 rango-search d-flex" style="font-size: 21px;padding: 0 10px 0 5px;"></i>
                        </div>
                        <img class="header_searchbar_close_button"
                            src="<?php echo e(asset('themes/volantijetcatering/assets/images/close.png')); ?>" alt="close icon"
                            style="height: 20px; width: 40px; padding: 0px 16px 0px 5px;display:none;" />
                        <div class="volanti_site_button pr-3">
                            
                            
                            <a href="<?php echo e(route('shop.product.parentcat')); ?>" target="_blank">menu</a>
                        </div>
                        <div class="icon-wrapper">


                            <!------------ copy login-section tap nav and added by umesh 06-07-2023----------->

                            <?php echo view_render_event('bagisto.shop.layout.header.account-item.before'); ?>


                            <div id="account">
                                <div class="d-inline-block welcome-content dropdown-toggle">
                                    <?php if(auth()->guard('customer')->user()): ?>
                                        <?php if(auth()->guard('customer')->user()->image): ?>
                                            <i class="align-vertical-top"><img class= "profile-small-icon"
                                                    src="<?php echo e(auth('customer')->user()->image_url); ?>"
                                                    alt="" /></i>
                                        <?php else: ?>
                                            <div class="customer-name col-12 text-uppercase">
                                                <?php echo e(substr(auth('customer')->user()->first_name, 0, 1)); ?>

                                            </div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                         <a href="<?php echo e(route('shop.customer.session.index')); ?>">
                                        <img src="<?php echo e(asset('/../themes/volantijetcatering/assets/images/profile-user.png')); ?>"
                                            alt="profile icon" height="25px" />
                                         </a>
                                    <?php endif; ?>



                                    <span class="text-center cursor-pointer">


                                        <?php
                                            $customerName = auth()->guard('customer')->user()
                                                ? auth()->guard('customer')->user()->first_name
                                                : '<a href="' .
                                                    route('shop.customer.session.index') .
                                                    '">' .
                                                    trans('shop::app.header.sign-in') .
                                                    '</a>';
                                        ?>



                                        <?php if(auth()->guard('customer')->user()): ?>
                                            <?php echo __('velocity::app.header.welcome-message', [
                                                'customer_name' => auth()->guard('customer')->user()
                                                    ? '<span class="truncate">' . Str::limit(auth()->guard('customer')->user()->first_name, 10) . '</span>'
                                                    : '<a href="' . route('shop.customer.session.index') . '">' . trans('shop::app.header.sign-in') . '</a>',
                                            ]); ?>

                                        <?php else: ?>
                                            <?php echo auth()->guard('customer')->user()
                                                ? auth()->guard('customer')->user()->first_name
                                                : '<a href="' . route('shop.customer.session.index') . '">' . trans('shop::app.header.sign-in') . '</a>'; ?>

                                        <?php endif; ?>

                                    </span>

                                    <?php if(auth()->guard('customer')->user()): ?>
                                        <span class="rango-arrow-down cursor-pointer"> </span>
                                    <?php endif; ?>
                                </div>

                                <?php if(auth()->guard('customer')->guest()): ?>
                                <?php endif; ?>

                                <?php if(auth()->guard('customer')->check()): ?>
                                    <div class="dropdown-list">
                                        <div class="dropdown-label  user__name">

                                            <?php echo e(auth()->guard('customer')->user()->first_name); ?>

                                        </div>

                                        <div class="dropdown-container profile__dropdown">
                                            <ul type="none">
                                                <li>
                                                    
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="33" height="23" fill="currentColor" class="bi bi-person" viewBox="0 2 8 16">
                                                        <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6m2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0m4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4m-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10s-3.516.68-4.168 1.332c-.678.678-.83 1.418-.832 1.664z"/>
                                                      </svg>
                                                    
                                                    <a href="<?php echo e(route('shop.customer.profile.index')); ?>"
                                                        class="unset p-2"><?php echo e(__('shop::app.header.profile')); ?></a>
                                                </li>

                                                <li>
                                                    
                                                    <svg version="1.1" id="Layer_1" height="20px" width="33px" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 2 10 138.88" style="enable-background:new 0 0 115.35 122.88;" xml:space="preserve"><g><path d="M25.27,86.92c-1.81,0-3.26-1.46-3.26-3.26s1.47-3.26,3.26-3.26h21.49c1.81,0,3.26,1.46,3.26,3.26s-1.46,3.26-3.26,3.26 H25.27L25.27,86.92L25.27,86.92z M61.1,77.47c-0.96,0-1.78-0.82-1.78-1.82c0-0.96,0.82-1.78,1.78-1.78h4.65c0.04,0,0.14,0,0.18,0 c1.64,0.04,3.1,0.36,4.33,1.14c1.37,0.87,2.37,2.19,2.92,4.15c0,0.04,0,0.09,0.05,0.14l0.46,1.82h39.89c1,0,1.78,0.82,1.78,1.78 c0,0.18-0.05,0.36-0.09,0.55l-4.65,18.74c-0.18,0.82-0.91,1.37-1.73,1.37l0,0l-29.18,0c0.64,2.37,1.28,3.65,2.14,4.24 c1.05,0.68,2.87,0.73,5.93,0.68h0.04l0,0h20.61c1,0,1.78,0.82,1.78,1.78c0,1-0.82,1.78-1.78,1.78H87.81l0,0 c-3.79,0.04-6.11-0.05-7.98-1.28c-1.92-1.28-2.92-3.46-3.92-7.43l0,0L69.8,80.2c0-0.05,0-0.05-0.04-0.09 c-0.27-1-0.73-1.69-1.37-2.05c-0.64-0.41-1.5-0.59-2.51-0.59c-0.05,0-0.09,0-0.14,0H61.1L61.1,77.47L61.1,77.47z M103.09,114.13 c2.42,0,4.38,1.96,4.38,4.38s-1.96,4.38-4.38,4.38s-4.38-1.96-4.38-4.38S100.67,114.13,103.09,114.13L103.09,114.13L103.09,114.13z M83.89,114.13c2.42,0,4.38,1.96,4.38,4.38s-1.96,4.38-4.38,4.38c-2.42,0-4.38-1.96-4.38-4.38S81.48,114.13,83.89,114.13 L83.89,114.13L83.89,114.13z M25.27,33.58c-1.81,0-3.26-1.47-3.26-3.26c0-1.8,1.47-3.26,3.26-3.26h50.52 c1.81,0,3.26,1.46,3.26,3.26c0,1.8-1.46,3.26-3.26,3.26H25.27L25.27,33.58L25.27,33.58z M7.57,0h85.63c2.09,0,3.99,0.85,5.35,2.21 s2.21,3.26,2.21,5.35v59.98h-6.5V7.59c0-0.29-0.12-0.56-0.31-0.76c-0.2-0.19-0.47-0.31-0.76-0.31l0,0H7.57 c-0.29,0-0.56,0.12-0.76,0.31S6.51,7.3,6.51,7.59v98.67c0,0.29,0.12,0.56,0.31,0.76s0.46,0.31,0.76,0.31h55.05 c0.61,2.39,1.3,4.48,2.23,6.47H7.57c-2.09,0-3.99-0.85-5.35-2.21C0.85,110.24,0,108.34,0,106.25V7.57c0-2.09,0.85-4,2.21-5.36 S5.48,0,7.57,0L7.57,0L7.57,0z M25.27,60.25c-1.81,0-3.26-1.46-3.26-3.26s1.47-3.26,3.26-3.26h50.52c1.81,0,3.26,1.46,3.26,3.26 s-1.46,3.26-3.26,3.26H25.27L25.27,60.25L25.27,60.25z"></path></g></svg>
                                                    
                                                    <a href="<?php echo e(route('shop.customer.orders.index')); ?>"
                                                        class="unset p-2"><?php echo e(__('velocity::app.shop.general.orders')); ?></a>
                                                </li>

                                                <?php if((bool) core()->getConfigData('general.content.shop.wishlist_option')): ?>
                                                    <li>
                                                        <a href="<?php echo e(route('shop.customer.wishlist.index')); ?>"
                                                            class="unset p-2"><?php echo e(__('shop::app.header.wishlist')); ?></a>
                                                    </li>
                                                <?php endif; ?>

                                                <?php if((bool) core()->getConfigData('general.content.shop.compare_option')): ?>
                                                    <li>
                                                        <a href="<?php echo e(route('velocity.customer.product.compare')); ?>"
                                                            class="unset p-2"><?php echo e(__('velocity::app.customer.compare.text')); ?></a>
                                                    </li>
                                                <?php endif; ?>

                                                <li>
                                                    <form id="customerLogout"
                                                        action="<?php echo e(route('shop.customer.session.destroy')); ?>"
                                                        method="POST">
                                                        <?php echo csrf_field(); ?>

                                                        <?php echo method_field('DELETE'); ?>
                                                    </form>
                                                    
                                                    
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="33" height="20" fill="currentColor" class="bi bi-box-arrow-right" viewBox="0 2 8 17">
                                                            <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                            <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                          </svg>
                                                    <a class="unset p-2"
                                                        href="<?php echo e(route('shop.customer.session.destroy')); ?>"
                                                        onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                                        <?php echo e(__('shop::app.header.logout')); ?>

                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <?php echo view_render_event('bagisto.shop.layout.header.account-item.after'); ?>


                            <!------------ended by umesh 06-07-2023----------->

                        </div>
                        <!--  <div class="cart-wrapper">
                                        
                                    </div> -->


                        <div class="left-wrapper">
                            <?php echo view_render_event('bagisto.shop.layout.header.wishlist.before'); ?>


                            <?php echo $__env->make('velocity::shop.layouts.particals.header-compts', ['isText' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                            <?php echo view_render_event('bagisto.shop.layout.header.cart-item.after'); ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>
<!-- shyam html end  05-07-2023-->
<?php $__env->startPush('scripts'); ?>
    <script type="text/javascript">
        (() => {
            document.addEventListener('scroll', e => {
                scrollPosition = Math.round(window.scrollY);

                if (scrollPosition > 50) {
                    document.querySelector('header').classList.add('header-shadow');
                } else {
                    document.querySelector('header').classList.remove('header-shadow');
                }
            });
        })();
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/layouts/header/index.blade.php ENDPATH**/ ?>