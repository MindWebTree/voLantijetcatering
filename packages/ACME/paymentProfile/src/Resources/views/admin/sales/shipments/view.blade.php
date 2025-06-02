@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.sales.shipments.view-title', ['shipment_id' => $shipment->id]) }}
@stop
{{-- @section('head')


    @include('paymentprofile::admin.links')

@stop --}}

@section('content-wrapper')
    @php $order = $shipment->order; @endphp

    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link"
                        onclick="window.location = '{{ route('admin.paymentprofile.shipments.index') }}'"></i>

                    {{ __('admin::app.sales.shipments.view-title', ['shipment_id' => $shipment->id]) }}
                </h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        <div class="page-content">
            <div class="sale-container">

                <accordian title="{{ __('admin::app.sales.orders.order-and-account') }}" :active="true">
                    <div slot="body">
                        <div class="sale">
                            <div class="sale-section">
                                <div class="secton-title">
                                    <span>{{ __('admin::app.sales.orders.order-info') }}</span>
                                </div>

                                <div class="section-content">
                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.shipments.order-id') }}
                                        </span>

                                        <span class="value">
                                            <a
                                                href="{{ route('admin.sales.orders.view', $order->id) }}">#{{ $order->increment_id }}</a>
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.order-date') }}
                                        </span>
                                        
                                        <span class="value">
                                            {{ core()->formatDate($order->created_at, 'm-d-Y h:i:s') }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.order-status') }}
                                        </span>

                                        <span class="value">
                                            {{ $order->status }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.channel') }}
                                        </span>

                                        <span class="value">
                                            {{ $order->channel_name }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="sale-section">
                                <div class="secton-title">
                                    <span>{{ __('admin::app.sales.orders.account-info') }}</span>
                                </div>

                                <div class="section-content">
                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.customer-name') }}
                                        </span>

                                        <span class="value">
                                            {{ $shipment->order->customer_full_name }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.email') }}
                                        </span>

                                        <span class="value">
                                            {{ $shipment->order->customer_email }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </accordian>

                @if ($order->billing_address || $order->shipping_address)
                    <accordian title="{{ __('admin::app.sales.orders.address') }}" :active="true">
                        <div slot="body">
                            <div class="sale">
                                @if ($order->billing_address)
                                    <div class="sale-section">
                                        <div class="secton-title">
                                            <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                                        </div>

                                        <div class="section-content">

                                            @include ('admin::sales.address', [
                                                'address' => $order->billing_address,
                                            ])

                                        </div>
                                    </div>
                                @endif

                                @if ($order->shipping_address)
                                    <div class="sale-section">
                                        <div class="secton-title">
                                            <span>{{ __('admin::app.sales.orders.shipping-address') }}</span>
                                        </div>

                                        <div class="section-content">

                                            @include ('admin::sales.address', [
                                                'address' => $order->shipping_address,
                                            ])

                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </accordian>
                @endif
                <accordian title="{{ __('admin::app.sales.orders.payment-and-shipping') }}" :active="true">
                    <div slot="body">
                        <div class="sale">
                            <div class="sale-section">
                                <div class="secton-title">
                                    <span>{{ __('admin::app.sales.orders.payment-info') }}</span>
                                </div>

                                <div class="section-content">
                                    @if (isset($order->payment))
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.orders.payment-method') }}
                                            </span>

                                            <span class="value">
                                                {{ core()->getConfigData('sales.paymentmethods.' . $order->payment->method . '.title') }}
                                            </span>
                                        </div>
                                    @endif


                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.currency') }}
                                        </span>

                                        <span class="value">
                                            {{ $order->order_currency_code }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="sale-section admin_shipment">
                                <div class="secton-title">
                                    <span>{{ __('admin::app.sales.orders.shipping-info') }}</span>
                                </div>

                                <div class="section-content">
                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.shipping-method') }}
                                        </span>

                                        <span class="value">
                                            {{ $order->shipping_title }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.shipping-price') }}
                                        </span>

                                        <span class="value">
                                            {{ core()->formatBasePrice($order->base_shipping_amount) }}
                                        </span>
                                    </div>

                                    @if ($shipment->inventory_source || $shipment->inventory_source_name)
                                        <div class="row">
                                            <span class="title">
                                                {{ __('admin::app.sales.shipments.inventory-source') }}
                                            </span>

                                            <span class="value">
                                                {{ $shipment->inventory_source ? $shipment->inventory_source->name : $shipment->inventory_source_name }}
                                            </span>
                                        </div>
                                    @endif

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.shipments.carrier-title') }}
                                        </span>

                                        <span class="value">
                                            {{ $shipment->carrier_title }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.shipments.tracking-number') }}
                                        </span>

                                        <span class="value">
                                            {{ $shipment->track_number }}
                                        </span>
                                    </div>
                                    {{-- <div class="row">
                                        <span class="title">
                                            Delivery Partner
                                        </span>

                                        <span class="value">
                                            {{ $deliver_partner->name }}
                                        </span>
                                    </div> --}}


                                    <div class="row">

                                        <!-- Hidden dropdown, initially not displayed -->
                                        <form id="deliveryPartnerForm"
                                            action="{{ route('admin.shipment.deliveryPartner.update', $shipment->id) }}"
                                            method="POST" class="deliveryPartnerForm">
                                            @csrf
                                            <span class="title">
                                                Delivery Partner
                                            </span>
                                            <span class="value" id="deliveryPartnerName">
                                                {{ $deliver_partners->firstWhere('id', $shipment->delivery_partner)->name ?? 'Not Assigned' }}
                                            </span>
                                            <select class="value-dropdown" name="delivery_partner"
                                                id="deliveryPartnerDropdown" style="display: none;">
                                                @foreach ($deliver_partners as $partner)
                                                    <option value="{{ $partner->id }}"
                                                        {{ $partner->id == $shipment->delivery_partner ? 'selected' : '' }}>
                                                        {{ $partner->name }} ({{ $partner->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @if (auth('admin')->user()->role_id == 1 && $order->status != 'delivered')
                                                <!-- Edit button -->
                                                <button type="button" id="editDeliveryPartner">edit</button>
                                                <button type="button" id="closeDeliveryPartnerDropdown"
                                                    style="display: none;">close</button>

                                                <!-- Save button for the form -->
                                                <button type="submit" id="saveDeliveryPartner"
                                                    style="display: none;">save</button>
                                            @endif

                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </accordian>

                <accordian title="Order Summary" :active="true">
                    <div slot="body" class="delivery__images__container">
                        <section class="order__summary mt-5">
                            {{-- <h3>Shopping Cart</h3> --}}


                            @if ($order->total_item_count < 1)
                                <div class="empty__item text-center my-4">
                                    <p class="my-3">Order item is empty, Please add products to show here!</p>
                                </div>
                            @else
                                <div class="table">
                                    <div class="table-responsive">
                                        <table>

                                            <thead>

                                                @if (auth('admin')->user()->role_id == 1)

                                                    <tr class="order_view_table_head">
                                                        <th>Item</th>
                                                        <th>Product</th>
                                                        <th>Special instructions</th>
                                                        <th>Price</th>
                                                        <th>Qty</th>
                                                        <th>Sub Total</th>
                                                        @if ($order->status === 'pending' || $order->status === 'accepted')
                                                            <th>Action</th>
                                                        @endif
                                                    </tr>
                                                @else
                                                    <tr class="order_view_table_head">
                                                        <th>Item</th>
                                                        <th>Product</th>
                                                        <th>Special instructions</th>
                                                        <th>Qty</th>
                                                    </tr>
                                                @endif

                                            </thead>

                                            <tbody class="table__body">
                                                @php
                                                    $orders = DB::table('order_items')
                                                        ->where('order_id', $order->id)
                                                        ->where('parent_id', null)
                                                        ->get();

                                                @endphp
                                                {{-- @dd($order->item) --}}
                                                @foreach ($order->items as $item)
                                                    @php
                                                        $optionLabel = null;
                                                        $specialInstruction = null;
                                                        $notes = null;
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

                                                        if (isset($item->additional['special_instruction'])) {
                                                            $specialInstruction =
                                                                $item->additional['special_instruction'];
                                                        }
                                                        // dd($notes);
                                                        $notes = DB::table('order_items')
                                                            ->where('id', $item->id)
                                                            ->where('order_id', $order->increment_id)
                                                            ->value('additional_notes');

                                                    @endphp

                                                    <tr class="order_view_table_body">
                                                        <td
                                                            style="
                                                            max-width: 130px;">
                                                            {{-- <div>
                                                                <img class="product__img"
                                                                    src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                                    alt="Product" style="height: 70px;width: 80px;" />
                                                            </div> --}}

                                                            @if (isset($notes))
                                                                <p class="m-0 display__notes">{{ $notes }}</p>
                                                            @endif

                                                            {{-- @if ($order->status === 'pending' || $order->status === 'accepted')
                                                                @if (isset($notes))
                                                                    <p class="m-0 add__note mt-2" data-toggle="modal"
                                                                        data-target="#updateNote{{ $item->id }}">edit
                                                                        Order
                                                                        Notes
                                                                    </p>
                                                                @else
                                                                    <p class="m-0 add__note" data-toggle="modal"
                                                                        data-target="#addNote{{ $item->id }}">Add
                                                                        Order
                                                                        Notes
                                                                    </p>
                                                                @endif
                                                                <p class="m-0 cursor-auto product__edits "
                                                                    data-toggle="modal"
                                                                    data-target="#product-edit{{ $item->id }}"><img
                                                                        class="ml-3"
                                                                        src="/themes/volantijetcatering/assets/images/pencil.png"
                                                                        height="10px" alt="">edit</p>
                                                            @endif --}}

                                                            {{-- update note modal --}}

                                                            {{-- <div class="modal fade product__edit"
                                                                id="updateNote{{ $item->id }}" tabindex="-1"
                                                                role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title product__modal__title"
                                                                                id="myModalLabel">
                                                                                update order note
                                                                            </h5>

                                                                            <button id="order-add-note" type="button"
                                                                                class="">
                                                                                <span aria-hidden="true">update</span>
                                                                                <span class="btn-ring-modal"></span>
                                                                            </button>

                                                                        </div>
                                                                        <div class="modal-body d-flex edit__product"
                                                                            id="{{ $item->id }}"
                                                                            data="{{ $item->id }}">

                                                                            <textarea placeholder="Notes..." class="w-100 p-2" name="" id="add_note" cols="30" rows="10"
                                                                                style="height: 115px;">{{ isset($notes) ? $notes : '' }}</textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> --}}
                                                            {{-- add note modal --}}

                                                            {{-- <div class="modal fade product__edit"
                                                                id="addNote{{ $item->id }}" tabindex="-1"
                                                                role="dialog" aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title product__modal__title"
                                                                                id="myModalLabel">
                                                                                Add order note
                                                                            </h5>

                                                                            <button id="order-add-note" type="button"
                                                                                class="">
                                                                                <span aria-hidden="true">add</span>
                                                                                <span class="btn-ring-modal"></span>
                                                                            </button>


                                                                        </div>
                                                                        <div class="modal-body d-flex edit__product"
                                                                            id="{{ $item->id }}">
                                                                            <textarea class="w-100 p-2" name="" id="add_note" cols="30" rows="10" style="height: 115px;"
                                                                                placeholder="Notes..."></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> --}}

                                                            @php
                                                                $inventoryQty1 = 0;
                                                                $inventoryQty2 = 0;

                                                                if ($item->type === 'configurable') {
                                                                    $optionId = DB::table('order_items')
                                                                        ->select('product_id')
                                                                        ->where('parent_id', $item->id)
                                                                        ->first();

                                                                    // Check if $optionId is not null before proceeding
                                                                    if ($optionId) {
                                                                        $optionInventory = DB::table(
                                                                            'product_inventory_indices',
                                                                        )
                                                                            ->where('product_id', $optionId->product_id)
                                                                            ->select('qty')
                                                                            ->first();

                                                                        // Use the quantity of the option if it exists
                                                                        if ($optionInventory) {
                                                                            $inventoryQty1 = $optionInventory->qty;
                                                                        }
                                                                    }
                                                                }

                                                                // If the product is not of type 'configurable' or no option quantity is found

                                                                if ($item->product_id) {
                                                                    $productInventory = DB::table(
                                                                        'product_inventory_indices',
                                                                    )
                                                                        ->where('product_id', $item->product_id)
                                                                        ->select('qty')
                                                                        ->first();

                                                                    // Use the quantity of the product if it exists
                                                                    if ($productInventory) {
                                                                        $inventoryQty2 = $productInventory->qty;
                                                                    }
                                                                }

                                                                $modalId = 'product-edit' . $item->id;
                                                            @endphp

                                                            {{-- <div class="modal fade product__edit"
                                                                id="{{ $modalId }}" tabindex="-1" role="dialog"
                                                                aria-labelledby="exampleModalCenterTitle"
                                                                aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered"
                                                                    role="document">
                                                                    <div class="modal-content modal__note">
                                                                        <div class="modal-header">
                                                                            <h5 class="modal-title product__modal__title"
                                                                                id="myModalLabel">
                                                                                Edit Product Price
                                                                            </h5>

                                                                            <button type="button" class=""
                                                                                id="editSave">
                                                                                <span aria-hidden="true">save</span>
                                                                            </button>
                                                                        </div>

                                                                        <div class="displayErrors"></div>
                                                                        <div class="modal-body d-flex edit__product"
                                                                            id="{{ $item->product_id }}">
                                                                            <input type="hidden" id="editHiddenInput"
                                                                                name="{{ $item->type }}"
                                                                                quantity="{{ $item->qty_ordered }}"
                                                                                value="{{ $item->id }}"
                                                                                data="{{ $item->weight }}"
                                                                                totalQty="{{ $item->type === 'configurable' ? $inventoryQty1 : $inventoryQty2 }}">

                                                                            <img src="/cache/medium/product/278/s09QJX1kqQwX8zLXByqS8gU836SU5oPgp47G7ov3.png"
                                                                                alt="Product" style="height: 70px" />

                                                                            <div class="w-100 pl-2">
                                                                                <p class="m-0 product__name">
                                                                                    {{ $item->name }}
                                                                                    @if ($optionLabel)
                                                                                        ({{ $optionLabel }})
                                                                                    @endif
                                                                                </p>
                                                                                <div class="group__input__field my-2">
                                                                                    <button class="border-0"
                                                                                        id="editMinusBtn">-</button>
                                                                                    <input type="number"
                                                                                        class="text-center w-25 border-0 bg-light p-1"
                                                                                        value="{{ $item->qty_ordered }}"
                                                                                        id="editQuantityInput">
                                                                                    <button class="border-0"
                                                                                        id="editPlusBtn">+</button>
                                                                                </div>
                                                                                <div class="price">
                                                                                    @php
                                                                                        $price = number_format(
                                                                                            $item->base_price,
                                                                                            2,
                                                                                            '.',
                                                                                            '',
                                                                                        );
                                                                                    @endphp
                                                                                    <input type="number"
                                                                                        id="editProductPrice"
                                                                                        value="{{ $price }}"
                                                                                        class="text-center w-25 border-0 bg-light">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div> --}}

                                                        </td>
                                                        <td>
                                                            {{ $item->name }}
                                                            @if ($optionLabel)
                                                                ({{ $optionLabel }})
                                                            @endif
                                                        </td>

                                                        @if (isset($specialInstruction))
                                                            <td class="special-intruction">
                                                                <p>{{ $specialInstruction }}</p>
                                                            </td>
                                                        @else
                                                            <td class="special-intruction text-center">
                                                                <p></p>
                                                            </td>
                                                        @endif

                                                        @if (auth('admin')->user()->role_id == 1)
                                                            <td>{{ core()->formatBasePrice($item->base_price) }}</td>
                                                        @endif

                                                        <td>
                                                            <span class="qty-row">
                                                                {{ $item->qty_ordered }}
                                                            </span>

                                                        </td>
                                                        @if (auth('admin')->user()->role_id == 1)
                                                            <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                                            </td>
                                                        @endif
                                                @endforeach
                                                </tr>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </section>
                    </div>
                </accordian>


                @if (auth('admin')->user()->role_id != 1)
                    <accordian title="Delivery Confirmation Snapshot" :active="true">
                        <div slot="body" class="delivery__images__container delivery_buttton">

                            {!! view_render_event('sales.order.page_action.before', ['order' => $order]) !!}

                            @if ($order->status !== 'delivered')
                                <div class="shipped_button my-3 d-flex justify-content-center w-100">
                                    <!-- sandeep add code image accept -->
                                    <input type="file" id="imageUpload" accept=".png, .jpg, .jpeg" name="images[]" class="d-none" multiple
                                        style="display: none">
                                    <button id="uploadTrigger" class="btn btn-primary">Upload Image</button>
                                    <input type="hidden" id="order_id" value="{{ $order->id }}"></input>
                                    <input type="hidden" id="shipment_id" value="{{ $shipment->id }}"></input>


                                </div>
                                <span id="image_size_vaild" style="color: red"></span>

                                <div class="image-preview-container my-3 d-flex justify-content-center flex-wrap w-100">
                                    <!-- Images will be appended here by the JavaScript -->
                                </div>
                                @endif
                            @if (isset($delivery_images) && count($delivery_images) > 0)
                                <div class="image-preview">
                                    @foreach ($delivery_images as $delivery_image)
                                        {{-- @dd($delivery_image) --}}
                                        <div class="m-2">
                                            <img class="image-preview-item"
                                                src="{{ asset($delivery_image->attachment) }}" alt="Delivery Image" />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            {{-- sandeep add code --}}
                            @if ($order->status !== 'delivered')
                            <div class="shipped_button delivery_button_center" style="text-align:right">
                                <button id="order_view_shipped" class="order__shipped modal_open_button" disabled
                                    style=" cursor: not-allowed;">Submit</button>

                                    <div class="modal_parent">
                                        <div class="modal">
                                            <div class="modal-content">
                                                <span class="close">&times;</span>
                                                <div class="delivery_confirm_image">
                                                    <img src="{{ asset('/themes/volantijetcatering/assets/images/accept.png') }}"
                                                        alt="">
                                                </div>
                                                <p>Are you sure </p>
                                                <p>You want to proceed ?</p>
                                                <button id="order_delivery_confirm" class="modal_submit_button"
                                                    style="">Confirm <span class="btn-ring"></span></button>
                                            </div>
                                        </div>


                                    </div>
                            </div>

                            @endif
                                
                            {!! view_render_event('sales.order.page_action.after', ['order' => $order]) !!}
                        </div>
                    </accordian>
                @endif


                @if (auth('admin')->user()->role_id == 1 && isset($delivery_images) && count($delivery_images) > 0)
                    <accordian title="Delivery Confirmation Snapshot" :active="true">
                        <div slot="body" class="delivery__images__container">

                            <div class="image-preview">
                                @foreach ($delivery_images as $delivery_image)
                                    <div class="m-2">
                                        <img class="image-preview-item" src="{{ asset($delivery_image->attachment) }}"
                                            alt="Delivery Image" />
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </accordian>
                @endif



                {{-- <accordian title="{{ __('admin::app.sales.orders.products-ordered') }}" :active="true">
                    <div slot="body">

                        <div class="table">
                            <div class="table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin::app.sales.orders.SKU') }}</th>
                                            <th>{{ __('admin::app.sales.orders.product-name') }}</th>
                                            <th>{{ __('admin::app.sales.orders.qty') }}</th>
                                        </tr>
                                    </thead>

                                    <tbody>

                                        @foreach ($shipment->items as $item)
                                            <tr>
                                                <td>{{ $item->sku }}</td>
                                                <td>
                                                    {{ $item->name }}

                                                    @if (isset($item->additional['attributes']))
                                                        <div class="item-options">

                                                            @foreach ($item->additional['attributes'] as $attribute)
                                                                <b>{{ $attribute['attribute_name'] }} :
                                                                </b>{{ $attribute['option_label'] }}</br>
                                                            @endforeach

                                                        </div>
                                                    @endif
                                                </td>
                                                <td>{{ $item->qty }}</td>
                                            </tr>
                                        @endforeach

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </accordian> --}}
            </div>
        </div>
    </div>
@stop
@push('scripts')
    <script>
        $(document).ready(function() {
            // Edit button click event
            $('#editDeliveryPartner').click(function() {
                $('#deliveryPartnerName').hide();
                $('#deliveryPartnerDropdown').show();
                $('#closeDeliveryPartnerDropdown').show();
                $('#saveDeliveryPartner').show(); // Show the save button
                $(this).hide();
            });

            // Close button click event
            $('#closeDeliveryPartnerDropdown').click(function() {
                $('#deliveryPartnerName').show();
                $('#deliveryPartnerDropdown').hide();
                $(this).hide();
                $('#saveDeliveryPartner').hide(); // Hide the save button
                $('#editDeliveryPartner').show();
            });



            $(".modal_open_button").click(function() {
                var modal = $(this).parent().find(".modal");
                modal.show();

                modal.find(".close").click(function() {
                    modal.hide();
                });
            });

            $(window).click(function(event) {
                if ($(event.target).hasClass('modal')) {
                    $('.modal').hide();
                }
            });
        });

        $(document).ready(function() {
            // Trigger file input click when upload button is clicked
            $('#uploadTrigger').on('click', function() {
                $('#imageUpload').click();
            });


            
            $('#imageUpload').on('change', function() {
                console.log(this.files, 'Files selected');
                if (this.files.length > 0) {
                    // Clear previous images
                    $('.image-preview-container').empty();

                    // Loop through all selected files
                    for (let i = 0; i < this.files.length; i++) {
                        let file = this.files[i];
                        let fileType = file.type;
                        let fileSize = file.size / 1024 / 1024; // Size in MB


                     // Check if file is an image
                        console.log('filetype',fileType);
                        if (!fileType.match('image.*')) {
                            $('#image_size_vaild').text('Please select a valid image file.');
                            return;
                        }


                        // Check if file size is greater than 5MB
                        if (fileSize > 5) {
                            $('#image_size_vaild').text('File size should not exceed 5MB.');
                            return;
                        }


                        // If file is valid, read and preview the image
                        let reader = new FileReader();
                        reader.onload = function(e) {
                            // Create an image element for preview
                            let img = $('<img />', {
                                src: e.target.result,
                                class: 'img-preview',
                                style: 'max-width: 100px; max-height: 100px; margin: 5px;' // Example styling
                            });

                            // Append the image to the container for previewing
                            $('.image-preview-container').append(img).show();
                        }
                        reader.readAsDataURL(file);
                    }

                    // Enable the button if all files are valid
                    $('#order_view_shipped').prop('disabled', false).css('cursor', 'pointer');
                    $('#image_size_vaild').hide();
                } else {
                    // Disable the button if no files are selected
                    $('#order_view_shipped').prop('disabled', true);
                    $('.image-preview-container').hide();
                }
            });

            jQuery('body').on('click', '#order_delivery_confirm', function() {
                console.log('dsgsdgsdfgsdfgdfsg');
                var button = $(this);
                button.prop('disabled', true);
                $('#uploadTrigger').hide();
                button.html('<span class="btn-ring"></span>');
                $(".btn-ring").show().css('display', 'flex');

                // Set a timeout to hide the loading animation and re-enable the button
                setTimeout(function() {
                    $(".btn-ring").hide();
                    button.prop('disabled', false).text('Delivery');
                    $('#uploadTrigger').show();
                }, 20000);

                var formData = new FormData();
                var imageFiles = $('#imageUpload')[0].files; // Get all files
                for (var i = 0; i < imageFiles.length; i++) {
                    formData.append('images[]', imageFiles[i]); // Append each file to formData
                }
                formData.append('shipment_id', $('#shipment_id').val());
                formData.append('order_id', $('#order_id').val());
                formData.append('_token', $('meta[name="csrf-token"]').attr(
                    'content')); // Get CSRF token from meta tag
console.log('formdata',['form data',formData]);

                // AJAX request
                $.ajax({
                    url: "{{ route('admin.order.status') }}", // Replace with the correct URL
                    method: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        if (result.success) {
                            // alert(result.success); // Show success message
                            location.reload(); // Reload the page or redirect as needed
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                        alert(
                            'An error occurred while processing your request. Please try again.'
                        ); // Provide user feedback
                    },
                    complete: function() {
                        $(".btn-ring").hide();
                        button.prop('disabled', false).text('Delivery');
                        $('#uploadTrigger').show();
                    }
                });
            });


        });
    </script>
@endpush
