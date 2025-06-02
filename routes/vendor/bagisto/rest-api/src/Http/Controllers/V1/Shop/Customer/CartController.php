<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Elasticsearch\Endpoints\Ml\Validate;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\RestApi\Http\Resources\V1\Shop\Checkout\CartResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CartController extends CustomerController
{
    /**
     * Get the customer cart.
     *
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
    
        $cart = Cart::getCart();

        return response([
            'data' => $cart ? new CartResource($cart) : null,
        ]);
    }

    /**
     * Add item to the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Webkul\Customer\Repositories\WishlistRepository $wishlistRepository
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function add(Request $request, WishlistRepository $wishlistRepository, int $productId)
    {

        // sandeep || add validation 
       $product = DB::table('products')
                      ->where('id',$productId)
                      ->select('type')
                      ->first();
                  
        if($product->type == 'configurable'){
           return response()->json([
            'message' => "Please select option", 
          ]);
        }

        // sandeep || add qyt error message
        $qty = $request->quantity;
        if(!$qty && $qty <= 0){
            return response([
                'message' => "The Quantity field must be 1 or more",
            ], 422);
        }

         $customer = $this->resolveShopUser($request);

        try {
            Event::dispatch('checkout.cart.item.add.before', $productId);
         
            $result = Cart::addProduct($productId, $request->all());
      
            if (is_array($result) && isset($result['warning'])) {
                return response([
                    'message' => $result['warning'],
                ], 400);
            }
    
            $wishlistRepository->deleteWhere(['product_id' => $productId, 'customer_id' => $customer->id]);

            Event::dispatch('checkout.cart.item.add.after', $result);

            Cart::collectTotals();

        
            $cart = Cart::getCart();
            
            return response([
                'data'    => $cart ? new CartResource($cart) : null,
                'message' => __('rest-api::app.checkout.cart.item.success'),
            ], 200);
        } catch (Exception $e) {
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update the cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Webkul\Checkout\Repositories\CartItemRepository  $cartItemRepository
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CartItemRepository $cartItemRepository)
    {
        $this->validate($request, [
            'qty' => 'required|array',
        ]);

        foreach ($request->qty as $qty) {
            if ($qty <= 0) {
                return response([
                    'message' => __('rest-api::app.checkout.cart.quantity.illegal'),
                ], 422);
            }
        }

        foreach ($request->qty as $itemId => $qty) {
            $item = $cartItemRepository->findOneByField('id', $itemId);

            Event::dispatch('checkout.cart.item.update.before', $itemId);

            Cart::updateItems(['qty' => $request->qty]);

            Event::dispatch('checkout.cart.item.update.after', $item);
        }

        Cart::collectTotals();

        $cart = Cart::getCart();

        return response([
            'data'    => $cart ? new CartResource($cart) : null,
            'message' => __('rest-api::app.checkout.cart.quantity.success'),
        ], 200);
    }

    /**
     * Remove item from the cart.
     *
     * @param  int  $cartItemId
     * @return \Illuminate\Http\Response
     */
    public function removeItem($cartItemId)
    {
        
        Event::dispatch('checkout.cart.item.delete.before', $cartItemId);

        // Cart::removeItem($cartItemId);
        // sandeep || add delete code
        DB::table('cart_items')->where('cart_id', $cartItemId)->delete();

        DB::table('cart')->where('id', $cartItemId)->delete();

        Event::dispatch('checkout.cart.item.delete.after', $cartItemId);

        Cart::collectTotals();
                              
        $cart = Cart::getCart();

        return response([
            'data'    => $cart ? new CartResource($cart) : null,
            'message' => __('rest-api::app.checkout.cart.item.success-remove'),
        ]);
    }
     
    /**
     * Empty the cart.
     *
     * @return \Illuminate\Http\Response
     */
    function empty() {
        Event::dispatch('checkout.cart.delete.before');

        Cart::deActivateCart();

        Event::dispatch('checkout.cart.delete.after');

        $cart = Cart::getCart();

        return response([
            'data'    => $cart ? new CartResource($cart) : null,
            'message' => __('rest-api::app.checkout.cart.item.success-remove'),
        ]);
    }

    /**
     * Apply the coupon code.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function applyCoupon(Request $request)
    {
        $couponCode = $request->code;

        try {
            if (strlen($couponCode)) {
                Cart::setCouponCode($couponCode)->collectTotals();

                if (Cart::getCart()->coupon_code == $couponCode) {
                    
                    $cart = Cart::getCart();

                    return response([
                        'data'    => $cart ? new CartResource($cart) : null,
                        'message' => __('rest-api::app.checkout.cart.coupon.success'),
                    ]);
                }
            }

            return response([
                'message' => __('rest-api::app.checkout.cart.coupon.invalid'),
            ], 400);
        } catch (\Exception $e) {
            report($e);

            return response([
                'message' => __('rest-api::app.checkout.cart.coupon.apply-issue'),
            ], 400);
        }
    }

    /**
     * Remove the coupon code.
     *
     * @return \Illuminate\Http\Response
     */
    public function removeCoupon()
    {
        Cart::removeCouponCode()->collectTotals();
                    
        $cart = Cart::getCart();

        return response([
            'data'    => $cart ? new CartResource($cart) : null,
            'message' => __('rest-api::app.checkout.cart.coupon.success-remove'),
        ]);
    }

    /**
     * Move cart item to wishlist.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function moveToWishlist($cartItemId)
    {
        Event::dispatch('checkout.cart.item.move-to-wishlist.before', $cartItemId);

        Cart::moveToWishlist($cartItemId);

        Event::dispatch('checkout.cart.item.move-to-wishlist.after', $cartItemId);

        Cart::collectTotals();

        $cart = Cart::getCart();

        return response([
            'data'    => $cart ? new CartResource($cart) : null,
            'message' => __('rest-api::app.checkout.cart.move-wishlist.success'),
        ]);
    }
}
