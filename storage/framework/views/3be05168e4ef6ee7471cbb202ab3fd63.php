
<header-component
    items-count-src="<?php echo e(route('velocity.product.item-count')); ?>"
></header-component>

<?php
$showCompare = (bool) core()->getConfigData('general.content.shop.compare_option');

$showWishlist = (bool) core()->getConfigData('general.content.shop.wishlist_option');
?>

<?php $__env->startPush('scripts'); ?>
    <script type="text/x-template" id='header-component-template'>
        <div>
            <wishlist-component-with-badge
                is-customer="<?php echo e(auth()->guard('customer')->check() ? 'true' : 'false'); ?>"
                is-text="<?php echo e(isset($isText) && $isText ? 'true' : 'false'); ?>"
                src="<?php echo e(route('shop.customer.wishlist.index')); ?>"
                v-if="<?php echo e($showWishlist ? 'true' : 'false'); ?>"
                :wishlist-item-count='wishlistCount'>
            </wishlist-component-with-badge>

            <compare-component-with-badge
                is-customer="<?php echo e(auth()->guard('customer')->check() ? 'true' : 'false'); ?>"
                is-text="<?php echo e(isset($isText) && $isText ? 'true' : 'false'); ?>"
                src="<?php echo e(auth()->guard('customer')->check() ? route('velocity.customer.product.compare') : route('velocity.product.compare')); ?>"
                v-if="<?php echo e($showCompare ? 'true' : 'false'); ?>"
                :compare-item-count='compareCount'>
            </compare-component-with-badge>
            
            <?php echo $__env->make('shop::checkout.cart.mini-cart', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        </div>
    </script>

    <script>
        Vue.component('header-component',{
            template: '#header-component-template',

            props: ['itemsCountSrc'],

            data: function() {
                return {
                    isCustomer: "<?php echo e(auth()->guard('customer')->check() ? true : false); ?>",
                    compareCount: 0,
                    wishlistCount: 0,
                }
            },

            watch: {
                '$root.headerItemsCount': function () {
                    this.updateHeaderItemsCount();
                },
            },

            mounted () {
                this.updateHeaderItemsCount();
            },

            methods: {
                updateHeaderItemsCount: async function () {
                    if (this.isCustomer != true) {
                        let comparedItems = this.getStorageValue('compared_product');

                        if (comparedItems) {
                            this.compareCount = comparedItems.length;
                        }
                    } else {
                        const response = await fetch(this.itemsCountSrc);
                        const data = await response.json();

                        this.compareCount = data.compareProductsCount;
                        this.wishlistCount = data.wishlistedProductsCount;
                    }
                },
            },
        })
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Velocity/src/Providers/../Resources/views/shop/layouts/particals/header-compts.blade.php ENDPATH**/ ?>