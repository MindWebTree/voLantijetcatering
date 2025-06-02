<?php
    $term = request()->input('term');
    $image_search = request()->input('image-search');

    if (! is_null($term)) {
        $serachQuery = 'term='.request()->input('term');
    }
?>

<div class="header" id="header">
    <div class="header-top">
        <div class="left-content">
            <ul class="logo-container">
                <li>
                    <a href="<?php echo e(route('shop.home.index')); ?>" aria-label="Logo">
                        <?php if($logo = core()->getCurrentChannel()->logo_url): ?>
                            <img class="logo" src="<?php echo e($logo); ?>" alt="" />
                        <?php else: ?>
                            <img class="logo" src="<?php echo e(bagisto_asset('images/logo.svg')); ?>" alt="" />
                        <?php endif; ?>
                    </a>
                </li>
            </ul>

            <ul class="search-container">
                <li class="search-group">
                    <form role="search" action="<?php echo e(route('shop.search.index')); ?>" method="GET" style="display: inherit;">
                        <label for="search-bar" style="position: absolute; z-index: -1;">Search</label>
                        <input
                            required
                            name="term"
                            type="search"
                            value="<?php echo e(! $image_search ? $term : ''); ?>"
                            class="search-field"
                            id="search-bar"
                            placeholder="<?php echo e(__('shop::app.header.search-text')); ?>"
                        >

                        <image-search-component></image-search-component>

                        <div class="search-icon-wrapper">

                            <button class="" class="background: none;" aria-label="Search">
                                <i class="icon icon-search"></i>
                            </button>
                        </div>
                    </form>
                </li>
            </ul>
        </div>

        <div class="right-content">

            <span class="search-box"><span class="icon icon-search" id="search"></span></span>

            <ul class="right-content-menu">

                <?php echo view_render_event('bagisto.shop.layout.header.comppare-item.before'); ?>


                <?php
                    $showCompare = (bool) core()->getConfigData('general.content.shop.compare_option');

                    $showWishlist = (bool) core()->getConfigData('general.content.shop.wishlist_option');
                ?>

                <?php echo view_render_event('bagisto.shop.layout.header.compare-item.after'); ?>


                <?php echo view_render_event('bagisto.shop.layout.header.currency-item.before'); ?>


                <?php if(core()->getCurrentChannel()->currencies->count() > 1): ?>
                    <li class="currency-switcher">
                        <span class="dropdown-toggle">
                            <?php echo e(core()->getCurrentCurrencyCode()); ?>


                            <i class="icon arrow-down-icon"></i>
                        </span>

                        <ul class="dropdown-list currency">
                            <?php $__currentLoopData = core()->getCurrentChannel()->currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <?php if(isset($serachQuery)): ?>
                                        <a href="?<?php echo e($serachQuery); ?>&currency=<?php echo e($currency->code); ?>"><?php echo e($currency->code); ?></a>
                                    <?php else: ?>
                                        <a href="?currency=<?php echo e($currency->code); ?>"><?php echo e($currency->code); ?></a>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php echo view_render_event('bagisto.shop.layout.header.currency-item.after'); ?>



                <?php echo view_render_event('bagisto.shop.layout.header.account-item.before'); ?>


                <li>
                    <span class="dropdown-toggle">
                        <i class="icon account-icon"></i>
                      
                        <span class="name"><?php echo e(__('shop::app.header.account')); ?></span>

                        <i class="icon arrow-down-icon"></i>
                    </span>

                    <?php if(auth()->guard('customer')->guest()): ?>
                        <ul class="dropdown-list account guest">
                            <li>
                                <div>
                                    <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                        <?php echo e(__('shop::app.header.title')); ?>

                                    </label>
                                </div>

                                <div style="margin-top: 5px;">
                                    <span style="font-size: 12px;"><?php echo e(__('shop::app.header.dropdown-text')); ?></span>
                                </div>

                                <div class="button-group">
                                    <a class="btn btn-primary btn-md" href="<?php echo e(route('shop.customer.session.index')); ?>" style="color: #ffffff">
                                        <?php echo e(__('shop::app.header.sign-in')); ?>

                                    </a>

                                    <a class="btn btn-primary btn-md" href="<?php echo e(route('shop.customer.register.index')); ?>" style="float: right; color: #ffffff">
                                        <?php echo e(__('shop::app.header.sign-up')); ?>

                                    </a>
                                </div>
                            </li>

                            <hr>

                            <?php if($showWishlist): ?>
                                <li>
                                    <a href="<?php echo e(route('shop.customer.wishlist.index')); ?>">
                                        <?php echo e(__('shop::app.header.wishlist')); ?>

                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if($showCompare): ?>
                                <li>
                                    <a href="<?php echo e(route('velocity.product.compare')); ?>">
                                        <?php echo e(__('shop::app.customer.compare.text')); ?>

                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if(auth()->guard('customer')->check()): ?>
                        <ul class="dropdown-list account customer">
                            <li>
                                <div>
                                    <label style="color: #9e9e9e; font-weight: 700; text-transform: uppercase; font-size: 15px;">
                                        <?php echo e(auth()->guard('customer')->user()->first_name); ?>

                                    </label>
                                </div>

                                <ul>
                                    <li>
                                        <a href="<?php echo e(route('shop.customer.profile.index')); ?>"><?php echo e(__('shop::app.header.profile')); ?></a>
                                    </li>

                                    <?php if($showWishlist): ?>
                                        <li>
                                            <a href="<?php echo e(route('shop.customer.wishlist.index')); ?>">
                                                <?php echo e(__('shop::app.header.wishlist')); ?>

                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if($showCompare): ?>
                                        <li>
                                            <a href="<?php echo e(route('velocity.customer.product.compare')); ?>">
                                                <?php echo e(__('shop::app.customer.compare.text')); ?>

                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <li>
                                        <form id="customerLogout" action="<?php echo e(route('shop.customer.session.destroy')); ?>" method="POST">
                                            <?php echo csrf_field(); ?>

                                            <?php echo method_field('DELETE'); ?>
                                        </form>

                                        <a
                                            href="<?php echo e(route('shop.customer.session.destroy')); ?>"
                                            onclick="event.preventDefault(); document.getElementById('customerLogout').submit();">
                                            <?php echo e(__('shop::app.header.logout')); ?>

                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                    <?php endif; ?>
                </li>

                <?php echo view_render_event('bagisto.shop.layout.header.account-item.after'); ?>



                <?php echo view_render_event('bagisto.shop.layout.header.cart-item.before'); ?>


                <li class="cart-dropdown-container">

                    <?php echo $__env->make('shop::checkout.cart.mini-cart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

                </li>

                <?php echo view_render_event('bagisto.shop.layout.header.cart-item.after'); ?>


            </ul>

            <span class="menu-box" ><span class="icon icon-menu" id="hammenu"></span>
        </div>
    </div>

    <div class="header-bottom" id="header-bottom">
        <?php echo $__env->make('shop::layouts.header.nav-menu.navmenu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    </div>

    <div class="search-responsive mt-10" id="search-responsive">
        <form role="search" action="<?php echo e(route('shop.search.index')); ?>" method="GET" style="display: inherit;">
            <div class="search-content">
                <button style="background: none; border: none; padding: 0px;">
                    <i class="icon icon-search"></i>
                </button>

                <image-search-component></image-search-component>

                <input type="search" name="term" class="search">
                <i class="icon icon-menu-back right"></i>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/@tensorflow-models/mobilenet" defer></script>

    <script type="text/x-template" id="image-search-component-template">
        <div v-if="image_search_status">
            <label class="image-search-container" :for="'image-search-container-' + _uid">
                <i class="icon camera-icon"></i>

                <input type="file" :id="'image-search-container-' + _uid" ref="image_search_input" v-on:change="uploadImage()"/>

                <img :id="'uploaded-image-url-' +  + _uid" :src="uploaded_image_url" alt="" width="20" height="20" style="display:none" />
            </label>
        </div>
    </script>

    <script>

        Vue.component('image-search-component', {

            template: '#image-search-component-template',

            data: function() {
                return {
                    uploaded_image_url: '',
                    image_search_status: "<?php echo e(core()->getConfigData('general.content.shop.image_search') == '1' ? 'true' : 'false'); ?>" == 'true'
                }
            },

            methods: {
                uploadImage: function() {
                    var imageInput = this.$refs.image_search_input;

                    if (imageInput.files && imageInput.files[0]) {
                        if (imageInput.files[0].type.includes('image/')) {
                            var self = this;

                            if (imageInput.files[0].size <= 2000000) {
                                self.$root.showLoader();

                                var formData = new FormData();

                                formData.append('image', imageInput.files[0]);

                                axios.post("<?php echo e(route('shop.image.search.upload')); ?>", formData, {headers: {'Content-Type': 'multipart/form-data'}})
                                    .then(function(response) {
                                        self.uploaded_image_url = response.data;

                                        var net;

                                        async function app() {
                                            var analysedResult = [];

                                            var queryString = '';

                                            net = await mobilenet.load();

                                            const imgElement = document.getElementById('uploaded-image-url-' +  + self._uid);

                                            try {
                                                const result = await net.classify(imgElement);

                                                result.forEach(function(value) {
                                                    queryString = value.className.split(',');

                                                    if (queryString.length > 1) {
                                                        analysedResult = analysedResult.concat(queryString)
                                                    } else {
                                                        analysedResult.push(queryString[0])
                                                    }
                                                });
                                            } catch (error) {
                                                self.$root.hideLoader();

                                                window.flashMessages = [
                                                    {
                                                        'type': 'alert-error',
                                                        'message': "<?php echo e(__('shop::app.common.error')); ?>"
                                                    }
                                                ];

                                                self.$root.addFlashMessages();
                                            };

                                            localStorage.searched_image_url = self.uploaded_image_url;

                                            queryString = localStorage.searched_terms = analysedResult.join('_');

                                            self.$root.hideLoader();

                                            window.location.href = "<?php echo e(route('shop.search.index')); ?>" + '?term=' + queryString + '&image-search=1';
                                        }

                                        app();
                                    })
                                    .catch(function(error) {
                                        self.$root.hideLoader();

                                        window.flashMessages = [
                                            {
                                                'type': 'alert-error',
                                                'message': "<?php echo e(__('shop::app.common.error')); ?>"
                                            }
                                        ];

                                        self.$root.addFlashMessages();
                                    });
                            } else {

                                imageInput.value = '';

                                        window.flashMessages = [
                                            {
                                                'type': 'alert-error',
                                                'message': "<?php echo e(__('shop::app.common.image-upload-limit')); ?>"
                                            }
                                        ];

                                self.$root.addFlashMessages();

                            }
                        } else {
                            imageInput.value = '';

                            alert('Only images (.jpeg, .jpg, .png, ..) are allowed.');
                        }
                    }
                }
            }
        });

    </script>

    <script>
        $(document).ready(function() {

            $('body').delegate('#search, .icon-menu-close, .icon.icon-menu', 'click', function(e) {
                toggleDropdown(e);
            });

            <?php if(auth()->guard('customer')->check()): ?>
                <?php
                    $compareCount = app('Webkul\Velocity\Repositories\VelocityCustomerCompareProductRepository')
                        ->count([
                            'customer_id' => auth()->guard('customer')->user()->id,
                        ]);
                ?>

                let comparedItems = JSON.parse(localStorage.getItem('compared_product'));
                $('#compare-items-count').html(<?php echo e($compareCount); ?>);
            <?php endif; ?>

            <?php if(auth()->guard('customer')->guest()): ?>
                let comparedItems = JSON.parse(localStorage.getItem('compared_product'));
                $('#compare-items-count').html(comparedItems ? comparedItems.length : 0);
            <?php endif; ?>

            function toggleDropdown(e) {
                var currentElement = $(e.currentTarget);

                if (currentElement.hasClass('icon-search')) {
                    currentElement.removeClass('icon-search');
                    currentElement.addClass('icon-menu-close');
                    $('#hammenu').removeClass('icon-menu-close');
                    $('#hammenu').addClass('icon-menu');
                    $("#search-responsive").css("display", "block");
                    $("#header-bottom").css("display", "none");
                } else if (currentElement.hasClass('icon-menu')) {
                    currentElement.removeClass('icon-menu');
                    currentElement.addClass('icon-menu-close');
                    $('#search').removeClass('icon-menu-close');
                    $('#search').addClass('icon-search');
                    $("#search-responsive").css("display", "none");
                    $("#header-bottom").css("display", "block");
                } else {
                    currentElement.removeClass('icon-menu-close');
                    $("#search-responsive").css("display", "none");
                    $("#header-bottom").css("display", "none");
                    if (currentElement.attr("id") == 'search') {
                        currentElement.addClass('icon-search');
                    } else {
                        currentElement.addClass('icon-menu');
                    }
                }
            }
        });
    </script>
<?php $__env->stopPush(); ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/layouts/header/index.blade.php ENDPATH**/ ?>