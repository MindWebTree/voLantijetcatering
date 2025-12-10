@extends('shop::layouts.master')

@section('page_title')
Customization Services | Volanti Jet Catering
@stop

@section('seo')
<meta name="title" content="Customization Services | Volanti Jet Catering" />
<meta name="description" content="Customization Services | Volanti Jet Catering" />
<meta name="keywords" content="" />
<link rel="canonical" href="{{ url()->current() }}" />

@stop


@section('content-wrapper')
<div class="container mt-5">
    <div class="card border-0 p-4 thank-page-card">
        <div class="card-body text-center">

            <!-- Header Section -->
            <div class="thankyou_page_header mb-5">
                <img src="/themes/volantijetcatering/assets/images/tick.png" style="width: 100px" alt="tick icon">
                <h1 class="card-title">Thank You for Contacting Us</h1>
                <p class="thank-tittle">One of our sales representatives will get in touch with you soon.</p>
            </div>

            <!-- Customer Details Section -->
            <div class="row justify-content-center mb-4">
                <div class="col-12 col-md-8">
                    <h2 class="mb-3 customer-detail">Customer Details</h2>
                    <div class="table-responsive">
                        <table class="table table-bordered thankyou-page-table">
                            <tbody>
                                <!-- First Name -->
                                <tr>
                                    <td><strong>First Name:</strong></td>
                                    <td>{{ $inqueryData->fname }}</td>
                                </tr>

                                <!-- Last Name -->
                                <tr>
                                    <td><strong>Last Name:</strong></td>
                                    <td>{{ $inqueryData->lname }}</td>
                                </tr>

                                <!-- Email -->
                                <tr>
                                    <td><strong>Email:</strong></td>
                                    <td>{{ $inqueryData->email }}</td>
                                </tr>

                                <!-- Mobile Number -->
                                <tr>
                                    <td><strong>Mobile Number:</strong></td>
                                    <td>{{ $inqueryData->mobile_number }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Message Section -->
            <div class="row justify-content-center inquery-message">
                <div class="col-12 col-md-8">
                    <h2 class="mb-2 enquiry-detail">Enquiry details</h2>
                    <p class="text-muted inquery_message">{{ $inqueryData->message }}</p>
                </div>
            </div>

            <div class="row justify-content-center inquery-files mt-4">
                <div class="col-12 col-md-8 col-lg-6 custom-width-desktop">
                    <div class="row justify-content-center">
                        {{-- @dd($selectFiles) --}}
                        @foreach ($selectFiles as $file)
                            @php
                          
                                $extension = pathinfo($file, PATHINFO_EXTENSION); 
                            @endphp
            
                            <div class="col-12 col-sm-6 col-md-6 col-lg-4 col-xl-3 mb-3"> 
                                <div class="inquery_file d-flex align-items-center">
                                    <!-- Display different images based on the file extension -->
                                    @if ($extension === 'pdf')
                                        <img src="/themes/volantijetcatering/assets/images/pdf.png" alt="PDF icon" style="height: 20px" class="mr-2 inquery_image">
                                    @elseif ($extension === 'doc' || $extension === 'docx')
                                        <img src="/themes/volantijetcatering/assets/images/doc.png" alt="DOC icon" style="height: 20px" class="mr-2 inquery_image">
                                    @elseif ($extension === 'xls' || $extension === 'xlsx')
                                        <img src="/themes/volantijetcatering/assets/images/xlsx.png" alt="XLS icon" style="height: 20px" class="mr-2 inquery_image">
                                    @else
                                        <img src="/themes/volantijetcatering/assets/images/file.png" alt="File icon" style="height: 20px" class="mr-2 inquery_image">
                                    @endif
            
                                    <!-- File download link -->
                                    <a href="{{ route('Inquery.downloadfile', ['file' => basename($file)]) }}" target="_blank" class="inquery_download_file">{{ basename($file) }}</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <!-- Continue Shopping Button -->
            <div class="text-center mt-4 continue-shopping">
                <button class="continue-shopping-button">
                   <a href="{{url('/menu')}}" class="btn-link">Continue Shopping</a>
                </button>
            </div>
        </div>
    </div>
</div>
@stop
