@component('shop::emails.layouts.master')

@push('css')
    <style>
        /* Reset some default styles to ensure consistency */
        body,
        table,
        td,
        p {
            margin: 0;
            padding: 0;
            font-family: arial;
            color: #444444;
        }

        /* Set the background color for the entire email */
        body {
            background-color: #fff;
        }

        /* Add some spacing around the content */
        table.wrapper {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 0;
            background-color: #ffffff;
        }

        /* Style the header section */
        .header {
            background-color: #007bff;
            color: #ffffff;
            padding: 10px 0;
            text-align: center;
        }

        /* Style the receipt details section */
        .receipt-details {
            padding: 20px;
        }

        /* Style the table */
        table.receipt-table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Style the table headers */
        table.receipt-table th {
            background-color: #f2f2f2;
            padding: 10px;
            text-align: left;
        }

        /* Style the table rows */
        table.receipt-table td {
            border-bottom: 1px solid #ddd;
            padding: 10px;
        }

        /* Style the total amount */
        .total-amount {
            text-align: right;
            font-weight: bold;
        }

        /* Add some spacing for better readability */
        p {
            margin-bottom: 10px;
        }

        .table-width {
            max-width: 690px;
            margin: auto;
            display: flex;
        }

        /* @media only screen and (max-width: 520px) {
                                                    table tr {
                                                        display: flex !important;
                                                        flex-wrap: wrap;
                                                        gap: 10px;
                                                    }
                                                } */

        @media only screen and (max-width: 768px) {
            table.wrapper {
                max-width: 100% !important;
            }

            table.receipt-table th,
            table.receipt-table td {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }

            table.wrapper img {
                width: 100%;
                max-width: 100%;
                height: auto;
            }
        }
    </style>
@endpush

<table class="wrapper" style="margin: auto;width:100%;max-width:90%;">

            <tr
            style="
            text-align: center;
            padding: 30px 0 0 0;
            display: block;
            width: 90%;
            ">
            <td colspan="2" style="text-align: center !important; width: 100%; display: block">
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
            </td>
        </tr>
        <tr
            style="
            background: #f6f6f6;
            margin-top: 20px;
            border-top: 1px dashed black;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            ">
            <td style="text-align: left">
                <h1
                    style="
                padding-bottom: 15px;
                color: #000000;
                font-size: 24px;
                font-weight: bold;
                margin-top: 0;
                ">
                    Thank you for your order!
                </h1>
            
                 <div style="font-size: 20px; color: #242424; line-height: 30px; margin-bottom: 34px;padding-top:5px;">
                <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
                    {{ __('shop::app.mail.order.dear', [
                        'customer_name' => 
                            (!empty($order['customer_first_name'] ?? '') && !empty($order['customer_last_name'] ?? ''))
                                ? ($order['customer_first_name'] ?? '') . ' ' . ($order['customer_last_name'] ?? '')
                                : ($fboDetails->full_name ?? 'Customer')
                    ]) }}
                </p>

                <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
                    {!! __('shop::app.mail.order.order_greeting', [
                        'order_id' => '<span style="color: rgb(26, 106, 233); font-weight: bold;">#' . ($order['increment_id'] ?? 'N/A') . '</span>',
                        'created_at' => core()->formatDate($order['created_at'] ?? now(), 'm-d-Y H:i:s'),
                    ]) !!}
                </p>
            </div>
            </td>
        </tr>
    <tr>
        <td colspan="3" style="width: 100%">
            <div
                style="
            border-top: 1px dotted black;
              border-bottom: 1px dotted black;
              padding: 0;
              text-align: center;
            ">
                <h3 style="padding: 15px 0px;font-weight: bold;margin: 0px;font-size: 20px;">
                    Order Details
                </h3>
            </div>
        </td>
    </tr>
