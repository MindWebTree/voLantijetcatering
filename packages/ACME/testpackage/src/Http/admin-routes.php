<?php

Route::group([
        'prefix'        => 'admin/testpackage',
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('', 'ACME\testpackage\Http\Controllers\Admin\testpackageController@index')->defaults('_config', [
            'view' => 'testpackage::admin.index',
        ])->name('admin.testpackage.index');

});