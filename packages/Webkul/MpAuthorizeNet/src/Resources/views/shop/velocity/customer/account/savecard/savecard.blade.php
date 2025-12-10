@extends('shop::customers.account.index')

@section('page_title')
    {{ __('mpauthorizenet::app.customer.account.savecard.index.page-title') }}
@endsection

@section('page-detail-wrapper')        
        <div class="account-head mb-0">
            <span class="back-icon">
                <a href="{{ route('customer.account.index') }}">
                    <i class="icon icon-menu-back"></i>
                </a>
            </span>
            <span class="account-heading">
                {{ __('mpauthorizenet::app.customer.account.savecard.index.title') }}
            </span>

            <span class="account-action">
                <a id="add-new-card" style="cursor: pointer;" class="theme-btn light unset pull-right">
                    {{ __('mpauthorizenet::app.customer.account.savecard.index.add') }}
                </a>
            </span>
        </div>

        <div class="account-items-list">
            <div class="table" style="margin-top:20px;">
                    <table class="table">
                        <thead style="text-align: center;">
                            <tr>
                                <th>{{ __('mpauthorizenet::app.customer.account.savecard.index.isdefault') }}</th>
    
                                <th>{{ __('mpauthorizenet::app.customer.account.savecard.index.id') }}</th>
            
                                <th>{{ __('mpauthorizenet::app.customer.account.savecard.index.card-number') }}</th>
            
                                <th>{{ __('mpauthorizenet::app.customer.account.savecard.index.action') }}</th>
            
                            </tr>
                        </thead>
            
                        <tbody style="text-align:center;" class="list-order">
                            <tr></tr>
                            @foreach($cardDetail as $key =>$cardDetails)
                            <tr id="row{{$cardDetails->id}}">
                                <td>
                                    <span>
                                        <input type="radio" class="isdefault" id="{{$cardDetails->id}}" name="radio" @if($cardDetails->is_default == '1') checked="checked" @endif>
                                        <label class="radio-view" for="{{$cardDetails->id}}"></label>
                                    </span>
                                </td>
                                <td>{{$cardDetails->id}}</td>
                                <td>{{$cardDetails->last_four}}</td>
                                <td><span class="rango-delete fs20 delete" id="{{$cardDetails->id}}" style="cursor:pointer;"></span></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
              </div>
        </div>
@endsection
@push('scripts')

@php 
if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1') {
    $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_api_login_ID');
    $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_transaction_key');
} else {
    $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.api_login_ID');
    $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.transaction_key');
}
@endphp 

        @if(core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1')
        <script type="text/javascript" src="{{asset('vendor/webkul/mpauthorizenet/assets/js/AcceptUITest.js')}}" charset="utf-8"></script>
        @else
        <script type="text/javascript" src="{{asset('vendor/webkul/mpauthorizenet/assets/js/AcceptUI.js')}}" charset="utf-8"></script>
        @endif
        <form id="paymentForm" method="POST" action="">
            <input type="hidden" name="dataValue" id="dataValue" />
            <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
            <button type="button" id="authorizePay" style="display:none"
                class="AcceptUI"
                data-billingAddressOptions='{"show":true, "required":false}' 
                data-apiLoginID="{{$merchantLoginId}}" 
                data-clientKey="{{core()->getConfigData('sales.paymentmethods.mpauthorizenet.client_key')}}"
                data-acceptUIFormBtnTxt="Submit" 
                data-acceptUIFormHeaderTxt="Card Information" 
                data-responseHandler="responseHandler">Pay
            </button>
        </form>

<script>
    $(document).on("click","#add-new-card",function() {
        $("#authorizePay").trigger('click');
    });

    $(document).on("click",".isdefault",function(){
        id = this.id;
        $.ajax({
            type: "GET",
            url: "{{route('mpauthorizenet.account.make.card.default')}}",
            data: {id:this.id},
            success: function( response ) {
                if (response.success == 'true') {
                    console.log('updated');
                } else {
                    console.log('not updated !');
                }
            }
        });
    });

    $(document).on("click",'.delete',function(){
        var result = confirm("Are you sure want to delete this card ?");
        if (result) {
            var row = "#"+'row'+this.id;
            $.ajax({
                type: "GET",
                url: "{{route('mpauthorizenet.delete.saved.cart')}}", 
                data: {id:this.id},
                success: function( response ) {
                    if (response == '1') {
                        $(row).css('display', 'none');
                    }
                }
            });
        }
    });

    function responseHandler(response) {
        
        if (response.messages.resultCode === "Error") {
            var i = 0;
            while (i < response.messages.message.length) {
                alert(response.messages.message[i].text);
                console.log(
                    response.messages.message[i].code + ": " +
                    response.messages.message[i].text
                );
                i = i + 1;
            }
        } else {
            paymentFormUpdate(response);
        }
    }

    function paymentFormUpdate(response) {
        
        document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
        document.getElementById("dataValue").value = response.opaqueData.dataValue;

        _token = "{{csrf_token()}}";

        $.ajax({
            type: "POST",
            url: "{{route('mpauthorizenet.account.store.card')}}",
            data: {_token:_token,response:response},
            success: function( response ) {
                if (response.cardExist != 'true') { 
                    var $tr = $('<tr id="row'+response.id+'">');
                    $tr.append($('<td/>').html('<span><input type="radio" id="'+response.id+'" name="radio" class="isdefault"><label class="radio-view" for="'+response.id+'"></label> </span>'));
                    $tr.append($('<td/>').html(response.id));
                    $tr.append($('<td/>').html(response.last_four));
                    $tr.append($('<td/>').html('<span class="rango-delete fs20 delete" id='+response.id+' style="cursor:pointer;"></span>'));
                    $('.list-order tr:first').before($tr);
                } else {
                    alert('Card already exist !');
                }
            }
        });
    }

</script>
@endpush
