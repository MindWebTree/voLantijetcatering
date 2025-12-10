@component('shop::emails.layouts.master')
    <div>
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

        <div style="padding: 30px;">
            <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
                <p style="font-weight: bold;font-size: 20px;color: #242424;line-height: 24px;">
                    {{ __('shop::app.mail.customer.registration.dear', ['customer_name' => $data['fullname']]) }},
                </p>

                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    {!! __('shop::app.mail.customer.registration.greeting') !!}
                </p>
            </div>

            <div style="font-size: 16px;color: #5E5E5E;line-height: 30px;margin-bottom: 20px !important;">
                {{ __('shop::app.mail.customer.registration.summary') }}
            </div>

            <p style="text-align: center;padding: 20px 0;">
                <a href="{{ route('shop.customer.session.index') }}" style="padding: 10px 20px;background: #0041FF;color: #ffffff;text-transform: uppercase;text-decoration: none; font-size: 16px">
                    {{ __('shop::app.header.sign-in') }}
                </a>
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {!!
                    __('shop::app.mail.order.help', [
                        'support_email' => '<a style="color:#0041FF" href="mailto:' . core()->getSenderEmailDetails()['email'] . '">' . core()->getSenderEmailDetails()['email']. '</a>'
                        ])
                !!}
            </p>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                {{ __('shop::app.mail.customer.registration.thanks') }}
            </p>
        </div>
    </div>
@endcomponent
