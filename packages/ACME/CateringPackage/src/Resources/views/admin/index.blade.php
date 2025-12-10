@extends('admin::layouts.master')

@section('page_title')
    Package CateringPackage
@stop

@section('content-wrapper')

    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.settings.cateringpackages.title') }}</h1>
            </div>

            <div class="page-action">

                <div class="page-action">
                @if (bouncer()->hasPermission('admin.cateringpackage.create'))
                    <a href="{{ route('admin.cateringpackage.create')}}" class="btn btn-lg btn-primary">
                        {{ __('admin::app.settings.cateringpackages.add-title') }}
                    </a>
                @endif
            </div>
          
            </div>
        </div>

        <div class="page-content">

    <datagrid-plus src="{{ route('admin.cateringpackage.index') }}"></datagrid-plus>
  
        </div>
    </div>
@stop