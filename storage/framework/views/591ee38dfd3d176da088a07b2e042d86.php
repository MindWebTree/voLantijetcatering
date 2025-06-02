<?php
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Session;
    use Webkul\Checkout\Facades\Cart;
    use Illuminate\Support\Facades\Request;
    use Webkul\Checkout\Repositories\CartRepository;

    // $guestToken = Session::token();
    // $airportArr = Db::table('delivery_location_airports')->pluck('name')->toArray();

    // // Retrieve the guest session ID
    // $guestSessionId = Session::getId();
    // $cartItems = Session::get('cart');
    //echo session()->get('cart')->id;

    // $customer = auth()->guard('customer')->user();

    // if (Auth::check()) {
    //     $islogin = 1;
    //     $address = Db::table('addresses')
    //         ->where('customer_id', $customer->id)
    //         ->where('address_type', 'customer')
    //         ->orderBy('id', 'desc') // Assuming 'created_at' is a timestamp column
    //         ->first();

    // } else {
    //     $islogin = 0;
    //     $address = Db::table('addresses')
    //         ->where('customer_token', $guestToken)
    //         ->where('address_type', 'customer')
    //         ->first();
    // }

    // // sandeep
    // if ($address) {
    //     $airpport = DB::table('delivery_location_airports as al')
    //         ->join('airport_fbo_details as af', 'af.airport_id', '=', 'al.id')
    //         ->where('al.name', $address->airport_name)
    //         ->where('af.id', $address->airport_fbo_id)
    //         ->select('af.name as fbo_name', 'al.id as airport_id')
    //         ->first();
    //     // dd($airpport);
    // }

    // dd($AirportId);
    // if ($address) {
    //     $AirportsName = DB::table('delivery_location_airports')
    //         ->select('name','id')
    //         ->get();

    //  }

    // sandeep get airport data and fbo data
    $Airports = DB::table('delivery_location_airports')->select('name', 'id')->get();

    
    // $AirportsFbo = DB::table('airport_fbo_details')->select('name', 'id')->get();
    
    if(Auth::check()){
        $AirportsFbo = DB::table('airport_fbo_details')
            ->select('name', 'id')
            ->where('customer_id', $customer->id)
            ->orWhere(function ($query) {
                $query->whereNull('customer_id')
                    ->whereNull('customer_token');
            })
            ->get();
       }else{
        $AirportsFbo = DB::table('airport_fbo_details')
            ->select('name', 'id')
            ->where('customer_token', $guestToken)
            ->orWhere(function ($query) {
                $query->whereNull('customer_id')
                    ->whereNull('customer_token');
            })
            ->get();
       }
    //    dd($AirportsFbo);
    //    dd($defaultAddress);
?>

  

<!-- Modal -->
<?php if(Auth::check()): ?>
    <form action="">
        <?php echo csrf_field(); ?>
        <div class="modal fade sandeep_model" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header address-header pb-0">
                        <h3 v-if="allAddress.length > 0">Update location</h3>
                        <h3 v-else>Select location</h3>

                        <button type="button" class="close " data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
                            <div class="search-content">
                                <div class="row">
                                    <div class="col-lg-9 col-md-9 col-8 pr-0">
                                        <input type="text" id="auto_search" class="form-control"
                                            placeholder="Search Delivery Location" attr=""
                                            <?php if(isset($address)): ?> value="" <?php endif; ?>>
                                        <div id="address-list" class="suggestion-list"></div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-3">
                                        <input type="hidden" id="airport_id" value="0" class="">
                                        <button v-if="allAddress.length > 0" class="btn btn-danger m-auto address-btn"
                                             type="button" id="address_update">Search</button>
                                        <button v-else class="btn btn-danger m-auto address-btn" type="button"
                                            id="address_update">Search</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        
                        <div class="search__wrapper">
                            <input type="text" id="airport-fbo-input" class="form-control pr-4 pl-1 bg-white pointer"
                                placeholder="Airport Fbo Detail" readonly
                                <?php if(isset($airpport)): ?> value="" <?php endif; ?>>
                            <img class="Navigation-image pointer pr-2" id="airportImageFbo"
                                src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/down-arrow.svg')); ?>"
                                alt="" height="20px" />
                            <div id="checkout_airport-fbo-list" class="custom-dropdown-list text-justify d-none">
                            </div>

                            <input type="hidden" id="selected-fbo-id" name="selected_fbo_id">

                        </div>

                    </div>
                </div>
            </div>
        </div>

    <div class="modal fade add_fbo_modal" id="exampleModalCenter" tabindex="-1" role="dialog"
    aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-center" id="exampleModalCenterTitle">
                    <img class="Navigation-image"
                        src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/store.svg')); ?>"
                        alt="" />
                    Add New Fbo
                </h5>
                <button type="button" class="fboClose" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-container">
                    <div class="input_wrapper">
                        <label for="fbo-name" class="mandatory">Fbo Name</label>
                        <input type="text" class="control" id="fbo-name" name="name"
                            v-validate="'required'" value="" />
                            <!-- sandeep comment code -->
                         <!-- <span class="control-error"
                            v-if="errors.has('name')">{{ errors.first('name') }}</span> -->
                            <span class="control-error" id="name-error">
                            </span>
                    </div> 

                    <div class="input_wrapper">
                        <label for="fbo-address" class="mandatory">Address</label>
                        <textarea v-validate="'required'" class="control" id="fbo-address" name="address" rows="5"></textarea>
                         <!-- sandeep comment code -->
                        <!-- <span class="control-error"
                            v-if="errors.has('address')">{{ errors.first('address') }}</span> -->
                            <span class="control-error" id="address-error">
                            </span>
                    </div>
                    <div class="input_wrapper">
                        <label for="fbo-notes">Notes (Optional)</label>
                        <textarea class="control" id="fbo-notes" name="notes" rows="5"></textarea>
                        <!-- <span class="control-error"
                            v-if="errors.has('notes')">{{ errors.first('notes') }}</span> -->
                            <span class="control-error" id="notes-error">
                            </span>
                    </div>

                    <button id="add-fbo-button">
                        <img class='suggestion-icon'
                            src='/themes/volantijetcatering/assets/images/home/plus-circle1.svg'>
                        ADD</button>
                </div>
            </div>

        </div>
    </div>
