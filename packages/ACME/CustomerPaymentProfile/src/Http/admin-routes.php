<?php

Route::group([
        'prefix'        => 'admin/customerpaymentprofile',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'ACME\CustomerPaymentProfile\Http\Controllers\Admin\CustomerPaymentProfileController@index')->defaults('_config', [
            'view' => 'customerpaymentprofile::admin.index',
        ])->name('admin.customerpaymentprofile.index');

});