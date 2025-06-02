@extends('shop::layouts.master')

@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('customHelper', 'Webkul\Velocity\Helpers\Helper')

@section('page_title')
    {{ trim($product->meta_title) != '' ? $product->meta_title : $product->name }}
@stop

@section('seo')
    <meta name="description"
        content="{{ trim($product->meta_description) != '' ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}" />

    <meta name="keywords" content="{{ $product->meta_keywords }}" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    @php
        $images = product_image()->getGalleryImages($product);

        $productImages = [];

        foreach ($images as $key => $image) {
            array_push($productImages, $image['medium_image_url']);
        }

        $productBaseImage = product_image()->getProductBaseImage($product, $images);
    @endphp


    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->name }}" />

    <meta name="twitter:description" content="{{ $product->description }}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->name }}" />

    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:description" content="{{ $product->description }}" />

    <meta property="og:url" content="{{ route('shop.productOrCategory.index', $product->url_key) }}" />



    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
@stop

@push('css')
    <style type="text/css">
        .related-products {
            width: 100%;
        }

        .recently-viewed {
            margin-top: 20px;
        }

        .store-meta-images>.recently-viewed:first-child {
            margin-top: 0px;
        }

        .main-content-wrapper {
            margin-bottom: 0px;
        }

        .buynow {
            height: 40px;
            text-transform: uppercase;
        }


        /* //model css */


        .field_required::after {
            content: " *";
            color: red;
            /* Change color as needed */
        }

        .sendbutton {
            width: 100px;
            background-color: #f84661;
            color: white;
            border: 0;
            height: 37px;
            border-radius: 5px;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
        }

        .send__button {
            display: flex;
            justify-content: center;
        }

        .inquerymodelbutton {
            height: 50px;
            width: 200px;
            background-color: #cccccc;
            color: #000;
            font-size: 15px;
            font-weight: 600;
            text-align: center;
            cursor: pointer;
            display: inline-block;
            line-height: 50px;
            border: 0
        }

        .inquerymodelbutton:focus {
            outline: none;
        }

        .customize_modal {
            /* background: #000000b5; */
            /* background: #8b8b8bb5;
                                transition: 0.1s all ease-in-out */
        }

        .customize_modal .modal-content {
            height: auto;
        }

        #message {
            padding: 5px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            box-shadow: none;
        }

        #message:focus {
            border-color: #3272d7;

        }
    </style>
@endpush

