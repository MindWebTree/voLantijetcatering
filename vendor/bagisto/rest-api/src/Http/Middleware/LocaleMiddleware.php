<?php

namespace Webkul\RestApi\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Webkul\Core\Repositories\LocaleRepository;
use Webkul\Shop\src\Model\ApiRequestLog;
use Auth;
use Illuminate\Support\Facades\DB;
use Webkul\RestApi\Traits\ProvideUser;
use Laravel\Sanctum\PersonalAccessToken;

class LocaleMiddleware
{
    /**
     * Locale repository.
     *
     * @var \Webkul\Core\Repositories\LocaleRepository
     */
    protected $localeRepository;
    use ProvideUser;

    /**
     * Create a middleware instance.
     *
     * @param  \Webkul\Core\Repositories\LocaleRepository  $localeRepository
     * @return void
     */
    public function __construct(LocaleRepository $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {

        // dd( $request);
        // dd($request->fullUrl());
     // sandeep add code for track api requests
     $shopUser = $this->resolveShopUser($request);

     if(!$shopUser){
        $guestToken = $request->bearerToken() ?? $request->header('token');
        if ($guestToken) {
            $accessToken = PersonalAccessToken::findToken($guestToken);
            if ($accessToken && $accessToken->tokenable_type === 'Webkul\Customer\Models\Customer') {
                $shopUser = $accessToken->tokenable; 
            }
        }
    }

        if (isset($shopUser->id)) {
        $customer_id = $shopUser->id;

        
        // insert device and user information in api request log table
        DB::table('api_request_log')->insert([
            'device_id' => $request->header('device_id'),
            'device_name' => $request->header('device_name'),
            'os' => $request->header('os'),
            'customer_id' => $customer_id,
            'url' => $request->fullUrl(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // insert data in device table
        DB::table('device_table')->insert([
            'device_id' => $request->header('device_id'),
            'device_name' => $request->header('device_name'),
            'os' => $request->header('os'),
            'device_token' => $request->header('device_token'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

        $localeCode = $request->header('x-locale');

        if ($localeCode && $this->localeRepository->findOneByField('code', $localeCode)) {
            app()->setLocale($localeCode);
            return $next($request);
        }

        app()->setLocale(core()->getDefaultChannel()->default_locale->code);

        return $next($request);
    }
}
