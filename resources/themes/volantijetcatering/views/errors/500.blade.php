@extends('shop::layouts.master')

@section('page_title')
    {{ __('admin::app.error.500.page-title') }}
@stop

@section('content-wrapper')

    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-8 text-center">
                <h1 class="display-4 text-danger">Oops! Something Went Wrong</h1>
 <div class="error_message pt-4">
                <p class="lead">We're sorry, but something unexpected happened. Our team is investigating the issue right now.</p>
                <p>We understand how frustrating this can be, and we're doing our best to get things back up and running. Please try again later.</p>
                <p>If the issue persists, please <a href="mailto:jetcatering@volantiscottsdale.com">contact our support team</a> for more information.</p>
            </div>
        </div>
</div>
    </div>
@endsection