@section('full-content-wrapper')
    <div class="single-product-page-overlay"></div>
    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <div class="container">
        {{ Breadcrumbs::render('shop.productOrCategory.index', $product) }}
    </div>
    <div class="row no-margin custom-row ">
        <section class="col-12 product-detail container">

            <div class="layouter">
                <product-view>
                    <div class="form-container">
                        @csrf()

                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        {{-- <div class="container-fluid"> --}}

                        <div class="row  single-product-page">
                            {{-- product-gallery --}}
                            <div class="left col-md-6 col-12 single-product-page-image-section">
                                @include ('shop::products.view.gallery')
                            </div>

                            {{-- right-section --}}
                            <div class="right col-md-6 col-12 single-product-page-text-input-section">
                                {{-- product-info-section --}}
                                <div class="info">
                                    <h2 class="col-12 product_title">{{ $product->name }}</h2>

                                    @if ($total = $reviewHelper->getTotalReviews($product))
                                        <div class="reviews col-lg-12">
                                            <star-ratings push-class="mr5"
                                                :ratings="{{ round($reviewHelper->getAverageRating($product)) }}"></star-ratings>

                                            <div class="reviews">
                                                <span>
                                                    {{ __('shop::app.reviews.ratingreviews', [
                                                        'rating' => round($reviewHelper->getAverageRating($product)),
                                                        'review' => $total,
                                                    ]) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- @include ('shop::products.view.stock', ['product' => $product]) --}}

                                    <div class="col-12 price">

                                        {{-- @include ('shop::products.price', ['product' => $product]) --}}
                                        @include ('shop::products.view.short-description')

                                        @if (Webkul\Tax\Helpers\Tax::isTaxInclusive() && $product->getTypeInstance()->getTaxCategory())
                                            <span>
                                                {{ __('velocity::app.products.tax-inclusive') }}
                                            </span>
                                        @endif
                                    </div>

                                    @if (count($offers = $product->getTypeInstance()->getCustomerGroupPricingOffers()) > 0)
                                        <div class="col-12">
                                            @foreach ($offers as $offer)
                                                {{ $offer }}
                                            @endforeach
                                        </div>
                                    @endif


                                    <!-- ram || 01-09-2023 || add add instaructions and made for fields in product page -->
                                    @if (!$product->isSaleable())
                                        <p class="text-danger product_outof_stock">Out Of Stock</p>
                                    @else
                                        @include ('shop::products.view.configurable-options')
                                    @endif
                                    {{-- @dd($product->quantity); --}}
                                    <div class="col-12 instruction {{ $product->isSaleable() ? 'mt-5' : 'mt-3' }}">
                                        <div>
                                            <label>Special Instructions <span>(optional)</span></label><br>
                                            <textarea name="special_instruction" class="special-instruction" id="special_instruction" rows="5"></textarea>
                                            {{-- <p>*No price altering substitutions/additions</p> --}}
                                        </div>
                                        {{-- <div class="mt-4">
                                            <label>Made for <span>(Optional)</span></label><br>
                                            <textarea name="made_for" id="made-for" class="special-instruction" rows="3"></textarea>
                                        </div> --}}
                                    </div>
                                    <!-- ram || 01-09-2023 || end add add instaructions and made for fields in product page -->


                                </div>

                                @include ('shop::products.view.downloadable')

                                @include ('shop::products.view.grouped-products')

                                @include ('shop::products.view.bundle-options')

                                <div class="col-12 product-actions">
                                    @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                        @include ('shop::products.buy-now', [
                                            'product' => $product,
                                        ])
                                    @endif

                                </div>

                                <div class="row" id="scroll">


                                    <div class="__quantity ms-0 stat-quantity">
                                        {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                        @if ($product->getTypeInstance()->showQuantityBox())
                                            <div class="col-12" id="fff">
                                                <quantity-changer
                                                    quantity-text="{{ __('shop::app.products.quantity') }}"></quantity-changer>
                                            </div>
                                        @else
                                            <input type="hidden" name="quantity" value="1">
                                        @endif

                                        {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}
                                    </div>
                                    {{-- @dd($product) --}}
                                    <div class="__addtocart addtocartbtn ">
                                        @include ('shop::products.add-to-cart', [
                                            'form' => false,
                                            'product' => $product,
                                            'showCartIcon' => false,
                                            // 'showCompare' => (bool) core()->getConfigData('general.content.shop.compare_option'), //compare-icon removed
                                        ])
                                    </div>


                                    <div class="inquerymodelWrapper">
                                        <button type="button" class="inquerymodelbutton" data-toggle="modal"
                                            data-target="#exampleModal">
                                            <i class="fa-solid fa-message-text"></i>Customization Order
                                        </button>
                                    </div>

                                </div>

                            </div>

                        </div>

                        {{-- @include ('shop::products.view.attributes', [
                                'active' => true,
                            ]) --}}

                        {{-- product long description --}}
                        {{-- @include ('shop::products.view.description') --}}

                        {{-- reviews count --}}
                        {{-- @include ('shop::products.view.reviews', ['accordian' => true]) --}}

                    </div>
                    {{-- </div> --}}
            </div>

            </product-view>
    </div>
    </section>
    <div class="related-products container mt-4">
        <div class="row">
            @include('shop::products.view.related-products')
            @include('shop::products.view.you-may-also-like')
            @include('shop::products.view.up-sells')
        </div>
    </div>
    </div>

    <!-- Modal -->
    <div class="modal fade customize_modal" id="exampleModal" tabindex="-1" role="dialog"
        aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel" style="font-weight: 600;">Custom Order Request</h5>
                    <button type="button" id="close" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body inquiry_modal_body">
                    <!-- sucess mesage  -->
                    <div class="inqueryMessage" style="display:none;" id="InqueryMessage">
                        <img src="{{ asset('themes/volantijetcatering/assets/images/accept.png') }}" alt=""
                            id="SuccessIcon">
                        <h3>Thank you for contacting us.</h3>
                        <p> One of our sales rep will get in touch with you soon.
                        <p>
                    </div>
                    <!-- error message -->
                    <div id="errorContainer" class="alert alert-danger" style="display: none;">

                    </div>

                    <form id="inquiryForm" enctype="multipart/form-data">
                        @csrf
                        {{-- <div class="form-group">
                            <label for="name" class="field_required">Name</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                id="name" placeholder="Enter your name" required value="{{ old('name') }}">

                        </div>
                        <div class="form-group">
                            <label for="email" class="field_required">Email address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                id="email" name="email" placeholder="Enter your email" required
                                value="{{ old('email') }}">
                        </div> --}}


                        <div class="row">
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="name" class="field_required">Name</label>
                                    <input type="text" name="name"
                                        class="form-control @error('name') is-invalid @enderror" id="name"
                                        placeholder="" required value="{{ old('name') }}">

                                </div>
                            </div>
                            <div class="col-md-6 col-lg-6 col-sm-12">
                                <div class="form-group">
                                    <label for="email" class="field_required">Email address</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        id="email" name="email" placeholder="" required
                                        value="{{ old('email') }}">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="phone" class="field_required">Phone number</label>
                            <input type="number" class="form-control @error('mobile_number') is-invalid @enderror"
                                id="phone" name="mobile_number" placeholder="" required
                                value="{{ old('mobile_number') }}">

                        </div>
                        <div class="form-group">
                            <label for="message" class="field_required">Message</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5"
                                placeholder="" required>{{ old('message') }}</textarea>

                        </div>

                        <div class="form-group">
                            <label class="field_required" for="uploadfile">Upload Files</label>
                            <input type="file" class="form-control-file @error('uploadfile.*') is-invalid @enderror"
                                id="uploadfile" name="uploadfile[]" multiple required>

                            <div id="fileError" class="text text-danger"></div>
                        </div>

                        <div class="send__button">
                            <button type="submit" class="sendbutton">Send</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>





    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}
