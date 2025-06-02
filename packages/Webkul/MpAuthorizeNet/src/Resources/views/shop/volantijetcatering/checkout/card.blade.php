@if($payment['method'] == "mpauthorizenet")
<div class="">
    @include('mpauthorizenet::shop.components.add-card')
    @include('mpauthorizenet::shop.components.saved-cards')
    {{-- sandeep add div for append credit form --}}
    <div id="customAcceptUIContainer" class="p-3 mr-2 mr-lg-0 mr-md-0"></div>
</div>
@endif
