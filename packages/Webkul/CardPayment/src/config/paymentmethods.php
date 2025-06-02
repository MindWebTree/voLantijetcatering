<?php
return [
    'paypal_standard' => [
        'code' => 'card_payment',
        'title' => 'Card Payment',
        'description' => 'Card Payment',
        'class' => 'Webkul\CardPayment\Payment\CardPaymen',
        'sandbox' => true,
        'active' => true,
        'sort' => 5,
    ]
];