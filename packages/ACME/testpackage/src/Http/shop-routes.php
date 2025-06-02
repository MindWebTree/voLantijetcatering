<?php

Route::group([
        'prefix'     => 'testpackage',
        'middleware' => ['web', 'theme', 'locale', 'currency']
    ], function () {

        Route::get('/', 'ACME\testpackage\Http\Controllers\Shop\testpackageController@index')->defaults('_config', [
            'view' => 'testpackage::shop.index',
        ])->name('shop.testpackage.index');

});