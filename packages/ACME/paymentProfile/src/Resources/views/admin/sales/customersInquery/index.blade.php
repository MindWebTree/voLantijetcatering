@extends('admin::layouts.content')

@section('page_title')
    {{ __('Customers Inquery') }}
@stop

@section('content')
    <div class="content">
        <div class="page-header">
            <div class="page-title">
                <h1 class="customer_inquery_title">{{ __('Customers Inquery') }}</h1>
            </div>

            <div class="page-action">
                <div class="export-import" @click="showModal('downloadDataGrid')">
                    <i class="export-icon"></i>
                    <span>
                        {{ __('admin::app.export.export') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="page-content customer-inquiry-content">
            <order-datagrid-plus src="{{ route('admin.sales.customersInquery.displayInquerys') }}"></order-datagrid-plus>
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
    {{-- Include the export script --}}
    @include('admin::export.export', ['gridName' => app('Webkul\Admin\DataGrids\CustomerInqueryDataGrid')])

@endpush
