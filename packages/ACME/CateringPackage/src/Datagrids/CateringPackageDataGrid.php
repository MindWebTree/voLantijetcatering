<?php

namespace ACME\CateringPackage\Datagrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Core\Models\Channel;



class CateringPackageDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    public function prepareQueryBuilder()
    {   
        $queryBuilder = DB::table('delivery_location_airports')->select('id')->addSelect('id','name','latitude','longitude','country','display_order','active','created_at','updated_at','deleted_at');
        
        $this->setQueryBuilder($queryBuilder);
    }

    public function addColumns()
    {   
        $this->addColumn([
            'index'      => 'id',
            'label'      => 'id',
            'type'       => 'number',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index'      => 'name',
            'label'      => trans('admin::app.datagrid.name'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'latitude',
            'label'      => trans('admin::app.datagrid.latitude'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);


        $this->addColumn([
            'index'      => 'longitude',
            'label'      => trans('admin::app.datagrid.longitude'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

         $this->addColumn([
            'index'      => 'country',
            'label'      => 'Country',
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]); 

        $this->addColumn([
            'index'      => 'display_order',
            'label'      => trans('admin::app.datagrid.display_order'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'active',
            'label'      => trans('admin::app.datagrid.active'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('admin::app.datagrid.created_at'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index'      => 'updated_at',
            'label'      => trans('admin::app.datagrid.updated_at'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'deleted_at',
            'label'      => trans('admin::app.datagrid.deleted_at'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title'  => trans('admin::app.datagrid.edit'),
            'method' => 'GET',
            'route'  => 'admin.cateringpackage.edit',
            'icon'   => 'icon pencil-lg-icon',
        ]);

       
        $this->addAction([
            'title'  => trans('admin::app.datagrid.delete'),
            'method' => 'POST',
            'route'  => 'admin.cateringpackage.delete',
            'icon'   => 'icon trash-icon',
        ]);
    }

    /**
     * Prepare mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {   
        $this->addMassAction([
            'type'   => 'delete',
            'label'  => trans('admin::app.datagrid.delete'),
            'action' => route('admin.sliders.mass_delete'),
            'method' => 'POST',
        ]);
    }
}