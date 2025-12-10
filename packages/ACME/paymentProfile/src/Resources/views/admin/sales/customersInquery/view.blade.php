@extends('admin::layouts.content')

@section('page_title')
    {{ __('Customer Inquery') }}
@stop

@section('content')
<div class="content full-page">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <i class="icon angle-left-icon back-link" onclick="window.location = '{{ route('admin.sales.customersInquery.displayInquerys') }}'"></i>

                    {{ __('Inquiry id') }} #{{ $inquerys->id }}

                </h1>
            </div>

            <div class="page-action">
            </div>
        </div>

        <div class="page-content">
            <div class="sale-container">

             
                    <div slot="body">
                        <div class="sale">
                            <div class="sale-section">
                                <div class="secton-title">
                                    <span>{{ ('Inquery Information') }}</span>
                                </div>

                                <div class="section-content">
                                    <div class="row">
                                        <span class="title">
                                            {{('Id') }}
                                        </span>

                                        <span class="value">
                                            <a href="{{ route('admin.sales.customersInquery.viewInquery', $inquerys->id) }}">#{{ $inquerys->id }}</a>
                                        </span>
                                    </div>

                                    <!-- <div class="row">
                                        <span class="title">
                                            {{ __('admin::app.sales.orders.order-date') }}
                                        </span>

                                        <span class="value">
                                            {{ core()->formatDate($inquerys->created_at, 'Y-m-d H:i:s') }}
                                        </span>
                                    </div> -->

                                    <div class="row">
                                        <span class="title">
                                            {{ __('First Name') }}
                                        </span>

                                        <span class="value">
                                            {{ $inquerys->fname }}
                                        </span>
                                    </div>


                                    <div class="row">
                                        <span class="title">
                                            {{ __('Last Name') }}
                                        </span>

                                        <span class="value">
                                            {{ $inquerys->lname }}
                                        </span>
                                    </div>


                                    <div class="row">
                                        <span class="title">
                                            {{ __('Email') }}
                                        </span>

                                        <span class="value">
                                            {{ $inquerys->email }}
                                        </span>
                                    </div>

                                    <div class="row">
                                        <span class="title">
                                            {{ __('Mobile Number') }}
                                        </span>

                                        <span class="value">
                                            {{ $inquerys->mobile_number }}
                                        </span>
                                    </div>

                                    <div class="row" style="justify-content: normal;display:flex;align-items: start;max-height:200px;overflow:auto;">
                                        <span class="title">
                                            {{ __('Message') }}
                                        </span>

                                        <span class="value">
                                            {{ $inquerys->message }}
                                        </span>
                                        </div>

                                    <div class="row" style="justify-content: normal;display:flex;align-items: start;">
                                        <span class="title">
                                            {{ __('Files') }}
                                        </span>
                                    
                                        <span class="value">
                                         
                                        @foreach ($selectFiles as $file)
                                         <a href="{{ route('admin.sales.customersInquery.downloadfile', ['file' => basename($file)]) }}" target="_blank">{{ basename($file) }}</a><br>
                                        @endforeach
                                    
                                      </span>

                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </accordian>


@stop


