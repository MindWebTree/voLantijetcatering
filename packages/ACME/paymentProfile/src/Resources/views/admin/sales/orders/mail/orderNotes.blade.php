<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            background-color: #ffffff;
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        img {
            width: 100%;
            max-width: 300px;
            display: block;
            margin: 0 auto;
        }

        h3 {
            color: #cc0000;
            margin-top: 35px;
        }

        p {
            color: #555;
            line-height: 1.6;
        }

        .text-info {
            color: #007bff;
        }

        span.order-id {
            color: blue;
            font-weight: bold;
        }

        span.order-date {
            color: navy;
        }

        span.status {
            color: #cc0000;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container order_view_status">
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
            {{-- <table style="width: 100%">
                <tr>
                    <td>
                        <div
                            style="
                    background: #f6f6f6;
                    width: 100%;
                    float: left;
                    display: block;
                    vertical-align: text-top;
                    border-top: 1px dotted black;
                    border-bottom: 1px dotted black;
                    margin-top: 20px;">
                            <div class="table-responsive">
                                <table style="width: -webkit-fill-available ;">
                                    <thead>
                                        <tr>
                                            <th style="text-align: left;padding: 8px">
                                                Notes
                                            </th>
                                            <th style="text-align: left;padding: 8px">
                                                {{ __('shop::app.customer.account.order.view.product-name') }}</th>
                                            <th style="text-align: left;padding: 8px">{{ __('shop::app.customer.account.order.view.qty') }}
                                            </th>
                                            <th style="text-align: left;padding: 8px">
                                                Price
                                            </th>
                                        </tr>
                                    </thead>
                
                                    <tbody>
                                        @foreach ($order->items as $item)
                                            @php
                                                $optionLabel = null;
                                                $specialInstruction = null;
                                                $notes = null;
                                                if (isset($item->additional['attributes'])) {
                                                    $attributes = $item->additional['attributes'];

                                                    foreach ($attributes as $attribute) {
                                                        if (isset($attribute['option_label']) && $attribute['option_label'] != '') {
                                                            $optionLabel = $attribute['option_label'];
                                                        }
                                                    }
                                                }

                                                if (isset($item->additional['special_instruction'])) {
                                                    $specialInstruction = $item->additional['special_instruction'];
                                                }

                                                $notes = DB::table('order_items')
                                                        ->where('id', $item->id)
                                                        ->where('order_id', $order->increment_id)
                                                        ->value('additional_notes');
                                            @endphp
                                            <tr class="order_view_table_body" style="height: 110px;">
                                                <td
                                                    style="
                                                max-width: 130px;font-size:11px;">
                                                    @if (isset($notes))
                                                    <p class="m-0">{{ $notes }}</p>
                                                    @endif
                                                </td>
                                                <td style="
                                                max-width: 200px;overflow: auto;">
                                                    {{ $item->name }}
                                                    @if ($optionLabel)
                                                        ({{ $optionLabel }})
                                                    @endif
                                                    @if (!empty($specialInstruction))
                                                    <div class="d-flex" style="gap:4px;font-size:11px;max-height: 100px;"><span>Special Instruction:  </span>
                                                        <p class="m-0 display__notes" style="font-weight:500;margin:0 !important;"> {{ $specialInstruction }}</p>
                                                        </div>
                                                    @endif
                                                </td>

                                                @if ($order->status === 'pending')
                                                    <td>NA</td>
                                                @else
                                                    <td>{{ core()->formatBasePrice($item->price) }}
                                                        <p style="margin: 0;" class="qty-row">
                                                            Qty: 
                                                            {{ $item->qty_ordered }}
                                                        </p>
                                                    </td>
                                                    
                                                @endif

                                                @if ($order->status === 'pending')
                                                    <td>NA</td>
                                                @else
                                                    <td>{{ core()->formatBasePrice($item->base_total + $item->base_tax_amount - $item->base_discount_amount) }}
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </td>
                </tr>
            </table> --}}
        <p style="font-weight:600;">Order Id: #{{ $order->id }}</p>
        <P>order date: {{ date('m-d-Y h:i:s A', strtotime($order->created_at)) }}</P>
        <span style="font-weight: 600;">Comments: {{ $comment->notes }}</span>
        <div style="margin-top: 20px;">
                    <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                        {!!
                            __('shop::app.mail.order.help', [
                                'support_email' => '<a style="color:#0041FF" href="mailto:' . core()->getSenderEmailDetails()['email'] . '">' . core()->getSenderEmailDetails()['email']. '</a>'
                                ])
                        !!}
                    </p>
        </div>
    </div>
</body>

</html>
