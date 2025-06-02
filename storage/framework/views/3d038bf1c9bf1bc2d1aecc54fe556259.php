<?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    use Webkul\Checkout\Facades\Cart;
    use Illuminate\Support\Facades\Request;
    use Webkul\Checkout\Repositories\CartRepository;
    use Carbon\Carbon;
    
    $guestToken = Session::token();
    // dd($guestToken);
    $airportArr = Db::table('delivery_location_airports')->pluck('name')->toArray();

    // Retrieve the guest session ID
    $guestSessionId = Session::getId();
    $cartItems = Session::get('cart');
    //echo session()->get('cart')->id;
    
    $customer = auth()->guard('customer')->user();
    if (isset($customer->id) != null) {
        $fboDetails = DB::table('fbo_details')
            ->where('customer_id', $customer->id)
            ->orderBy('id', 'DESC')
            ->first();
    } else {
        $fboDetails = DB::table('fbo_details')->Where('customer_token', $guestToken)->orderBy('id', 'DESC')->first();
    }
    
    $airport_fbo = '';
    if (Auth::check()) {
        $islogin = 1;
        $address = Db::table('addresses')
            ->where('customer_id', $customer->id)
            ->where('address_type', 'customer')
            ->orderBy('created_at', 'desc') 
            ->first();

        if (isset($address->airport_fbo_id)) {
            $airport_fbo = DB::table('airport_fbo_details')
                ->where('id', $address->airport_fbo_id)
                ->value('name');
        }
    } else {
        $islogin = 0;
        $address = Db::table('addresses')->where('customer_token', $guestToken)->first();
        if (isset($address->airport_fbo_id)) {
            $airport_fbo = DB::table('airport_fbo_details')
                ->where('id', $address->airport_fbo_id)
                ->value('name');
        }
    }
    
    if (isset($address->airport_name) && $address->airport_name != '') {
        $airport_id = DB::table('delivery_location_airports')
            ->where('name', $address->airport_name)
            ->first();
    }
    if (isset($fboDetails->delivery_date)) {
        $date = $fboDetails->delivery_date;

        // Create a DateTime object from the date string
        $dateObj = new DateTime($date);

        // Get today's date
    $today = new DateTime('today');

    // Get tomorrow's date
        $tomorrow = new DateTime('tomorrow');

        // Compare the delivery date with today's date and tomorrow's date
        if ($dateObj->format('Y-m-d') == $today->format('Y-m-d')) {
            // If delivery date is today, show today's date in the desired format
        $formattedDate = 'Today'; // Example format: "Thursday 3/26"
    } elseif ($dateObj->format('Y-m-d') == $tomorrow->format('Y-m-d')) {
        // If delivery date is tomorrow, show tomorrow's date in the desired format
            $formattedDate = 'Tomorrow'; // Example format: "Friday 3/27"
        } else {
            // If not today or tomorrow, show the delivery date in the original format
            // Get the day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
            $dayOfWeek = $dateObj->format('w');

            // Array of days of the week
            $daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            // Get the day of the week name
            $dayName = $daysOfWeek[$dayOfWeek];

            // Get the month
            $month = $dateObj->format('n');

            // Get the day of the month
            $dayOfMonth = $dateObj->format('j');

            // Format the date string
            $formattedDate = $dayName . ' ' . $month . '/' . $dayOfMonth;
        }

        // echo $formattedDate;
    }
    if (isset($fboDetails->delivery_time)) {
        $time = $fboDetails->delivery_time;
    }

?>



