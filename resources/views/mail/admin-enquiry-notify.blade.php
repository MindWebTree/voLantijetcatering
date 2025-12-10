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

            h2 {
                color: #000;
                margin-bottom: 15px;
            }

            @media only screen and (max-width: 768px) {
                table.wrapper {
                    max-width: 100% !important;
                }
            }
        </style>
    @endpush

    <table class="wrapper">
        <tr>
            <td style="text-align:center;padding:15px 0;">
                <a href="{{ route('shop.home.index') }}">
                    <img src="https://images.squarespace-cdn.com/content/v1/6171dbc44e102724f1ce58cf/eda39336-24c7-499b-9336-c9cee87db776/VolantiStickers-11.jpg?format=1500w"
                        alt="Volantijet Catering" style="width:180px;margin:0 auto;display:block;">
                </a>
            </td>
        </tr>

        <tr>
            <td class="content">
                <h2>New Custom Order Enquiry Received</h2>

                <p>Dear Admin,</p>
                <p>You have received a new custom order enquiry from your website:</p>

                <table width="100%" cellpadding="5" cellspacing="0" style="margin-top:15px;">
                    <tr>
                        <td width="30%" style="font-weight:bold;">First Name:</td>
                        <td>{{ $data['first_name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Last Name:</td>
                        <td>{{ $data['last_name'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Email:</td>
                        <td>{{ $data['email'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Phone:</td>
                        <td>{{ $data['phone'] ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;vertical-align:top;">Message:</td>
                        <td>{{ $data['message'] ?? 'N/A' }}</td>
                    </tr>
                    @if (!empty($data['file_urls']))
                        <tr>
                            <td style="font-weight:bold;vertical-align:top;">Uploaded Files:</td>
                            <td>
                                @foreach ($data['file_urls'] as $file)
                                    <a href="{{ route('Inquery.downloadfile', ['file' => basename($file)]) }}" target="_blank">{{ basename($file) }}</a><br>
                                @endforeach
                            </td>
                        </tr>
                    @endif

                </table>

                <p style="margin-top:25px;">Please review the enquiry and respond accordingly.</p>
            </td>
        </tr>

        <tr>
            <td class="footer">
                © {{ date('Y') }} Volanti Jet Catering. All rights reserved.
            </td>
        </tr>
    </table>
@endcomponent
