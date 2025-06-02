<?php
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use ACME\paymentProfile\Http\Controllers\Shop\paymentProfileController;
use Illuminate\Support\Facades\Route;


Route::get('/CheckoutCustomOrders', [paymentProfileController::class, 'payment_details'])->middleware(['web', 'theme', 'locale', 'currency'])->name('invoice.detail');

Route::get('/checkoutCustomOrder', [paymentProfileController::class, 'view'])
    ->middleware(['web', 'theme', 'locale', 'currency'])
    ->defaults('_config', [
        'view' => 'shop::sales.invoice-form',
    ])->name('order-invoice-view');


Route::group([
    'prefix' => 'paymentprofile',
    'middleware' => ['web', 'theme', 'locale', 'currency']
], function () {

    Route::get('/', 'ACME\paymentProfile\Http\Controllers\Shop\paymentProfileController@index')->defaults('_config', [
        'view' => 'paymentprofile::shop.index',
    ])->name('shop.paymentprofile.index');

    /**
     * invoice form
     * 
     */

    // Route::get('/invoicePayment', [paymentProfileController::class, 'payment_view'])->name('order-invoice-payment');
    /**
     * invoice detail
     * 
     */

    Route::post('/CheckoutCustomOrdersdetail', [paymentProfileController::class, 'invoice_detail'])->defaults('_config', [
        'view' => 'shop::sales.invoice-detail',
    ])->name('order-invoice-view-detail');


    /**
     * Invoices routes.
     */

    Route::get('customer/invoices/create/{order_id}', [InvoicesController::class, 'create'])->defaults('_config', [
        'view' => 'paymentprofile::shop.volantijetcatering.invoices.mail.create
            ',
    ])->name('admin.sales.invoices.mail.create');


    // sandeep create quickBook invoice route
    // Route::get('/quickbooks/connect', [InvoicesController::class, 'connect'])->name('quickbooks.connect');
    // Route::get('/callback', [InvoicesController::class, 'callback'])->name('quickbooks.callback');
    // Route::get('/create-invoice/{orderId}', [InvoicesController::class, 'createInvoice'])->name('quickbooks.invoice.create');
    /**
     * Payment message
     * 
     */
    Route::get('customer/payment/success/message', [paymentProfileController::class, 'success_message'])->name('customer.payment.success.message');
    Route::get('customer/payment/error/message', [paymentProfileController::class, 'error_message'])->name('customer.payment.error.message');

    /* pdf test controller*/
    Route::get('pdfDownload/{id}', [InvoicesController::class, 'downloadPdf'])->defaults('_config', [
        'view' => 'shop::pdf.invoices',
    ]);

    // sandeep 

    // Route::get('/customer/showinquery',[paymentProfileController::class,'showInquery'])->name('show.inquery');
  
});


