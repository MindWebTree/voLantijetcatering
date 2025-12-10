<?php


use Illuminate\Support\Facades\Route;
use Webkul\Shop\Http\Controllers\CartController;
use ACME\CateringPackage\Http\Controllers\Shop\CartsController;
use ACME\CateringPackage\Http\Controllers\Shop\LoginController;
use ACME\CateringPackage\Http\Controllers\Shop\SignUpController;
// use Illuminate\Routing\Route;
use ACME\CateringPackage\Http\Controllers\Shop\CheckoutController;
use Webkul\Shop\Http\Controllers\ProductsCategoriesProxyController;


Route::group([
    'prefix' => 'cateringpackage',
    'middleware' => ['web', 'theme', 'locale', 'currency']
], function () {

    // Route::get('/', 'ACME\CateringPackage\Http\Controllers\Shop\CateringPackageController@index')->defaults('_config', [
    //     'view' => 'cateringpackage::shop.index',
    // ])->name('shop.cateringpackage.index');


});

//  Route::get('/', 'ACME\CateringPackage\Http\Controllers\Shop\CateringPackageController@index')->defaults('_config', [
//              'view' => 'shop::home.index',
// ])->name('shop.home.index')->middleware();



Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('/', 'ACME\CateringPackage\Http\Controllers\Shop\CateringPackageController@index')->defaults('_config', [
        'view' => 'shop::home.index',

    ])->name('shop.home.index');


    Route::post('address/create', 'ACME\CateringPackage\Http\Controllers\Shop\CateringPackageController@create')->defaults('_config', [
        'redirect' => 'shop::home.index',
    ])->name('shop.home.create');

    /**
     * Login routes.
     */

    Route::get('signIn', [LoginController::class, 'show'])->defaults('_config', [
        'view' => 'cateringpackage::shop.customer.signup',
    ])->name('shop.customer.session.index');

    Route::post('signIn', [LoginController::class, 'create'])->defaults('_config', [
        'redirect' => 'shop.customer.profile.index',
    ])->name('shop.customer.session.create');

    /**
     * Registration routes.
     */
    Route::get('register', [SignUpController::class, 'show'])->defaults('_config', [
        'view' => 'cateringpackage::shop.customer.signup',
    ])->name('shop.customer.register.index');

    Route::post('register', [SignUpController::class, 'create'])->defaults('_config', [
        'redirect' => 'shop.customer.profile.index',
    ])->name('shop.customer.register.create');

     /**
     * FBO routes.
     */

    Route::get('checkout/fbo-detail', [SignUpController::class, 'fbo_details'])->defaults('_config', [
        'view' => 'cateringpackage::shop.customer.fbo',
    ])->name('cateringpackage.shop.customer.fbo');

    Route::post('checkout/fbo-detail', [SignUpController::class, 'add_fbo_details'])->defaults('_config', [
    ])->name('cateringpackage.shop.customer.add-fbo');
    Route::post('checkout/fbo-profile-detail', [SignUpController::class, 'add_profile_fbo'])->defaults('_config', [
    ])->name('cateringpackage.shop.customer.add_profile_fbo');

    Route::post('checkout/fbo-detail/update', [SignUpController::class, 'update_fbo_detail'])->name('cateringpackage.shop.customer.update-fbo');
    Route::post('checkout/fbo-profile/update', [SignUpController::class, 'update_fbo_profile'])->name('cateringpackage.shop.customer.update-fbo-profile');

    /**
     * Checkout routes.
     */
    // Route::get('onepage/checkout', [CheckoutController::class, 'index'])->defaults('_config', [
    //     'view' => 'cateringpackage::shop.customer.checkout',
    // ])->name('shop.checkout.onepage.show_fbo_detail');
    // Route::get('onepage/checkout', [CheckoutController::class, 'show_fbo_detail'])->defaults('_config', [
    //     'view' => 'cateringpackage::shop.customer.checkout',
    // ])->name('shop.checkout.onepage.show_fbo_detail');

        /**
     * Cart routes.
     */
    Route::get('checkout/cart', [CartsController::class, 'index'])->defaults('_config', [
        'view' => 'shop::checkout.cart.index',
    ])->name('shop.checkout.cart.index');

    Route::post('checkout/cart/add/{id}', [CartsController::class, 'add'])->defaults('_config', [
        'redirect' => 'shop.checkout.cart.index',
    ])->name('shop.cart.add');

    // sandeep add route for update mini cart quantity
    Route::post('checkout/update/mini_cart/{id}', [CartsController::class, 'update_miniCart_item'])->defaults('_config', [
        'redirect' => 'shop.checkout.cart.index',
    ])->name('shop.update.mini_cart_item');

    /**
    * single product routes.
    */
    Route::get('single-product',[ProductsCategoriesProxyController::class,'product_info'])->name('single-product');
    // sandeep route

    Route::get('checkout/cart/remove/{id}', [CartsController::class, 'remove'])->name('shop.cart.remove');

    Route::post('checkout/cart/remove}', [CartsController::class, 'removeAllItems'])->name('shop.cart.remove.all.items');

    Route::post('checkout/cart', [CartsController::class, 'updateBeforeCheckout'])->defaults('_config', [
        'redirect' => 'shop.checkout.cart.index',
    ])->name('shop.checkout.cart.update');

    Route::get('checkout/cart/remove/{id}', [CartsController::class, 'remove'])->defaults('_config', [
        'redirect' => 'shop.checkout.cart.index',
    ])->name('shop.checkout.cart.remove');

    Route::post('move/wishlist/{id}', [CartsController::class, 'moveToWishlist'])->name('shop.move_to_wishlist');

    /**
     * Coupon routes.
     */
    Route::post('checkout/cart/coupon', [CartsController::class, 'applyCoupon'])->name('shop.checkout.cart.coupon.apply');

    Route::delete('checkout/cart/coupon', [CartsController::class, 'removeCoupon'])->name('shop.checkout.coupon.remove.coupon');

    /**
     * Checkout routes.
     */
    Route::get('onepage/checkout', [CheckoutController::class, 'index'])->defaults('_config', [
        'view' => 'cateringpackage::shop.customer.checkout',
    ])->name('shop.checkout.onepage.show_fbo_detail'); 

    Route::get('checkout/summary', [CheckoutController::class, 'summary'])->name('shop.checkout.summary');

    Route::post('checkout/save-address', [CheckoutController::class, 'saveAddress'])->name('shop.checkout.save_address');

    Route::post('checkout/save-shipping', [CheckoutController::class, 'saveShipping'])->name('shop.checkout.save_shipping');

    Route::post('checkout/save-payment', [CheckoutController::class, 'savePayment'])->name('shop.checkout.save_payment');

    Route::post('checkout/check-minimum-order', [CheckoutController::class, 'checkMinimumOrder'])->name('shop.checkout.check_minimum_order');

    Route::post('checkout/save-order', [CheckoutController::class, 'saveOrder'])->name('shop.checkout.save_order');

    Route::get('checkout/success', [CheckoutController::class, 'success'])->defaults('_config', [
        'view' => 'shop::checkout.success',
    ])->name('shop.checkout.success');

    Route::prefix('customer')->group(function () {
        /**
         * For customer exist check.
         */
        Route::post('/customer/exist', [CheckoutController::class, 'checkExistCustomer'])->name('shop.customer.checkout.exist');

        /**
         * For customer login checkout.
         */
        Route::post('/customer/checkout/login', [CheckoutController::class, 'loginForCheckout'])->name('shop.customer.checkout.login');
    });


    Route::post('airport/fbo-detail/store', 'ACME\CateringPackage\Http\Controllers\Shop\CateringPackageController@store')->defaults('_config', [
        'view' => 'shop::home.index',
    ])->name('shop.home.fbo-details.store');
    
});
