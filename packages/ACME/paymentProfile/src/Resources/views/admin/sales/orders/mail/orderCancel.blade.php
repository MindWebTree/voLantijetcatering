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
        {{-- <h3>Order Canceled</h3> --}}
        <p>Dear {{ $order->customer_first_name !== '' ? $order->customer_first_name : $order->fbo_full_name  }},</p>
        <p>We regret to inform you that your order #<span class="order-id">{{ $order->increment_id }}</span>, has been canceled.</p>
        <p>Please contact our customer service for more information or assistance.</p>
        {{-- <p>Status: <span class="status">{{ $order->status }}</span></p> --}}
    </div>
</body>
</html>