@endsection







@push('scripts')
    <script>
        jQuery(document).ready(function($) {
            $('body').on('change', '#uploadfile', function() {

                var files = $(this)[0].files;
                console.log(files)
                $('#fileError').hide().text('');

                for (var i = 0; i < files.length; i++) {
                    var file = files[i];

                    if (files.length > 5) {
                        $('#fileError').text('Maximum 5 files can be uploaded.').show();
                        $(this).val('');
                        return;
                    }

                    if (file.size > 2000 * 1024) {
                        $('#fileError').text('Maximum file size allowed is 2 MB.').show();
                        $(this).val('');
                        return;
                    }


                    var fileType = file.type;
                    console.log(fileType)
                    if (!(fileType === "application/pdf" ||
                            fileType ===
                            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" ||
                            fileType === "application/vnd.ms-excel" ||
                            fileType === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                        )) {
                        $('#fileError').text('Only upload PDF, DOCX, and XLS files.').show();
                        $(this).val('');
                        return;
                    }

                }

                $('#fileError').hide();


            });


            $(document).on('submit', '#inquiryForm', function(event) {
                event.preventDefault();

                var formData = new FormData(this);

                $.ajax({
                    url: "{{ route('store.inquery') }}",
                    type: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        $('#inquiryForm')[0].reset();
                        $('#errorContainer').empty().hide();
                        $('#inquiryForm').hide();
                        $('.inqueryMessage').show();


                        console.log('Form submitted successfully:', response);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error response:", xhr.responseText);

                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            // Clear previous errors
                            $('#errorContainer').empty();

                            var errorHtml = '<div><ul>';
                            $.each(errors, function(key, value) {
                                errorHtml += '<li>' + value + '</li>';
                            });
                            errorHtml += '</ul></div>';
                            $('#errorContainer').append(errorHtml);

                            // fill count error message

                            $('#errorContainer').text(xhr.responseJSON.error).fadeIn();

                            setTimeout(function() {
                                $('#errorContainer').fadeOut('slow', function() {
                                    $(this)
                                        .empty(); // Clear the message content
                                });
                            }, 5000);

                        } else {
                            $('#errorContainer').text(
                                'Error submitting form. Please try again.').fadeIn();
                            alert('Error submitting form. Please try again.');
                        }
                    }
                });
            });

        });
    </script>


    <script type="text/javascript" src="{{ asset('vendor/webkul/ui/assets/js/ui.js') }}"></script>

    <script type="text/javascript" src="{{ asset('themes/velocity/assets/js/jquery-ez-plus.js') }}"></script>

    <script type='text/javascript' src='https://unpkg.com/spritespin@4.1.0/release/spritespin.js'></script>

    <script type="text/x-template" id="product-view-template">


        <form
            method="POST"
            id="product-form"
            @click="onSubmit($event)"
            @submit.enter.prevent="onSubmit($event)"
            action="{{ route('shop.cart.add', $product->id) }}"
        >
            <input type="hidden" name="is_buy_now" v-model="is_buy_now">

            <slot v-if="slot"></slot>

            <div v-else>
                <div class="spritespin"></div>
            </div>
        </form>
    </script>




    <script>
        // jQuery(document).ready(function($) {
        //     $(document).on('submit', '#inquiryForm', function(event) {
        //         event.preventDefault();

        //         var formData = new FormData(this);
        //         console.log("Form data:", formData);

        //         $.ajax({
        //             url: "{{ route('store.inquery') }}",
        //             type: 'POST',
        //             data: formData,
        //             cache: false,
        //             contentType: false,
        //             processData: false,
        //             success: function(response) {
        //                 $('#inquiryForm')[0].reset();
        //                 $('#exampleModal').modal('hide'); 
        //                 alert('Form submitted successfully!');

        //                 console.log('Form submitted successfully:', response);
        //             },
        //             error: function(xhr, status, error) {
        //                 console.error("Error response:", xhr.responseText);

        //                 if (xhr.responseJSON && xhr.responseJSON.errors) {
        //                     displayErrors(xhr.responseJSON.errors);
        //                 } else {
        //                     alert('Error submitting form. Please try again.');
        //                 }
        //             }
        //         });
        //     });

        // });
    </script>



    <script>
        Vue.component('product-view', {
            inject: ['$validator'],
            template: '#product-view-template',
            data: function() {
                return {
                    slot: true,
                    is_buy_now: 0,
                }
            },

            mounted: function() {
                let currentProductId = '{{ $product->url_key }}';
                let existingViewed = window.localStorage.getItem('recentlyViewed');

                if (!existingViewed) {
                    existingViewed = [];
                } else {
                    existingViewed = JSON.parse(existingViewed);
                }

                if (existingViewed.indexOf(currentProductId) == -1) {
                    existingViewed.push(currentProductId);

                    if (existingViewed.length > 3)
                        existingViewed = existingViewed.slice(Math.max(existingViewed.length - 4, 1));

                    window.localStorage.setItem('recentlyViewed', JSON.stringify(existingViewed));
                } else {
                    var uniqueNames = [];

                    $.each(existingViewed, function(i, el) {
                        if ($.inArray(el, uniqueNames) === -1) uniqueNames.push(el);
                    });

                    uniqueNames.push(currentProductId);

                    uniqueNames.splice(uniqueNames.indexOf(currentProductId), 1);

                    window.localStorage.setItem('recentlyViewed', JSON.stringify(uniqueNames));
                }
            },

            methods: {
                onSubmit: function(event) {
                    if (event.target.getAttribute('type') != 'submit')
                        return;

                    event.preventDefault();

                    this.$validator.validateAll().then(result => {
                        if (result) {

                            this.is_buy_now = event.target.classList.contains('buynow') ? 1 : 0;

                            setTimeout(function() {
                                document.getElementById('product-form').submit();
                            }, 0);
                        } else {
                            this.activateAutoScroll();
                        }
                    });
                },

                activateAutoScroll: function(event) {

                    /**
                     * This is normal Element
                     */
                    const normalElement = document.querySelector(
                        '.control-error:first-of-type'
                    );

                    /**
                     * Scroll Config
                     */
                    const scrollConfig = {
                        behavior: 'smooth',
                        block: 'end',
                        inline: 'nearest',
                    }

                    if (normalElement) {
                        normalElement.scrollIntoView(scrollConfig);
                        return;
                    }
                }
            }
        });

        window.onload = function() {
            var thumbList = document.getElementsByClassName('thumb-list')[0];
            var thumbFrame = document.getElementsByClassName('thumb-frame');
            var productHeroImage = document.getElementsByClassName('product-hero-image')[0];

            if (thumbList && productHeroImage) {
                for (let i = 0; i < thumbFrame.length; i++) {
                    thumbFrame[i].style.height = (productHeroImage.offsetHeight / 4) + "px";
                    thumbFrame[i].style.width = (productHeroImage.offsetHeight / 4) + "px";
                }

                if (screen.width > 720) {
                    thumbList.style.width = (productHeroImage.offsetHeight / 4) + "px";
                    thumbList.style.minWidth = (productHeroImage.offsetHeight / 4) + "px";
                    thumbList.style.height = productHeroImage.offsetHeight + "px";
                }
            }

            window.onresize = function() {
                if (thumbList && productHeroImage) {

                    for (let i = 0; i < thumbFrame.length; i++) {
                        thumbFrame[i].style.height = (productHeroImage.offsetHeight / 4) + "px";
                        thumbFrame[i].style.width = (productHeroImage.offsetHeight / 4) + "px";
                    }

                    if (screen.width > 720) {
                        thumbList.style.width = (productHeroImage.offsetHeight / 4) + "px";
                        thumbList.style.minWidth = (productHeroImage.offsetHeight / 4) + "px";
                        thumbList.style.height = productHeroImage.offsetHeight + "px";
                    }
                }
            }
        };
    </script>
