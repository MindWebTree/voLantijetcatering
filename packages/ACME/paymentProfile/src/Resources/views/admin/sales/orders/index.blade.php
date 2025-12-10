@extends('admin::layouts.content')

@section('page_title')
    {{ __('admin::app.sales.orders.title') }}
@stop

@section('content-wrapper')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1>{{ __('admin::app.sales.orders.title') }}</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
                @if (auth('admin')->user()->role_id == 1)
                    <a href="{{ route('custom.add-order') }}" class="btn btn-lg btn-primary">
                        Add Order
                    </a>
                @endif

            </div>
        </div>

        <div class="page-content">
            <order-datagrid-plus src="{{ route('admin.sales.order.index') }}"></order-datagrid-plus>
        </div>
    </div>

    <modal id="downloadDataGrid" :is-open="modalIds.downloadDataGrid">
        <h3 slot="header">{{ __('admin::app.export.download') }}</h3>
        <div slot="body">
            <export-form></export-form>
        </div>
    </modal>

@stop

@push('scripts')
    {{-- @include('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\OrdersDataGrid')]) --}}
    @if (auth()->guard('admin')->check() && auth()->guard('admin')->user()->role_id == 2)
        @include('admin::export.export', [
            'gridName' => app('Webkul\Admin\DataGrids\DeliveryOrdersDataGrid'),
        ])
    @else
        @include('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\OrdersDataGrid')])
    @endif
@endpush
