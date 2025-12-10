<?php
use ACME\paymentProfile\Http\Controllers\Admin\ShipmentsController;
use Illuminate\Support\Facades\Route;
use ACME\paymentProfile\Http\Controllers\Admin\OrdersController;
use App\Http\Middleware\checkDeliveryOrderAssign;
use ACME\paymentProfile\Http\Controllers\Admin\QuickBookController;
use ACME\paymentProfile\Http\Controllers\Admin\paymentProfileController;
use ACME\paymentProfile\Http\Controllers\Admin\InvoicesController;
use Illuminate\Support\Facades\Artisan;

Route::group([
    'prefix' => 'admin/paymentprofile',
    'middleware' => ['web', 'admin']
], function () {

    Route::get('', 'ACME\paymentProfile\Http\Controllers\Admin\paymentProfileController@index')->defaults('_config', [
        'view' => 'paymentprofile::admin.index',
    ])->name('admin.paymentprofile.index');

    Route::get('/clear-cache', function () {
        Artisan::call('optimize:clear');
        return back()->with('success', 'Application cache cleared!');
    })->name('admin.clear.cache');

    /**
     * Orders routes.
     */
    // Route::get('orders', [OrdersController::class, 'index'])->defaults('_config', [
    //     'view' => 'paymentprofile::admin.sales.orders.index',
    // ])->name('admin.sales.order.index');    
    Route::get('customers/orders', [OrdersController::class, 'index'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.index',
    ])->name('admin.sales.order.index');

    Route::get('customers/orders/view/{id}', [OrdersController::class, 'view'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.view',
    ])->middleware(checkDeliveryOrderAssign::class)->name('admin.sale.order.view');


    Route::get('customers/orders/package-slip/{id}', [OrdersController::class, 'package_slip'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.package-slip',
    ])->name('admin.sale.order.package-slip');

    Route::get('customersorders/cancel/{id}', [OrdersController::class, 'cancel'])->defaults('_config', [
        'view' => 'paymentprofile::sales.orders.cancel',
    ])->name('admin.sale.order.cancel');

    // Route::post('orders/create/{order_id}', [OrdersController::class, 'comment'])->name('admin.sales.order.comment');
    Route::post('orders/create-comment/{order_id}', [OrdersController::class, 'comments'])->name('admin.sales.order.comments');

    /**
     * Shipments routes.
     */
    Route::get('shipments', [ShipmentsController::class, 'index'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.shipments.index',
    ])->name('admin.paymentprofile.shipments.index');

    Route::get('shipments/create/{order_id}', [ShipmentsController::class, 'create'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.shipments.create',
    ])->name('admin.paymentprofile.shipments.create');

    Route::post('shipments/create/{order_id}', [ShipmentsController::class, 'store'])->defaults('_config', [
        'redirect' => 'admin.sale.order.view',
    ])->name('admin.paymentprofile.shipments.store');

    Route::get('shipments/view/{id}', [ShipmentsController::class, 'view'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.shipments.view',
    ])->name('admin.paymentprofile.shipments.view');

    Route::post('delivery', [ShipmentsController::class, 'delivery'])->name('admin.order.status');

    Route::post('shipments/delivery/update/{id}', [ShipmentsController::class, 'update_delivery'])->name('admin.shipment.deliveryPartner.update');

    /**
     * Get products
     */

    Route::get('customers/product/view', [OrdersController::class, 'get_product'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.view',
    ])->name('admin.sale.order.view.products');

    /**
     * fbo Update
     */

    Route::post('admin/order-view/fbo-update/{order_id}', [OrdersController::class, 'update_order_fbo'])->name('order-view.fbo-update');


    /**
     * Get search order
     */

    Route::get('customers/address/view', [OrdersController::class, 'search_address'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.view',
    ])->name('admin.sale.order.view.address');

    Route::post('customers/address/create', [OrdersController::class, 'create_address'])->defaults('_config', [
        'redirect' => 'paymentprofile::admin.sales.orders.view',
    ])->name('admin.sale.order.view.search-address');

    /**
     * add products
     * 
     */
    Route::post('admin/add-order', [OrdersController::class, 'add_orders'])->defaults('_config', [
        'redirect' => 'paymentprofile::admin.sales.orders.view',
    ])->name('order-view.add-order');

    Route::post('admin/add-packaging-slip', [OrdersController::class, 'add_packaging_slip'])->defaults('_config', [
        'redirect' => 'paymentprofile::admin.sales.orders.view',
    ])->name('order-view.add-packaging-slip');
    Route::delete('admin/delete-packaging-slip/{id}', [OrdersController::class, 'delete_packaging_slip'])->name('order-view.delete-packaging-slip');
    Route::post('admin/download-packaging-slip/{slip_id}/{order_id}', [OrdersController::class, 'download_packaging_slip'])->name('order-view.download-packaging-slip');
    Route::post('admin/print-packaging-slip/{slip_id}/{order_id}', [OrdersController::class, 'print_package_slip'])->name('order-view.print.pdf.slip');

    // Route::post('admin/add-order',[OrdersController::class, 'add_orders'])->name('order-view.add-order');

    /**
     * Order Accept
     * 
     */

    Route::get('admin/order/accept/{id}', [OrdersController::class, 'order_accept'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.accept',
    ])->name('order-view.order-accept');

    /**
     * Order Reject
     * 
     */

    Route::post('admin/order/reject', [OrdersController::class, 'order_reject'])->name('order-view.order-reject');

    /**
     * add note
     * 
     */

    Route::post('admin/order/add-note', [OrdersController::class, 'add_note'])->name('order-view.add-note');

    /**
     * add products
     * 
     */
    Route::post('admin/edit-order-products', [OrdersController::class, 'edit_product'])->defaults('_config', [
        'redirect' => 'paymentprofile::admin.sales.orders.view',
    ])->name('order-view.edit-order-product');
    /**
     * remove products
     * 
     */
    // Route::get('order/remove-products/{id}',[OrdersController::class, 'remove_product'])->name('order-view.remove-order-product');
    // Route::get('order/{order_id}/remove-products/{id}', [OrdersController::class, 'remove_product'])->name('order-view.remove-order-product');

    Route::get('order/{order_id}/remove-products/{id}', [OrdersController::class, 'remove_product'])->name('order-view.remove-order-product');
    Route::post('order/update-billing/', [OrdersController::class, 'update_billing_address'])->name('order-view.update-billing-address');
    Route::post('order/update-purchase-no/', [OrdersController::class, 'update_purchase_no'])->name('order-view.update-purchase-no');
    Route::post('order/add-handler-agent/', [OrdersController::class, 'add_handler_agent'])->name('order-view.add-handler-agent');


    // routes/web.php

    // Route::get('/create-admin-payment-session', [OrdersController::class,'createAdminPaymentSession'])->name('create.admin.payment.session');


    Route::post('order/airport/fbo-detail/store', [OrdersController::class, 'store_fbo_detail'])->name('admin.fbo-details.store');

    //custom order

    Route::get('custom-order', [ordersController::class, 'custom_order'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.orders.custom-order.create',
    ])->name('custom.add-order');
    Route::post('create-custom-order', [ordersController::class, 'create_custom_order'])->name('create.custom.add-order');



    Route::get('customers/inquery', [paymentProfileController::class, 'displayInquerys'])->defaults('_config', [
        'view' => 'paymentprofile::admin.sales.customersInquery.index',
    ])->name('admin.sales.customersInquery.displayInquerys');

    Route::get('customers/inquery/view/{id}', [paymentProfileController::class, 'viewInquery'])->name('admin.sales.customersInquery.viewInquery');

    Route::post('customers/inquery/delete/{id}', [paymentProfileController::class, 'destroyInquery'])->name('admin.sales.customersInquery.destroyInquery');


    Route::get('customers/inquery/downloadfile/{file}', [paymentProfileController::class, 'downloadfile'])->name('admin.sales.customersInquery.downloadfile');

});





// sandeep add quickbook route
Route::post('/webhook/quickbooks', [QuickBookController::class, 'checkInvoiceStatus'])->name('quickbooks.checkInvoiceStatus');

