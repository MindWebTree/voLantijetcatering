@extends('admin::layouts.master')

@section('page_title')
    Airport Fbo Details
@stop

@section('content-wrapper')

    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link"
                        onclick="window.location = '{{ route('admin.cateringpackage.index') }}'"></i>
                    Airport Fbo Details
                </h1>
            </div>

            <div class="page-action">

                <div class="page-action">
                    @if (bouncer()->hasPermission('admin.cateringpackage.airport-fbo-details.create'))
                        <a href="{{ route('admin.cateringpackage.fbo-details.create', ['id' => $id]) }}"
                            class="btn btn-lg btn-primary">
                            {{-- {{ __('admin::app.settings.cateringpackages.add-title') }} --}}
                            Add Fbo
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="page-content">  

            <datagrid-plus
                src="{{ route('admin.cateringpackage.airport-fbo-details.index', ['id' => $id]) }}"></datagrid-plus>
        </div>
    </div>
@stop
