<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;


class CustomerCartDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';
    
    public function prepareQueryBuilder()
    {
        $customerId = request('customer_id');

        $queryBuilder = DB::table('cart')
            ->leftJoin('cart_items', 'cart_items.cart_id', '=', 'cart.id')
            ->where('cart.customer_id', $customerId)
            ->where('cart.is_active', 1)
            ->whereNull('cart_items.parent_id')
            ->select(
                'cart_items.id as id',
                'cart_items.name',
                'cart_items.quantity',
                'cart_items.price',
                'cart_items.total',
                'cart_items.tax_amount',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(cart_items.additional, '$.special_instruction')) as special_instruction"),
                'cart_items.created_at'
            );
        $this->setQueryBuilder($queryBuilder);
        
    }

    public function addColumns()
    {

        $this->addColumn([
            'index' => 'name',
            'label' => 'Product Name',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'quantity',
            'label' => 'Quantity',
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'price',
            'label' => 'Price',
            'type' => 'price',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'total',
            'label' => 'Total',
            'type' => 'price',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'tax_amount',
            'label' => 'Tax',
            'type' => 'price',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'special_instruction',
            'label' => 'Special Instruction',
            'type' => 'string',
            'searchable' => false,
            'sortable' => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.datagrid.created_at'),
            'type' => 'datetime',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);
    }

    //  public function prepareActions()
    // {
    //     $this->addAction([
    //         'title'  => trans('admin::app.datagrid.view'),
    //         'method' => 'GET',
    //         'route'  => 'admin.sales.customersInquery.viewInquery',
    //         'icon'   => 'icon eye-icon',
    //     ]);

    //     $this->addAction([
    //         'title'  => trans('admin::app.datagrid.delete'),
    //         'method' => 'POST',
    //         'route'  => 'admin.sales.customersInquery.destroyInquery',
    //         'icon'  => 'icon trash-icon',
    //     ]);
    // }

}
