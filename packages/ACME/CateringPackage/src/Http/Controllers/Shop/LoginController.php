<?php

namespace ACME\CateringPackage\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;

use Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Http\Requests\CustomerLoginRequest;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    // use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;
    
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_config = request('_config');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        return auth()->guard('customer')->check()
            ? redirect()->route('shop.customer.profile.index')
            : view($this->_config['view']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Webkul\Customer\Http\Requests\CustomerLoginRequest $request
     * @return \Illuminate\Http\Response
     */
    public function create(CustomerLoginRequest $request)
    {

     
        $request->validated();
  
        if (
            !auth()->guard('customer')->attempt($request->only
            (['email', 'password']))
        ) {
            session()->flash(
                'error',
                trans('shop::app.customer.login-form.invalid-creds')
            );

            return redirect()->back();
        }

        if (!auth()->guard('customer')->user()->status) {

            auth()->guard('customer')->logout();

            session()->flash('warning', trans('shop::app.customer.login-form.not-activated'));

            return redirect()->back();
        }

        if (!auth()->guard('customer')->user()->is_verified) {

            session()->flash('info', trans('shop::app.customer.login-form.verify-first'));

            Cookie::queue(Cookie::make('enable-resend', 'true', 1));

            Cookie::queue(Cookie::make('email-for-resend', $request->get('email'), 1));

            auth()->guard('customer')->logout();

            return redirect()->back();
        }

    
        // After successful login, retrieve customer_token if it exists
        $customer_token = $request->_token;
        $customer_id = auth()->guard('customer')->user()->id;
        
        // sandeep comment code
        // if ($customer_token) {
        //     $existingtoken = DB::table('fbo_details')
        //         ->where('customer_token', $customer_token)
        //         ->whereNotNull('customer_id') //check customer_id exist or not
        //         ->first();
            
        //         // dd($existingtoken);
        //     // $existingid = DB::table('fbo_details')
        //     //     ->where('customer_id', $customer_id)
        //     //     ->WhereNull('customer_token') //check customer_token exist or not
        //     //     ->first();

        //     if ($existingtoken) {
        //         if (
        //             !empty($existingtoken->full_name) &&
        //             !empty($existingtoken->phone_number) &&
        //             !empty($existingtoken->email_address) &&
        //             !empty($existingtoken->tail_number)
        //         ) {
        //             DB::table('fbo_details')->where('customer_token', $customer_token)->update([
        //                 'customer_id' => $customer_id                      
        //             ]);

        //         }else{
        //             DB::table('fbo_details')->where('customer_id', $customer_id)->update([
        //                 'delivery_date' => $existingtoken->delivery_date,
        //                 'delivery_time' => $existingtoken->delivery_time,
        //             ]);
        //         } 
        //     }

        //     // sandeep add code
        //     $existingAirportFbo = DB::table('airport_fbo_details')
        //     ->where('customer_token', $customer_token)
        //     ->first();

        //     if($existingAirportFbo){
        //         DB::table('airport_fbo_details')
        //         ->where('customer_token', $customer_token)
        //         ->update([
        //             'customer_id' => $customer_id,
        //         ]);
        //     }
        // }
        /**
         * Event passed to prepare cart after login.
         */
        Event::dispatch('customer.after.login', $request->get('email'));

        // // sandeep add previous page redirect code 
        $previous_url = $request->previous_url;
        log::info('url',['url'=>$previous_url]);
        session()->forget('previous_url');
        if(!empty($previous_url)){
                $parsedPath = parse_url($previous_url, PHP_URL_PATH);
                $redirectPaths = ['checkout/cart', 'checkout/success', 'items-count', 'customer/account/orders/view'];

                $shouldRedirect = collect($redirectPaths)->contains(function ($path) use ($parsedPath) {
                    return str_contains($parsedPath, $path);
                });

                $isStaticFile = preg_match('/\.(js|css|map|jpg|png|gif)$/', $parsedPath);
    
                if ($shouldRedirect || $isStaticFile) {

            // if (collect($redirectPaths)->contains(fn($path) => str_ends_with($parsedPath, $path)) || preg_match('/\.(js|css|map|jpg|png|gif)$/', $previous_url)) {
               return redirect()->route($this->_config['redirect']);
            } else {
                return redirect($previous_url);
            }
        }else{
           return redirect()->route($this->_config['redirect']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        auth()->guard('customer')->logout();

        Event::dispatch('customer.after.logout', $id);

        return redirect()->route($this->_config['redirect']);
    }
}