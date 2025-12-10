<?php

namespace Webkul\Shop\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class OrderDataGrid extends DataGrid
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
        $queryBuilder = DB::table('orders as order')
        ->select('order.id', 'order.increment_id', 'order.status', 'order.created_at', 'order.grand_total', 'order.order_currency_code', 'addresses.airport_name','addresses.address1','handling-agent.Handling_charges')
        ->rightjoin('addresses', 'order.id', '=', 'addresses.order_id')
        ->leftjoin('handling-agent', 'order.id', '=', 'handling-agent.order_id')
        ->where('order.customer_id', auth()->guard('customer')->user()->id)
        ->where('addresses.address_type','order_shipping')
        ->distinct();
     
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
            'index'      => 'increment_id',
            'label'      => trans('shop::app.customer.account.order.index.order_id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('shop::app.customer.account.order.view.order-date'),
            'type'       => 'datetime',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'grand_total',
            'label'      => trans('shop::app.customer.account.order.index.total'),
            'type'       => 'number',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($value) {
                
                return core()->formatPrice($value->grand_total, $value->order_currency_code);
            },
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('shop::app.customer.account.order.index.status'),
            'type'       => 'checkbox',
            'options'    => [
                'processing'      => trans('shop::app.customer.account.order.index.processing'),
                'completed'       => trans('shop::app.customer.account.order.index.completed'),
                'canceled'        => trans('shop::app.customer.account.order.index.canceled'),
                'closed'          => trans('shop::app.customer.account.order.index.closed'),
                'pending'         => trans('shop::app.customer.account.order.index.pending'),
                'pending_payment' => trans('shop::app.customer.account.order.index.pending-payment'),
                'fraud'           => trans('shop::app.customer.account.order.index.fraud'),
            ],
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($value) {
                if ($value->status == 'processing') {
                    return '<span class="badge badge-md badge-success">' . trans('shop::app.customer.account.order.index.processing') . '</span>';
                } elseif ($value->status == 'completed') {
                    return '<span class="badge badge-md badge-success">' . trans('shop::app.customer.account.order.index.completed') . '</span>';
                } elseif ($value->status == 'rejected') {
                    return '<span class="badge badge-md badge-danger">' . trans('shop::app.customer.account.order.index.rejected') . '</span>';
                } elseif ($value->status == 'closed') {                    
                } elseif ($value->status == 'canceled') {
                    return '<span class="badge badge-md badge-danger">' . trans('shop::app.customer.account.order.index.canceled') . '</span>';
                } elseif ($value->status == 'closed') {                    
                    return '<span class="badge badge-md badge-info">' . trans('shop::app.customer.account.order.index.closed') . '</span>';
                } elseif ($value->status == 'pending') {
                    return '<span class="badge badge-md badge-warning">' . trans('shop::app.customer.account.order.index.pending') . '</span>';
                } elseif ($value->status == 'invoice sent') {
                    return '<span class="badge badge-md badge-primary">' . trans('Invoice-sent') . '</span>';
                } elseif ($value->status == 'pending_payment') {
                    return '<span class="badge badge-md badge-warning">' . trans('shop::app.customer.account.order.index.pending-payment') . '</span>';
                } elseif ($value->status == 'fraud') {                    
                    return '<span class="badge badge-md badge-danger">' . trans('shop::app.customer.account.order.index.fraud') . '</span>';
                }
                elseif ($value->status == 'paid') {                    
                    return '<span class="badge badge-md badge-success paid">Paid</span>';
                }
                elseif ($value->status == 'accepted') {           
                  //  sandeep chenge status 
                    return '<span class="badge badge-md badge-success accept">Accepted</span>';
                }
                elseif ($value->status == 'invoiced') {                    
                    return '<span class="badge badge-md badge-success accept">Accept</span>';
                }
                elseif ($value->status == 'delivered') {                    
                    return '<span class="badge badge-md badge-success accept">Delivered</span>';
                }
                elseif ($value->status == 'ready') {                    
                    return '<span class="badge badge-md badge-success accept">Ready</span>';
                }
                elseif ($value->status == 'shipped') {                    
                    return '<span class="badge badge-md badge-success accept">Shipped</span>';
                }
                
            },
            'filterable' => true,
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
            'title'  => trans('ui::app.datagrid.view'),
            'type'   => 'View',
            'method' => 'GET',
            'route'  => 'shop.customer.orders.view',
            'icon'   => 'icon eye-icon',
        ], true);
    }
}
