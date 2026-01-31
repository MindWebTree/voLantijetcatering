 @php
        $order = $invoice->order; 
        $airport_fbo = DB::table('airport_fbo_details')->where('id', $order->airport_fbo_id)->first();
        $transaction = $order->transactions->first();
        $isPaid = DB::table('order_status_log')
                    ->where('order_id', $order->id)
                    ->where('status_id', 4)
                    ->exists();

        // Transaction data processing
        $formattedAccountNumber = 'N/A';
        if(!empty($transaction)){
            $transactionData = json_decode($transaction->data, true);
            $accountNumber = null;
            $accountType = null;

            if (is_array($transactionData) && count($transactionData) === 2) {
                $keys = $transactionData[0];
                $values = $transactionData[1];

                $accountNumberIndex = array_search('accountNumber', $keys);
                $accountTypeIndex = array_search('accountType', $keys);

                if ($accountNumberIndex !== false) {
                    $accountNumber = $values[$accountNumberIndex] ?? null;
                }
                if ($accountTypeIndex !== false) {
                    $accountType = $values[$accountTypeIndex] ?? null;
                }
            }

            $lastFour = $accountNumber ? substr($accountNumber, -4) : null;
            $displayAccountType = !empty($accountType) ? $accountType : 'XXXX';
            $formattedAccountNumber = $lastFour ? $displayAccountType . '****' . $lastFour : 'N/A';
        }

        // Order Notes
        use ACME\paymentProfile\Models\OrderNotes;
        $comments = OrderNotes::orderBy('id', 'desc')->where('order_id', $order->id)->get();
    @endphp


