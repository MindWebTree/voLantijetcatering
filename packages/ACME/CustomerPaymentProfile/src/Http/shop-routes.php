<?php

Route::group([
        'prefix'     => 'customerpaymentprofile',
        'middleware' => ['web', 'theme', 'locale', 'currency']
    ], function () {

        Route::get('/', 'ACME\CustomerPaymentProfile\Http\Controllers\Shop\CustomerPaymentProfileController@index')->defaults('_config', [
            'view' => 'customerpaymentprofile::shop.index',
        ])->name('shop.customerpaymentprofile.index');

});