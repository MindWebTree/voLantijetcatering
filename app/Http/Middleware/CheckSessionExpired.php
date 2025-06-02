<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cookie;


class CheckSessionExpired
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $session = session()->get('_previous');
        $xsrfToken = $request->cookie('XSRF-TOKEN');
        $sessionId = $request->cookie('volantijetcatering_session');
        $session = session()->get('_previous');
       
        if(!$request->is('admin/*') && !$request->is('api/*')){
            if(!$xsrfToken && !$session && $sessionId){
                return redirect('/');
            }
        }
        return $next($request);
    }
}
