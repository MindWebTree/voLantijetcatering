<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\Ui\DataGrid\DataGrid;
use Webkul\Core\Models\Channel;



class FboDetailsDataGrid extends DataGrid
{
    protected $index = 'id';

    protected $sortOrder = 'desc';

    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
        parent::__construct();
    }

    // public function prepareQueryBuilder()
    // {
    //     // $queryBuilder = DB::table('airport_fbo_details')->select('id')->addSelect('id','name','phone','email','address','country','created_at','updated_at');

    //     // $this->setQueryBuilder($queryBuilder);
    // }


    public function prepareQueryBuilder()
    {

        $queryBuilder = DB::table('airport_fbo_details')
            ->select('id', 'name', 'address', 'notes', 'country', 'created_at', 'updated_at')
            ->where('airport_id', $this->id);


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


        // dd($this->gridId);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('admin::app.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index' => 'address',
            'label' => 'Address',
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
        $this->addColumn([
            'index' => 'notes',
            'label' => 'Notes',
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
        $this->addColumn([
            'index' => 'updated_at',
            'label' => trans('admin::app.datagrid.updated_at'),
            'type' => 'string',
            'searchable' => true,
            'sortable' => true,
            'filterable' => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'title' => trans('admin::app.datagrid.edit'),
            'method' => 'GET',
            'route' => 'admin.cateringpackage.fbo-details.edit',
            // 'route' => route('admin.cateringpackage.fbo-details.edit', ['id' => $this->gridId, 'airport_id' => $this->id]),        
            'icon' => 'icon pencil-lg-icon',
        ]);

        $this->addAction([
            'title' => trans('admin::app.datagrid.delete'),
            'method' => 'POST',
            'route' => 'admin.cateringpackage.fbo-details.delete',
            'icon' => 'icon trash-icon',
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
            'type' => 'delete',
            'label' => trans('admin::app.datagrid.delete'),
            'action' => route('admin.sliders.mass_delete'),
            'method' => 'POST',
        ]);
    }
}