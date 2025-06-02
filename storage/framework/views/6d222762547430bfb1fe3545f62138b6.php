<?php
    // $cards = collect();
    if(auth()->guard('customer')->check()) {
        $customer_id = auth()->guard('customer')->user()->id;
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customer_id]);
    }
?>

<?php if(isset($cards) && !$cards->isEmpty()): ?>
<div class="mpauthorizenet-add-card" style="padding-left:15px;padding-top:25px; padding-right:10px; margin-top:-15px; margin-bottom:8px;" id="saved-card-heading">
    <span class="control-info mb-5 mt-5"> Choose a saved card 
        
        to proceed.
    </span>
</div>

<?php endif; ?><?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/MpAuthorizeNet/src/Providers/../Resources/views/shop/volantijetcatering/components/add-card.blade.php ENDPATH**/ ?>