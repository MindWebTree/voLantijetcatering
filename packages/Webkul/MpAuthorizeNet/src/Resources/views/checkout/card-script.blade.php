@if (request()->is('checkout/onepage'))
    @php
        if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1') {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_transaction_key');
        } else {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.transaction_key');
        }

        if (auth()->guard('customer')->check()) {
            $IsCustomer = 'true';
        } else {
            $IsCustomer = 'false';
        }

    @endphp

    <!-- JQ is needed to get the multiple document on ready instances and the rest part for the stripe payments integration works swiftly, plain js was creating delay and blocking of events on the ui which was hindering all the required code to be executed -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    @if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1')
        <script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    @else
        <script type="text/javascript" src="https://js.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    @endif

    <form id="paymentForm" method="POST" action="">
        <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        <button type="button" id="authorizePay" style="display:none" class="AcceptUI"
            data-billingAddressOptions='{"show":true, "required":false}' data-apiLoginID="{{ $merchantLoginId }}"
            data-clientKey="{{ core()->getConfigData('sales.paymentmethods.mpauthorizenet.client_key') }}"
            data-acceptUIFormBtnTxt="Submit" data-acceptUIFormHeaderTxt="Card Information"
            data-responseHandler="responseHandler">Pay
        </button>
    </form>

    <script>
        $(document).ready(function() {
            console.log('ddd1sdf1');
            eventBus.$on('after-checkout-payment-section-added', function() {
                // this part in this ready function will be executed on the basis of the event fired from the payment section's mounted hook and it will inject this code in to the window's event bus and the rest part of the code will be fired after that

                savedCardSelectedCard = false;

                $(document).ready(function() {

                    $('input[type="radio"]').not('#saved-cards input[type="radio"]').on('click',
                        function() {
                            console.log('ddd11');
                            if ($(this).attr('id') == 'mpauthorizenet') {
                                $('.mpauthorizenet-add-card').css('display', 'block');
                                $('#checkout-payment-continue-button').attr("disabled",
                                    "disabled");
                                $('.mpauthorizenet-cards-block').css('display', 'block');
                                if ($(
                                        '.authroizenet-card-info > .radio-container > input[type="radio"]')
                                    .is(':checked')) {
                                    radioID = $(
                                        '.authroizenet-card-info > .radio-container > input[type="radio"]:checked'
                                        ).attr('id');
                                    savedCardSelectedId = radioID;
                                    savedCardSelectedCard = true;
                                    $('#checkout-payment-continue-button').removeAttr(
                                        "disabled", "disabled");
                                }
                            } else {
                                $('.mpauthorizenet-add-card').css('display', 'none');
                                $('#checkout-payment-continue-button').removeAttr("disabled",
                                    "disabled");
                                $('.mpauthorizenet-cards-block').css('display', 'none');
                            }

                        });

                    $(document).on("click", "#open-mpauthorizenet-modal", function() {
                        $("#authorizePay").trigger('click');
                    });

                    $(document).on("click", "#delete-card", function() {
                        var result = confirm("Do you want to delete this card ?");
                        if (result) {
                            var deleteId = $(this).data('id');
                            $.ajax({
                                type: 'GET',
                                url: '{{ route('mpauthorizenet.delete.saved.cart') }}',
                                data: {
                                    id: deleteId
                                },

                                success: function(data) {
                                    if (data == 1) {
                                        removeSavedCardNode(deleteId);
                                    }
                                },
                                error: function(data) {
                                    console.log(data);
                                }
                            });
                        }
                    });

                    if ($('input[id="mpauthorizenet"]').is(':checked')) {
                        $('.mpauthorizenet-add-card').css('display', 'block');
                        $('#checkout-payment-continue-button').attr("disabled", "disabled");
                        $('.mpauthorizenet-cards-block').css('display', 'block');
                        if ($('.authroizenet-card-info > .radio-container > input[type="radio"]')
                            .is(':checked')) {
                            radioID = $(
                                '.authroizenet-card-info > .radio-container > input[type="radio"]:checked'
                                ).attr('id');
                            savedCardSelectedId = radioID;
                            savedCardSelectedCard = true;
                            $('#checkout-payment-continue-button').removeAttr("disabled",
                                "disabled");
                            sendToken();
                        }
                    }

                    $(document).on('click',
                        '.authroizenet-card-info > .radio-container > input[type="radio"]',
                        function() {
                            savedCardSelectedId = $(this).attr('id');
                            $('#checkout-payment-continue-button').removeAttr("disabled",
                                "disabled");
                            savedCardSelectedCard = true;
                            sendToken();
                        });

                    $('input[type="radio"]').not('#saved-cards input[type="radio"]').on('click',
                        function() {
                            if ($(this).attr('id') == 'mpauthorizenet') {
                                $('#checkout-payment-continue-button').on('click', function() {
                                    if (savedCardSelectedCard) {
                                        sendToken();
                                    }
                                });
                            }
                        });

                    function sendToken() {
                        _token = "{{ csrf_token() }}";
                        $.ajax({
                            type: "POST",
                            url: "{{ route('mpauthorizenet.get.token') }}",
                            data: {
                                _token: _token,
                                savedCardSelectedId: savedCardSelectedId
                            },
                            success: function(response) {
                                if (response.success == 'true') {
                                    console.log('true');
                                } else {
                                    console.log('false')
                                }
                            }
                        });
                    }

                    function removeSavedCardNode(deleteId) {
                        nodeId = $('.authroizenet-card-info').each(function() {
                            if ($(this).attr('id') == deleteId) {
                                $(this).remove();
                            }
                        });
                    }
                });
            });
        });

        function responseHandler(response) {
            console.log(response);
            if (response.messages.resultCode === "Error") {
                var i = 0;
                while (i < response.messages.message.length) {
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
            var IsCustomer = {{ $IsCustomer }};

            if (IsCustomer) {
                var result = confirm("Do you want to save card for future ? ");

            } else {
                var result = 'guest';
            }

            document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
            document.getElementById("dataValue").value = response.opaqueData.dataValue;

            _token = "{{ csrf_token() }}";

            $.ajax({
                type: "POST",
                url: "{{ route('mpauthorizenet.get.token') }}",
                data: {
                    _token: _token,
                    response: response,
                    result: result
                },
                success: function(response) {
                    if (response.success == 'true') {
                        $('#checkout-payment-continue-button').removeAttr("disabled", "disabled");
                        $('.mpauthorizenet-cards-block').css('display', 'none');
                        $('.mpauthorizenet-add-card').css('display', 'none');
                    } else {
                        $('#checkout-payment-continue-button').attr("disabled", "disabled");
                    }
                }
            });
        }
    </script>


@endif
