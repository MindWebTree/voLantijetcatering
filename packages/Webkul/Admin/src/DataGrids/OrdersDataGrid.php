<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Sales\Models\OrderAddress;
use Webkul\Ui\DataGrid\DataGrid;

class OrdersDataGrid extends DataGrid
{
    /**
     * Index.
     *
     * @var string
     */
    protected $index = 'id';

    /**
     * Sort order.
     *
     * @var string
     */
    protected $sortOrder = 'desc';

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {

        $queryBuilder = DB::table('orders')
            ->leftJoin('addresses as order_address_shipping', function ($leftJoin) {
                $leftJoin->on('order_address_shipping.order_id', '=', 'orders.id')
                    ->where('order_address_shipping.address_type', OrderAddress::ADDRESS_TYPE_SHIPPING);
            })
            ->leftJoin('addresses as order_address_billing', function ($leftJoin) {
                $leftJoin->on('order_address_billing.order_id', '=', 'orders.id')
                    ->where('order_address_billing.address_type', OrderAddress::ADDRESS_TYPE_BILLING);
            });

        if (auth()->guard('admin')->user()->role_id == 2) {
            $queryBuilder = $queryBuilder->join('shipments', function ($join) {
                $adminUserId = auth()->guard('admin')->user()->id;
                $join->on('shipments.order_id', '=', 'orders.id')
                    ->where('shipments.delivery_partner', '=', $adminUserId);
            });
        }

        $queryBuilder = $queryBuilder
            ->addSelect([
                'orders.id',
                'orders.increment_id',
                'orders.base_sub_total',
                'orders.base_grand_total',
                'orders.total_qty_ordered',
                DB::raw("IF(orders.customer_first_name = '' AND orders.customer_last_name = '', orders.fbo_full_name, CONCAT_WS(' ', orders.customer_first_name, orders.customer_last_name)) AS user_name"),
                DB::raw("IFNULL(orders.customer_email, orders.fbo_email_address) AS customer_email"),
                'orders.created_at',
                'channel_name',
                'orders.status',
                DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name) as billed_to'),
                DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name) as shipped_to')
            ]);

        // Add filters
        // $this->addFilter('status', 'orders.status');
        $this->addFilter('billed_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_billing.first_name, " ", ' . DB::getTablePrefix() . 'order_address_billing.last_name)'));
        $this->addFilter('shipped_to', DB::raw('CONCAT(' . DB::getTablePrefix() . 'order_address_shipping.first_name, " ", ' . DB::getTablePrefix() . 'order_address_shipping.last_name)'));
        $this->addFilter('increment_id', 'orders.increment_id');
        $this->addFilter('created_at', 'orders.created_at');
//        $this->addFilter('customer_email', 'orders.customer_email');
        $this->addFilter('customer_email', DB::raw("IFNULL(orders.customer_email, orders.fbo_email_address)"));
        $this->setQueryBuilder($queryBuilder);
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index' => 'increment_id',
            'label' => 'ORDER ID',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.datagrid.order-date'),
            'type' => 'datetime',
            'sortable' => false,
            'searchable' => false,
            'filterable' => false,
            // sandeep add date time formate
            'closure' => function ($row) {
                return date('m-d-Y h:i:s A', strtotime($row->created_at));
            },
        ]);

        $this->addColumn([
            'index' => 'user_name',
            'label' => 'Customer',
            'type' => 'string',
            'searchable' => false,
            'sortable' => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'customer_email',
            'label' => 'Email Address',
            'type' => 'email',
            'searchable' => true,
            'sortable' => true,
            'filterable' => false,
        ]);
        if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role_id != 2) {
            $this->addColumn([
                'index' => 'base_grand_total',
                'label' => trans('admin::app.datagrid.grand-total'),
                'type' => 'price',
                'searchable' => false,
                'sortable' => true,
                'filterable' => false,
            ]);

            $this->addColumn([
                'index' => 'total_qty_ordered',
                'label' => 'Qty',
                'type' => 'quantity',
                'sortable' => true,
                'searchable' => false,
                'filterable' => false,
            ]);
        }

        // $this->addColumn([
        //     'index' => 'status',
        //     'label' => trans('admin::app.datagrid.status'),
        //     'type' => 'checkbox',
        //     'options' => [
        //         'pending' => trans('shop::app.customer.account.order.index.pending'),
        //         'accepted' => trans('shop::app.customer.account.order.index.accepted'),
        //         'invoice-sent' => trans('shop::app.customer.account.order.index.invoice-sent'),
        //         'paid' => trans('shop::app.customer.account.order.index.paid'),
        //         'void' => trans('shop::app.customer.account.order.index.void'),
        //         'ready' => trans('shop::app.customer.account.order.index.ready'),
        //         'shipped' => trans('shop::app.customer.account.order.index.shipped'),
        //         'rejected' => trans('shop::app.customer.account.order.index.rejected'),
        //         'canceled' => trans('shop::app.customer.account.order.index.canceled'),
        //         'delivered' => 'Delivered',
        //     ],
        $options = [
            'pending' => trans('shop::app.customer.account.order.index.pending'),
            'accepted' => trans('shop::app.customer.account.order.index.accepted'),
            'invoice-sent' => trans('shop::app.customer.account.order.index.invoice-sent'),
            'paid' => trans('shop::app.customer.account.order.index.paid'),
            'void' => trans('shop::app.customer.account.order.index.void'),
            'ready' => trans('shop::app.customer.account.order.index.ready'),
            'shipped' => trans('shop::app.customer.account.order.index.shipped'),
            'rejected' => trans('shop::app.customer.account.order.index.rejected'),
            'canceled' => trans('shop::app.customer.account.order.index.canceled'),
            'delivered' => 'Delivered',
        ];

        if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role_id == 2) {
            $options = [
                'shipped' => trans('shop::app.customer.account.order.index.shipped'),
                'canceled' => trans('shop::app.customer.account.order.index.canceled'),
                'delivered' => 'Delivered',
            ];
        }


        $this->addColumn([
            'index' => 'status',
            'label' => trans('admin::app.datagrid.status'),
            'type' => 'checkbox',
            'options' => $options,
            'sortable' => true,
            'searchable' => false,
            'filterable' => true,
            'closure' => function ($value) {
                if ($value->status == 'paid') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-paid') . '</span>';
                } elseif ($value->status == 'completed') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-success') . '</span>';
                } elseif ($value->status == 'ready') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-ready') . '</span>';
                } elseif ($value->status == 'shipped') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-shipped') . '</span>';
                } elseif ($value->status == 'delivered') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-deliver') . '</span>';
                } elseif ($value->status == 'canceled') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-canceled') . '</span>';
                } elseif ($value->status == 'invoice sent') {
                    return '<span class="badge badge-md badge-info">' . trans('admin::app.sales.orders.order-status-invoice-sent') . '</span>';
                } elseif ($value->status == 'pending') {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending') . '</span>';
                } elseif ($value->status == 'accepted') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-accepted') . '</span>';
                } elseif ($value->status == 'rejected') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-rejected') . '</span>';
                }
            },
        ]);
    }

    /**
     * Prepare actions.
     *
     * @return void
     */
    public function prepareActions()
    {
        $this->addAction([
            'title' => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route' => 'admin.sale.order.view',
            'icon' => 'icon eye-icon',
        ]);
    }
}