@endpush

{{-- @if (session('add_in_cart') != null)
    @push('scripts')

        <script>
            setTimeout(() => {
                jQuery('#mini-cart').click();
            }, 4000);
        </script>
    @endpush
@endif --}}




{{-- @if (session('add_in_cart') != null)
    @push('scripts')
        <script>
            var clicked = false;
            function clickMiniCart() {
                console.log(  $('body').find('#cart-modal-content').length,$('body').find('#mini-cart').length);
                if($('body').find('#cart-modal-content').length>0 && $('body').find('#mini-cart').length>0){
                    console.log('sss');
                if (!clicked) {
                    console.log('click');
                    jQuery('#cart-modal-content').addClass('slide-cart-modal');
                    clicked = true;
                }
                }
               
            }
            setInterval(clickMiniCart,1000);
        </script>
    @endpush
@endif --}}

@if (session('add_in_cart') != null)
    @push('scripts')
        <script>
            let clicked = false;
            var intervalId;

            function clickMiniCart() {
                if ($('body').find('#cart-modal-content').length > 0 && $('body').find('#mini-cart').length > 0 && !clicked) {
                    jQuery('#cart-modal-content').addClass('slide-cart-modal');
                    clicked = true;
                    clearInterval(intervalId);
                }
            }
            intervalId = setInterval(clickMiniCart, 3000);
        </script>
    @endpush
@endif
