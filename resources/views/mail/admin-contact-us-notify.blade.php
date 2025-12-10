@component('shop::emails.layouts.master')
    @push('css')
        <style>
            body,
            table,
            td,
            p {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                color: #444;
            }

            body {
                background-color: #fff;
            }

            table.wrapper {
                width: 100%;
                max-width: 700px;
                margin: 0 auto;
                background-color: #ffffff;
            }

            .header {
                background-color: #007bff;
                color: #ffffff;
                padding: 15px 0;
                text-align: center;
            }

            .content {
                padding: 25px;
                font-size: 15px;
                line-height: 24px;
            }

            .footer {
                border-top: 1px solid #eee;
                margin-top: 25px;
                padding-top: 10px;
                text-align: center;
                font-size: 13px;
                color: #777;
            }

            @media only screen and (max-width: 768px) {
                table.wrapper {
                    max-width: 100% !important;
                }
            }
        </style>
    @endpush

    <table class="wrapper" style="width:100%;max-width:700px;margin:0 auto;background:#fff;">
        <tr>
            <td style=";color:#fff;text-align:center;padding:15px 0;">
                <a href="{{ route('shop.home.index') }}">
                    <img src="https://images.squarespace-cdn.com/content/v1/6171dbc44e102724f1ce58cf/eda39336-24c7-499b-9336-c9cee87db776/VolantiStickers-11.jpg?format=1500w"
                        alt="Volantijet Catering" style="width:180px;margin:0 auto;display:block;">
                </a>
            </td>
        </tr>

        <tr>
            <td style="padding:25px;font-family:Arial,sans-serif;font-size:15px;line-height:24px;color:#444;">
                <h2 style="color:#000;">New Contact Message Received</h2>

                <p>Dear Admin,</p>
                <p>You have received a new message through the Contact Us form on your website:</p>

                <table width="100%" cellpadding="5" cellspacing="0" style="margin-top:15px;">
                    <tr>
                        <td width="30%" style="font-weight:bold;">Name:</td>
                        <td>{{ $name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Email:</td>
                        <td>{{ $email ?? 'N/A' }}</td>
                    </tr>
                    {{-- <tr>
                        <td style="font-weight:bold;">Phone:</td>
                        <td>{{ $data['phone'] ?? 'N/A' }}</td>
                    </tr> --}}
                    <tr>
                        <td style="font-weight:bold;vertical-align:top;">Message:</td>
                        <td>{{ $messageData ?? 'N/A' }}</td>
                    </tr>
                </table>

                <p style="margin-top:25px;">Please review the message and respond accordingly.</p>
            </td>
        </tr>

        <tr>
            <td style="border-top:1px solid #eee;text-align:center;padding:10px;color:#777;font-size:13px;">
                © {{ date('Y') }} Volanti Jet Catering. All rights reserved.
            </td>
        </tr>
    </table>
@endcomponent
