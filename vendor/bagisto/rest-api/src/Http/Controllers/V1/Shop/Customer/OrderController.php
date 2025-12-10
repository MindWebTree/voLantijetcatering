<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Illuminate\Http\Request;
use Webkul\RestApi\Http\Resources\V1\Shop\Sales\OrderResource;
use Webkul\Sales\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;


class OrderController extends CustomerController
{
    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return OrderRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return OrderResource::class;
    }

    /**
     * Cancel customer's order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(Request $request, $id)
    {
        $order = $this->resolveShopUser($request)->all_orders()->find($id);

        if ($order && $this->getRepositoryInstance()->cancel($order)) {
            return response([
                'message' => __('rest-api::app.common-response.success.cancel', ['name' => 'Order']),
            ]);
        }

        return response([
            'message' => __('rest-api::app.common-response.error.something-went-wrong'),
        ]);
    }


        // sandeep add funtion for get order timeline
        public function getTimeline($id){

            $orderRepository = app($this->repository());
            $order = $orderRepository->findOneWhere(['id' => $id]);
            
            if (!$order) {
                return response()->json([
                    'message' => "Order not found",
                ]);
            }

            $order_status_id = [3, 5, 6, 7, 10, 11];
            $status_update = null;

            if ((isset($order->status_id) && $order->status_id == 10) || $order->status_id == 11) {
                    $order_status = DB::table('order_status_log')
                    ->leftJoin('order_status', 'order_status.id', 'order_status_log.status_id')
                    ->where('order_status_log.order_id', $id)
                    ->select('order_status.*')
                    ->whereNotIn('status_id', [3, 5])
                    ->get();

                    $status_update = DB::table('order_status_log')
                    ->join('order_status', 'order_status.id', 'order_status_log.status_id')
                    ->where('order_id', $id)
                    ->select('order_status_log.updated_at', 'order_status_log.status_id', 'order_status.status')
                    ->whereNotIn('status_id', [3, 5])
                    ->get();

            } else {
                    $order_status = DB::table('order_status')
                    ->whereNotIn('id', $order_status_id)
                    ->get();


                    $status_update = DB::table('order_status_log')
                    ->join('order_status', 'order_status.id', 'order_status_log.status_id')
                    ->where('order_id', $id)
                    ->select('order_status_log.updated_at', 'order_status_log.status_id', 'order_status.status')
                    ->whereNotIn('status_id', [3, 5])
                    ->get();
            }

            $status_update = $status_update ?? collect();

            // Create a map of status to its order and details in $status_update
            $dynamicOrder = $status_update->pluck('status')->flip()->toArray();
            $dynamicDetails = $status_update->keyBy('status');
    
            // Sort $order_status based on the order in $status_update
            $sortedStatuses = $order_status->sort(function ($a, $b) use ($dynamicOrder) {
                $aOrder = array_key_exists($a->status, $dynamicOrder) ? $dynamicOrder[$a->status] : PHP_INT_MAX;
                $bOrder = array_key_exists($b->status, $dynamicOrder) ? $dynamicOrder[$b->status] : PHP_INT_MAX;
                return $aOrder - $bOrder;
            });
    

            // Update the timestamps and add dynamic data
            $result = $sortedStatuses->map(function ($item) use ($dynamicDetails) {
                $updateObject = $dynamicDetails->get($item->status);
                if ($updateObject) {
                    $item->updated_at = $updateObject->updated_at;
                } else {
                    $item->updated_at = null;
                }
                return $item;
            });
    

            return response([
                'data'    => $result,
            ], 200);

        }
}
