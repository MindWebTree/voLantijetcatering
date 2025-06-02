<?php

Route::group(['middleware' => ['web']], function () {
    Route::prefix('checkout')->group(function () {
        Route::get('/mpauthorizenet/card/delete', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetConnectController@deleteCard')->name('mpauthorizenet.delete.saved.cart');

        Route::post('/sendtoken', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetConnectController@collectToken')->name('mpauthorizenet.get.token');

        Route::get('/create/charge', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetConnectController@createCharge')->name('mpauthorizenet.make.payment');

        // Route::get('/create/profile', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetConnectController@createCustomerProfile')->name('mpauthorizenet.make.payment');
    });
});


























 