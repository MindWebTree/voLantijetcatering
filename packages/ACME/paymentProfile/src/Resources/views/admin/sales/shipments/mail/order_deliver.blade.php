<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

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

        p {
            color: #555;
            line-height: 1.6;
            font-size: 14px;

        }
    </style>

</head>

<body>

    <body style="font-family: Arial, sans-serif; font-size: 16px; line-height: 1.5;">
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
        <h2 style="color: #333;">Order Delivery Confirmation</h2>
        @if ($order->customer_first_name != '')
            <p style="color: #666;">Dear {{ $order->customer_first_name . ' ' . $order->customer_last_name }},</p>
        @else
            <p style="color: #666;">Dear {{ $order->fbo_full_name }},</p>
        @endif
        <p style="color: #666;">Your order has been successfully delivered</p>

        <p style="color: #666;">Thank you for shopping with us!</p>
        </div>
    </body>
</body>

</html>