</div>
    </form>

<?php else: ?>
    <form id="AirportForm">
        <?php echo csrf_field(); ?>
        <div class="modal fade sandeep_model" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header address-header pb-0">
                        <?php if($customer_address): ?>
                            <h3>Update location</h3>
                        <?php else: ?>
                            <h3>Add location</h3>
                        <?php endif; ?>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="input-group">
                            <div class="search-content">
                                <div class="row">
                                    <div class="col-lg-9 col-md-9 col-8 pr-0">
                                        <input type="text" id="auto_search" class="form-control"
                                            placeholder="Search Delivery Location"
                                            attr="<?php echo e(isset($airpport) ? $airpport->airport_id : ''); ?>"
                                            <?php if(isset($address)): ?> value="<?php echo e($address->airport_name); ?>" <?php endif; ?>>
                                        <div id="address-list" class="suggestion-list"></div>
                                    </div>
                                    <div class="col-lg-3 col-md-3 col-3">
                                        <input type="hidden" id="airport_id" value="0">
                                        <?php if($customer_address): ?>
                                            <button class="btn btn-danger m-auto address-btn" type="button"
                                                id="address_update">Search</button>
                                        <?php else: ?>
                                            <button class="btn btn-danger m-auto address-btn" type="button"
                                                id="address_update">Search</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        

                        <div class="search__wrapper">
                            <input type="text" id="airport-fbo-input" class="form-control bg-white pr-4 pl-1 pointer"
                                 placeholder="Airport Fbo Detail" readonly
                                <?php if(isset($airpport)): ?> value="<?php echo e($airpport->fbo_name); ?>" <?php endif; ?>>
                            <img class="Navigation-image pointer pr-2" id="airportImageFbo"
                                src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/down-arrow.svg')); ?>"
                                alt="" height="20px" />
                            <div id="checkout_airport-fbo-list" class="custom-dropdown-list text-justify d-none">
                                <!-- Options will be inserted here -->
                            </div>

                            <input type="hidden" id="selected-fbo-id" name="selected_fbo_id">


                        </div>
                    </div>
                </div>
            </div>
        </div>

         
         <div class="modal fade add_fbo_modal" id="exampleModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
             <div class="modal-content">
                 <div class="modal-header">
                     <h5 class="modal-title text-center" id="exampleModalCenterTitle">
                         <img class="Navigation-image"
                             src="<?php echo e(asset('themes/volantijetcatering/assets/images/home/store.svg')); ?>"
                             alt="" />
                         Add New Fbo
                     </h5>
                     <button type="button" class="fboClose" data-dismiss="modal"
                         aria-label="Close">
                         <span aria-hidden="true">&times;</span>
                     </button>
                 </div>
                 <div class="modal-body">
                     <div class="form-container">
                         <div class="input_wrapper">
                             <label for="fbo-name" class="mandatory">Fbo Name</label>
                             <input type="text" class="control" id="fbo-name" name="name"
                                 v-validate="'required'" value="" />
                             
                                 <span class="control-error" id="name-error">
                                 </span>
                         </div>
                         <div class="input_wrapper">
                             <label for="fbo-address" class="mandatory">Address</label>
                             <textarea v-validate="'required'" class="control" id="fbo-address" name="address" rows="5"></textarea>
                             
                             <span class="control-error" id="address-error">
                             </span>
                         </div>
                         <div class="input_wrapper">
                             <label for="fbo-notes">Notes (Optional)</label>
                             <textarea class="control" id="fbo-notes" name="notes" rows="5"></textarea>
                             
                             <span class="control-error" id="notes-error">
                             </span>
                         </div>

                         <button id="add-fbo-button">
                             <img class='suggestion-icon'
                                 src='/themes/volantijetcatering/assets/images/home/plus-circle1.svg'>
                             ADD</button>
                     </div>
                 </div>

             </div>
         </div>
     </div>
     <!-- End Modal for adding FBO -->


    </form>


