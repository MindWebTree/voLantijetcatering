<?php if($payment['method'] == "mpauthorizenet"): ?>
<div class="">
    <?php echo $__env->make('mpauthorizenet::shop.components.add-card', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('mpauthorizenet::shop.components.saved-cards', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    
    <div id="customAcceptUIContainer" class="p-3 mr-2 mr-lg-0 mr-md-0"></div>
</div>
<?php endif; ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/MpAuthorizeNet/src/Providers/../Resources/views/shop/volantijetcatering/checkout/card.blade.php ENDPATH**/ ?>