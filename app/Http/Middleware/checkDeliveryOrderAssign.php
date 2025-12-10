<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Sales\Models\Shipment;

class checkDeliveryOrderAssign
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = auth()->guard('admin')->user();
        if ($user->role_id == 2) {
            $orderId = $request->route('id');
            $isAssigned = Shipment::where('order_id', $orderId)
                ->where('delivery_partner', $user->id)
                ->exists();

            if (!$isAssigned) {
                return redirect()->route('admin.sales.order.index');
                // return abort(403, 'You are not authorized to view this order.');
            }
        }

        return $next($request);
    }
}