<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice - {{ $order->increment_id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Montserrat', DejaVu Sans, Arial, sans-serif;
            color: #444444;
            font-size: 12px;
            line-height: 1.4;
        }

        table {
            border-collapse: collapse;
        }

        .wrapper {
            width: 100%;
            max-width: 700px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Logo */
        .logo-section {
            text-align: center;
            padding: 30px 0;
            border-bottom: 2px dashed #000;
        }

        .logo-section img {
            max-width: 300px;
            height: auto;
        }

        /* Thank You Section */
        .thank-you-section {
            background: #f6f6f6;
            padding: 20px;
            border-bottom: 1px dashed #000;
        }

        .thank-you-header {
            width: 100%;
        }

        .thank-you-header table {
            width: 100%;
        }

        .thank-you-header h1 {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 0 0 15px 0;
        }

        .contact-info {
            text-align: right;
            font-size: 12px;
        }

        .contact-info a {
            color: #007bff;
            text-decoration: none;
            font-weight: 600;
        }

        .order-number {
            margin: 10px 0;
            font-size: 14px;
        }

        .pay-button {
            display: inline-block;
            background: #444444;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            padding: 10px 20px;
            font-weight: 600;
            margin-top: 10px;
        }

        /* Order Details Title */
        .section-title {
            text-align: center;
            border-top: 1px dotted #000;
            border-bottom: 1px dotted #000;
            padding: 15px 0;
            margin: 20px 0;
        }

        .section-title h3 {
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        /* Order Details Section */
        .order-details-section {
            background: #f6f6f6;
            padding: 20px;
        }

        .order-meta {
            margin-bottom: 20px;
            font-size: 13px;
        }

        .order-meta p {
            margin: 5px 0;
        }

        /* Three Column Layout */
        .three-column-table {
            width: 100%;
            border-top: 1px solid #ddd;
            margin-top: 15px;
        }

        .three-column-table td {
            width: 33.33%;
            padding: 15px;
            vertical-align: top;
            border-right: 1px solid #ddd;
            font-size: 12px;
        }

        .three-column-table td:last-child {
            border-right: none;
        }

        .three-column-table h4 {
            font-size: 15px;
            font-weight: bold;
            margin: 0 0 8px 0;
        }

        .three-column-table p {
            margin: 3px 0;
        }

        /* Additional Notes & Payment */
        .additional-section {
            margin-top: 15px;
            padding-top: 15px;
        }

        .additional-section table {
            width: 100%;
        }

        .additional-section td {
            padding: 15px;
            vertical-align: top;
            font-size: 12px;
        }

        .additional-section h4 {
            font-weight: bold;
            margin-bottom: 8px;
        }

        /* Products Section */
        .products-section {
            background: #f6f6f6;
            padding: 20px 0;
            border-top: 1px dotted #000;
            border-bottom: 1px dotted #000;
            margin-top: 20px;
        }

        .products-table {
            width: 100%;
            border-collapse: collapse;
        }

        .products-table thead {
            background-color: #dfdfdf;
        }

        .products-table th {
            text-align: left;
            padding: 10px 8px;
            font-weight: 600;
            font-size: 12px;
        }

        .products-table td {
            padding: 10px 8px;
            vertical-align: top;
            font-size: 12px;
        }

        .products-table tbody tr:nth-child(even) {
            background-color: #dfdfdf;
        }

        .products-table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        .special-instruction {
            font-size: 11px;
            margin-top: 5px;
            padding: 5px;
            background: #f9f9f9;
        }

        /* Footer Section */
        .footer-section {
            margin-top: 20px;
        }

        .footer-table {
            width: 100%;
        }

        .footer-table td {
            vertical-align: top;
        }

        .order-notes-column {
            width: 44%;
            padding-right: 20px;
        }

        .order-notes-column h4 {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .comment-list {
            list-style: none;
            max-height: 120px;
            overflow-y: auto;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ddd;
            font-size: 11px;
        }

        .comment-list li {
            margin-bottom: 8px;
            color: #666;
        }

        .totals-column {
            width: 56%;
            text-align: right;
        }

        .totals-column p {
            margin: 8px 0;
            font-size: 13px;
        }

        .totals-column strong {
            font-weight: 700;
        }

        /* Print Styles */
        @media print {
            body {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }

            .wrapper {
                max-width: 100%;
                padding: 10px;
            }

            .pay-button {
                display: none;
            }

            .products-table thead {
                background-color: #dfdfdf !important;
            }

            .products-table tbody tr:nth-child(even) {
                background-color: #dfdfdf !important;
            }

            .products-table tbody tr:nth-child(odd) {
                background-color: #ffffff !important;
            }

            @page {
                margin: 15mm;
            }
        }
    </style>
</head>
<body>

    <div class="wrapper">
        <!-- Logo Section -->
        <div class="logo-section">
            <img src="https://images.squarespace-cdn.com/content/v1/6171dbc44e102724f1ce58cf/eda39336-24c7-499b-9336-c9cee87db776/VolantiStickers-11.jpg?format=1500w"
                alt="Volantijet Catering" />
        </div>

        <!-- Thank You Section -->
        <div class="thank-you-section">
            <div class="thank-you-header">
                <table>
                    <tr>
                        <td style="width: 60%; vertical-align: top;">
                            <h1>Thank you for your order!</h1>
                        </td>
                        <td style="width: 40%; vertical-align: top;">
                            <div class="contact-info">
                                <p>
                                    Need Help?<br/>
                                    Call us <a href="tel:480-657-2426">(480.657.2426)</a> or<br/>
                                    <a href="mailto:jetcatering@volantiscottsdale.com">Email us</a>
                                </p>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="order-number">
                <p>Order No: <strong>{{ $order->increment_id }}</strong></p>
            </div>

            @if (!$isPaid && $transaction === null && $order->status !== 'pending')
            <div style="margin-top: 10px;">
                <a href="{{ route('order-invoice-view', ['orderid' => $order->id, 'customerid' => $order->customer_id]) }}"
                    class="pay-button">Pay Now</a>
                
                @if(!empty($order->quickbook_invoice_link))
                <span style="font-weight: 600; margin: 0 10px;">OR</span>
                <a href="{{ $order->quickbook_invoice_link }}" class="pay-button">Pay with QuickBooks</a>
                @endif
            </div>
            @endif
        </div>

        <!-- Order Details Title -->
        <div class="section-title">
            <h3>Order Details</h3>
        </div>

        <!-- Order Details Section -->
        <div class="order-details-section">
            <div class="order-meta">
                <p><strong>Order No:</strong> {{ $order->increment_id }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('m/d/Y') }}</p>
                <p>
                    <strong>Delivery Date & Time:</strong>
                    {{
                        ($order->delivery_date && $order->delivery_time)
                        ? date('m/d/Y, g:i A', strtotime($order->delivery_date . ' ' . $order->delivery_time))
                        : 'N/A'
                    }}
                </p>
            </div>

            <!-- Three Column Layout -->
            <table class="three-column-table">
                <tr>
                    <!-- Client Information -->
                    <td>
                        <h4>Account Information</h4>
                        <p>{{ $order->fbo_full_name ?? 'N/A' }}</p>
                        <p>{{ $order->fbo_email_address ?? 'N/A' }}</p>
                        <p>{{ $order->fbo_phone_number ?? 'N/A' }}</p>
                    </td>

                    <!-- Airport + FBO Details -->
                    <td>
                        <h4>Airport</h4>
                        <p>{{ $order->shipping_address->airport_name ?? 'N/A' }}</p>
                        <p>{{ $order->shipping_address->address1 ?? 'N/A' }}</p>

                        <h4 style="margin-top: 12px;">FBO Details</h4>
                        <p>{{ $airport_fbo->name ?? $order->airport_fbo_name ?? 'N/A' }}</p>
                        <p>{{ $airport_fbo->address ?? $order->airport_fbo_address ?? 'N/A' }}</p>
                    </td>

                    <!-- Aircraft Information -->
                    <td>
                        <h4>Aircraft Information</h4>
                        <p>{{ $order->fbo_tail_number ?? 'N/A' }}</p>
                        <p>{{ $order->fbo_packaging ?? 'N/A' }}</p>
                        <p>{{ $order->fbo_service_packaging ?? 'N/A' }}</p>
                    </td>
                </tr>
            </table>

            <!-- Additional Notes and Payment Details -->
            @if(!empty($order->cart->fbo_additional_notes) || $isPaid)
            <div class="additional-section">
                <table>
                    <tr>
                        @if(!empty($order->cart->fbo_additional_notes))
                        <td style="width: {{ $isPaid ? '65%' : '100%' }}; border-right: {{ $isPaid ? '1px solid #ddd' : 'none' }};">
                            <h4>Additional Notes:</h4>
                            <p>{{ $order->cart->fbo_additional_notes }}</p>
                        </td>
                        @endif

                        @if($isPaid)
                        <td style="width: {{ !empty($order->cart->fbo_additional_notes) ? '35%' : '100%' }};">
                            <h4>Payment Details</h4>
                            @if(!empty($transaction))
                                <p style="margin: 0;"><strong>Status: </strong>{{ $order->status }}</p>
                                <p><strong>Transaction Id:</strong> {{ $transaction->transaction_id }}</p>
                                <p><strong>Method:</strong> Credit Card</p>
                                <p><strong>Card:</strong> {{ $formattedAccountNumber }}</p>
                            @else
                                <p style="margin: 0;"><strong>Status: </strong>{{ $order->status }}</p>
                                <p><strong>Method:</strong> Quickbook</p>
                            @endif
                        </td>
                        @endif
                    </tr>
                </table>
            </div>
            @endif
        </div>

        <!-- Products Section -->
        
        <div class="products-section">
            <table class="products-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Notes</th>
                        <th style="width: 45%;">Name</th>
                        <th style="width: 15%;">Qty</th>
                        <th style="width: 15%;">Persons</th>
                        <th style="width: 25%;">Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        @php
                            $optionLabel = null;
                            $specialInstruction = null;
                            
                            if (isset($item->additional['attributes'])) {
                                foreach ($item->additional['attributes'] as $attribute) {
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
                        <tr>
                            <td style="font-size: 11px;">
                                {{ trim($notes ?? '') !== '' ? $notes : 'N/A' }}
                            </td>
                            <td>
                                <strong>{{ $item->name }}</strong>
                                @if ($optionLabel)
                                    <br/><small>({{ $optionLabel }})</small>
                                @endif
                                @if (!empty($specialInstruction))
                                    <div class="special-instruction">
                                        <strong>Special Instruction:</strong><br/>
                                        {{ $specialInstruction }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if ($order->status === 'pending')
                                    NA
                                @else
                                    {{ core()->formatBasePrice($item->price) }}<br/>
                                    <small>Qty: {{ $item->qty_ordered }}</small>
                                @endif
                            </td>
                            <td>
                                {{ $item->additional['persons'] ?? 'N/A' }}
                            </td>
                            <td>
                                @if ($order->status === 'pending')
                                    NA
                                @else
                                    {{ core()->formatBasePrice($item->base_total - $item->base_discount_amount) }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        <!-- Footer: Order Notes and Totals -->
        <div class="footer-section">
            <table class="footer-table">
                <tr>
                    <!-- Order Notes -->
                    <td class="order-notes-column">
                        <div class="customer-notes" style="padding: 10px;padding-left: 0px;font-size: 15px;color: #c50606;">
                            @if(isset($order->include_cutlery) && $order->include_cutlery ==1)
                            <strong>Instruction:</strong> Cutlery included
                        @endif
                        </div>
                        
                        @if ($comments->count() > 0)
                            <h4>ORDER NOTES:</h4>
                            <ul class="comment-list">
                                @foreach ($comments as $comment)
                                    <li>
                                        <strong>{{ $comment->is_admin === 1 ? 'Support' : 'Customer' }}:</strong>
                                        {{ $comment->notes }}
                                        <br/>
                                        <span style="font-size: 10px; color: #999;">
                                            ({{ date('m/d/Y h:i A', strtotime($comment->created_at)) }})
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </td>

                    <!-- Totals -->
                    <td class="totals-column">
                        @if ($order->status === 'pending')
                            <p>SubTotal: <strong>NA</strong></p>
                            <p>Tax: <strong>NA</strong></p>
                            <p>Agent Handler: <strong>NA</strong></p>
                            <p>Fbo Fee: <strong>NA</strong></p>
                            <p>Delivery Charge: <strong>NA</strong></p>
                            <p style="font-size: 15px; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                                <strong>Order Total:</strong> <strong>NA</strong>
                            </p>
                        @else
                            <p>SubTotal: <strong>{{ core()->formatBasePrice($order->sub_total) }}</strong></p>
                            
                            @if (isset($order->tax_amount))
                            <p>Tax: <strong>{{ core()->formatBasePrice($order->tax_amount) }}</strong></p>
                            @endif

                            <p>
                                Agent Handler: 
                                <strong>
                                    @if (isset($order->handlingAgent->Handling_charges))
                                        {{ core()->formatBasePrice($order->handlingAgent->Handling_charges) }}
                                    @else
                                        {{ core()->formatBasePrice(0) }}
                                    @endif
                                </strong>
                            </p>

                            <p>Fbo Fee: <strong>{{ core()->formatBasePrice($order->fbo_fee) }}</strong></p>
                            <p>Delivery Charge: <strong>{{ core()->formatBasePrice(round(($order->sub_total * 10) / 100, 2)) }}</strong></p>


                            <p style="font-size: 15px; margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                                <strong>Order Total:</strong>
                                <strong>
                                    @if (isset($order->handlingAgent->Handling_charges))
                                        {{ core()->formatBasePrice( $order->grand_total + $order->handlingAgent->Handling_charges) }}
                                    @else
                                        {{ core()->formatBasePrice($order->grand_total) }}
                                    @endif
                                </strong>
                            </p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>