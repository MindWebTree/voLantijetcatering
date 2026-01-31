@extends('admin::layouts.master')

@section('page_title')
    {{ __('admin::app.sales.invoices.view-title', ['invoice_id' => $invoice->increment_id ?? $invoice->id]) }}
@stop

@section('content-wrapper')
    @php
        $order = $invoice->order;
    @endphp

    <div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    {!! view_render_event('sales.invoice.title.before', ['order' => $order]) !!}

                    <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.sales.invoices.index') }}'"></i>

                    {{ __('admin::app.sales.invoices.view-title', ['invoice_id' => $invoice->increment_id ?? $invoice->id]) }}

                    {!! view_render_event('sales.invoice.title.after', ['order' => $order]) !!}
                </h1>
            </div>

            <div class="page-action">
                {!! view_render_event('sales.invoice.page_action.before', ['order' => $order]) !!}

                @if($order->status != 'pending' && $order->status != 'processing')
                <a
                    href="javascript:void(0);"
                    class="btn btn-lg btn-primary"
                    @click="showModal('duplicateInvoiceFormModal')">
                    {{ __('admin::app.sales.invoices.send-duplicate-invoice') }}
                </a>           
                @endif

                <a href="{{ route('admin.sales.invoices.print', $invoice->id) }}" class="btn btn-lg btn-primary">
                    {{ __('admin::app.sales.invoices.print') }}
                </a>

                {!! view_render_event('sales.invoice.page_action.after', ['order' => $order]) !!}
            </div>
        </div>

        <div class="page-content">
            <tabs>
                <tab name="{{ __('admin::app.sales.orders.info') }}" :selected="true">
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
                                                    {{ __('admin::app.sales.invoices.order-id') }}
                                                </span>

                                                <span class="value">
                                                    <a href="{{ route('admin.sale.order.view', $order->id) }}">#{{ $order->increment_id }}</a>
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.invoice.increment_id.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.order-date') }}
                                                </span>

                                                <span class="value">
                                                    {{ core()->formatDate($order->created_at, 'm/d/Y h:i A') }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.invoice.created_at.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.order-status') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->status_label }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.invoice.status_label.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">
                                                    {{ __('admin::app.sales.orders.channel') }}
                                                </span>

                                                <span class="value">
                                                    {{ $order->channel_name }}
                                                </span>
                                            </div>

                                            {!! view_render_event('sales.invoice.channel_name.after', ['order' => $order]) !!}
                                            @if ($order->purchase_order_no)
                                            <div class="row">
                                                <span class="title">
                                                    Purchase Order No.
                                                </span>

                                                <span class="value">
                                                    {{ $order->purchase_order_no }}
                                                </span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="sale-section">
                                        <div class="secton-title">
                                            <span>{{ __('admin::app.sales.orders.account-info') }}</span>
                                        </div>
                                        <div class="section-content">
                                            <div class="row">
                                                <span class="title">{{ __('admin::app.sales.orders.customer-name') }}</span>
                                                <span class="value">{{ trim($invoice->order->customer_full_name) !== ''
                                                        ? trim($invoice->order->customer_full_name)
                                                        : $invoice->order->fbo_full_name }}</span>
                                            </div>

                                            {!! view_render_event('sales.invoice.customer_name.after', ['order' => $order]) !!}

                                            <div class="row">
                                                <span class="title">{{ __('admin::app.sales.orders.email') }}</span>
                                                <span class="value">{{ $invoice->order->customer_email ?? $invoice->order->fbo_email_address }}</span>
                                            </div>

                                            {!! view_render_event('sales.invoice.customer_email.after', ['order' => $order]) !!}
                                            
                                            <div class="row">
                                                <span class="title">Phone No.</span>
                                                <span class="value">{{ $invoice->order->fbo_phone_number }}</span>
                                            </div>

                                            <div class="row">
                                                <span class="title">Tail Number</span>
                                                <span class="value">{{ $invoice->order->fbo_tail_number }}</span>
                                            </div>

                                            <div class="row">
                                                <span class="title">Packaging</span>
                                                <span class="value">{{ $invoice->order->fbo_packaging }}</span>
                                            </div>
                                            
                                            <div class="row">
                                                <span class="title">Service Packaging</span>
                                                <span class="value">{{ $invoice->order->fbo_service_packaging }}</span>
                                            </div>

                                            <div class="row">
                                                <span class="title">Delivery Date</span>
                                                <span class="value">
                                                    {{ $invoice->order && $invoice->order->delivery_date
                                                        ? \Carbon\Carbon::parse($invoice->order->delivery_date)->format('m/d/Y')
                                                        : '-' }}
                                                </span>
                                            </div>
                                            <div class="row">
                                                <span class="title">Delivery Time</span>
                                                <span class="value">{{ $invoice->order->delivery_time }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </accordian>

                        @if (
                            $order->billing_address
                            || $order->shipping_address
                        )
                            <accordian title="{{ __('admin::app.sales.orders.address') }}" :active="true">
                                <div slot="body">
                                    <div class="sale">
                                        @if ($order->billing_address)
                                            <div class="sale-section">
                                                <div class="secton-title" >
                                                    <span>{{ __('admin::app.sales.orders.billing-address') }}</span>
                                                </div>

                                                <div class="section-content">
                                                    @include ('admin::sales.address', ['address' => $order->billing_address])

                                                    {!! view_render_event('sales.invoice.billing_address.after', ['order' => $order]) !!}
                                                </div>
                                            </div>
                                        @endif
                                        @if ($order->shipping_address)
                                            <div class="sale-section">
                                                <div class="secton-title">
                                                    <span>{{ __('admin::app.sales.orders.shipping-address') }}</span>
                                                </div>

                                                <div class="section-content">
                                                    @include ('admin::sales.address', ['address' => $order->shipping_address])

                                                    {!! view_render_event('sales.invoice.shipping_address.after', ['order' => $order]) !!}
                                                </div>
                                            </div>
                                        @endif

                                    </div>
                                </div>
                            </accordian>

                            <accordian title="Airport Address And Handlig Agent" :active="true">
                                <div slot="body">
                                    <div class="sale">

                                        @if (isset($order->shipping_address))
                                            <div class="sale-section">
                                                <div class="secton-title">
                                                    <span>Airport Address</span>
                                                </div>

                                                <div class="section-content">

                                                <div class="row">
                                                    <span class="title">Airport Name</span>
                                                    <span class="value">{{ $invoice->order->shipping_address->airport_name }}</span>
                                                </div>

                                                <div style="display: flex;word-wrap: break-word;">
                                                    <span class="title" style="min-width: 200px">Airport Address</span>
                                                    <span class="value">{{ $invoice->order->shipping_address->address1 }}</span>
                                                </div>

                                                <div class="row">
                                                    <span class="title">Airport Fbo Name</span>
                                                    <span class="value">{{ $airport_fbo->name }}</span>
                                                </div>

                                                <div class="row">
                                                    <span class="title">Airport Fbo Address</span>
                                                    <span class="value">{{ $airport_fbo->address }}</span>
                                                </div>

                                                </div>
                                            </div>
                                        @endif

                                        @if ($handlingAgent)
                                            <div class="sale-section">
                                                <div class="secton-title">
                                                    <span>Handling Agent</span>
                                                </div>

                                                <div class="section-content">
                                                <div class="row">
                                                    <span class="title">Name</span>
                                                    <span class="value">{{ $handlingAgent->Name }}</span>
                                                </div>
                                                <div style="display: flex;word-wrap: break-word;">
                                                    <span class="title" style="min-width: 200px">Phone Number</span>
                                                    <span class="value">{{ $handlingAgent->Mobile }}</span>
                                                </div>
                                                <div style="display: flex;word-wrap: break-word;">
                                                    <span class="title" style="min-width: 200px">PPR Permit</span>
                                                    <span class="value">{{ $handlingAgent->PPR_Permit }}</span>
                                                </div>
                                                <div style="display: flex;word-wrap: break-word;">
                                                    <span class="title" style="min-width: 200px">Handling Charges</span>
                                                    <span class="value">{{ $handlingAgent->Handling_charges }}</span>
                                                </div>

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </accordian>

                        @endif

                        <accordian title="{{ __('admin::app.sales.orders.products-ordered') }}" :active="true">
                            
                            <div slot="body">
                                <div class="table">
                                    
                                    <div class="table-responsive">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>{{ __('admin::app.sales.orders.SKU') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.product-name') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.price') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.qty') }}</th>
                                                    <th>Persons</th>
                                                    <th>{{ __('admin::app.sales.orders.subtotal') }}</th>
                                                    <th>{{ __('admin::app.sales.orders.tax-amount') }}</th>
                                                    @if ($invoice->order->base_discount_amount > 0)
                                                        <th>{{ __('admin::app.sales.orders.discount-amount') }}</th>
                                                    @endif
                                                    <th>{{ __('admin::app.sales.orders.grand-total') }}</th>
                                                </tr>
                                            </thead>

                                            <tbody>
                                                @foreach ($invoice->order->items as $item)
                                                    <tr>
                                                        <td>{{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td>

                                                        <td>
                                                            {{ $item->name }}

                                                            @if (isset($item->additional['attributes']))
                                                                <div class="item-options">

                                                                    @foreach ($item->additional['attributes'] as $attribute)
                                                                        <b>{{ $attribute['attribute_name'] }} : </b>{{ $attribute['option_label'] }}</br>
                                                                    @endforeach

                                                                </div>
                                                            @endif
                                                            @if (!empty($item->additional['special_instruction']))
                                                            <div class="" style="gap:4px;font-size:11px;    margin-top: 10px; max-height: 100px;"><span><b>Special Instruction: </b> </span><br>
                                                            <span>{{ $item->additional['special_instruction'] }}</span>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td>{{ core()->formatBasePrice($item->base_price) }}</td>
                                                        <td>{{ $item->qty_ordered }}</td>
                                                        <td>{{ $item->additional['persons'] }}</td>

                                                        <td>{{ core()->formatBasePrice($item->base_total) }}</td>

                                                        <td>{{ core()->formatBasePrice($item->base_tax_amount) }}</td>

                                                        @if ($invoice->order->base_discount_amount > 0)
                                                            <td>{{ core()->formatBasePrice($item->base_discount_amount) }}</td>
                                                        @endif

                                                        <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="customer-notes" style="padding: 10px;padding-left: 0px;font-size: 15px; text-align: left;">
                                        @if(isset($order->include_cutlery) && $order->include_cutlery ==1)
                                                <strong>Instruction:</strong> Cutlery included
                                        @endif
                                    </div>
                                </div>



                                <table class="sale-summary">
                                    <tr>
                                        <td>{{ __('admin::app.sales.orders.subtotal') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatBasePrice($order->base_sub_total) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('admin::app.sales.orders.shipping-handling') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatBasePrice($order->base_shipping_amount) }}</td>
                                    </tr>

                                    <tr>
                                        <td>{{ __('admin::app.sales.orders.tax') }}</td>
                                        <td>-</td>
                                        <td>{{ core()->formatBasePrice($order->base_tax_amount) }}</td>
                                    </tr>

                                    @if ($order->base_discount_amount > 0)
                                        <tr>
                                            <td>{{ __('admin::app.sales.orders.discount') }}</td>
                                            <td>-</td>
                                            <td>{{ core()->formatBasePrice($order->base_discount_amount) }}</td>
                                        </tr>
                                    @endif

                                    <tr>
                                        <td>Fbo Fee</td>
                                        <td>-</td>
                                        <td>{{ core()->formatBasePrice($order->fbo_fee) }}</td>
                                    </tr>

                                    <tr>
                                        <td>Delivery Charge</td>
                                        <td>-</td>
                                        <td>{{ core()->formatBasePrice(round(($order->sub_total * 10) / 100, 2)) }}</td>
                                    </tr>

                                    <tr class="bold">
                                        <td>{{ __('admin::app.sales.orders.grand-total') }}</td>
                                        <td>-</td>
                                        <td>
                                            @if (isset($handlingAgent->Handling_charges))
                                            {{ core()->formatBasePrice($order->grand_total + $handlingAgent->Handling_charges) }}
                                            @else
                                            {{ core()->formatBasePrice($order->grand_total) }}
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </accordian>
                    </div>
                </tab>

                <tab name="{{ __('admin::app.sales.transactions.title') }}" :selected="false">
                    <div class="sale-container">
                        <datagrid-plus src="{{ route('admin.sales.invoices.transactions', $invoice->id) }}"></datagrid-plus>
                    </div>
                </tab>
            </tabs>
        </div>
    </div>

    <modal id="duplicateInvoiceFormModal" :is-open="modalIds.duplicateInvoiceFormModal">
        <h3 slot="header">{{ __('admin::app.sales.invoices.send-duplicate-invoice') }}</h3>

        <div slot="body">
            <form
                method="POST"
                action="{{ route('admin.sales.invoices.send_duplicate', $invoice->id) }}"
                @submit.prevent="onSubmit">
                @csrf()

                <div class="control-group" :class="[errors.has('email') ? 'has-error' : '']">
                    <label for="email" class="required">{{ __('admin::app.admin.emails.email') }}</label>

                    <input
                        class="control"
                        id="email"
                        v-validate="'required|email'"
                        type="email"
                        name="email"
                        data-vv-as="&quot;{{ __('admin::app.admin.emails.email') }}&quot;"
                        value="{{ $invoice->order->customer_email ?? $invoice->order->fbo_email_address }}" />

                    <span
                        class="control-error"
                        v-text="errors.first('email')"
                        v-if="errors.has('email')">
                    </span>
                </div>

                <button type="submit" class="btn btn-lg btn-primary float-right">
                    {{ __('admin::app.sales.invoices.send') }}
                </button>
            </form>
        </div>
    </modal>
@stop
