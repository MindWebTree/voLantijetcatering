<?php

return [
    [
        'key' => 'orderManagement',
        'name' => 'Order Management',
        'route' => 'admin.paymentprofile.index',
        'sort' => 2
    ], [
        'key' => 'orderManagement.order',
        'name' => 'Orders',
        'route' => 'admin.sales.order.index',
        'sort' => 1,
        // 'icon-class' => '',
    ], [
        'key'   => 'orderManagement.order.view',
        'name'  => 'admin::app.acl.view',
        'route' => 'admin.sale.order.view',
        'sort'  => 1,
    ], [
        'key'   => 'orderManagement.order.cancel',
        'name'  => 'cancel',
        'route' => 'admin.sale.order.cancel',
        'sort'  => 2,
    ]
];