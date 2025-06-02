
 @php 
 use Illuminate\Support\Facades\DB;
 use Illuminate\Support\Facades\Session;
 use Webkul\Checkout\Facades\Cart;
 use Illuminate\Support\Facades\Request;
 use Webkul\Checkout\Repositories\CartRepository;

 $guestToken = Session::token();
 $airportArr = Db::table('delivery_location_airports')->pluck('name')->toArray();
 
// Retrieve the guest session ID
 $guestSessionId = Session::getId();
 $cartItems = Session::get('cart');
 //echo session()->get('cart')->id;

 $customer = auth()->guard('customer')->user();
  
  if(Auth::check())
  {
    $islogin = 1;
    $address = Db::table('addresses')->where('customer_id',$customer->id)->first(); 			
  }
  else{

    $islogin = 0;
    $address = Db::table('addresses')->where('customer_token',$guestToken)->first();
  }
  
 @endphp
 
{{-- <div class="custom-secion">
	<div class="row">
		<div class="form-group col-12">
			<label for="exampleInputPassword1">Address</label>
			<input type="text" class="form-control" id="auto_search" placeholder="Search Delivery Location"  @if (isset($address)) value="{{$address->airport_name}}" @endif>
			<div id="address-list" ></div>
		</div>
	</div>
	<button class="btn btn-primary" id="address_btn" >Submit</button>
</div> --}}



 <div class="col-md-12 search-section-1">
                        <div class="input-group"><a href="{{core()->getConfigData('helloworld.settings.settings.Add Link')}}"><button 
                        class="btn browse-menu" type="button" id="BROWSE_MENU">BROWSE MENU</button>
                        </a>                        
                            <div class="search-content">
                                <div class="searchbar">
                                    
                                <img class="Navigation-image" src="http://127.0.0.1:8000/themes/velocity/assets/images/navigation.png" alt=""/>
                                 <input type="text"  id="auto_search" class="form-control" placeholder="Search Delivery Location"  @if (isset($address)) value="{{$address->airport_name}}" @endif>
                                 
                                 

                                  <button class="btn btn-secondary search-button" type="button"><img class="search-image" src="{{ asset('themes/velocity/assets/images/location.png')}}" alt=""/></button></div>
                                  <div id="address-list" class="suggestion-list"></div>
                               
                            </div> <button class="btn start-order" disabled type="button" id="address_btn">START ORDER</button>
                        </div>
                    </div>
                   


 @push('scripts')

 


 
 
<script>  

jQuery( document ).ready(function() {
  if(window.location.pathname=='/'){
    jQuery('#home-right-bar-container').hide();
  }

    var islogin = '<?php echo $islogin; ?>';
    var customer_token = '<?php echo $guestToken; ?>';
    var customerArray = <?php echo json_encode($airportArr); ?>;

    
    //alert(customerArray);
    var al_name = jQuery('#auto_search').val();
    if(al_name!='')
    {
    	jQuery('#address_btn').prop('disabled', false);
    	jQuery('body').on('click', '#address_btn', function () {
    		window.location = "/breakfast";
        // console.log('fgghfgah');
    	});

    }
   
 

    jQuery('body').on('keyup', '#auto_search', function () {
        // console.log('hfggh');
        var name = jQuery(this).val();
         // here when ajax hit then show airport  
        
              if( $.inArray(name, customerArray) === -1 ) {
             
                jQuery('#address_btn').prop('disabled', true);
                //debugger;

            }


         	
         	 // $('#address_btn').prop('disabled', false);
	         	 $.ajax({
	            url: "{{ route('shop.home.index') }}",

	            type: 'GET',
	            data: {
	                'name': name
	            },
	            success: function (result) {
	                console.log(result);
	                jQuery("#address-list").html(result);
	            }
	        });

        })
        


    jQuery('body').on('click', 'li', function () {
       
       jQuery('#address_btn').prop('disabled', false);
       var name = jQuery(this).attr('data-attr');

        var airport_id = jQuery(this).attr('attr');

        var input_val = jQuery("#auto_search").val(name);  

        jQuery("#address-list").html("");
         
         jQuery('body').on('click', '#address_btn', function () {
         	var delivery_address = jQuery("#auto_search").val();
    	
         	var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
         	
              // here when ajax hit then create airport or update
               $.ajax({
                    url: "{{ route('shop.home.create') }}",
                    type: 'POST',
                    data: {
                        //'_token': $('input[name=_token]').val(),
                        //'_token': csrfToken,
                        "_token": "{{ csrf_token() }}",
                        'islogin':islogin,
                        'delivery_address': delivery_address,
                        'airport_id':airport_id,
                        'customer_token':customer_token
                    },
                    success: function (result) {  
                        location.reload(); 
                       window.location = "/breakfast";
                        window.flashMessages = [{
						          'type': 'alert-success',
						         'message': 'Updated'
						 }];
            
                      //  $("#address-list").html(result);
                    }
               }); 
              });

       });
    

    // jQuery('#BROWSE_MENU').prop('disabled', false);
    // 	jQuery('body').on('click', '#BROWSE_MENU', function () {
    // 		window.location = "http://127.0.0.1:8000/breakfast";
    // 	});
    });

</script>


@endpush
