@component('shop::emails.layouts.master')

    <h1 style="color: #e3342f; font-size: 24px; margin-bottom: 20px;text-align:center">{{ $orderData['Mail_Heading'] }}</h1>

    <p style="font-size: 14px; color: #333;">Dear Admin,</p>

    <p style="font-size: 14px; color: #333;">
        We would like to inform you that an error occurred during the order processing.  
        Please find the order details below:
    </p>

    <ul style="font-size: 14px; color: #333; line-height: 1.6; list-style-type: none; padding-left: 0;">
        <li><strong>Order ID: </strong> {{ $orderData['order_id'] ?? 'N/A' }}</li>
        <li><strong>Status: </strong> {{ $orderData['status'] ?? 'N/A' }}</li>
        <li><strong>Customer Name: </strong> {{ $orderData['Name'] ?? 'N/A' }}</li>
        <li><strong>Customer Email: </strong> {{ $orderData['Email'] ?? 'N/A' }}</li>
        <li><strong>Error Message: </strong> {{ $orderData['error_message'] ?? 'N/A' }}</li>
    </ul>

    <p style="font-size: 14px; color: #333; margin-top: 20px;">
        Kindly review the error and take necessary action to ensure proper order processing.  
        You can also refer to the system logs for more technical information.
    </p>

    <p style="font-size: 14px; color: #333;">Thank you for your attention to this matter.</p>

@endcomponent
