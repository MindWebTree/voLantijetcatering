@php
    // $cards = collect();
    if(auth()->guard('customer')->check()) {
        $customer_id = auth()->guard('customer')->user()->id;
        $cards = app('Webkul\MpAuthorizeNet\Repositories\MpAuthorizeNetRepository')->findWhere(['customers_id' => $customer_id]);
    }
@endphp

@if (isset($cards) && !$cards->isEmpty())
<div class="mpauthorizenet-add-card" style="padding-left:15px;padding-top:25px; padding-right:10px; margin-top:-15px; margin-bottom:8px;" id="saved-card-heading">
    <span class="control-info mb-5 mt-5"> Choose a saved card 
        {{-- <a id="open-mpauthorizenet-modal" style="color: #0041FF !important; cursor: pointer;">
            {{ __('mpauthorizenet::app.add-card') }}
        </a> --}}
        to proceed.
    </span>
</div>
{{-- @else
<div class="mpauthorizenet-add-card" style="padding-left:50px; padding-right:10px; margin-top:-15px; margin-bottom:8px; display:none" id="unsave-card-heading">
    <span class="control-info mb-5 mt-5">Please
        <a id="open-mpauthorizenet-modal" style="color: #0041FF !important; cursor: pointer;">
            {{ __('mpauthorizenet::app.add-card') }}</a> to proceed.
    </span>
</div> --}}
@endif