<tr style="background: #f6f6f6;padding: 20px;display: block;vertical-align: text-top;">
    <td style="padding: 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3">
                    Order No: {{ $order['increment_id'] }}
                </td>
            </tr>
            <tr>
                <td colspan="3" style="padding-bottom: 15px;">
                    Order Date & Time: {{ date('m-d-Y h:i:s A', strtotime($order['created_at'] )) }}
                </td>
            </tr>
            <tr>
                <!-- Account Information -->
                <td width="30%" valign="top" style="padding-right: 10px;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">{{ __('shop::app.fbo-detail.client-info') }}</p>
                    <p style="margin: 0;">{{ $order['fbo_full_name'] ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order['fbo_email_address'] ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order['fbo_phone_number'] ?? 'N/A' }}</p>
                </td>

                <!-- Address -->
                <td width="30%" valign="top" style="padding: 0 10px;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">Address</p>
                    <p style="margin: 0;">{{ $order['shipping_address']['airport_name'] ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order['shipping_address']['address1'] ?? 'N/A' }}</p>
                </td>

                <!-- Aircraft Information -->
                <td width="30%" valign="top" style="padding-left: 10px;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">{{ __('shop::app.fbo-detail.aircraft-info') }}</p>
                    <p style="margin: 0;">{{ $order['fbo_tail_number'] ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order['fbo_packaging'] ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order['fbo_service_packaging'] ?? 'N/A' }}</p>
                </td>
            </tr>
        </table>
    </td>
</tr>
    <tr>
        <td>
            <table style="width: 100%">
                <tr>
                    <td>
                        <div
                            style="
                    background: #f6f6f6;
                    width: 100%;
                    float: left;
                    padding: 20px 0 20px 0;
                    display: block;
                    vertical-align: text-top;
                    border-top: 1px dotted black;
                    border-bottom: 1px dotted black;
                    margin-top: 20px;">
                            <div class="table-responsive" style="max-height: 500px;overflow-x: auto;text-align:left;">
                                <table style="width: -webkit-fill-available;border-collapse: collapse;" class="order-items-table">
                                    <thead>
                                        <tr style="background-color: #dfdfdf;">
                                            <th style="padding: 8px">
                                                SKU
                                            </th>
                                            <th style="padding: 8px">
                                            Product Name</th>
                                            <th style="padding: 8px">
                                                Qty
                                            </th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @foreach (($order['items'] ?? []) as $item)
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
                                                    $specialInstruction = $item->additional['special_instruction'];
                                                }

                                                $notes = DB::table('order_items')
                                                    ->where('id', $item->id ?? 0)
                                                    ->where('order_id', $order['increment_id'] ?? '')
                                                    ->value('additional_notes');
                                            @endphp
                                            <tr class="order_view_table_body" style="min-height: 60px; {{ $loop->index % 2 !== 0 ? 'background-color: #dfdfdf;' : 'background-color: #ffffff;' }}">
                                                <td
                                                    style="
                                            max-width: 130px;overflow: auto;padding:8px;">
                                                    {{-- <div>
                                                        <img class="product__img"
                                                            src="https://volantiscottsdale.mindwebtree.com/cache/large/product/118/LXDS3Ev1pMyGKEHvrBdRXM2856om0XaBPwnFOdb3.png"
                                                            alt="Product" style="height: 70px;width: 80px;" />
                                                    </div> --}}
                                                {{ $item->sku ?? 0 }}
                                                </td>
                                                {{-- @dd($item) --}}
                                                <td style="
                                                max-width: 250px;overflow: auto;padding:8px;">
                                                    {{ $item->name ?? 'N/A' }}
                                                    @if ($optionLabel)
                                                        ({{ $optionLabel }})
                                                    @endif
                                                    @if (!empty($specialInstruction))
                                                    <div class="" style="gap:4px;font-size:11px;max-height: 100px;"><span><b>Special Instruction: </b> </span><br>
                                                    <span>{{ $item['additional']['special_instruction'] }}</span>
                                                        </div>
                                                    @endif
                                                </td>

                                                    <td style="
                                                padding:8px;">
                                                            {{ $item->qty_ordered ?? 0 }}
                                                    </td>

                                                {{-- <td>
                                                    <span class="qty-row">
                                                        Qty:
                                                        {{ $item->qty_ordered }}
                                                    </span>
                                                </td> --}}
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>



        {{-- <div style="margin-top: 65px;font-size: 16px;color: #5E5E5E;line-height: 24px;display: inline-block">
            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.order.final-summary') }}
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {!! __('shop::app.mail.order.help', [
                    'support_email' =>
                        '<a style="color:#0041FF" href="mailto:' .
                        core()->getAdminEmailDetails()['email'] .
                        '">' .
                        core()->getAdminEmailDetails()['email'] .
                        '</a>',
                ]) !!}
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.order.thanks') }}
            </p>
        </div> --}}
    </div>
@endcomponent