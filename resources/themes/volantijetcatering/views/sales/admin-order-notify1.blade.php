@component('shop::emails.layouts.master')
    <div style="text-align: center;">
        <a href="{{ route('shop.home.index') }}">
            {{-- @include ('shop::emails.layouts.logo') --}}
            <img style="width: 100%;
        max-width: 300px;
        display: block;
        margin: 0 auto;"
                src="https://images.squarespace-cdn.com/content/v1/6171dbc44e102724f1ce58cf/eda39336-24c7-499b-9336-c9cee87db776/VolantiStickers-11.jpg?format=1500w"
                alt="Volantijet Catering" />
        </a>
    </div>
    <div style="padding: 30px;">
        <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
            <span style="font-weight: bold;">
                {{ __('shop::app.mail.order.heading') }}
            </span> <br>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.order.dear', ['customer_name' => $admin_name]) }},
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                We are pleased to inform you that a new order has been placed on our website. Below are the details of
                the
                order: <span style="color: rgb(26, 106, 233)">#{{ $order['increment_id'] }}</span>,
                {{ core()->formatDate($order['created_at'], 'Y-m-d H:i:s') }}
            </p>
        </div>

        <div style="font-weight: bold;font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 20px !important;">
            {{ __('shop::app.mail.order.summary') }}
        </div>

        <div style="display: flex;flex-direction: row;margin-top: 20px;justify-content: space-between;margin-bottom: 40px;">
            @if (isset($order['shipping_address']))
            {{-- @if ($order->shipping_address) --}}
                <div style="line-height: 25px;">
                    <div style="font-weight: bold;font-size: 16px;color: #242424;">
                        {{ __('shop::app.mail.order.shipping-address') }}
                    </div>

                    <div>
                        {{ $order['shipping_address']['company_name'] ?? '' }}
                    </div>

                    <div>
                        {{ $order['shipping_address']['name'] }}
                    </div>

                    <div>
                        {{ $order['shipping_address']['address1'] }}
                    </div>

                    <div>
                        {{ $order['shipping_address']['postcode'] . ' ' . $order['shipping_address']['city'] }}
                    </div>

                    <div>
                        {{ $order['shipping_address']['state'] }}
                    </div>

                    <div>
                        {{ core()->country_name($order['shipping_address']['country']) }}
                    </div>

                    <div style="margin-bottom: 40px;">
                        {{ __('shop::app.mail.order.contact') }} : {{ $order['fbo_phone_number'] }}
                    </div>

                </div>
            @endif

            {{-- @if ($order->billing_address)
                <div style="line-height: 25px;">
                    <div style="font-weight: bold;font-size: 16px;color: #242424;">
                        {{ __('shop::app.mail.order.billing-address') }}
                    </div>

                    <div>
                        {{ $order->billing_address->company_name ?? '' }}
                    </div>

                    <div>
                        {{ $order->billing_address->name }}
                    </div>

                    <div>
                        {{ $order->billing_address->address1 }}
                    </div>

                    <div>
                        {{ $order->billing_address->postcode . ' ' . $order->billing_address->city }}
                    </div>

                    <div>
                        {{ $order->billing_address->state }}
                    </div>

                    <div>
                        {{ core()->country_name($order->billing_address->country) }}
                    </div>

                    <div>---</div>

                    <div style="margin-bottom: 40px;">
                        {{ __('shop::app.mail.order.contact') }} : {{ $order->billing_address->phone }}
                    </div>

                </div>
            @endif --}}
        </div>

        <div class="section-content">
            <div class="table mb-20">
                <table style="overflow-x: auto; border-collapse: collapse;
        border-spacing: 0;width: 100%">
                    <thead>
                        <tr style="background-color: #f2f2f2">
                            <th style="text-align: left;padding: 8px">
                                {{ __('shop::app.customer.account.order.view.SKU') }}
                            </th>
                            <th style="text-align: left;padding: 8px">
                                {{ __('shop::app.customer.account.order.view.product-name') }}</th>
                            <th style="text-align: left;padding: 8px">
                                {{ __('shop::app.customer.account.order.view.qty') }}
                            </th>
                        </tr>
                    </thead>

                    <tbody> 
                        @foreach ($order['items'] as $item)
                            <tr>
                                <td data-value="{{ __('shop::app.customer.account.order.view.SKU') }}"
                                    style="text-align: left;padding: 8px">
                                    {{-- {{ $item->getTypeInstance()->getOrderedItem($item)->sku }}</td> --}}
                                    {{ $item['sku'] }}</td>

                                <td data-value="{{ __('shop::app.customer.account.order.view.product-name') }}"
                                    style="text-align: left;padding: 8px">
                                    {{ $item['name'] }}

                                    @if (isset($item['additional']['attributes']))
                                        <div class="item-options">

                                            @foreach ($item['additional']['attributes'] as $attribute)
                                                <b>{{ $attribute['attribute_name'] }} :
                                                </b>{{ $attribute['option_label'] }}</br>
                                            @endforeach

                                        </div>
                                    @endif
                                </td>



                                <td data-value="{{ __('shop::app.customer.account.order.view.qty') }}"
                                    style="text-align: left;padding: 8px">{{ $item['qty_ordered'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <p>Please review the order details and process it accordingly.</p>
@endcomponent
