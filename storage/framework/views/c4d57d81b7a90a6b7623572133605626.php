<?php if(request()->is('onepage/checkout')): ?>

    <?php
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

    ?>

    <!-- JQ is needed to get the multiple document on ready instances and the rest part for the stripe payments integration works swiftly, plain js was creating delay and blocking of events on the ui which was hindering all the required code to be executed -->
    <script src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
    <?php if(core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1'): ?>
        <script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    <?php else: ?>
        <script type="text/javascript" src="https://js.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    <?php endif; ?>

    <form id="paymentForm" method="POST" action="">
        <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        <button type="button" id="authorizePay" style="display:none" class="AcceptUI"
            data-billingAddressOptions='{"show":true, "required":false}' data-apiLoginID="<?php echo e($merchantLoginId); ?>"
            data-clientKey="<?php echo e(core()->getConfigData('sales.paymentmethods.mpauthorizenet.client_key')); ?>"
            data-acceptUIFormBtnTxt="Submit" data-acceptUIFormHeaderTxt="Card Information"
            data-responseHandler="responseHandler">Pay
        </button>
    </form>

    <script>


        $(document).ready(function() {

            // setTimeout(() => {
            //     $('#checkout-place-order-button').attr('disabled', 'disabled');
            // }, 2000);

            eventBus.$on('after-checkout-payment-section-added', function() {
                // this part in this ready function will be executed on the basis of the event fired from the payment section's mounted hook and it will inject this code in to the window's event bus and the rest part of the code will be fired after that

                savedCardSelectedCard = false;

                $(document).ready(function() {

                    // sandeep add payment-saved class
                    $('.payment-saved input[type="radio"]').not('#saved-cards input[type="radio"]').on('click',
                        function() {
                            if ($(this).attr('id') == 'mpauthorizenet') {
                                $('.mpauthorizenet-add-card').css('display', 'block');
                                $('#checkout-payment-continue-button').attr("disabled",
                                    "disabled");
                                $('.mpauthorizenet-cards-block').css('display', 'block');
                                if ($(
                                        '.authroizenet-card-info > .radio-container > input[type="radio"]'
                                    )
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
                                console.log('not show credit card');
                                $('.mpauthorizenet-add-card').css('display', 'none');
                                $('#checkout-payment-continue-button').removeAttr("disabled",
                                    "disabled");
                                $('.mpauthorizenet-cards-block').css('display', 'none');
                            }

                        });



                    // $('.payment-unsave input[type="radio"]').not('#saved-cards input[type="radio"]').on('click',  function() {
                    //     $("#open-mpauthorizenet-modal").trigger('click');
                    // });

                    // $(document).on("click", "#open-mpauthorizenet-modal", function() {
                    // $("#authorizePay").trigger('click');
                    // console.log('samdesdfhd click');

                    // // sandeep add code
                    // $('.authroizenet-card-info > .radio-container > input[type="radio"]')
                    // .prop('checked', false);
                    // // $('#checkout-place-order-button').prop('disabled', true)
                    // paymentsaved = false;

                    // });

                    $(document).on("click", "#delete-card", function() {
                        var card_id = $(this).data('id');
                        $('.payment-delete-model-btn').click();
                        $('body').on('click', '#payment_delete_model .accept', function() {
                            delete_card(card_id);
                            $('.payment-delete-model-btn').click();
                        })
                        $('body').on('click', '#payment_delete_model .cancel', function() {
                            $('.payment-delete-model-btn').click();
                        })


                        //var result = confirm("Do you want to delete this card ?");

                        function delete_card(deleteId) {

                            $.ajax({
                                type: 'GET',
                                url: '<?php echo e(route('mpauthorizenet.delete.saved.cart')); ?>',
                                data: {
                                    id: deleteId
                                },

                                success: function(data) {

                                    // sandeep add code
                                    paymentsaved = false;
                                 // sandeep add new code 
                                $('#saved-card-heading').hide();
                                // $('#saved-card-heading').html(`
                                //     <span class="control-info mb-5 mt-5">
                                //         Please <a id="open-mpauthorizenet-modal" style="color: rgb(0, 65, 255) !important; cursor: pointer;">
                                //         Add new card</a> to proceed.
                                //     </span>
                                // `);

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
                            // hideAuthorizeNetUI();
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

                        _token = "<?php echo e(csrf_token()); ?>";
                        $.ajax({
                            type: "POST",
                            url: "<?php echo e(route('mpauthorizenet.get.token')); ?>",
                            data: {
                                _token: _token,
                                savedCardSelectedId: savedCardSelectedId
                            },
                            success: function(response) {
                                console.log('send token response');
                                console.log(checkPlacedOrder);
                                var address_checkbox = $(
                                    '.address-container input[type="radio"]');
                                var checked = address_checkbox.is(':checked');
                                paymentsaved = true;
                                var acknowledge_checkbox = $('#acknowledge_checkbox')
                                    .is(':checked');
                                var fbo_name = $('#AirportFbo_Name').text();
                                console.log('fbo_name',fbo_name);
                                if (response.success == 'true' && checked &&
                                    acknowledge_checkbox && fbo_name != "") {
                                    // $('#checkout-place-order-button').removeAttr(
                                    //     'disabled');
                                } else {
                                    // $('#checkout-place-order-button').attr('disabled',
                                    //     'disabled');
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
            console.log('user payment error message');
            // sandeep || add error message
            if (response.messages.resultCode === "Error") {
                var i = 0;
                while (i < response.messages.message.length) {
                    $('.card_erorr_message').removeClass('d-none');
                    $('.payment_error_message').text(response.messages.message[i].text); 
                    $('html, body').animate({
                        scrollTop: $('.card_erorr_message').offset().top - 100 
                    }, 500);  
                    setTimeout(function(){
                        $('.payment_error_message').text('');
                        $('.card_erorr_message').addClass('d-none');
                    },4000); 
                    console.log(
                        response.messages.message[i].code + ": " +
                        response.messages.message[i].text
                    );
                    i = i + 1;
                }
            } else {
                console.log('hide credit card form');
                // hideAuthorizeNetUI();
                paymentFormUpdate(response);
                $('.card_success_message').removeClass('d-none').text('Card submitted successfully!');
                $('html, body').animate({
                        scrollTop: $('.card_success_message').offset().top - 100 
                    }, 500);
                setTimeout(function(){
                    $('.card_success_message').addClass('d-none').text('');
                }, 4000);
            }
        }

        function paymentFormUpdate(response) {
            console.log('payment response');
            var IsCustomer = <?php echo e($IsCustomer); ?>;
             //var result = confirm("Do you want to save card for future ? ");
            if (IsCustomer) {
                $('.payment-model-btn').click();
                let hasClicked = false;
                $('body').on('click', '#payment_model .accept', function() {
                    console.log('accept payment');
                    hasClicked = true;
                    result = true;
                    save_card(result);
                    $('.payment-model-btn').click();
                })
                $('body').on('click', '#payment_model .cancel', function() {
                    console.log('cancel payment');
                    hasClicked = true;
                    result = false;
                    save_card(result);
                    $('.payment-model-btn').click();
                })

                console.log('hasClicked',hasClicked);
            // sandeep add code for click save payment close button and body  
            $(document).off('click', 'body, .save-payment-close').on('click', 'body, .save-payment-close', function (event) {
                if (!hasClicked) {
                    console.log('close save payment popop');
                    hasClicked = true;
                    result = false;
                    save_card(result);
                    $(this).off('click');
                }
            });
     
                // sandeep add off click code 
                // $('body').off('click', '#payment_model .accept').on('click', '#payment_model .accept', function() {
                //     console.log('accept payment');
                //     result = true;
                //     save_card(result);
                //     $('.payment-model-btn').click();
                // });

                // $('body').off('click', '#payment_model .cancel').on('click', '#payment_model .cancel', function() {
                //     console.log('cancel payment');
                //     result = false;
                //     save_card(result);
                //     $('.payment-model-btn').click();
                // });



            } else {
                var result = 'guest';
                save_card(result);
            }

            document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
            document.getElementById("dataValue").value = response.opaqueData.dataValue;

            function save_card($result) {
                console.log('result',result);
                _token = "<?php echo e(csrf_token()); ?>";
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(route('mpauthorizenet.get.token')); ?>",
                    data: {
                        _token: _token,
                        response: response,
                        result: result
                    },
                    success: function(response) {
                        if (response.success == 'true') {
                            console.log('save card success response');
                            paymentsaved = true;
                            var acknowledge_checkbox = $('#acknowledge_checkbox').is(':checked');
                            $('#checkout-payment-continue-button').removeAttr("disabled", "disabled");
                            // $('.mpauthorizenet-cards-block').css('display', 'none');
                            // $('.mpauthorizenet-add-card').css('display', 'none');
                            // sandeep add
                            var fbo_name = $('#airport_fbo_details').find('#AirportFbo_Name').text();
                            console.log($('#airport-fbo-input').val(),'this is the clg');
                            var address_checkbox = $(
                                '.address-container input[type="radio"]');
                            var checked = address_checkbox.is(':checked');
                            if (checked && acknowledge_checkbox && fbo_name != '') {
                                // $('#checkout-place-order-button').removeAttr('disabled');
                                // sandeep add code for click on place order button 
                                 if(checkPlacedOrder == "placed_order"){
                                  $('#checkout-place-order-button'). trigger('click');
                                }
                            }
                        } else {
                            $('#checkout-payment-continue-button').attr("disabled", "disabled");
                        }
                    }
                });
            }
        }
    </script>
<?php elseif(
    (isset($orderId) && request()->is('admin/paymentprofile/customers/orders/view/' . $orderId)) ||
        (isset($customerId) &&
            request()->is('CheckoutCustomOrders*') &&
            request()->input('orderid') == $orderId &&
            request()->input('customerid') == $customerId)): ?>
    
    <?php
        if (core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1') {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.test_transaction_key');
        } else {
            $merchantLoginId = core()->getConfigData('sales.paymentmethods.mpauthorizenet.api_login_ID');
            $merchantAuthentication = core()->getConfigData('sales.paymentmethods.mpauthorizenet.transaction_key');
        }

    ?>


    

    <?php if(core()->getConfigData('sales.paymentmethods.mpauthorizenet.debug') == '1'): ?>
        <script type="text/javascript" src="https://jstest.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    <?php else: ?>
        <script type="text/javascript" src="https://js.authorize.net/v3/AcceptUI.js" charset="utf-8"></script>
    <?php endif; ?>

    <form id="paymentForm" method="POST" action="">
        <input type="hidden" name="dataValue" id="dataValue" />
        <input type="hidden" name="dataDescriptor" id="dataDescriptor" />
        <button type="button" id="authorizePay" style="display:none" class="AcceptUI"
            data-billingAddressOptions='{"show":true, "required":false}' data-apiLoginID="<?php echo e($merchantLoginId); ?>"
            data-clientKey="<?php echo e(core()->getConfigData('sales.paymentmethods.mpauthorizenet.client_key')); ?>"
            data-acceptUIFormBtnTxt="Submit" data-acceptUIFormHeaderTxt="Card Information"
            data-responseHandler="responseHandler">Pay
        </button>
    </form>

    <script>
        $(document).ready(function() {


            // $(document).on("click", "#open-mpauthorizenet-modal", function() {
            //     console.log('jhsavjdh')
            //     $("#authorizePay").trigger('click');

            // });
            // eventBus.$on('after-checkout-payment-section-added', function() {
            // this part in this ready function will be executed on the basis of the event fired from the payment section's mounted hook and it will inject this code in to the window's event bus and the rest part of the code will be fired after that

            savedCardSelectedCard = false;
            $(document).ready(function() {

                $(document).on("click", "#open-mpauthorizenet-modal", function() {
                    console.log('sandeep click');
                    $('body').find('#authorizePay').trigger('click');
                    $('#AcceptUIContainer').addClass('show');
                    $('#AcceptUIBackground').addClass('show');

                    // sandeep add code
                    $('.authroizenet-card-info > .radio-container > input[type="radio"]')
                    .prop('checked', false);
                    $('#collect_payment').prop('disabled', true)
                    $('#collect_payment').addClass('pay_disable');
                    paymentsaved = false;
                });

                $(document).on("click", "#delete-card", function() {
                    var card_id = $(this).data('id');
                    console.log(card_id, '111111111111');
                    $('.payment-delete-model-btn').click();
                    $('body').on('click', '#payment_delete_model .accept', function() {
                        console.log(card_id, 'testttt');
                        delete_card(card_id);
                        $('.payment-delete-model-btn').click();
                    })
                    $('body').on('click', '#payment_delete_model .cancel', function() {
                        $('.payment-delete-model-btn').click();
                    })

                    //var result = confirm("Do you want to delete this card ?");

                    function delete_card(deleteId) {

                        $.ajax({
                            type: 'GET',
                            url: "<?php echo e(route('mpauthorizenet.delete.saved.cart')); ?>",
                            data: {
                                id: deleteId,
                                customerId: customerId,
                            },

                            success: function(data) {
                                if (data == 1) {
                                    removeSavedCardNode(deleteId);
                                    window.location.href =
                                        "<?php echo e(route('customer.payment.success.message')); ?>"
                                }


                            },
                            error: function(data) {
                                console.log(data);
                            }
                        });
                    }
                });
                var currentRoute = window.location.pathname;
                var currentPath = currentRoute.substring(currentRoute.lastIndexOf('/') + 1);

                if (currentRoute === '/admin/paymentprofile/customers/orders/view/' + order_id ||
                    currentPath === 'CheckoutCustomOrders') {
                    $('.mpauthorizenet-add-card').css('display', 'block');
                    $('.mpauthorizenet-cards-block').css('display', 'block');
                    if ($('.authroizenet-card-info > .radio-container > input[type="radio"]')
                        .is(':checked')) {
                        radioID = $(
                            '.authroizenet-card-info > .radio-container > input[type="radio"]:checked'
                        ).attr('id');
                        savedCardSelectedId = radioID;
                        savedCardSelectedCard = true;
                        console.log('1');
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
                        console.log(savedCardSelectedId, 'id')
                        console.log('2')
                        sendToken();
                        console.log('3')
                    });

                // $('input[type="radio"]').not('#saved-cards input[type="radio"]').on('click',
                //     function() {
                //         if ($(this).attr('id') == 'mpauthorizenet') {
                //             $('#checkout-payment-continue-button').on('click', function() {
                //                 if (savedCardSelectedCard) {
                //                     sendToken();

                //                 }
                //             });
                //         }
                //     });

                function sendToken() {
                    _token = "<?php echo e(csrf_token()); ?>";
                    $.ajax({
                        type: "POST",
                        url: "<?php echo e(route('mpauthorizenet.get.token')); ?>",
                        data: {
                            _token: _token,
                            savedCardSelectedId: savedCardSelectedId,
                            order_id: order_id,
                            customerId: customerId
                        },
                        success: function(response) {
                            console.log(response)
                            if (response.success == 'true') {
                                $('#collect_payment').prop('disabled', false);
                                $('#collect_payment').removeClass('pay_disable');
                                console.log('true');
                            } else {
                                console.log('false');
                                $('#collect_payment').prop('disabled', true)
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



            // });
        });

        function responseHandler(response) {
            console.log('admin payment erorr messsahe');
            if (response.messages.resultCode === "Error") {
                var i = 0;
                while (i < response.messages.message.length) {
                    // sandeep add payment error message
                    $('.card_erorr_message').removeClass('d-none');
                    $('.payment_error_message').text(response.messages.message[i].text);   
                    setTimeout(function(){
                        $('.payment_error_message').text('');
                        $('.card_erorr_message').addClass('d-none');
                    },4000); 
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

        var order_id = $('#order_order_id').val();
        var admin_id = $('#admin_id').val();
        var customerId = $('#order_customer_id').val();
        //console.log(admin_id,'admin');

        function paymentFormUpdate(response) {
            var result = 'guest';
            save_card(result);
            console.log(response);

            document.getElementById("dataDescriptor").value = response.opaqueData.dataDescriptor;
            document.getElementById("dataValue").value = response.opaqueData.dataValue;

            function save_card($result) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo e(route('mpauthorizenet.get.token')); ?>",
                    data: {
                        _token: "<?php echo e(csrf_token()); ?>",
                        response: response,
                        order_id: order_id
                    },
                    success: function(response) {
                        console.log(response)
                        if (response.success == 'true') {
                            $('#collect_payment').prop('disabled', false);
                            $('#collect_payment').removeClass('pay_disable');
                        } else {
                            $('#collect_payment').prop('disabled', true);
                        }
                    }
                });
            }
        }

        $(document).on('click', '#collect_payment', function() {
            pay();
            $('#collect_payment').prop({
                'disabled': true,
                'style': 'cursor: not-allowed'
            });

        });

        function pay() {

            _token = "<?php echo e(csrf_token()); ?>";
            $.ajax({
                type: "GET",
                url: "<?php echo e(route('mpauthorizenet.make.payment')); ?>",
                data: {
                    _token: _token,
                    order_id: order_id,
                    customerId: customerId
                },
                success: function(response) {

                    if (response == true) {

                        if (admin_id) {
                            console.log('success');
                            window.location.href = "<?php echo e(route('customer.payment.success.message')); ?>";
                        } else {

                            location.reload();
                        }
                    } else {
                        window.location.href = "<?php echo e(route('customer.payment.error.message')); ?>";
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    // Handle the error as needed (show a message, log it, etc.)
                }
            });
        }
    </script>
    

<?php endif; ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/packages/Webkul/MpAuthorizeNet/src/Providers/../Resources/views/shop/volantijetcatering/checkout/card-script.blade.php ENDPATH**/ ?>