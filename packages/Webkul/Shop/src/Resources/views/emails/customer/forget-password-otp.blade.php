@component('shop::emails.layouts.master')

<h2>Hello {{ $customer->first_name . ' ' . $customer->last_name }},</h2>
    
<p>You have requested to reset your password. Here is your OTP:</p>

<h1 style="font-size: 36px; letter-spacing: 5px; text-align: center; padding: 10px; background: #f5f5f5;">
    {{ $otp }}
</h1>

<p>This OTP will expire in 10 minutes.</p>

<p>If you didn't request this password reset, please ignore this email.</p>

<p>Thanks</p>

@endcomponent