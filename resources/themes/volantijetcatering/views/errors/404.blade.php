@extends('shop::layouts.master')

@section('page_title')
    {{ __('admin::app.error.404.page-title') }}
@stop

@section('content-wrapper')
<div class="d-flex flex-column justify-content-center align-items-center pt-5">
    <div class="error_page">
        <div class="error_page_heading text-center">
            <h1 class="">Page Not Found</h1>
        </div>
        <div class="error_page_body pt-5">
            <div class="col-12 text-start">
                <p class="font-weight-bold">The page you requested was not found, and we have a fine guess why.</p>
                <ul class="pt-4 pl-3">
                    <li>If you typed the URL directly, please make sure the spelling is correct.</li>
                    <li>The page no longer exists. In this case, we profusely apologize for the inconvenience and for any damage this may cause.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
