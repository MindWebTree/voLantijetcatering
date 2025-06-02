<?php
    $cards = collect();
    if (auth()->guard('customer')->check()) {
        $customer_id = auth()->guard('customer')->user()->id;
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customer_id]);

    } elseif (isset($customerId) && $customerId) {
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customerId]);
    }

?>

<?php if(auth()->guard('customer')->check() || isset($customerId) && $customerId): ?>
    <div class="mpauthorizenet-cards-block" id="saved-cards" style="padding-left: 15px; padding-bottom: 10px; margin-bottom:10px;">
        <div class="control-info mt-10 mb-10">
            <?php $__currentLoopData = $cards; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $card): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="authroizenet-card-info" id="<?php echo e($card->id); ?>">
                    <label class="radio-container">
                        <input type="radio" name="saved-card" class="saved-card-list" id="<?php echo e($card->id); ?>"
                            value="<?php echo e($card->id); ?>" <?php if($card->is_default == '1'): ?> checked="checked" <?php endif; ?>>
                        <span class="checkmark"></span>
                    </label>
                    <span class="icon currency-card-icon"></span>
                    <span class="card-last-four" style="margin-left:16px;"> <?php echo e($card->last_four); ?></span>
                    <a id="delete-card" style="color: #ff0000 !important; cursor: pointer;"
                       data-id="<?php echo e($card->id); ?>"><?php echo e(__('mpauthorizenet::app.delete')); ?></a>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

<?php endif; ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/MpAuthorizeNet/src/Providers/../Resources/views/shop/volantijetcatering/components/saved-cards.blade.php ENDPATH**/ ?>