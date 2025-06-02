<div class="input-group">
    <form
        method="GET"
        role="search"
        id="search-form"
        action="<?php echo e(route('velocity.search.index')); ?>">
        <div
            class="btn-toolbar full-width search-form"
            role="toolbar">

            <searchbar-component>
                <template v-slot:image-search>
                    <image-search-component
                        status="<?php echo e(core()->getConfigData('general.content.shop.image_search') == '1' ? 'true' : 'false'); ?>"
                        upload-src="<?php echo e(route('shop.image.search.upload')); ?>"
                        view-src="<?php echo e(route('shop.search.index')); ?>"
                        common-error="<?php echo e(__('shop::app.common.error')); ?>"
                        size-limit-error="<?php echo e(__('shop::app.common.image-upload-limit')); ?>">
                    </image-search-component>
                </template>
            </searchbar-component>

        </div>
    </form>
</div><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Velocity/src/Providers/../Resources/views/shop/layouts/particals/search-bar.blade.php ENDPATH**/ ?>