<?php

namespace ACME\CateringPackage\Http\Controllers\Shop;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;


use ACME\CateringPackage\DataGrids\CateringPackageDataGrid;
use ACME\CateringPackage\Repositories\CateringPackageRepository;
use Illuminate\Support\Facades\Event;

use Illuminate\Http\Request;

use Webkul\Core\Repositories\SliderRepository;
use Webkul\Product\Repositories\SearchRepository;
use Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Support\Facades\Log;

class CateringPackageController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller.
     *
     * @return void
     */
    public function __construct(protected CateringPackageRepository $CateringPackageRepository)
    {
        $this->_config = request('_config');
    }

    
    /**
     * Display a listing of the resource.
     *  
     * @return \Illuminate\View\View
     */

    // this function show airtport when keyup input filed
    public function index(Request $request)
    {

        if ($request->ajax() && $request->type === 'address_search') {

            // dd($request);
            // $addresses  = Db::table('delivery_location_airports')->where('name', 'like', '%' . $request->name . '%')->get();

            // $addresses = DB::table('delivery_location_airports')->where('active','1')->where('name', 'like', '%' . $request->name . '%')->orWhere('address', 'like', '%' . $request->name . '%')->get();

            $addresses = DB::table('delivery_location_airports')
            ->where('active', '1')
            ->where(function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->name . '%')
                    ->orWhere('address', 'like', '%' . $request->name . '%');
            })
            ->get();

            //     $addresses =  DB::table('delivery_location_airports')
            // ->select('*')
            // ->whereRaw("MATCH(name) AGAINST(? IN BOOLEAN MODE)", [$request->name])
            // ->get();


            $output = '';

            if (count($addresses) > 0) {
                $output .= '<ul class="list-group">';
                foreach ($addresses as $addresse) {
                    // dd($addresse);
                    // $output.= "<li class='list-group-item' attr='$addresse->id'>".$addresse->name. "</li>";
                    $output .= "<li class='list-group-item pl-2 pl-md-3 pl-lg-3' attr='$addresse->id' data-attr='$addresse->name'> <div class='row m-0'><div class='suggestion-img-div mr-1'><img class='suggestion-icon m-0' src='/themes/velocity/assets/images/location-pin.png' style='position: relative; top: 0;'></div><div>" . "<b class='airport-name'>" . $addresse->name . "</b>" . "<br>" . $addresse->address . "</div></div></li>";
                }
                $output .= '</ul>';
            } else {
              // sandeep add ul tag
                $output .= '<ul class="list-group" style="height:65px; overflow:hidden">';
                $output .= '<li class="list-group-item font-weight-bolder m-auto"> No any delivery location found <li>';
                $output .= '</ul>';
            }

            return $output;
        } elseif ($request->ajax() && $request->type == 'airport_fbo_detail') {

            $airport_fbo_details = DB::table('airport_fbo_details')
                ->where('airport_id', $request->airport_id)
                ->where(function ($query) {
                    $query->where(function ($subquery) {
                        $subquery->whereNull('customer_id')
                            ->whereNull('customer_token');
                    })
                        ->orWhere(function ($subquery) {
                            if (auth()->guard('customer')->check()) {
                                $subquery->Where('customer_id', auth()->guard('customer')->user()->id);
                            } else {
                                $subquery->Where('customer_token', request()->input('_token'));
                            }
                        });
                })
                ->get();


            // dd($airport_fbo_details);   
            $output = '';
            if (count($airport_fbo_details) > 0) {
                foreach ($airport_fbo_details as $airport_fbo_detail) {
                    $output .= "<div class='custom-option p-0' id='add_airport_fbo' data-id='{$airport_fbo_detail->id}' data-attr='{$airport_fbo_detail->name}'>
                                    <div class='row m-0 my-2'>
                                        <div class='suggestion-img-div mr-1'>
                                            <img class='suggestion-icon m-0' src='/themes/volantijetcatering/assets/images/home/pin-2-map.svg' style='position: relative; top: 0;'>
                                        </div>
                                        <div class='text-start' style='flex:1'>
                                            <strong class='airport-name'>{$airport_fbo_detail->name}</strong><br>
                                            {$airport_fbo_detail->address}
                                        </div>
                                    </div>
                                </div>";
                }
                $output .= "<div class='custom-option d-flex justify-content-center add_fbo_detail modal_open_button' id='option_id' data-toggle='modal' data-target='#exampleModalCenter'>
                                <div class='row m-0' >
                                    <div class='suggestion-img-div mr-1'>
                                        <img class='suggestion-icon m-0' src='/themes/volantijetcatering/assets/images/home/plus-circle.svg' style='position: relative; top: 0;'>
                                    </div>
                                    <div class='text-start' style='flex:1'>
                                        <strong class='airport-name'>Add FBO</strong>
                                    </div>
                                </div>
                            </div>";
            } else {
                $output .= '<div class="custom-option font-weight-bolder text-danger py-2 ml-2">No FBO details found</div>';
                $output .= "<div class='custom-option d-flex justify-content-center add_fbo_detail modal_open_button' id='option_id'  data-toggle='modal' data-target='#exampleModalCenter'>
                                <div class='row m-0'>
                                    <div class='suggestion-img-div mr-1'>
                                        <img class='suggestion-icon m-0' src='/themes/volantijetcatering/assets/images/home/plus-circle.svg' style='position: relative; top: 0;'>
                                    </div>
                                    <div class='text-start' style='flex:1'>
                                        <strong class='airport-name'>Add FBO</strong>
                                    </div>
                                </div>
                            </div>";
            } 

            return response()->json(['options' => $output]);
        }

        return view($this->_config['view']);
    }


    /*this function insert data into delivery_location_airports table when clickc airport li tag*/
    public function create(Request $request)
    {

           // sandeep  update defualt address in addresses table
          if ($request->has('type') && $request->type == 'Update_Fbo_Billing') {
            $customer_last_address = request()->input('addresses_id');
            // $customer_id = request()->input('customer_id');
            DB::table('addresses')
            ->where('customer_id', auth()->guard('customer')->id())
            ->update(['default_address' => '0']);
           
            DB::table('addresses')
                ->where('id', $customer_last_address)
                ->update(
                    [
                        'created_at' => now(),
                        'default_address' => '1',
                    ]
                );

            return response()->json([
                'status' => 'true',
                'message' => 'Billing update data...!',
            ]);
        }

        if ($request->has('type') && $request->type == 'add_checkout_fbo') {
            $customerId = $request->islogin == '1' ? auth()->guard('customer')->id() : null;
            $customerToken = $request->islogin != '1' ? $request->_token : null;
            // Start building the query
            $query = DB::table('addresses')
                ->where('address_type', 'customer');

            if ($customerId) {
                $query->where('customer_id', $customerId);
            } else {
                // dd($customerToken);
                $query->where('customer_token', $customerToken);
            }

            // Ensure we update only the latest record based on created_at timestamp
            $query->latest('created_at')->first();
            // Perform the update
            $updated = $query->update(['airport_fbo_id' => $request->input('selected_fbo_id')]);

            if ($updated) {
                // Fetch updated airport FBO details
                $updatedAirportFboId = $request->input('selected_fbo_id');
                $airportFboDetails = DB::table('airport_fbo_details')
                    ->select('name', 'address')
                    ->where('id', $updatedAirportFboId)
                    ->first();

                $response = [
                    'status' => 'success',
                    'message' => $customerId ? 'Data successfully updated!' : 'Guest customer airport successfully updated!',
                    'data' => $airportFboDetails
                ];

                return response()->json($response);
            } else {
                // Handle if update fails
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to update airport FBO data.'
                ]);
            }
        }





        if (isset($request->date)) {
            $dateString = $request->date;

            // Check if the requested date is "Today" or "Tomorrow"
            if ($dateString == "Today") {

                $date = new DateTime(); // Set date to today
            } elseif ($dateString == "Tomorrow") {
                $date = new DateTime('+1 day'); // Set date to tomorrow
            } else {
                // Parse the date string
                $date = DateTime::createFromFormat('l n/j', $dateString);
            }

            // Format the date as desired
            $formattedDate = $date->format('Y-n-j');

            $customer_token = $request->_token;
            // if (Auth::check() === true) {
            //     $customer_id = DB::table('fbo_details')->pluck('customer_id')->toArray();

            //     // if (!in_array(auth()->user()->id, $customer_id)) {
            //     DB::table('fbo_details')
            //         ->updateOrInsert(
            //             ['customer_id' => auth()->user()->id],
            //             [
            //                 'delivery_time' => $request->time,
            //                 'delivery_date' => $formattedDate,
            //                 'customer_id' => auth()->user()->id,
            //             ]
            //         );
            //     session(['token' => $customer_token]);
            //     // }

            // } else {
            //     DB::table('fbo_details')
            //         ->updateOrInsert(
            //             ['customer_token' => $customer_token],
            //             [
            //                 'delivery_time' => $request->time,
            //                 'delivery_date' => $formattedDate,
            //                 'customer_token' => $customer_token,
            //             ]
            //         );
            //     session(['token' => $customer_token]);
            // }

        //   sandeep add code update latest fbo details
            if (Auth::check() === true) {
                $customer_id = DB::table('fbo_details')->pluck('customer_id')->toArray();
                
                $latestRecord = DB::table('fbo_details')
                    ->where('customer_id', auth()->user()->id)
                    ->orderBy('id', 'desc')
                    ->first();
            
                if ($latestRecord) {
                    DB::table('fbo_details')
                        ->where('id', $latestRecord->id)
                        ->update([
                            'delivery_time' => $request->time,
                            'delivery_date' => $formattedDate,
                        ]);
                } else {
                    DB::table('fbo_details')
                        ->insert([
                            'delivery_time' => $request->time,
                            'delivery_date' => $formattedDate,
                            'customer_id' => auth()->user()->id,
                        ]);
                }
                session(['token' => $customer_token]);
            } else {
                $latestRecord = DB::table('fbo_details')
                    ->where('customer_token', $customer_token)
                    ->orderBy('id', 'desc')
                    ->first();
            
                if ($latestRecord) {
                    DB::table('fbo_details')
                        ->where('id', $latestRecord->id)
                        ->update([
                            'delivery_time' => $request->time,
                            'delivery_date' => $formattedDate,
                        ]);
                } else {
                    DB::table('fbo_details')
                        ->insert([
                            'delivery_time' => $request->time,
                            'delivery_date' => $formattedDate,
                            'customer_token' => $customer_token,
                        ]);
                }
                session(['token' => $customer_token]);
            }
        }

        if (!isset($request->time_update) && $request->time_update != true && !request()->has('type')) {

            $airport_data = Db::table('delivery_location_airports')->where('id', $request->airport_id)->first();

            $country_states = Db::table('country_states')->where('id', $airport_data->state)->first();
        }

        if ($request->islogin === '1') {

            $customer = auth()->guard('customer')->user();
     

            // $airport_data = Db::table('delivery_location_airports')->where('id',$request->airport_id)->first();

            // $country_states = Db::table('country_states')->where('id', $airport_data->state)->first();
            $addresses = Db::table('addresses')->pluck('customer_id')->toArray();
         
            if (!in_array($customer->id, $addresses)) {

                $addressId = DB::table('addresses')->insertGetId([
                    'postcode' => $airport_data->zipcode,
                    'state' => $country_states->code,
                    'country' => $country_states->country_code,
                    'address1' => $airport_data->address,
                    'customer_id' => $customer->id,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name,
                    'email' => $customer->email,
                    'airport_name' => $airport_data->name,
                    'address_type' => 'customer',
                    'airport_fbo_id' => $request->input('selected_fbo_id'),
                    'created_at' => now(),
                ]);

                $airportData = DB::table('addresses')
                ->where('id', $addressId)
                ->first();
                
            return response()->json([
                'status' => 'true',
                'message' => 'successfully added data...!',
                'data' => $airportData
            ]);

            
            } else {

                if (isset($request->update_airport_id) && $request->update_airport_id != 0) {

                    DB::table('addresses')
                        ->where('customer_id', $customer->id)
                        ->where('id', $request->update_airport_id)
                        ->update(
                            [
                                'postcode' => $airport_data->zipcode,
                                'state' => $country_states->code,
                                'country' => $country_states->country_code,
                                'address1' => $airport_data->address,
                                'customer_id' => $customer->id,
                                'first_name' => $customer->first_name,
                                'last_name' => $customer->last_name,
                                'email' => $customer->email,
                                'airport_name' => $airport_data->name,
                                'airport_fbo_id' => $request->input('selected_fbo_id'),
                            ]
                        );

                    // sandeep  get airport data
                    $airportData = DB::table('addresses')
                        ->where('id', $request->update_airport_id)
                        ->first();


                    return response()->json([
                        'status' => 'true',
                        'message' => 'successfully update data...!',
                        'data' => $airportData
                    ]);

                    // echo json_encode(['status' => 'successfully update data...!', 'data'=>$airportData ]);

                } else {

                    $customer_last_address = request()->input('address_id');
                    // dd($customer_last_address); 
                    // sandeep add code
                    DB::table('addresses')
                    ->where('customer_id', $customer->id)
                    ->update(['default_address' => '0']);

                    DB::table('addresses')
                        ->where('id', $customer_last_address)
                        ->update(
                            [
                                // 'postcode' => $customer_last_address,
                                'state' => $country_states->code,
                                'country' => $country_states->country_code,
                                'address1' => $airport_data->address,
                                'customer_id' => $customer->id,
                                'first_name' => $customer->first_name,
                                'last_name' => $customer->last_name,
                                'email' => $customer->email,
                                'airport_name' => $airport_data->name,
                                'airport_fbo_id' => $request->input('selected_fbo_id'),
                                'default_address' => '1',
                                'created_at' => now(),
                            ]
                        );

                    $airportData = DB::table('addresses')
                        ->where('id', $customer_last_address)
                        ->first();

                    return response()->json([
                        'status' => 'true',
                        'message' => 'successfully update data...',
                        'data' => $airportData
                    ]);

                    // echo json_encode(['status' => 'successfully update data...!']);
                }
            }
        }
        // guest user airport entry
        else {

            session(['token' => $request->_token]);

            $addresses = Db::table('addresses')->pluck('customer_token')->toArray();
            if (!in_array($request->customer_token, $addresses)) {

                $addressId = DB::table('addresses')->insertGetId([

                    'postcode' => $airport_data->zipcode,
                    'state' => $country_states->code,
                    'country' => $country_states->country_code,
                    'address1' => $airport_data->address,
                    'customer_token' => $request->customer_token,
                    'airport_name' => $airport_data->name,
                    'address_type' => 'customer',
                    'airport_fbo_id' => $request->input('selected_fbo_id'),
                    'created_at' => now(),
                ]);


                // sandeep get address data
                $airportData = DB::table('addresses')
                    ->where('id', $addressId)
                    ->first();

                return response()->json([
                    'status' => 'true',
                    'message' => 'successfully guest customer airport added',
                    'data' => $airportData
                ]);

                // echo json_encode(['status' => 'successfully guest customer airport added','data'=>$]);
            }

            // if guest user address table already exist then update
            else {
                if (isset($request->update_airport_id) && $request->update_airport_id != 0) {
                    $addressId = DB::table('addresses')
                        ->where('customer_token', $request->customer_token)
                        ->where('id', $request->update_airport_id)
                        ->update([
                            'postcode' => $airport_data->zipcode,
                            'state' => $country_states->code,
                            'country' => $country_states->country_code,
                            'address1' => $airport_data->address,
                            'customer_token' => $request->customer_token,
                            'airport_fbo_id' => $request->input('selected_fbo_id'),
                            'airport_name' => $airport_data->name,
                            
                        ]);
                    echo json_encode(['status' => 'successfully guest customer airport updated2']);
                } else {

                    $customer_last_address = $addresses = DB::table('addresses')
                        ->select('id')
                        ->where('customer_token', $request->customer_token)
                        ->where('address_type', 'customer')
                        ->orderBy('created_at', 'desc')
                        ->first();


                    $addressId = DB::table('addresses')
                        ->where('id', $customer_last_address->id)
                        ->update([
                            'postcode' => $airport_data->zipcode,
                            'state' => $country_states->code,
                            'country' => $country_states->country_code,
                            'address1' => $airport_data->address,
                            'customer_token' => $request->customer_token,
                            'airport_fbo_id' => $request->input('selected_fbo_id'),
                            'airport_name' => $airport_data->name,

                        ]);


                    // sandeep get address data
                    $airportData = DB::table('addresses')
                        ->where('id', $customer_last_address->id)
                        ->first();

                    // echo json_encode(['status' => 'successfully guest customer airport updated','data' => $airportData  ]);

                    return response()->json([
                        'status' => 'true',
                        'message' => 'successfully guest customer airport updated3',
                        'data' => $airportData
                    ]);

                }

            }
        }
    }

    // store airport fbo
    public function store()
    {
        $validatedData = $this->validate(request(), [
            'name' => 'required',
            'address' => 'required',
        ]);

        // Check if the user is authenticated
        if (auth()->check()) {
            $customerId = auth()->user()->id;
            $customerToken = null;
        } else {
            $customerId = null;
            $customerToken = request()->input('_token');
        }

        $insertedId = DB::table('airport_fbo_details')->insertGetId([
            'name' => $validatedData['name'],
            'address' => $validatedData['address'],
            'airport_id' => request()->input('airport_id'),
            'notes' => request()->input('notes'),
            'customer_id' => $customerId,
            'customer_token' => $customerToken,
        ]);

        // Start building the query
        $query = DB::table('addresses')
            ->where('address_type', 'customer');

        if ($customerId) {
            $query->where('customer_id', $customerId);
        } else {
            // dd($customerToken);
            $query->where('customer_token', $customerToken);
        }
        
        $addressId = request()->input('address_Id');
        if($addressId){
            $query->where('id',$addressId);
        }
        // Ensure we update only the latest record based on created_at timestamp
        $query->latest('created_at')->first();

        $query->update(['airport_fbo_id' => $insertedId]);

        // Log::info('Inserted record ID: ' . $insertedId);
        $insertedRecord = DB::table('airport_fbo_details')->where('id', $insertedId)->first();
        // if ($inserted) {
        session()->flash('success', trans('Airport Fbo Details added successfully'));

        return response()->json(['response' => true, 'data' => $insertedRecord]);

    }
}
