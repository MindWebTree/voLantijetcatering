<?php

use Diglactic\Breadcrumbs\Breadcrumbs;

use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

 use Illuminate\Support\Facades\DB;
/**
 * Profile routes.
 */
Breadcrumbs::for('shop.customer.profile.index', function (BreadcrumbTrail $trail) {
    $trail->push(trans('shop::app.customer.account.profile.index.title'), route('shop.customer.profile.index'));
});

Breadcrumbs::for('shop.customer.profile.edit', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('velocity::app.shop.general.update'), route('shop.customer.profile.edit'));
});

/**
 * Order routes.
 */
Breadcrumbs::for('shop.customer.orders.index', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('shop::app.customer.account.order.index.page-title'), route('shop.customer.orders.index'));
});

Breadcrumbs::for('shop.customer.orders.view', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('shop.customer.orders.index');

    $trail->push(trans('velocity::app.shop.general.view'), route('shop.customer.orders.view', $id));
});

/**
 * Downloadable products.
 */
Breadcrumbs::for('shop.customer.downloadable_products.index', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('shop::app.customer.account.downloadable_products.title'), route('shop.customer.downloadable_products.index'));
});

/**
 * Wishlists.
 */
Breadcrumbs::for('shop.customer.wishlist.index', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('shop::app.customer.account.wishlist.page-title'), route('shop.customer.wishlist.index'));
});

/**
 * Compare.
 */
Breadcrumbs::for('velocity.customer.product.compare', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('velocity::app.customer.compare.compare_similar_items'), route('velocity.customer.product.compare'));
});

/**
 * Reviews.
 */
Breadcrumbs::for('shop.customer.reviews.index', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push(trans('shop::app.customer.account.review.index.page-title'), route('shop.customer.reviews.index'));
});

/**
 * Addresses.
 */
Breadcrumbs::for('shop.customer.addresses.index', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.profile.index');

    $trail->push('Fbo detail', route('shop.customer.addresses.index'));
});

Breadcrumbs::for('shop.customer.addresses.create', function (BreadcrumbTrail $trail) {
    $trail->parent('shop.customer.addresses.index');

    $trail->push(trans('shop::app.customer.account.address.create.page-title'), route('shop.customer.addresses.create'));
});

Breadcrumbs::for('shop.customer.addresses.edit', function (BreadcrumbTrail $trail, $id) {
    $trail->parent('shop.customer.addresses.index');

    $trail->push(trans('shop::app.customer.account.address.edit.page-title'), route('shop.customer.addresses.edit', $id));
});

/**
 * menu routes.
 */

Breadcrumbs::for('shop.product.parentcat', function (BreadcrumbTrail $trail) {
    $trail->push('home' ,route('shop.home.index'));
    $trail->push('menu', route('shop.product.parentcat'));
});

/**
 * category routes.
 */
// Breadcrumbs::for('shop.productOrCategory.index', function (BreadcrumbTrail $trail, $category ) {
//     $trail->push('menu' ,route('shop.product.parentcat'));
//     $trail->push($category->name, route('shop.productOrCategory.index', $category->url_path));
// });




Breadcrumbs::for('shop.productOrCategory.index', function (BreadcrumbTrail $trail, $item) {
    $trail->push('menu', route('shop.product.parentcat'));

    if ($item instanceof \Webkul\Category\Models\Category) {
        // Logic for category breadcrumb
   
        $trail->push($item->name, route('shop.productOrCategory.index', $item->url_path));
    } elseif ($item instanceof \Webkul\Product\Models\Product) {
        // Logic for product breadcrumb
    
       $category=  DB::table('product_categories')
       ->join('category_translations', 'product_categories.category_id', '=', 'category_translations.category_id')
       ->select('category_translations.name', 'category_translations.slug')
       ->where('product_categories.product_id', $item->id)
       ->first();
        // dd($category);
       $trail->push($category->name, route('shop.productOrCategory.index', $category->slug));
     
        $trail->push($item->name, route('shop.productOrCategory.index',$item->url_key));
    }
});
/**
 * product routes.
 */
