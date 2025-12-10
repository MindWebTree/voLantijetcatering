<?php

namespace ACME\paymentProfile\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Core\Models\Channel;



class paymentProfileDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {

        $queryBuilder = DB::table('customer_payment_profile')->select('id')->addSelect('id', 'profile_id', 'payment_profile_id', 'customer_id', 'email', 'airport', 'order_id', 'billing_address', 'created_at');

        $this->setQueryBuilder($queryBuilder);

    }

    public function addColumns()
    {
        $this->addColumn([
            'index' => 'id',
            'label' => 'id',
            'type' => 'number',
            'searchable' => false,
            'sortable' => true,
            'filterable' => true,
        ]);




        $this->addColumn([
            'index' => 'profile_id',
            'label' => 'profile id',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index' => 'payment_profile_id',
            'label' => 'Payment Id',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'customer_id',
            'label' => 'Customer Id',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index' => 'email',
            'label' => 'Email',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'order_id',
            'label' => 'Order Id',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index' => 'airport',
            'label' => 'Airport Name',
            'type' => 'number',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index' => 'billing_address',
            'label' => 'Address',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.datagrid.created_at'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

    }

    // public function prepareActions()
    // {
    //     $this->addAction([
    //         'title'  => trans('admin::app.datagrid.edit'),
    //         'method' => 'GET',
    //         'route'  => 'admin.cateringpackage.edit',
    //         'icon'   => 'icon pencil-lg-icon',
    //     ]);


    //     $this->addAction([
    //         'title'  => trans('admin::app.datagrid.delete'),
    //         'method' => 'POST',
    //         'route'  => 'admin.cateringpackage.delete',
    //         'icon'   => 'icon trash-icon',
    //     ]);
    // }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    // public function prepareMassActions()
    // {   
    //     $this->addMassAction([
    //         'type'   => 'delete',
    //         'label'  => trans('admin::app.datagrid.delete'),
    //         'action' => route('admin.sliders.mass_delete'),
    //         'method' => 'POST',
    //     ]);
    // }
}