<div class="col-md-10 m-auto search-section-1">
    <div class="input-group d-block">
        <div class="row home__airport__search">
            <div class="col-lg-3 col-md-3 col-12 mx-auto padding search_label_wrapper d-md-flex">
                <img class="Navigation-image"
                    src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/airport.svg')); ?>" alt="airplane image" />
                <p class="m-0 text-center">
                    Airport
                </p>
            </div>

            <div class="search-content col-lg-9 col-md-9 col-12 pr-0 pl-0 padding ">
                <div class="searchbar" id="airport_select_searchbar">
                    <img class="Navigation-image home_border_left"
                        src="<?php echo e(asset('themes/velocity/assets/images/navigation.png')); ?>" alt="location image" />
                    <input type="text" id="auto_search" class="form-control w-100 pr-2 pl-2"
                        placeholder="Search Delivery Location" 
                        <?php if(isset($address)): ?> data="<?php echo e($address->id); ?>" value="<?php echo e($address->airport_name); ?>" <?php endif; ?>>
                </div>
                <div id="address-list" class="suggestion-list"></div>
            </div>
        </div>

        
        <p id = "fboerror" class="text-danger d-none" style="color:red; text-align: center !important;"></p>
        <div class="row  airport__fbo__detail">
            <div class="col-lg-3 col-md-3 col-12 mx-auto padding search_label_wrapper d-md-flex">
                <img class="Navigation-image"
                    src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/store-label.svg')); ?>" alt="store image" />
                <p class="m-0 text-center fbo_name">
                    FBO
                </p>
            </div>
            <div class="search-content col-lg-9 col-md-9 col-12 pr-0 pl-0 padding ">
                <div class="searchbar home_border_left airport_fbo_name">
                    <!-- <i class="home_border_left px-lg-2"></i> -->
                    <input type="text" id="airport-fbo-input" class="form-control w-100 pr-2 pl-1 ml-3 pointer home_border_left"
                        placeholder="Airport Fbo Detail" readonly
                        <?php if(isset($airport_fbo)): ?> value="<?php echo e($airport_fbo); ?>" <?php endif; ?>>
                    <img class="Navigation-image pointer" id="airport-fbo-input"
                        src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/down-arrow.svg')); ?>"
                        alt="home down arrow" />
                    <input type="hidden" id="selected-fbo-id" name="selected_fbo_id" <?php if(isset($address)): ?> value="<?php echo e($address->airport_fbo_id); ?>" <?php endif; ?>>
                    <div id="airport-fbo-list" class="custom-dropdown-list text-justify d-none">
                        <!-- Options will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
        

        <div class="row home__date__time">
            <div
                class="col-lg-3 col-md-3 col-12 mx-lg-auto padding search_label_wrapper search_label_wrapper_first search_label_wrapper_when d-md-flex">
                <img class="Navigation-image" src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/time.svg')); ?>"
                    alt="time image" />
                <p class="m-0 text-left whrn-text">
                    When
                </p>
            </div>

            <div class="col-lg-5 col-md-5 col-6 pr-0 pl-0 padding search_label_wrapper" id="time_search_label">
                <img class="Navigation-image pl-3"
                    src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/calendar-alt.svg')); ?>" alt="calender image" />
                <div class='datetime'>
                    
                    <div class="form-group m-0">
                        <div class='input-group date time_date_picker d-block' id='datetimepicker1'>
                            <input type="text" id="daySelect"
                                value="<?php echo e(isset($formattedDate) ? $formattedDate : ''); ?>" placeholder="Select Date"
                                readonly>

                            <div class="delivery_select  delivery_select_date" style="width: 100% !important">
                                <ul id="dayList"></ul>
                            </div>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-6 pr-0 pl-0 padding search_label_wrapper pl-3 d-flex delivery_time_slot">
                <img class="Navigation-image" src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/time.svg')); ?>"
                    alt="time image" />
                <div class='datetime'>
                    <div class="form-group m-0">
                        <div class='input-group date time_date_picker d-block' id='datetimepicker2'>
                            <input type="text" class="p-lg-0 p-md-0" id="timeSlots" value="<?php echo e(isset($time) ? $time : ''); ?>"
                                placeholder="Select Time" readonly>

                            <div class="delivery_select delivery_select_time" style="width: 100% !important">
                                <ul id="timeSlotsList"></ul>
                            </div>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="w-100 my-3">
            <button class="btn start-order" type="button" id="address_btn">START ORDER</button>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>

    <script>
        $(document).on('click', function(event) {

            var target = $(event.target);
            if (!target.closest('#datetimepicker1').length && !target.is('#datetimepicker1')) {
                $('.delivery_select_date').hide();
            }
            if (!target.closest('#datetimepicker2').length && !target.is('#datetimepicker2')) {
                $('.delivery_select_time').hide();
            }
            if (!target.closest('.searchbar').length && !target.is('.searchbar')) {
                $('.address-list').hide();
            }
        });


        var airport_id = 0;
        <?php if(isset($airport_id)): ?>
            airport_id = <?php echo e($airport_id->id); ?>;
        <?php endif; ?>

        jQuery(document).ready(function() {

            //21-05-2024 || airport fbo detail dropdown show and hide start//





            // $('body').on('click', '#airport-fbo-input', function() {
            //     $('#airport-fbo-list').toggle();
            //     //sandeep
            //     // $('#airport-fbo-input').on('click', function() {
            //     if ($('#auto_search').val().trim() === '') {
            //         $('#fboerror').text('Please select delivery location.').removeClass('d-none').fadeIn();;
            //         setTimeout(function() {
            //             $('#fboerror').fadeOut(function() { 
            //                 $(this).addClass('d-none');
            //             });
            //         }, 3000);
            //     } else {
            //         $('#fboerror').addClass('d-none');
            //     }
            // });

            $(document).on('click', '.custom-option', function() {
                var selectedText = $(this).find('.airport-name').text().trim();
                var selectedId = $(this).data('id');
                // Check if selectedId is "abc" and return early if it is
                if ($(this).attr('id') === 'option_id') {
                    $('#airport-fbo-list').hide();
                    return;
                }
                $('#airport-fbo-input').val(selectedText);
                $('#airport-fbo-input').data('selected-id', selectedId);
                $('#selected-fbo-id').val(selectedId); // Store the selected ID in the hidden input
                $('#airport-fbo-list').hide();
                if ($('#daySelect').val() != '' && $('#timeSlots').val() !=
                    '' && $('#selected-fbo-id').val() != '' && $('#selected-fbo-id').val() != '0') {
                }
            });
            //21-05-2024 || airport fbo detail dropdown show and hide end//

            if (window.location.pathname == '/') {
                jQuery('#home-right-bar-container').hide();
            }

            var islogin = '<?php echo $islogin; ?>';
            var customer_token = '<?php echo $guestToken; ?>';
            var customerArray = <?php echo json_encode($airportArr); ?>;


            //alert(customerArray);
            var al_name = jQuery('#auto_search').val();
            if (al_name != '' && $('#daySelect').val() != '' && $('#timeSlots').val() != '' && ($('#selected-fbo-id').val() != '' && $('#selected-fbo-id').val() != '0')) {

            }

            // sandeep ||add timeout code for airport list search
            let typingTimer;
            const typingDelay = 500;

            jQuery('body').on('keyup click', '#auto_search', function() {
                $('#airport-fbo-list').hide();
                clearTimeout(typingTimer); 
                // console.log('hfggh');
                var name = jQuery(this).val();
                // here when ajax hit then show airport  

                if ($.inArray(name, customerArray) === -1) {

                    // jQuery('#address_btn').prop('disabled', true);
                    // jQuery('.search-button').prop('disabled', true);

                }

                typingTimer = setTimeout(function() {
                // $('#address_btn').prop('disabled', false);

                $.ajax({
                    url: "<?php echo e(route('shop.home.index')); ?>",

                    method: 'GET',
                    data: {
                        'name': name,
                        'type': 'address_search'
                    },
                    success: function(result) {
                        console.log(result);
                        jQuery("#address-list").html(result);
                    }
                });
            }, typingDelay);
            })



            jQuery('body').on('click', ' #address-list li', function() {
                $('#selected-fbo-id').val('');
                $('#airport-fbo-input').val('');
                var name = jQuery(this).attr('data-attr');

                airport_id = jQuery(this).attr('attr');
                console.log(airport_id);
                var input_val = jQuery("#auto_search").val(name);

                jQuery("#address-list").html("");
                if (airport_id) {
                    airport_fbo();
                }
            });


            // sandeep add code
            jQuery('body').on('click', '#airport-fbo-input', function () {
                if (jQuery('.custom-dropdown-list').css('display') == 'none') {
                    $('#airport-fbo-list').toggle();  
                    if (airport_id) {
                         airport_fbo();
                     }
                }

                if ($('#auto_search').val().trim() === '') {
                    $('#fboerror').text('Please select delivery location.').removeClass('d-none').fadeIn();;
                    setTimeout(function() {
                        $('#fboerror').fadeOut(function() { 
                            $(this).addClass('d-none');
                        });
                    }, 3000);
                } else {
                    $('#fboerror').addClass('d-none');
                }

            });

            jQuery('body').on('click', '#add-fbo-button', function() {
                let fboName = $('#fbo-name').val().trim();
                let fboaddress = $('#fbo-address').val().trim();
                let fboNotes = $('#fbo-notes').val().trim();
                let originalContent = $(this).html();
                if(fboName && fboaddress){
                $(this).html('<span class="btn-ring"></span>');
                $(this).find(".btn-ring").show();
                }
                if (airport_id) {
                    $.ajax({
                        url: "<?php echo e(route('shop.home.fbo-details.store')); ?>",
                        method: 'POST',
                        data: {
                            '_token': "<?php echo e(csrf_token()); ?>",
                            'name': fboName,
                            'address': fboaddress,
                            'notes': fboNotes,
                            'airport_id': airport_id,
                        },
                        //updated 
                        success: function(response) {
                            if (response.response) {
                                $('.fboClose').click();
                                resetFormFields();
                                updateFboDetails(response.data);
                                if (airport_id) {
                                    airport_fbo();
                                }
                                $('#add-fbo-button').html(originalContent);
                                $(this).find('.btn-ring').hide();
                            }
                        },

                        error: function(xhr,status,error) {
                            if (xhr.status === 422) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]).show();
                                });
                            }
                            $('#add-fbo-button').html(originalContent);
                            $(this).find('.btn-ring').hide();
                        }
                    });
                }
            });
            // Function to reset form fields
            function resetFormFields() {
                $('.fboClose').click();
                $('#fbo-name, #fbo-address, #fbo-notes').val('');
            }

            // sandeep || add code for remove padding on body
            $(document).click('body, .fboClose',function(){
                $('#exampleModalCenter').on('hidden.bs.modal', function() {
                      $('body').addClass('pr-0');
                      console.log('modal hide');
               });
            });


            // Function to update FBO details with the response data
            function updateFboDetails(data) {
                $('#airport-fbo-input').val(data.name).data('selected-id', data.id);
                $('#selected-fbo-id').val(data.id);
            }
            //21-05-2024 || airport fbo detail dropdown ajax end//

            let airport_fbo = () => {
                $.ajax({
                    url: "<?php echo e(route('shop.home.index')); ?>",

                    method: 'GET',
                    data: {
                        '_token': "<?php echo e(csrf_token()); ?>",
                        'airport_id': airport_id,
                        'type': 'airport_fbo_detail'
                    },
                    success: function(response) {
                        if (response.options) {
                            $('#airport-fbo-list').removeClass('d-none')
                            $("#airport-fbo-list").html(response.options);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            }
            jQuery('body').on('click', '#address_btn', function() {
                let selected_fbo_id = jQuery('#selected-fbo-id').val();
                let date = jQuery("#daySelect").val();
                let time = jQuery("#timeSlots").val();
                var delivery_address = jQuery("#auto_search").val();

                var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');

               
                if(!airport_id){
                    let element = $('#airport_select_searchbar');
                    element.addClass('homepage_error_focus');
                    $('html, body').animate({ scrollTop: element.offset().top - 150 }, 500);
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus');
                    }, 3000);
                    return;
                }

                if(!selected_fbo_id){
                    let element = $('.airport_fbo_name');
                    element.addClass('homepage_error_focus');
                    $('html, body').animate({ scrollTop: element.offset().top - 150 }, 500);
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus');
                    }, 3000);
                    return;
                }

                if(!date){
                    let element = $('#time_search_label');
                    element.addClass('homepage_error_focus');
                    $('html, body').animate({ scrollTop: element.offset().top - 150 }, 500);
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus');
                    }, 3000);
                    return;
                }

                if(!time){
                    let element = $('.delivery_time_slot');
                    element.addClass('homepage_error_focus');
                    $('html, body').animate({ scrollTop: element.offset().top - 150 }, 500);
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus');
                    }, 3000);
                    return;
                }


                if(airport_id && selected_fbo_id && date && time){
                let originalContent = $(this).html();
                $(this).html('<span class="btn-ring"></span>');
                $(this).find(".btn-ring").show();
                $(this).find('.btn-ring').css({
                'display': 'flex',
                'justify-content': 'center',
                'align-items': 'center'
            });
        }

                // $.ajaxSetup({
                //     headers: {
                //         'X-CSRF-TOKEN': csrfToken
                //     }
                // });

                // here when ajax hit then create airport or update
                $.ajax({
                    url: "<?php echo e(route('shop.home.create')); ?>",
                    type: 'POST',
                    data: {
                        //'_token': $('input[name=_token]').val(),
                        //'_token': csrfToken,
                        "_token": "<?php echo e(csrf_token()); ?>",
                        'islogin': islogin,
                        'delivery_address': delivery_address,
                        'airport_id': airport_id,
                        'customer_token': customer_token,
                        'date': date,
                        'time': time,
                        'address_id': jQuery('#auto_search').attr('data'),
                        'selected_fbo_id': selected_fbo_id,
                    },
                    success: function(result) {

                        console.log(result);
                        window.location = "/menu";
                        // window.flashMessages = [{
                        //     'type': 'alert-success',
                        //     'message': 'Updated'
                        // }];

                        //  $("#address-list").html(result);
                    },
                    error: function(xhr, status, error) {
                    // Error par original state restore karein
                    $('#address_btn').html(originalContent);
                    $(this).find('.btn-ring').hide();
                     console.log("Error:", error);
                     }
                });
            });


            // jQuery('#BROWSE_MENU').prop('disabled', false);
            // 	jQuery('body').on('click', '#BROWSE_MENU', function () {
            // 		window.location = "http://127.0.0.1:8000/breakfast";
            // 	});




        });
    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/home/searchaddress/searchaddress.blade.php ENDPATH**/ ?>