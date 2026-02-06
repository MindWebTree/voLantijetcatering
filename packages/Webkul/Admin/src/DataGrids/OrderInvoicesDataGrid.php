<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;

class OrderInvoicesDataGrid extends DataGrid
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
        $dbPrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('invoices')
            ->leftJoin('orders as ors', 'invoices.order_id', '=', 'ors.id')
            ->leftJoin('handling-agent as ha', 'ha.order_id', '=', 'ors.id')
            ->select('invoices.id as id', 'ors.increment_id as order_id', 'ors.status as order_status', 'invoices.created_at as created_at')
            ->selectRaw("CASE WHEN {$dbPrefix}invoices.increment_id IS NOT NULL THEN {$dbPrefix}invoices.increment_id ELSE {$dbPrefix}invoices.id END AS increment_id")
            ->selectRaw("
                (
                    {$dbPrefix}ors.base_grand_total + IFNULL({$dbPrefix}ha.Handling_charges, 0)
                ) as base_grand_total
            ");
        $this->addFilter('increment_id', 'invoices.increment_id');
        $this->addFilter('order_id', 'ors.increment_id');
        $this->addFilter('base_grand_total', 'ors.base_grand_total');
        $this->addFilter('created_at', 'invoices.created_at');

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
            'label'      => trans('admin::app.datagrid.id'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'order_id',
            'label'      => trans('admin::app.datagrid.order-id'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.invoice-date'),
            'type'       => 'datetime',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'base_grand_total',
            'label'      => trans('admin::app.datagrid.grand-total'),
            'type'       => 'price',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'order_status',
            'label'      => trans('admin::app.datagrid.status'),
            'type'       => 'string',
            'sortable'   => true,
            'searchable' => true,
            'filterable' => true,
            'closure' => function ($value) {
                if ($value->order_status == 'paid') {
                    return '<span class="badge badge-md badge-success"  style="background:#16a34a">' . trans('admin::app.sales.orders.order-status-paid') . '</span>';
                } elseif ($value->order_status == 'completed') {
                    return '<span class="badge badge-md badge-success ">' . trans('admin::app.sales.orders.order-status-success') . '</span>';
                } elseif ($value->order_status == 'ready') {
                    return '<span class="badge badge-md badge-success">' . trans('admin::app.sales.orders.order-status-ready') . '</span>';
                } elseif ($value->order_status == 'shipped') {
                    return '<span class="badge badge-md badge-success" style="background:#ff8f00">' . trans('admin::app.sales.orders.order-status-shipped') . '</span>';
                } elseif ($value->order_status == 'delivered') {
                    return '<span class="badge badge-md badge-success" style="background:#059669">' . trans('admin::app.sales.orders.order-status-deliver') . '</span>';
                } elseif ($value->order_status == 'canceled') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-canceled') . '</span>';
                } elseif ($value->order_status == 'invoice sent') {
                    return '<span class="badge badge-md badge-info" style="background:#0027ff">' . trans('admin::app.sales.orders.order-status-invoice-sent') . '</span>';
                } elseif ($value->order_status == 'pending') {
                    return '<span class="badge badge-md badge-warning">' . trans('admin::app.sales.orders.order-status-pending') . '</span>';
                } elseif ($value->order_status == 'accepted') {
                    return '<span class="badge badge-md badge-success" style="background:#10b981">' . trans('admin::app.sales.orders.order-status-accepted') . '</span>';
                } elseif ($value->order_status == 'rejected') {
                    return '<span class="badge badge-md badge-danger">' . trans('admin::app.sales.orders.order-status-rejected') . '</span>';
                }

                return ucfirst($value->order_status);
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
            'title'  => trans('admin::app.datagrid.view'),
            'method' => 'GET',
            'route'  => 'admin.sales.invoices.view',
            'icon'   => 'icon eye-icon',
        ]);
    }
}
