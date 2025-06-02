@extends('admin::layouts.master')

@section('page_title')
    PaymentProfile
@stop

@section('content-wrapper')

    <div class="content full-page dashboard">
        <div class="page-header">
            <div class="page-title">
                <h1>PaymentProfile</h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        <div class="page-content">
            <datagrid-plus src="{{ route('admin.paymentprofile.index') }}"></datagrid-plus>
        </div>
    </div>

@stop
