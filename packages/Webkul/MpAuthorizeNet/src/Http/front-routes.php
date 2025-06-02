<?php

Route::group(['middleware' => ['web','customer','theme', 'locale', 'currency']], function () {
    Route::prefix('mpauthorizenet')->group(function () {
        Route::get('/account/save/card', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetAccountController@saveCard')->defaults('_config', [
            'view' => 'mpauthorizenet::shop.customer.account.savecard.savecard'
        ])->name('mpauthorizenet.account.save.card');

        Route::post('/account/store/card', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetAccountController@storeCard')->name('mpauthorizenet.account.store.card');

        Route::get('/account/make/card/default', 'Webkul\MpAuthorizeNet\Http\Controllers\MpAuthorizeNetAccountController@cardDefault')->name('mpauthorizenet.account.make.card.default');
    });
});