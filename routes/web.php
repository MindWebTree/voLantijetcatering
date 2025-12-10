<?php

use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\QuickBookController;
use Webkul\Shop\Http\Controllers\AgeVerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/quickbook-connect',[QuickBookController::class,'connect'])->name('quickbook.connect');

Route::get('/callback', [QuickBookController::class, 'callback'])->name('quickbooks.callback');


Route::post('/user/save-dob', [AgeVerificationController::class, 'save_user_dob']);
