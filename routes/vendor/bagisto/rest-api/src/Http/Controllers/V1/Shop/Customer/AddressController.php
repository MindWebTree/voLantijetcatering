<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Illuminate\Http\Request;
use Webkul\Customer\Http\Requests\CustomerAddressRequest;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\RestApi\Http\Resources\V1\Shop\Customer\CustomerAddressResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Sanctum\PersonalAccessToken;

class AddressController extends CustomerController

{
    /**
     * Repository class name.
     *
     * @return string
     */
    public function repository()
    {
        return CustomerAddressRepository::class;
    }

    /**
     * Resource class name.
     *
     * @return string
     */
    public function resource()
    {
        return CustomerAddressResource::class;
    }

    /**
     * Customer address repository instance.
     *
     * @var \Webkul\Customer\Repositories\CustomerAddressRepository
     */
    protected $customerAddressRepository;

    /**
     * Store address.
     *
     * @param  \Webkul\Customer\Http\Requests\CustomerAddressRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CustomerAddressRequest $request)
    {

        $customerDetail = $this->resolveShopUser($request);
        $customerId = $customerDetail->id;

        $fbodetail = DB::table('fbo_details')
        ->where('customer_id', $customerId)
        ->select('delivery_date','delivery_time')
        ->first();
        // dd($fbodetail);

        $validationRules = [
            'airport_id' => 'required',
        ];
        // Check if delivery_date and delivery_time are missing or null
        if (!$fbodetail || is_null($fbodetail->delivery_date) || $fbodetail->delivery_date === "" || 
            is_null($fbodetail->delivery_time) || $fbodetail->delivery_time === "") {
            $validationRules['delivery_date'] = 'required';
            $validationRules['delivery_time'] = 'required';
        }
       
        // Perform validation with all rules
        $validation = Validator::make($request->all(), $validationRules);
        
        // Check for validation errors
        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
            ], 422);
        }



        $data = $request->all();

        if (!array_key_exists('airport_fbo_id', $data) || empty($data['airport_fbo_id'])) {
             $validation = Validator::make($request->all(),[
                 'airport_fbo_name' => 'required',
                 'airport_fbo_address' => 'required',
             ]);

             if($validation->fails()){
                return response()->json([
                    'errors' => $validation->errors(),
                ], 422);
             }

            $airport_fbo_id = DB::table('airport_fbo_details')->insertGetId([
                'name' => $request->airport_fbo_name,
                'notes' => $request->airport_fbo_notes,
                'address' => $request->airport_fbo_address,
                'airport_id' => $request->airport_id,
                'customer_id' => $customerId,
            ]);
            $data['airport_fbo_id'] = $airport_fbo_id;
         }

        // sandeep || add date and time to fbo detail table 
        
        if($request->delivery_date || $request->delivery_time){
            DB::table('fbo_details')
            ->updateOrInsert(
                ['customer_id' => $customerId],
                [
                    'delivery_time' => $request->delivery_time,
                    'delivery_date' => $request->delivery_date,
                    'customer_id' => $customerId,
                ]
            );
        }

         $airport_data = Db::table('delivery_location_airports')->where('id', $request->airport_id)->first();
         $country_states = Db::table('country_states')->where('id', $airport_data->state)->first();
         $customer = DB::table('customers')->where('id',$customerId)->first();

        // $data['address1'] = implode(PHP_EOL, array_filter($data['address1']));

        // sandeep || add data 
        $data['airport_name'] = $airport_data->name;
        $data['address1'] = $airport_data->address;
        $data['postcode'] = $airport_data->zipcode;
        $data['state'] =  $country_states->code;
        $data['country'] = $country_states->country_code;
        $data['customer_id'] = $customerId;
        $data['first_name'] = $customer->first_name;
        $data['last_name'] = $customer->last_name;
        $data['email'] = $customer->email;
        $data['phone'] = $customer->phone;
        $data['address_type'] = 'customer';

        $customerAddress = $this->getRepositoryInstance()->create($data);
        return response([
            'data'    => new CustomerAddressResource($customerAddress),
            'message' => 'Your address has been created successfully.',
        ], 201);
    }

    /**
     * Update address.
     *
     * @param  \Webkul\Customer\Http\Requests\CustomerAddressRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(CustomerAddressRequest $request, int $id)
    {
          // sandeep || Add validation
          $validation = Validator::make($request->all(),[
            'airport_id'  => 'required',
        ]);

        if($validation->fails()){
            return response([
               'error' => $validation->errors(),
            ], 422);
        }

        $data = $request->all();
        
        // sandeep update airport id and airport fbo
        $customerDetail = $this->resolveShopUser($request);
        $customerId = $customerDetail->id;
        
        // $airport = DB::table('delivery_location_airports')
        // ->where('name', $request->airport_name)
        // ->select('id')
        // ->first();
        
        if (!array_key_exists('airport_fbo_id', $data) || empty($data['airport_fbo_id'])) {
            $validation = Validator::make($request->all(),[
                'airport_fbo_name' => 'required',
                'airport_fbo_address' => 'required',
            ]);

            if($validation->fails()){
               return response()->json([
                   'errors' => $validation->errors(),
               ], 422);
            }

            $airport_fbo_id = DB::table('airport_fbo_details')->insertGetId([
                'name' => $request->airport_fbo_name,
                'notes' => $request->airport_fbo_notes,
                'address' => $request->airport_fbo_address,
                'airport_id' => $request->airport_id,
                'customer_id' => $customerId,
            ]);
            
            $data['airport_fbo_id'] = $airport_fbo_id;
         }
         
          // sandeep || add date and time to fbo detail table        
            if($request->delivery_date || $request->delivery_time){
                DB::table('fbo_details')
                ->updateOrInsert(
                    ['customer_id' => $customerId],
                    [
                        'delivery_time' => $request->delivery_time,
                        'delivery_date' => $request->delivery_date,
                        'customer_id' => $customerId,
                    ]
                );
            }

         $airport_data = Db::table('delivery_location_airports')->where('id', $request->airport_id)->first();
         $country_states = Db::table('country_states')->where('id', $airport_data->state)->first();
         $customer = DB::table('customers')->where('id',$customerId)->first();

        // $data['address1'] = implode(PHP_EOL, array_filter($data['address1']));

                // sandeep || add data 
                $data['airport_name'] = $airport_data->name;
                $data['address1'] = $airport_data->address;
                $data['postcode'] = $airport_data->zipcode;
                $data['state'] = $country_states->code;
                $data['country'] = $country_states->country_code;
                $data['customer_id'] = $customerId;
                $data['first_name'] = $customer->first_name;
                $data['last_name'] = $customer->last_name;
                $data['email'] = $customer->email;
                $data['phone'] = $customer->phone;
                $data['address_type'] = 'customer';

        $customerAddress = $this->getRepositoryInstance()->update($data, $id);

        return response([
            'data'    => new CustomerAddressResource($customerAddress),
            'message' => 'Your address has been updated successfully.',
        ], 200);
    }

    /**
     * Delete customer address.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, int $id)
    {
        $customerAddress = $this->resolveShopUser($request)->addresses()->find($id);

        $customerAddress->delete();

        return response([
            'message' => __('rest-api::app.customers.address-deleted'),
        ], 200);
    }

    /* customer airport function*/
    public function airport(){
        $airports= DB::table('delivery_location_airports')->get();
        if (count($airports)>0) {  
            return $airports;  
        }
        else{
            return response([
                'message' => 'Airport not found',
            ], 404);
        }
    }

    
    public  function specific_airport(Request $request,int $id){
    //    sandeep || add code
        $token = $request->bearerToken();
        $customerId = '';

        if ($token) {
            $accessToken = PersonalAccessToken::findToken($token);
            if ($accessToken && $accessToken->tokenable_type === 'Webkul\Customer\Models\Customer') {
                $user = $accessToken->tokenable; 
                $customerId = $user->id;
            }
        }
    
        
        $airport= DB::table('delivery_location_airports')->where('id',$id)->get();
        $airportFbo = DB::table('airport_fbo_details')
        ->where('airport_id', $id)
        ->where(function ($query) use ($customerId) {
            if ($customerId) {
                $query->where('customer_id', $customerId)
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNull('customer_id')
                                 ->whereNull('customer_token');
                    });
            } else {
                $query->whereNull('customer_id')
                      ->whereNull('customer_token');
            }
        })
        ->select('id','name','notes','address','zipcode','state','country','airport_id')
        ->get();

        if (count($airport)>0) { 
            return response()->json([
                'data' => [
                 'airport' => $airport,
                 'airport-fbo' => $airportFbo,
                ]
            ], 200);  
        } 
        else {       
            return response([
                'message' => 'Airport not found',
            ], 404);
        }
    }

 }