<?php endif; ?>

<form data-vv-scope="address-form" class="custom-form pl-3">
    <div class="form-container" v-if="! this.new_billing_address">
        <div slot="body">
            <div class="address-container row full-width no-margin" id="address-container">
                <div :key="index" class="col-lg-6 col-md-12 address-holder pl0 ordering-from"
                    v-for='(addresses, index) in allAddress'>
                    
                    <div class="border-0" style="width: 100%;">
                        <div class="row address-row">
                            <div class="col-1">
                                <div class="radio" id="checked-radio">
                                    <input type="radio" name="billing[address_id]" class="Billing_Address_button"
                                        :id="'billing_address_id_' + addresses.id" :value="addresses.id"
                                        v-model="address.billing.address_id"
                                        data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.billing-address')); ?>&quot;"
                                        @change="validateForm('address-form')" />
                                    <label :for="'billing_address_id_' + addresses.id" class="radio-view px-2"></label>
                                </div>
                            </div>
                            
                            <div class="col-9 address-column">
                                <h4 class="card-title fw6">Ordering From</h4>
                                
                                <!-- Button trigger modal -->
                                <ul type="none" class="order-body">
                                    
                                    
                                    <li class="airport_fbo_id" v-text="addresses.airport_fbo_id" hidden></li>
                                    
                                    <li class="address_Id" v-text="addresses.id" hidden></li>

                                    <li class="airport_name" v-text="addresses.airport_name" id="airport_name"></li>

                                    <li v-text="addresses.address1" id="Airport_Address"></li>
                                    <li v-text="addresses.default_address" class="default_address"
                                        style="display:none"></li>
                                    <!-- <li v-text="addresses.state"></li>
                                        <li>
                                            <span v-text="addresses.country"></span>
                                            <span v-text="addresses.postcode"></span>
                                        </li> -->
                                    <!-- <li><?php echo e(__('shop::app.customer.account.address.index.contact')); ?> : {{ addresses.phone }}</li> -->
                                    <div class="mt-2 text-break"  id="airport_fbo_details" :class="{ 'd-none': !addresses.fbo_name }">
                                        <h5 class="child-card-title mb-0">Airport Fbo</h5>
                                        <li class="airport_fboName" v-text="addresses.fbo_name" id="AirportFbo_Name"></li>
                                        
                                    </div>
                                </ul>
                            </div>
                            <div class="col-1">
                                <span class="text-danger pointer edit-airport" data-toggle="modal"
                                    data-target="#exampleModal" id="Edit_Airport">edit</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- hide add new address -->
                <!-- <div class="col-lg-6 col-md-12 address-holder pl0">
                        <div class="card">
                            <div
                                @click="validateFormAfterAction"
                                class="card-body add-address-button">
                                <div class="cursor-pointer" @click="newBillingAddress()">
                                    <i class="material-icons">add_circle_outline</i>

                                    <span><?php echo e(__('shop::app.checkout.onepage.new-address')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div> -->
            </div>
            


            <div :class="`col-12 mt15 has-error ${errors.has('address-form.billing[address_id]') ? 'has-error' : ''}`">
                <span class="control-error" v-if="errors.has('address-form.billing[address_id]')"
                    v-text="errors.first('address-form.billing[address_id]')">
                </span>
            </div>

            
        </div>


    </div>

    <div class="form-container" v-else>
        <!-- <accordian :title="'<?php echo e(__('shop::app.checkout.onepage.billing-address')); ?>'" :active="true">
            <div class="form-header" slot="header">
                <h3 class="fw6 display-inbl">
                    <?php echo e(__('shop::app.checkout.onepage.billing-address')); ?>

                </h3>

                <i class="rango-arrow"></i>
            </div>

            <div class="col-12 no-padding" slot="body">
                <?php if(auth()->guard('customer')->check()): ?>
    <?php if(count(auth('customer')->user()->addresses)): ?>
    <a class="theme-btn light back-button text-up-14" @click="backToSavedBillingAddress()">

                                                                        <?php echo e(__('shop::app.checkout.onepage.back')); ?>

                                                                    </a>
    <?php endif; ?>
<?php endif; ?>



                <?php echo $__env->make('shop::checkout.onepage.customer-new-form', ['billing' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            
                
        </accordian> -->
        <?php if(Auth::check()): ?>
            <h4 class="card-title fw6 mt-3">Ordering From</h4>
            <button type="button" class="btn mb-3 theme-btn add-address-btn" style="font-size:14px" data-toggle="modal"
                data-target="#exampleModal">Choose Location</button>
        <?php else: ?>
            <?php if($customer_address == null): ?>
                <h4 class="card-title fw6 mt-3">Ordering From</h4>
                <button type="button" class="btn  mb-3 theme-btn add-address-btn" data-toggle="modal"
                    data-target="#exampleModal">Choose Location</button>
            <?php else: ?>
                <div class="address-container row full-width no-margin">
                    <div :key="" class="col-lg-6 col-md-12 address-holder pl0 ordering-from">
                        <div class="border-0 pb-0" style="width: 100%;">
                            <div class="row address-row">
                                <div class="col-1">
                                    <div class="radio" id="checked-radio">
                                        <input type="radio" name="billing[<?php echo e($customer_address->id); ?>]"
                                            :id="'billing_address_id_<?php echo e($customer_address->id); ?>'"
                                            :value="<?php echo e($customer_address->id); ?>" v-model="address.billing.address_id"
                                            data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.billing-address')); ?>&quot;"
                                            @change="validateForm('address-form')" />
                                        <label :for="'billing_address_id_<?php echo e($customer_address->id); ?>'"
                                            class="radio-view px-2"></label>
                                        <!-- <label :for="'billing_address_id_' + addresses.id" class="radio-view px-2"></label> -->
                                    </div>
                                </div>
                                <div class="col-9 address-column">
                                    <h4 class="card-title fw6">Ordering From</h4>
                                    <!-- Button trigger modal -->
                                    <ul type="none" class="order-body">
                                        
                                        
                                        
                                        <li class="airport_name" id="airport_name">
                                            <?php echo e($customer_address->airport_name); ?></li>
                                        <input type="hidden" id="selected_airport_id"
                                            value="<?php echo e($airpport->airport_id??''); ?>" />
                                        <li class="airport_fbo_id" hidden><?php echo e($customer_address->airport_fbo_id); ?></li>
                                        <li id="Airport_Address"><?php echo e($customer_address->address1); ?></li>
                                        <li class="default_address" style="display:none">
                                            <?php echo e($customer_address->default_address); ?></li>
                                        <!-- <li v-text="addresses.state"></li>
                                        <li>
                                            <span v-text="addresses.country"></span>
                                            <span v-text="addresses.postcode"></span>
                                        </li> -->
                                        <!-- <li><?php echo e(__('shop::app.customer.account.address.index.contact')); ?> : {{ addresses.phone }}</li> -->
                                    </ul>
                                </div>
                                <div class="col-1">
                                    <span class="text-danger pointer Airport_Edit" id="Edit_Airport"
                                        data-toggle="modal" data-target="#exampleModal">edit</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
           
        <div class="text-break pl-4 pl-lg-3 mb-2 pb-3  ml-lg-1  <?php echo e(!isset($airpport) ? 'd-none' : ''); ?>" id="airport_fbo_details" type="none">
            <h5 class="child-card-title mb-0">Airport Fbo</h5>
            <p class="airport_fboName mb-0" id="AirportFbo_Name"><?php echo e(isset($airpport) ? $airpport->fbo_name : ''); ?></p>
        </div>

                              
                             
    </div>

    <?php if($cart->haveStockableItems()): ?>
        <div class="form-container" v-if="! address.billing.use_for_shipping && ! this.new_shipping_address">

            <accordian :active="true" :title="'<?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?>'">

                <div class="form-header mb-30" slot="header">
                    <h3 class="fw6 display-inbl">
                        <?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?>

                    </h3>

                    <i class="rango-arrow"></i>
                </div>

                <div class="address-container row mb30 remove-padding-margin" slot="body">
                    <div class="col-lg-6 address-holder pl0" v-for='(addresses, index) in this.allAddress'>

                        <div class="card">
                            <div class="card-body row">
                                <div class="col-1">
                                    <div class="radio">
                                        <input type="radio" name="shipping[address_id]"
                                            :id="'shipping_address_id_' + addresses.id" :value="addresses.id"
                                            v-model="address.shipping.address_id" v-validate="'required'"
                                            data-vv-as="&quot;<?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?>&quot;"
                                            @change="validateForm('address-form')" />

                                        <label :for="'shipping_address_id_' + addresses.id"
                                            class="radio-view"></label>
                                    </div>
                                </div>

                                <div class="col-10">
                                    <h5 class="card-title fw6"
                                        v-text="`${addresses.first_name} ${addresses.last_name}`"></h5>

                                    <ul type="none">
                                        <li v-text="addresses.address1"></li>
                                        <li v-text="addresses.city"></li>
                                        <li v-text="addresses.state"></li>
                                        <li>
                                            <span v-text="addresses.country"></span>
                                            <span v-text="addresses.postcode"></span>
                                        </li>
                                        <li><?php echo e(__('shop::app.customer.account.address.index.contact')); ?> :
                                            {{ addresses.phone }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 address-holder pl0">
                        <div class="card">
                            <div @click="validateFormAfterAction" class="card-body add-address-button">
                                <div class="cursor-pointer" @click="newShippingAddress()">
                                    <i class="material-icons">
                                        add_circle_outline
                                    </i>

                                    <span><?php echo e(__('shop::app.checkout.onepage.new-address')); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div
                        :class="`col-12 mt15 has-error pl0 ${errors.has('address-form.shipping[address_id]') ? 'has-error' : ''}`">
                        <span class="control-error" v-if="errors.has('address-form.shipping[address_id]')"
                            v-text="errors.first('address-form.shipping[address_id]')"></span>
                    </div>
                </div>
            </accordian>
        </div>

        <div class="form-container" v-if="! address.billing.use_for_shipping && this.new_shipping_address">

            <accordian :active="true" :title="'<?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?>'">

                <div class="form-header" slot="header">
                    <h3 class="fw6 display-inbl">
                        <?php echo e(__('shop::app.checkout.onepage.shipping-address')); ?>

                    </h3>

                    <i class="rango-arrow"></i>
                </div>

                <div class="col-12 no-padding" slot="body">
                    <?php if(auth()->guard('customer')->check()): ?>
                        <?php if(count(auth('customer')->user()->addresses)): ?>
                            <a class="theme-btn light float-right text-up-14" @click="backToSavedShippingAddress()">

                                <?php echo e(__('shop::app.checkout.onepage.back')); ?>

                            </a>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php echo $__env->make('shop::checkout.onepage.customer-new-form', ['shipping' => true], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                </div>
            </accordian>
        </div>
    <?php endif; ?>
</form>

<?php $__env->startPush('scripts'); ?>
    
    <script>

      // Sandeep: Show all airport and FBO data
        var airportsData = <?php echo json_encode(isset($Airports) ? $Airports : [], 15, 512) ?>;
        var airportsFboData = <?php echo json_encode(isset($AirportsFbo) ? $AirportsFbo : [], 15, 512) ?>;
        var defaultAddressId = <?php echo json_encode(isset($defaultAddress->id) ? $defaultAddress->id : 0, 15, 512) ?>;
        var isAuthenticated = <?php echo json_encode(auth('customer')->check(), 15, 512) ?>;
        
        var airport_id = 0;
        <?php if(isset($airport_id)): ?>
        airport_id = <?php echo e($airport_id->id); ?>;
        <?php endif; ?>
        // 

        jQuery(document).ready(function() {
            
            $('body').on('click','#acknowledge_checkbox',function(){
                if (isAuthenticated) {
                    console.log('user is login');
                }else{
                    console.log('user not login');
                }
            });



            console.log('checkout page');

            // sandeep auto select last address in multiple address
            function checkElement(selector, callback) {
                var observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        if ($(selector).length > 1) {
                            callback();
                            observer.disconnect(); // Stop observing once the element is found
                        }
                    });
                });

                // Start observing the target node for mutations
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }


            checkElement('.Billing_Address_button', function() {
                if ($('body').find('.Billing_Address_button').length > 1) {
                    // sandeep add code 
                    var airport_fbo_id = $('.Billing_Address_button:checked').closest(".row").find(".airport_fbo_id").text();
                    if (defaultAddressId) {
                        var defaultAddress = jQuery('#billing_address_id_' + defaultAddressId);
                        defaultAddress.click();
                    } else {
                        var lastAddressData = $('#address-container #checked-radio input').last();
                        lastAddressData.click();
                    }
                    var airport_name = $('.edit-airport').closest(".row").find("ul .airport_name").text();
                }
            });


            // sandeep  add fbo detail 
            jQuery('body').on('click', '.edit-airport', function() {

                var airport_name = $(this).closest(".row").find("ul .airport_name").text();
                var airport_id = $(this).closest(".row").find("input[type='radio']").val();
                // var fbo_name = $(this).closest(".row").find("#AirportFbo_Name").text();
                var airport_fbo_id = $(this).closest(".row").find(".airport_fbo_id").text();
                var addresses_id = $(this).closest(".row").find(".address_Id").text();
                var fbo_name = $(this).closest(".row").find("ul #AirportFbo_Name").text();
                //console.log('fbo_name',fbo_name);
                // Update the input fields with the selected airport data
                $('#auto_search').val(airport_name);
                $('#auto_search').attr('data', addresses_id);

                // sandeep get airport id
                // Compare the selected airport name with the airport data and set the airport ID
                var matchedAirport = airportsData.find(airport => airport.name === airport_name);
                if (matchedAirport) {
                    $('#auto_search').attr('attr', matchedAirport.id);
                    // $('#Airports_Id').val(matchedAirport.id);
                }

                // // sandeep get fbo name 
                // var matchedAirportFbo = airportsFboData.find(airportFbo => airportFbo.id == airport_fbo_id);
                if (fbo_name) {
                    $('#airport-fbo-input').val(fbo_name);
                    $('#airport-fbo-input').attr('attr',fbo_name);
                    // $('#AirportFbo_Name').text(matchedAirportFbo.name);
                } else {
                    $('#airport-fbo-input').val('');
                }
                
            });


            // sandeep  check auto search and fbo input and enable button
            jQuery('body').on('click', '#Edit_Airport', function() {
                // sandeep check airport name and fbo input 
                var airport_fbo_id = $(this).closest(".row").find(".airport_fbo_id").text();
                $('#selected-fbo-id').val(airport_fbo_id);  
                var name = jQuery('#auto_search').val();
                var airportFbo = jQuery('#airport-fbo-input').val();
                var airportFboiD = jQuery('#selected-fbo-id').val();
                // Disable the button if both inputs are empty
            });



            //sandeep show aiport fbo
            jQuery('body').on('click', '.Billing_Address_button', function() {
                //console.log('sandeeo click redio buttton');
                var airport_fbo_id = $(this).closest(".row").find(".airport_fbo_id").text();
                var addresses_id = $(this).val();
                // if (airport_fbo_id == '' || airport_fbo_id === '0') {
                //     $('#airport_fbo_details').addClass('d-none');
                //     $('#AirportFbo_Name').text("");
                // } else {
                //     $('#airport_fbo_details').removeClass('d-none');
                // }

                // var matchedAirportFbo = airportsFboData.find(airportFbo => airportFbo.id == airport_fbo_id);
                // if (matchedAirportFbo) {
                //     $('#AirportFbo_Name').text(matchedAirportFbo.name);
                // }else{
                //     $('#AirportFbo_Name').text("");
                // } 

                var acknowledge_checkbox = $('#acknowledge_checkbox');

                var isAcknowledgeChecked = acknowledge_checkbox.prop('checked'); 
                var fbo_name = $('#airport_fbo_details').find('#AirportFbo_Name').text().trim();
                // var isButtonChecked = $(this).prop('checked');

                // if (isAcknowledgeChecked && paymentsaved && fbo_name !== "") {
                //     $('#checkout-place-order-button').prop('disabled', false);
                // } else {
                //     $('#checkout-place-order-button').prop('disabled', true); 
                // }

                // sandeep update default addresss at 
                // here when ajax hit then create airport or update
                $.ajax({
                    url: "<?php echo e(route('shop.home.create')); ?>",
                    type: 'POST',
                    data: {
                        "_token": "<?php echo e(csrf_token()); ?>",
                        "addresses_id": addresses_id,
                        'type': 'Update_Fbo_Billing',
                    },
                    success: function(result) {
                        
                    }

                });


            });

            if (window.location.pathname == '/') {
                jQuery('#home-right-bar-container').hide();
            }

            var islogin = '<?php echo $islogin; ?>';
            var customer_token = '<?php echo $guestToken; ?>';
            var customerArray = <?php echo json_encode($airportArr); ?>;

          // sandeep ||add timeout code for airport list search
             let typingTimer;
            const typingDelay = 500;
            jQuery('body').on('keyup click', '#auto_search', function() {
                var name = jQuery(this).val();
                clearTimeout(typingTimer); 
                // here when ajax hit then show airport  
                jQuery('#checkout_airport-fbo-list').hide();
                if ($.inArray(name, customerArray) === -1) {
                    // jQuery('#address_update').prop('disabled', true);
                }
 
                typingTimer = setTimeout(function() {
                $.ajax({
                    url: "<?php echo e(route('shop.home.index')); ?>",

                    type: 'GET',
                    data: {
                        'name': name,
                        'type': 'address_search'
                    },
                    success: function(result) {
                        //console.log(result);
                        jQuery("#address-list").html(result);
                    }
                });

            }, typingDelay);

            })



            jQuery('body').on('click', 'li', function() {
                // sandeep
                $('#airport-fbo-input').val('');
                $('#checkout_airport-fbo-list').hide();
                var airportFbo = jQuery('#airport-fbo-input').val();
                var fbo_id = jQuery("#selected-fbo-id").val();
                if (airportFbo && fbo_id != '') {
                    // jQuery('#address_update').prop('disabled', false);
                }

                var name = jQuery(this).attr('data-attr');
                var airport_id = jQuery(this).attr('attr');
                var input_val = jQuery("#auto_search").val(name);
                // sandeep 
                airport_id = jQuery(this).attr('attr');
                jQuery("#auto_search").attr('attr', airport_id);
                jQuery("#auto_search").val(name);


                jQuery("#address-list").html("");

            });

            //  sandeep update address detail  
            jQuery('body').on('click', '#address_update', function() {
console.log('click address button');
                var fbo_id = jQuery("#selected-fbo-id").val().trim();
                var airport_id = jQuery('#auto_search').attr('attr');
                var delivery_address = jQuery("#auto_search").val().trim();
                var address_Id = jQuery("#auto_search").attr('data');
                // fbo 
                var name = jQuery("#airport-fbo-input").val().trim();
                //sandeep 
 if(!airport_id){
                    let element = $('#auto_search');
                    element.addClass('homepage_error_focus').attr('style', 'border: 1px solid #f84661 !important;');
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus').removeAttr('style');
                    }, 3000);
                    return;
                }

                if(!fbo_id){
                    let element = $('#airport-fbo-input');
                    element.addClass('homepage_error_focus').attr('style', 'border: 1px solid #f84661 !important;');
                    setTimeout(() => {
                        element.removeClass('homepage_error_focus').removeAttr('style');
                    }, 3000);
                    return;
                }

                if (delivery_address === '' || name === '' || fbo_id === '') {
                    // jQuery('#address_update').prop('disabled', true);
                    e.preventDefault();
                    return false;
                }



                let originalContent = $(this).html();
                $(this).css('min-width', $(this).outerWidth());
                $(this).html('<span class="btn-ring"></span>');
                $(this).find(".btn-ring").show();
                $(this).find('.btn-ring').css({
                    'display': 'flex',
                    'justify-content': 'center',
                    'align-items': 'center'
                });

                jQuery("#airport-fbo-input").attr('value', name);

                var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');

                // here when ajax hit then create airport or update
                $.ajax({
                    url: "<?php echo e(route('shop.home.create')); ?>",
                    type: 'POST',
                    data: {
                        "_token": "<?php echo e(csrf_token()); ?>",
                        'islogin': islogin,
                        'delivery_address': delivery_address,
                        'airport_id': airport_id,
                        'update_airport_id': jQuery('#airport_id').val(),
                        'selected_fbo_id': fbo_id,
                        'address_id': address_Id,
                        'customer_token': customer_token
                    },

                    success: function(result) {

                        //console.log(result);
                        var airportId = result.data.id;        
                        location.reload();
                        window.flashMessages = [{
                            'type': 'alert-success',
                            'message': 'Updated'
                        }];
                    },
                    error: function(xhr, status, error) {
                    if (xhr.status === 419) {
                        window.location.href = '/';
                    }
               }
                });
            });

            
            $(document).on('click', '.custom-option', function() {
                var selectedText = $(this).find('.airport-name').text().trim();
                var selectedId = $(this).data('id');
                // Check if selectedId is "abc" and return early if it is
                if ($(this).attr('id') === 'option_id') {
                    $('#checkout_airport-fbo-list').hide();
                    // sandeep
                    return;
                }
                $('#airport-fbo-input').val(selectedText);
                $('#airport-fbo-input').data('selected-id', selectedId);
                $('#selected-fbo-id').val(selectedId); // Store the selected ID in the hidden input
                $('#checkout_airport-fbo-list').hide();

                // sandeep
                // jQuery('#address_update').prop('disabled', false);

            });

            jQuery('body').on('click', '#airport-fbo-input', function() {
                if (jQuery('.custom-dropdown-list').css('display') == 'block') {
                    return;
                }
                
                var airport_id = jQuery('#auto_search').attr('attr');
                $('#checkout_airport-fbo-list').toggle();
                // If an airport_id is present, make the AJAX call
                if (airport_id) {
                    $.ajax({
                        url: "<?php echo e(route('shop.home.index')); ?>",
                        method: 'GET',
                        data: {
                            '_token': "<?php echo e(csrf_token()); ?>",
                            'airport_id': airport_id,
                            'type': 'airport_fbo_detail'
                            },
                            success: function(response) {
                            //console.log(response.options);
                            if (response.options) {
                                $('#checkout_airport-fbo-list').removeClass('d-none');
                                $("#checkout_airport-fbo-list").html(response.options);
                            }
                        },
                            error: function(xhr, status, error) {
                                console.error('AJAX Error:', status, error);
                            }
                    });
                }
            });


            jQuery('body').on('click', '#add-fbo-button', function(event) {
                event.preventDefault();
                let originalContent = $(this).html();

                let airport_id = jQuery('#auto_search').attr('attr');
                let input_airport_id = $('#input_airport_id').val();
                let address_Id = jQuery("#auto_search").attr('data');
                //console.log('dsad', address_Id);
                let fboName = $('#fbo-name').val();
                let fboaddress = $('#fbo-address').val();
                let fboNotes = $('#fbo-notes').val();
                if(fboName && fboaddress){
                $(this).html('<span class="btn-ring"></span>');
                $(this).find(".btn-ring").show();
                }

                if (airport_id || input_airport_id) {
                    $.ajax({
                        url: "<?php echo e(route('shop.home.fbo-details.store')); ?>",
                        method: 'POST',
                        data: {
                            '_token': "<?php echo e(csrf_token()); ?>",
                            'name': fboName,
                            'address': fboaddress,
                            'notes': fboNotes,
                            'address_Id': address_Id,
                            'airport_id': airport_id || input_airport_id, // Use || for fallback

                        },
                        success: function(response) {
                            $('#airport-fbo-input').val(response.data.name);
                            $('#selected-fbo-id').val(response.data.id);
                            var fboId = $('#selected-fbo-id').val();
                            // sandeep search disabled button

                            if (response.response) {
                                resetFormFields();
                                // updateFboDetails(response.data);

                            }
                            $('#add-fbo-button').html(originalContent);
                            $('.btn-ring').hide();
                        },
                        error: function(xhr,status,error) {
                            if (xhr.status === 422) {
                                // $('#add-fbo-button').prop('disabled',true);
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $('#' + key + '-error').text(value[0]).show();
                                });
                            }
                            // sandeep add code
                            if (xhr.status === 419) {
                                window.location.href = '/';
                            }
                            $('#add-fbo-button').html(originalContent);
                            $('.btn-ring').hide();
                        }
                    });
                }
            });
            

            // sandeep ||add code 
            $('body').on('click','.modal_open_button',function(){
                // $('#add-fbo-button').prop('disabled',true);
            });


            // sandeep hide airport list after click
            $('body').on('click', '.close', function() {
                $('#checkout_airport-fbo-list').hide();
                $('.modal-backdrop').removeClass('modal-backdrop fade');
            });



            // sandeep || open modal code
            jQuery('body').on('click','.modal_open_button',function(){
             $('#exampleModalCenter').on('show.bs.modal', function (event) {
                    $('#exampleModalCenter').addClass('show');
                    $('.modal-backdrop').remove();
                    if (!$('.modal-backdrop.fade.show').length) {
                    $('<div>', {
                        class: 'modal-backdrop fade show'
                    }).appendTo('body');
                    }
                    if (!$('.modal-backdrop.fade.in').length) {
                        $('<div>', {
                            class: 'modal-backdrop fade in'
                        }).appendTo('body');
                    }

               })
                $('#exampleModalCenter').on('shown.bs.modal', function (event) {
                    $('body').addClass('modal-open');
               })
            });

            //Function to reset form fields
            function resetFormFields() {
                $('#fbo-name, #fbo-address, #fbo-notes').val('');
                $('.fboClose').click();
                $('#exampleModalCenter').removeClass('show').css('display', 'none');
                $('#exampleModalCenter').on('hidden.bs.modal', function (event) {
                  $('body').addClass('modal-open'); 
                });
           }

            jQuery('body').on('click', '#add_airport_fbo', function() {
                var delivery_address = jQuery("#auto_search").val();
                var csrfToken = jQuery('meta[name="csrf-token"]').attr('content');
                airportFbo(jQuery(this).data('id'));
            });
        });
                
        // sandeep add code
            $(document).on('click', 'body, .fboClose', function() {
            $('#exampleModalCenter').on('hidden.bs.modal', function (event) {
                    $('body').addClass('modal-open');
            })
        });

            // sandeep || check erorr in fbo model and stop form submitting
                $('body').on('click', '.fbo_Info', function(event) {
                    var hasError = false;
                    $('.control-group').each(function() {
                        var errorText = $(this).find('.control-error').text();
                        if (errorText.trim() !== '') {
                            hasError = true; 
                        }
                    });
                    if (hasError) {
                        event.preventDefault();
                    }else{
                        $(this).html('<span class="btn-ring"></span>');
                        $(this).find(".btn-ring").show();
                        $(this).find('.btn-ring').css({
                        'display': 'flex',
                        'justify-content': 'center',
                        'align-items': 'center'
                    });
                    }
                });

    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH /home/ubuntu/volantiScottsdale/resources/themes/volantijetcatering/views/checkout/onepage/customer-info.blade.php ENDPATH**/ ?>