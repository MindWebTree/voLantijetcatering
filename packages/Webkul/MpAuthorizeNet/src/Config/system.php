<?php

return [
   [
        'key' => 'sales.paymentmethods.mpauthorizenet',
        'name' => 'mpauthorizenet::app.admin.system.mpauthorize',
        'sort' => 6,
        'fields' => [
            [
                'name' => 'active',
                'title' => 'mpauthorizenet::app.admin.system.enable',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Yes',
                        'value' => true
                    ], [
                        'title' => 'No',
                        'value' => false
                    ]
                ],
                'validation' => 'required'
            ], [
                'name' => 'title',
                'title' => 'mpauthorizenet::app.admin.system.title',
                'type' => 'text',
                'validation' => 'required',
                'channel_based' => true,
                'locale_based' => true
            ], [
                'name' => 'description',
                'title' => 'mpauthorizenet::app.admin.system.description',
                'type' => 'textarea',
                'validation' => 'required',
                'channel_based' => false,
                'locale_based' => true
            ], [
                'name' => 'debug',
                'title' => 'mpauthorizenet::app.admin.system.debug',
                'type' => 'select',
                'options' => [
                    [
                        'title' => 'Sandbox',
                        'value' => true
                    ], [
                        'title' => 'Production',
                        'value' => false
                    ]
                ],
                'validation' => 'required'
            ],[
                'name' => 'client_key',
                'title' => 'mpauthorizenet::app.admin.system.client_key',
                'type' => 'text',
                'validation' => 'required'
            ], [
                'name' => 'api_login_ID',
                'title' => 'mpauthorizenet::app.admin.system.api_login_ID',
                'type' => 'password',
                'channel_based' => false,
                'locale_based' => false,
                'info' => 'Applicable if production is selected',
            ], [
                'name' => 'transaction_key',
                'title' => 'mpauthorizenet::app.admin.system.transaction_key',
                'type' => 'password',
                'channel_based' => false,
                'locale_based' => false,
                'info' => 'Applicable if production is selected',
            ], [
                'name' => 'test_api_login_ID',
                'title' => 'mpauthorizenet::app.admin.system.test_api_login_ID',
                'type' => 'password',
                'channel_based' => false,
                'locale_based' => false,
                'info' => 'Applicable if sandbox is selected',
            ], [
                'name' => 'test_transaction_key',
                'title' => 'mpauthorizenet::app.admin.system.test_transaction_key',
                'type' => 'password',
                'channel_based' => false,
                'locale_based' => false,
                'info' => 'Applicable if sandbox is selected',
            ],[
                'name' => 'sort',
                'title' => 'mpauthorizenet::app.admin.system.sort_order',
                'type' => 'select',
                'options' => [
                    [
                        'title' => '1',
                        'value' => 1
                    ], [
                        'title' => '2',
                        'value' => 2
                    ], [
                        'title' => '3',
                        'value' => 3
                    ], [
                        'title' => '4',
                        'value' => 4
                    ]
                ],
            ]
        ]
    ],
];