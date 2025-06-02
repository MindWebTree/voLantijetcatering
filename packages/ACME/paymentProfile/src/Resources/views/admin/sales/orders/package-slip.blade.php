@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.sales.orders.view-title', ['order_id' => $order->increment_id]) }}

@stop

@section('head')
    @include('paymentprofile::admin.links')
@stop

@section('content-wrapper')
    <div class="content full-page order_view_fullpage">
        <div class="page-header order_view_header">

            <div class="page-title order_view_title">
                <h1 class="text-secondary">
                    {!! view_render_event('sales.order.title.before', ['order' => $order]) !!}

                    <i class="icon angle-left-icon back-link"
                        onclick="window.location = '{{ route('admin.sales.order.index') }}'"></i>

                    Order ID: <strong id="orderID" class="text-dark">{{ $order->increment_id }}</strong>
                    {!! view_render_event('sales.order.title.after', ['order' => $order]) !!}
                </h1>
            </div>

            <div class="page-action order_view_status" style="margin-top: 0px !important">
                <p class="text-secondary">Order Status: <span
                        class="text-uppercase {{ $order->status }}">{{ $order->status }}</span></p>

                {{-- @dd($states) --}}
            </div>
        </div>
        <div class="content full-page order_view_fullpage px-2 px-lg-5 px-md-5">
            <div class="page-content">
                <div class="row order__view__content m-0" bis_skin_checked="1">
                    <!-- Your content goes here -->
                    <!-- For example -->

                    <span class="text-danger pointer edit-airport ml-auto add-packing-slip" data-toggle="modal"
                        data-target="#product-list">Add</span>
                    <div class="modal fade" id="product-list" tabindex="-1" role="dialog"
                        aria-labelledby="productListTitle" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered order_view_modal_dialog justify-content-md-center" role="document">
                            <div class="modal-content product_modal">
                                <div class="modal-header border-0 ">
                                    <h4 class="text-dark p-2">Create Item Slip</h4>
                                    <div class="action__button">
                                        <button type="button" class="cancel" id='close' data-dismiss="modal"
                                            aria-label="Close">
                                            <span aria-hidden="true">Cancel</span>
                                        </button>
                                        <button type="button" class="save ml-2" id='save'>
                                            <span aria-hidden="true">Create Slip</span>
                                            <span class="btn-ring-modal"></span>
                                        </button>
                                    </div>
                                </div>
                                <div class="errors" id="product_modal"></div>
                                <div class="modal-body " id="modal-contents">
                                    <input type="text" name="slip-name" id="slip-name" Placeholder="Slip Name"
                                        class="form-control">
                                    <br>
                                    @foreach ($order->items as $item)
                                        <!-- Your content goes here -->

                                        {{-- @dd($item) --}}
                                        @php

                                            $itemQty = $item->qty_ordered;
                                            $optionLabel = '';
                                            if (isset($item->additional['attributes'])) {
                                                $attributes = $item->additional['attributes'];

                                                foreach ($attributes as $attribute) {
                                                    if (
                                                        isset($attribute['option_label']) &&
                                                        $attribute['option_label'] != ''
                                                    ) {
                                                        $optionLabel = $attribute['option_label'];
                                                    }
                                                }
                                            }

                                            foreach ($packaging_meta_data as $item_id => $item_data) {
                                                if ($item_data['item'] == $item->id) {
                                                    $itemQty = $item->qty_ordered - $item_data['qty'];
                                                }
                                            }

                                        @endphp



                                        @if ($itemQty > 0)
                                            <div class="row search_product_list" bis_skin_checked="1">
                                                <div class="col-1" id="product_checkbox" bis_skin_checked="1">

                                                    <input type="checkbox" name="{{ $item->name }}"
                                                        id="{{ $item->id }}"
                                                        class="largerCheckbox search_product_checkbox checkboxName">
                                                </div>
                                                {{-- <div class="col-2 p-0" bis_skin_checked="1">
                                                    <img src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                        class="h-50 w-100" alt="">
                                                </div> --}}
                                                <div class="col-8 product__detail" bis_skin_checked="1">
                                                    <h5 class="m-0">{{ $item->name }}
                                                        @if ($optionLabel)
                                                            ({{ $optionLabel }})
                                                        @endif
                                                    </h5>

                                                    <div class="options" bis_skin_checked="1"></div>
                                                    <div class="row justify-content-start product__equi"
                                                        bis_skin_checked="1">
                                                        <div class="col-12" bis_skin_checked="1">
                                                            <div class="input-group mt-2" style=" gap: 12px;"
                                                                bis_skin_checked="1">
                                                                <div class="input-group-prepend" bis_skin_checked="1">
                                                                    <img id="minusBtn"
                                                                        src="/themes/volantijetcatering/assets/images/minus.png"
                                                                        alt="">
                                                                </div>
                                                                <input type="text"
                                                                    class="form-control text-center quantity__inp"
                                                                    aria-label="Quantity" readonly
                                                                    aria-describedby="basic-addon1"
                                                                    value="{{ $itemQty }}" min="0"
                                                                    max="{{ $itemQty }}" id="quantityInput">
                                                                <div class="input-group-append" bis_skin_checked="1">
                                                                    <img id="plusBtn"
                                                                        src="/themes/volantijetcatering/assets/images/plus-small.png"
                                                                        alt="">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <span class="quantity-limit-message text-danger"></span>

                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>






                            </div>
                        </div>
                    </div>



                </div>

                {{-- listing --}}


                <div class="order_status_log table my-3" bis_skin_checked="1">
                    <h3>Order Slip</h3>
                    <div class="table-responsive" bis_skin_checked="1">
                        <table style="font-size: 15px; " class="package-slip-table">
                            <thead>
                                <tr class="order_view_table_head">
                                    <th>Id</th>
                                    <th>Slip Name</th>
                                    <th>Item Qty</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody class="table__body">
                                <?php
                                $sr_no = 1;
                                ?>
                                @foreach ($packaging_lists as $packaging_list)
                                    <tr>
                                        <td class="border-none">{{ $sr_no++ }}</td>
                                        <td class="border-none">
                                            {{ $packaging_list['name'] }}
                                        </td>

                                        <td class="border-none">
                                            {{ $packaging_list['total_qty'] }}
                                        </td>
                                        <td class="border-none d-flex ">
                                            <form method="POST"
                                                action="{{ route('order-view.print.pdf.slip', ['slip_id' => $packaging_list['packaging_id'], 'order_id' => $order->increment_id]) }}"
                                                target="_blank">
                                                @csrf
                                                <button type="submit" class="order_view_invoice_button mt-1 mt-lg-0  print">Print
                                                    Slip</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('order-view.download-packaging-slip', ['slip_id' => $packaging_list['packaging_id'], 'order_id' => $order->increment_id]) }}"
                                                class="px-2">
                                                @csrf

                                                <button type="submit" class="order_view_invoice_button mt-1 mt-lg-0">Download
                                                    Slip</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ route('order-view.delete-packaging-slip', ['id' => $packaging_list['packaging_id']]) }}"
                                                class="px-2">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="order_view_invoice_button mt-1 mt-lg-0">Delete
                                                    Slip</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>


                {{-- end listing --}}



            </div>
        </div>
    </div>
    </div>
    </div>
    </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery.print/1.6.2/jQuery.print.js"
        integrity="sha512-BaXrDZSVGt+DvByw0xuYdsGJgzhIXNgES0E9B+Pgfe13XlZQvmiCkQ9GXpjVeLWEGLxqHzhPjNSBs4osiuNZyg=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        jQuery(document).ready(function() {


            // $('.print').printPage();


            var orderid = jQuery('#orderID').text();
            var checkedCheckboxIds = [];
            jQuery('body').on('click', '#plusBtn', function() {

                var parentContainer = $(this).parent().parent();
                var quantityInput = parentContainer.find('#quantityInput');
                var currentValue = parseInt(quantityInput.val(), 10);
                var currentValue = parseInt(quantityInput.val(), 10);
                var maxValue = parseInt(quantityInput.attr('max'));
                console.log(currentValue, maxValue);
                if (currentValue < maxValue) {
                    var newValue = currentValue + 1;

                    quantityInput.val(newValue);
                }



            });

            // Add click event listener to minus button
            jQuery('body').on('click', '#minusBtn', function() {
                var parentContainer = $(this).parent().parent();
                var quantityInput = parentContainer.find('#quantityInput');
                var currentValue = parseInt(quantityInput.val(), 10);
                if (currentValue > 1) {
                    var newValue = currentValue - 1;
                    quantityInput.val(newValue);
                }

            });
            jQuery('body').on('click', '#save', function() {
                var limitExceeded = false;
                var limitExceededMessage = 'You have exceeded the available quantity for some products.';
                var QuantityValue = false;
                var slip_name = jQuery('#slip-name').val();
                $('.checkboxName').each(function() {
                    var checkboxId = $(this).attr('id');
                    checkboxName = $(this).attr('name');


                    var quantityInput = $(this).closest('.search_product_list').find(
                        '#quantityInput');
                    var plusButton = $(this).closest('.row').find('.plusBtn');
                    var quantityLimitMessage = $(this).closest('.row').find(
                        '.quantity-limit-message');
                    var productQtyAvailable = $(this).closest('.row').find('#product_qty').val();
                    var quantityInputValue = parseInt(quantityInput.val(), 10) || 0;


                    var targetElement1 = $(this).closest('.search_product_list').find('.options');

                    if ($(this).prop('checked')) {
                        // alert(quantityInputValue)

                        var productExists = checkedCheckboxIds.some(item => item.product_id ===
                            checkboxId);

                        if (!productExists) {
                            var index = checkedCheckboxIds.findIndex(item => item.item_id ===
                                checkboxId);

                            if (index == -1) {

                                var obj = {
                                    'item_id': checkboxId,
                                    'qty': quantityInputValue

                                };
                                checkedCheckboxIds.push(obj);
                            }
                        }
                    } else {

                        // If unchecked, remove the checkbox id from the array
                        var index = checkedCheckboxIds.findIndex(item => item.item_id ===
                            checkboxId);
                        if (index !== -1) {
                            checkedCheckboxIds.splice(index, 1);
                        }
                    }
                });

                // ------------------------- ajax -------------------------------

                // $(this).css('cursor', 'not-allowed');
                // $(this).html('Loading...')


                if (checkedCheckboxIds.length === 0) {
                    displayErrorMessage('Please select at least one product.', 'checkbox');
                } else if (jQuery('#slip-name').val() == '') {
                    displayErrorMessage('Please fill Packaging slip field');
                } else {
                    $("body .cancel").hide();
                    $(this).html('<span class="btn-ring-modal"></span>');
                    $(".btn-ring-modal").show();
                    setTimeout(function() {
                        $(".btn-ring-modal").hide();
                        $(this).prop('disabled', false);
                    }, 20000);
                    console.log('befoere start exist');
                    if (checkedCheckboxIds.length > 0) {
                        console.log('start exist');
                        $(this).prop('disabled', true);
                        $.ajax({
                            url: "{{ route('order-view.add-packaging-slip') }}",
                            type: 'POST',
                            data: {
                                "_token": "{{ csrf_token() }}",
                                'items': checkedCheckboxIds,
                                'order_id': orderid,
                                'slip_name': slip_name,
                            },
                            success: function() {
                                location.reload();
                            }
                        });
                    }
                }

                function displayErrorMessage(message, customClass) {
                    console.log('Displaying error message:', message);

                    var classCheck = $('body').find('.errors' + ' ' + '.' + customClass);

                    if (classCheck.length <= 0) {
                        var alertElement = $(
                            '<div class="alert alert-warning alert-dismissible fade show m-0 p-2 ' +
                            customClass + '" role="alert">' +
                            '<strong>Warning!</strong> ' + message +
                            '<button type="button" class="close text-dark p-2" data-dismiss="alert" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span></button></div>');

                        alertElement.appendTo('.errors');

                        setTimeout(function() {
                            alertElement.fadeOut('slow', function() {
                                $(this).remove();
                            });
                        }, 3000);
                    }
                }


                function updateErrorMessage(targetElements) {
                    $.each(targetElements, function(index, targetElement) {
                        var errorMessageElement = $(targetElement).next('.error-message');

                        if (!errorMessageElement.length && optionsMissing) {
                            errorMessageElement = $('<span class="error-message"></span>')
                                .insertAfter(targetElement);
                        }

                        errorMessageElement.text('Please select an option for the product' + ' (' +
                            checkboxName + ')');
                    });
                }
            });


        })
    </script>
@endpush
