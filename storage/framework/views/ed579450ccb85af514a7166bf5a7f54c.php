<div class="footer">
    <div class="footer-content">
        <div class="footer-list-container">

            <?php
                $categories = [];

                foreach (app('Webkul\Category\Repositories\CategoryRepository')->getVisibleCategoryTree(core()->getCurrentChannel()->root_category_id) as $category){
                    if ($category->slug)
                        array_push($categories, $category);
                }
            ?>

            <?php if(count($categories)): ?>
                <div class="list-container">
                    <span class="list-heading"><?php echo e(__('admin::app.layouts.categories')); ?></span>

                    <ul class="list-group">
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li>
                                <a href="<?php echo e(route('shop.productOrCategory.index', $category->slug)); ?>"><?php echo e($category->name); ?></a>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php echo Blade::render(core()->getCurrentChannel()->footer_content); ?>


            <div class="list-container">
                <?php if(core()->getConfigData('customer.settings.newsletter.subscription')): ?>
                    <label class="list-heading" for="subscribe-field"><?php echo e(__('shop::app.footer.subscribe-newsletter')); ?></label>
                    <div class="form-container">
                        <form action="<?php echo e(route('shop.subscribe')); ?>">
                            <div class="control-group" :class="[errors.has('subscriber_email') ? 'has-error' : '']">
                                <input type="email" id="subscribe-field" class="control subscribe-field" name="subscriber_email" placeholder="Email Address" required><br/>

                                <button class="btn btn-md btn-primary"><?php echo e(__('shop::app.subscription.subscribe')); ?></button>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>

                <?php
                    $term = request()->input('term');

                    if (! is_null($term)) {
                        $serachQuery = 'term='.request()->input('term');
                    }
                ?>

                <label class="list-heading" for="locale-switcher"><?php echo e(__('shop::app.footer.locale')); ?></label>
                <div class="form-container">
                    <div class="control-group">
                        <select class="control locale-switcher" id="locale-switcher" onchange="window.location.href = this.value" <?php if(count(core()->getCurrentChannel()->locales) == 1): ?> disabled="disabled" <?php endif; ?>>

                            <?php $__currentLoopData = core()->getCurrentChannel()->locales()->orderBy('name')->get(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(isset($serachQuery)): ?>
                                    <option value="?<?php echo e($serachQuery); ?>&locale=<?php echo e($locale->code); ?>" <?php echo e($locale->code == app()->getLocale() ? 'selected' : ''); ?>><?php echo e($locale->name); ?></option>
                                <?php else: ?>
                                    <option value="?locale=<?php echo e($locale->code); ?>" <?php echo e($locale->code == app()->getLocale() ? 'selected' : ''); ?>><?php echo e($locale->name); ?></option>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        </select>
                    </div>
                </div>

                <div class="currency">
                    <label class="list-heading" for="currency-switcher"><?php echo e(__('shop::app.footer.currency')); ?></label>
                    <div class="form-container">
                        <div class="control-group">
                            <select class="control locale-switcher" id="currency-switcher" onchange="window.location.href = this.value">

                                <?php $__currentLoopData = core()->getCurrentChannel()->currencies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $currency): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(isset($serachQuery)): ?>
                                        <option value="?<?php echo e($serachQuery); ?>&currency=<?php echo e($currency->code); ?>" <?php echo e($currency->code == core()->getCurrentCurrencyCode() ? 'selected' : ''); ?>><?php echo e($currency->code); ?></option>
                                    <?php else: ?>
                                        <option value="?currency=<?php echo e($currency->code); ?>" <?php echo e($currency->code == core()->getCurrentCurrencyCode() ? 'selected' : ''); ?>><?php echo e($currency->code); ?></option>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/Shop/src/Providers/../Resources/views/layouts/footer/footer.blade.php ENDPATH**/ ?>