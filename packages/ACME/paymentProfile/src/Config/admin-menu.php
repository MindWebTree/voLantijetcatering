<?php

return [
    [
        'key' => 'orderManagement',
        'name' => 'Order Management',
        'route' => 'admin.sales.order.index',
        'sort' => 2,
        'icon-class' => 'order',
    ],
    [
        'key' => 'orderManagement.paymentProfile',
        'name' => 'Payment Profile',
        'route' => 'admin.paymentprofile.index',
        'sort' => 2,
        // 'icon-class' => '',
    ],
    [
        'key' => 'orderManagement.orders',
        'name' => 'Orders',
        'route' => 'admin.sales.order.index',
        'sort' => 1,
        // 'icon-class' => '',
    ],
    [
        'key' => 'orderManagement.customersInquery',
        'name' => 'Customers Inquery',
        'route' => 'admin.sales.customersInquery.displayInquerys',
        'sort' => 3,
        // 'icon-class' => '',
    ],
];