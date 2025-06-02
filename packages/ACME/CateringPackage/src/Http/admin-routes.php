<?php

use Illuminate\Support\Facades\Route;

Route::group([
        'prefix'        => 'delivery', //admin/cateringpackage
        'middleware'    => ['web', 'admin']
    ], function () {

        Route::get('airports', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@index')->defaults('_config', [
            'view' => 'cateringpackage::admin.index',
        ])->name('admin.cateringpackage.index');

        
        Route::get('airport/create', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@create')->defaults('_config', [
            'view' => 'cateringpackage::admin.create',
        ])->name('admin.cateringpackage.create');

            Route::post('airport/create', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@store')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.index',
        ])->name('admin.cateringpackage.store');


        Route::get('airport/edit/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@edit')->defaults('_config', [
            'view' => 'cateringpackage::admin.edit',
        ])->name('admin.cateringpackage.edit');


         Route::post('airport/edit/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@update')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.index', 
        ])->name('admin.cateringpackage.update');


         Route::post('countries', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@getStates')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.index
            ',
        ])->name('countries');


        Route::post('airport/delete/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@destroy')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.index',
        ])->name('admin.cateringpackage.delete');


        // Route::post('airport/delete/{id}', [SliderController::class, 'destroy'])->name('admin.sliders.delete');

        // Route::post('airport/mass-delete', 'ACME\CateringPackage\Http\Controllers\Admin\CateringPackageController@massDestroy')->defaults('_config', [
        //     'redirect' => 'admin.cateringpackage.index',
        // ])->name('admin.cateringpackage.mass_delete');



        // 20-05-2024 || airport fbo details //

        Route::get('airports/fbo-details/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@index')->defaults('_config', [
            'view' => 'cateringpackage::admin.airport-fbo-details.index',
        ])->name('admin.cateringpackage.airport-fbo-details.index');

        Route::get('airport/fbo-detail/create', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@create')->defaults('_config', [
            'view' => 'cateringpackage::admin.airport-fbo-details.create',
        ])->name('admin.cateringpackage.fbo-details.create');

        Route::post('airport/fbo-detail/create', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@store')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.airport-fbo-details.index',
        ])->name('admin.cateringpackage.fbo-details.store');

        Route::get('airport/fbo-detail/edit/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@edit')->defaults('_config', [
            'view' => 'cateringpackage::admin.airport-fbo-details.edit',
        ])->name('admin.cateringpackage.fbo-details.edit');

        Route::post('airport/fbo-detail/edit/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@update')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.airport-fbo-details.index', 
        ])->name('admin.cateringpackage.fbo-details.update');

        Route::post('airport/fbo-detail/delete/{id}', 'ACME\CateringPackage\Http\Controllers\Admin\AirportFboDetails@destroy')->defaults('_config', [
            'redirect' => 'admin.cateringpackage.airport-fbo-details.index',
        ])->name('admin.cateringpackage.fbo-details.delete');
        
});