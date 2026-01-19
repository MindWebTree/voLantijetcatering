@include('paymentprofile::admin.links')
    <?php
        $order = $invoice->order; 
        $airport_fbo = DB::table('airport_fbo_details')->where('id', $order->airport_fbo_id)->first();
        $transaction = $order->transactions->first();
        $isPaid = DB::table('order_status_log')
                    ->where('order_id', $order->id)
                    ->where('status_id', 4)
                    ->exists();
    ?>
@php
    use Carbon\Carbon;
    use Illuminate\Support\Facades\Session;
    use Illuminate\Support\Facades\Auth;
    // dd($airport_fbo);

@endphp
@push('css')
    <style>
        /* Reset some default styles to ensure consistency */
        body,
        table,
        td,
        p {
            margin: 0;
            padding: 0;
            font-family: 'Montserrat', arial;
            color: #444444;
        }

        /* Set the background color for the entire email */
        body {
            background-color: #fff;
            font-family: 'Montserrat', arial;
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


<table class="wrapper" style="margin: auto;width:100%;max-width:90%">
    {{-- @php
    dd($order);
@endphp --}}
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
        padding-bottom:0px;
        display: flex;
        justify-content: space-between;
        ">
        <td style="width: 50%; text-align: left">
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

        </td>
        <td style="width: 50%; text-align: right">
            <p>
                Need Help? <br/>
                Call us <a href="tel:1-866-864-8488">(480.657.2426)</a> or
                <a href="mailto:jetcatering@volantiscottsdale.com" style="color: #007bff; font-weight: 600">Email us
                </a>
            </p>
        </td>
    </tr>
        <tr
        style="
        background: #f6f6f6;
        padding: 20px;
        padding-top:0px;
        display: flex;
        justify-content: space-between;
        ">
        <td style="width: 100%; text-align: left">
                    <p style="padding-bottom: 0px;margin-top:0px;">
                Order No: <strong>{{ $order->increment_id }}</strong>
            </p>

    @if (!$isPaid && $transaction === null)
    <p style="display: flex; gap:10px;align-items:center;">
            <a href="{{ route('order-invoice-view', ['orderid' => $order->id, 'customerid' => $order->customer_id]) }}"
                style="
            background: #444444;
            text-decoration: none;
            border-radius: 5px;
            float: left;
            border: none;
            color: #fff;
            font-weight: 600;
            padding: 9px 15px;
            ">Pay Now</a>
            
            @if(!empty($order->quickbook_invoice_link))
            <span style="font-weight: 600;padding:10px;">OR</span>

            <a href="{{ $order->quickbook_invoice_link }}"
            style="
                background: #444444;
                text-decoration: none;
                border-radius: 5px;
                float: left;
                border: none;
                color: #fff;
                font-weight: 600;
                padding: 9px 15px;
            ">
            Pay with QuickBooks
        </a>

        @endif
            </p>
            @endif

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
    {{-- @dd($extraData); --}}
<tr style="background: #f6f6f6;">
    <td style="padding: 20px;">
        <table width="100%" cellpadding="0" cellspacing="0" border="0">

            <!-- Order Meta -->
            <tr>
                <td style="font-weight: 600; font-size: 14px;" colspan="3">
                    Order No: {{ $order->increment_id }}
                </td>
            </tr>

            <tr>
                <td colspan="3" style="font-size: 14px;">
                    Order Date: {{ $order->created_at->format('m/d/Y') }}
                </td>
            </tr>

            <tr>
                <td colspan="3" style="font-size: 14px;padding-bottom:30px;">
                Delivery Date & Time:
                {{
                    ($order->delivery_date && $order->delivery_time)
                    ? date(
                        'm/d/Y, g:i A',
                        strtotime($order->delivery_date . ' ' . $order->delivery_time)
                    )
                    : 'N/A'
                }}
            </td>
            </tr>

            <!-- Separator (same as previous design) -->
            <tr>
                <td colspan="3" style="border-top:1px solid #ddd;"></td>
            </tr>

            <!-- 3 Column Layout -->
            <tr style="word-wrap: break-word;">

                <!-- Account Information -->
                <td width="30%" valign="top" style="padding: 15px; border-right:1px solid #ddd;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">
                        {{ __('shop::app.fbo-detail.client-info') }}
                    </p>
                    <p style="margin: 0;">{{ $order->fbo_full_name ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order->fbo_email_address ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order->fbo_phone_number ?? 'N/A' }}</p>
                </td>

                <!-- Airport + FBO -->
                <td width="30%" valign="top" style="padding: 15px; border-right:1px solid #ddd;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">
                        Airport
                    </p>
                    <p style="margin: 0;">{{ $order->shipping_address->airport_name ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order->shipping_address->address1 ?? 'N/A' }}</p>

                    <p style="font-size: 15px; font-weight: bold; margin: 10px 0 5px;">
                        FBO Details
                    </p>

                    <p style="margin: 0;">
                        {{ $airport_fbo->name ?? $order->airport_fbo_name ?? 'N/A' }}
                    </p>
                    <p style="margin: 0;">
                        {{ $airport_fbo->address ?? $order->airport_fbo_address ?? 'N/A' }}
                    </p>
                </td>

                <!-- Aircraft Information -->
                <td width="30%" valign="top" style="padding: 15px;">
                    <p style="font-size: 15px; font-weight: bold; margin: 0 0 5px;">
                        {{ __('shop::app.fbo-detail.aircraft-info') }}
                    </p>
                    <p style="margin: 0;">{{ $order->fbo_tail_number ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order->fbo_packaging ?? 'N/A' }}</p>
                    <p style="margin: 0;">{{ $order->fbo_service_packaging ?? 'N/A' }}</p>
                </td>

            </tr>

            <!-- Separator before Additional Notes -->
            {{-- <tr>
                <td colspan="3" style="border-top:1px solid #ddd;"></td>
            </tr> --}}

            <!-- Additional Notes -->
            <tr>
                @if(!empty($order->cart->fbo_additional_notes))
                <td colspan="2" style="padding: 15px; border-right:1px solid #ddd;">
                        <p><strong>Additional Notes:</strong></p>
                        <p style="margin: 0;">{{ $order->cart->fbo_additional_notes }}</p>
                </td>
                @endif


                @php
                if(!empty($transaction)){
                    $transactionData = json_decode($transaction->data, true);

                    $accountNumber = null;
                    $accountType   = null;

                    if (is_array($transactionData) && count($transactionData) === 2) {
                        $keys   = $transactionData[0];
                        $values = $transactionData[1];

                        $accountNumberIndex = array_search('accountNumber', $keys);
                        $accountTypeIndex   = array_search('accountType', $keys);

                        if ($accountNumberIndex !== false) {
                            $accountNumber = $values[$accountNumberIndex] ?? null;
                        }

                        if ($accountTypeIndex !== false) {
                            $accountType = $values[$accountTypeIndex] ?? null;
                        }
                    }

                    // Last 4 digits
                    $lastFour = $accountNumber ? substr($accountNumber, -4) : null;

                    // Account type fallback
                    $displayAccountType = !empty($accountType) ? $accountType : 'XXXX';

                    // Final formatted value
                    $formattedAccountNumber = $lastFour
                        ? $displayAccountType . '****' . $lastFour
                        : 'N/A';
                }
                @endphp            


                @if($isPaid)
                <td colspan="1" style="padding: 15px;">
                    <p><strong>Payment Details</strong></p>
                    @if(!empty($transaction))
                        <p style="margin: 0;"><strong>Status: </strong>{{ $order->status }}</p>
                        <p style="margin: 0;"><strong>Transaction Id: </strong>{{ $transaction->transaction_id }}</p>
                        <p style="margin: 0;"><strong>Method: </strong>Credit Card</p>
                        <p style="margin: 0;"><strong>Card: </strong>{{ $formattedAccountNumber }}</p>
                    @else
                        <p style="margin: 0;"><strong>Status: </strong>{{ $order->status }}</p>
                        <p style="margin: 0;"><strong>Method: </strong>Quickbook</p>
                    @endif
                </td>

                @endif
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
                            <div class="table-responsive" style="max-height: 500px;overflow-x: auto;">
                                <table style="width: -webkit-fill-available;border-collapse: collapse;" class="order-items-table">
                                    <thead>
                                        <tr style="background-color: #dfdfdf;">
                                            <th style="text-align: left;padding: 8px">
                                                Notes
                                            </th>
                                            <th style="text-align: left;padding: 8px">
                                                {{ __('shop::app.customer.account.order.view.product-name') }}</th>
                                            <th style="text-align: left;padding: 8px">{{ __('shop::app.customer.account.order.view.qty') }}
                                            </th>
                                            <th style="text-align: left;padding: 8px">Persons
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
                                                    ->where('id', $item->id)
                                                    ->where('order_id', $order->increment_id)
                                                    ->value('additional_notes');
                                            @endphp
                                            <tr class="order_view_table_body" style="height: 60px; {{ $loop->index % 2 !== 0 ? 'background-color: #dfdfdf;' : 'background-color: #ffffff;' }}">
                                                <td
                                                    style="
                                                max-width: 110px;overflow: auto;">
                                                    {{-- <div>
                                                        <img class="product__img"
                                                            src="https://volantiscottsdale.mindwebtree.com/cache/large/product/118/LXDS3Ev1pMyGKEHvrBdRXM2856om0XaBPwnFOdb3.png"
                                                            alt="Product" style="height: 70px;width: 80px;" />
                                                    </div> --}}
                                        
                                                        <p class="m-0"
                                                            style="max-height: 100px;overflow-y: auto;font-size: 11px;padding: 8px;">
                                                            {{ trim($notes ?? '') !== '' ? $notes : 'N/A' }}</p>
                                    
                                                </td>
                                                {{-- @dd($item) --}}
                                                <td style="
                                                max-width: 200px;overflow: auto;padding: 8px;">
                                                    {{ $item->name }}
                                                    @if ($optionLabel)
                                                        ({{ $optionLabel }})
                                                    @endif
                                                    @if (!empty($specialInstruction))
                                                    <div class="" style="gap:4px;font-size:11px; margin-top: 10px;max-height: 100px;"><span><b>Special Instruction: </b> </span>
                                                        <p class="m-0 display__notes" style="font-weight:500;margin:0px"> {{ $specialInstruction }}</p>
                                                        </div>
                                                    @endif
                                                </td>

                                                @if ($order->status === 'pending')
                                                    <td style="padding: 8px;">NA</td>
                                                @else
                                                    <td>
                                                        <p style="margin: 0;padding: 8px;" class="qty-row">
                                                            {{ $item->qty_ordered }}
                                                        </p>
                                                    </td>
                                                @endif

                                                <td>
                                                    <p>{{ $item->additional['persons'] ?? 'N/A' }}</p>
                                                </td>
                                                @if ($order->status === 'pending')
                                                    <td>NA</td>
                                                @else
                                                {{-- sandeep delete code  + $item->base_tax_amount - $item->base_discount_amount--}}
                                                    <td>{{ core()->formatBasePrice($item->base_total - $item->base_discount_amount) }}
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
            </table>
        </td>
    </tr>

    <table style="margin: auto;width:100%;max-width:90%" class="table-width">
        <tbody class="w-100">
            <tr style="vertical-align:text-top;display:flex;vertical-align:text-top;justify-content: space-around; line-break:anywhere">
                <td style="width: 44%;">
                    <div class="customer-notes" style="padding: 10px;padding-left: 0px;font-size: 15px;color: #c50606;">
                        @if(isset($order->include_cutlery) && $order->include_cutlery ==1)
                        <strong>Instruction:</strong> Cutlery included
                    @endif
                    </div>
                    
                    <div>
                    @php
                        use ACME\paymentProfile\Models\OrderNotes;

                        $comments = OrderNotes::orderBy('id', 'desc')->where('order_id', $order->id)->get();
                    @endphp

                    @if ($comments->count() > 0)
                        <div class="d-block">
                            <b class="text-break">ORDER NOTES:</b>
                            <ul class="comment_list m-0"
                                style="height:100px;overflow:auto;padding: 0;list-style: none;margin: 0;">
                                @foreach ($comments as $comment)
                                    <li class="d-flex" style="margin: 0;">
                                        @if ($comment->is_admin === 1)
                                            <p class="w-100"
                                            style="color: rgb(157, 157, 157);font-size: 13px;margin:0;">
                                                Support:
                                                <span>{{ $comment->notes }}</span>
                                                <span class="float-right">({{ date('m/d/Y h:i:s A', strtotime($comment->created_at)) }})</span>
                                            </p>
                                        @else
                                            <p class="w-100" style="color: rgb(157, 157, 157);font-size: 13px;">
                                                Customer:
                                                <span>{{ $comment->notes }}</span>
                                                <span class="float-right">{{ date('m/d/Y h:i:s A', strtotime($comment->created_at)) }}</span>
                                            </p>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    </div>
                </td>
                <td style="width: 56%;">
                    <p style="margin-bottom: 10px; text-align: right">
                        SubTotal :
                        {{-- @if ($order->status === 'pending')
                                <strong>NA</strong>
                            @else --}}
                            {{-- sandeep delete code  $order->grand_total --}}
                        <strong>{{ core()->formatBasePrice($order->sub_total) }}</strong>
                        {{-- @endif --}}
                    </p>
                    {{-- <p style="margin-bottom: 10px; text-align: right">
                            Discount : 
                            <strong>%Order.SubtotalDiscount%</strong>
                        </p> --}}

                    <p style="margin-bottom: 10px; text-align: right">
                        Tax :
                        {{-- @if ($order->status === 'pending')
                                <strong>NA</strong>
                            @else --}}
                        @if (isset($order->tax_amount))
                            <strong>{{ core()->formatBasePrice($order->tax_amount) }}</strong>
                        @endif

                        {{-- @endif --}}
                    </p>
                    <p style="margin-bottom: 10px; text-align: right">
                        Agent Handler :

                        @if (isset($agent))
                            <strong>{{ core()->formatBasePrice($agent->Handling_charges) }}</strong>
                        @else
                            <strong>{{ core()->formatBasePrice(0) }}</strong>
                        @endif

                        {{-- @endif --}}
                    </p>

                    <p style="margin-bottom: 10px; text-align: right">
                        Fbo Fee :
                        @if (isset($order->fbo_fee))
                            <strong>{{ core()->formatBasePrice($order->fbo_fee) }}</strong>
                        @endif
                    </p>

                    <p style="margin-bottom: 10px; text-align: right">
                        Delivery Charge :
                        @if (isset($order->tax_amount))
                            <strong>{{ core()->formatBasePrice(round(($order->sub_total * 10) / 100, 2)) }}</strong>
                        @endif
                    </p>

                    <p style="margin-bottom: 10px; text-align: right">
                        Order Total :
                        {{-- @if ($order->status === 'pending')
                                <strong>NA</strong>
                            @else --}}
                        <strong>

                            @if (isset($agent))
                                {{ core()->formatBasePrice($order->grand_total + $agent->Handling_charges) }}
                            @else
                                {{ core()->formatBasePrice($order->grand_total) }}
                            @endif


                        </strong>
                        {{-- @endif --}}
                    </p>
                </td>
            </tr>
        </tbody>
    </table>
</table>
