<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\RestApi\Http\Resources\V1\Shop\Customer\CustomerResource;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


use Illuminate\Support\Facades\api_Password;

class AuthController extends CustomerController
{

    use SendsPasswordResetEmails;

    /**
     * Customer respository instance.
     *
     * @var \Webkul\Customer\Repositories\CustomerRepository
     */
    protected $customerRepository;

    /**
     * Customer group repository instance.
     *
     * @var \Webkul\Customer\Repositories\CustomerGroupRepository
     */
    protected $customerGroupRepository;

    /**
     * Controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customerRepository
     * @param  \Webkul\Customer\Repositories\CustomerGroupRepository  $customerGroupRepository
     * @return void
     */
    public function __construct(

        CustomerRepository $customerRepository,
        CustomerGroupRepository $customerGroupRepository
    ) {
        parent::__construct();

        $this->customerRepository = $customerRepository;

        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * Register the customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        /* add validation message*/
        $customMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'min' => 'The :attribute must be at least :min characters.',
        ];

    //   sandeep || add mobile validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|min:10|max:14',
            'email' => 'required|email|unique:customers,email|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
            'password' => 'required|confirmed|min:6',
        ], $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors();

            if ($errors->has('email') && $errors->get('email')[0] === 'The email has already been taken.') {

                return response([
                    'message' => 'Email address is already Exist.',
                ], 208); // 409 Conflict status code for conflict in resource
            }
            return response([
                'message' => $validator->errors(),
            ], 422);
        }
         
        // sandeep || add mobile number
        $user = $this->customerRepository->create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone_number,
            'password' => bcrypt($request->password),
            'is_verified' => 1,
            'channel_id' => core()->getCurrentChannel()->id,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id,
        ]);

        return response([
            'message' => 'Your account has been created successfully.',
        ], 201);

    }

    /**
     * Login the customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {


        $customMessages = [
            'required' => 'The :attribute field is required.',
            'email' => 'The :attribute must be a valid email address.',
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
            'password' => 'required',
        ], $customMessages);

        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
            ], 422);
        }


        // $request->validate([
        //     'email'    => 'required|email',
        //     'password' => 'required',
        // ]);

        if (!EnsureFrontendRequestsAreStateful::fromFrontend($request)) {

            $validator = Validator::make($request->all(), [
                'device_name' => 'required',
            ]);

            // sandeep || add response
           if($validator->fails()){
            return response([
                'message' => $validator->errors(),
            ],422);
           }

            $customer = $this->customerRepository->where('email', $request->email)->first();

            if (!$customer || !Hash::check($request->password, $customer->password)) {

                // throw ValidationException::withMessages([
                //     'email' => ['The provided credentials are incorrect.'],
                // ])->status(400);
                        return response([
            'message' => 'Invalid Email or Password',
        ], 422);
            }

            /**
             * Preventing multiple token creation.
             */

            $customer->tokens()->delete();
            // if($request->bearerToken()){
            //     $guest_customer=DB::table('personal_access_tokens')->select('tokenable_id')->where('token',$request->bearerToken())->get();
            //     $guest_customer_id=$guest_customer[0]->tokenable_id;  
            //     dd($guest_customer_id);    
            //     $updateResult = DB::table('fbo_details')
            //     ->where('id', 1)
            //     ->update(['status' => 'approved', 'other_column' => 'new_value']);  
            //   }      

            // sandeep add code

            return response([
                'data' => new CustomerResource($customer),
                'message' => 'Logged in successfully.',
                // 'token'  =>  $tokenWithId,
                // sandeep comment code
                'token' => $customer->createToken($request->device_name, ['role:customer'])->plainTextToken,
            ], 200);
        }


        if (Auth::attempt($request->only(['email', 'password']))) {
            $request->session()->regenerate();
            return response([
                'data' => new CustomerResource($this->resolveShopUser($request)),
                'message' => 'Logged in successfully.',
            ], 200);
        }

        return response([
            'message' => 'Invalid Email or Password',
        ], 400);
    }

    /**
     * Get details for current logged in customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function get(Request $request)
    {
        $customer = $this->resolveShopUser($request);

        return response([
            'data' => new CustomerResource($customer),
        ]);
    }

    /**
     * Update the customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $customer = $this->resolveShopUser($request);


        $customMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
            'confirmed' => 'The :attribute confirmation does not match.',
            'min' => 'The :attribute must be at least :min characters.',
        ];

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'gender' => 'required',
            'date_of_birth' => 'nullable|date|before:today',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/|unique:customers,email,' . $customer->id,
            'password' => 'required|confirmed|min:6',
        ], $customMessages);


        if ($validator->fails()) {

            $errors = $validator->errors();

            if ($errors->has('email') && $errors->get('email')[0] === 'The email has already been taken.') {

                return response([
                    'message' => 'Email address is already Exist.',
                ], 208); // 409 Conflict status code for conflict in resource
            }

            return response([
                'message' => $validator->errors(),
            ], 422);
        }







        // $request->validate([
        //     'first_name'    => 'required',
        //     'last_name'     => 'required',
        //     'gender'        => 'required',
        //     'date_of_birth' => 'nullable|date|before:today',
        //     'email'         => 'email|unique:customers,email,' . $customer->id,
        //     'password'      => 'confirmed|min:6',
        // ]);

        $data = $request->all();


        if (!isset($data['password']) || !$data['password']) {
            unset($data['password']);
        } else {
            $data['password'] = bcrypt($data['password']);
        }

        $updatedCustomer = $this->customerRepository->update($data, $customer->id);

        return response([
            'data' => new CustomerResource($updatedCustomer),
            'message' => 'Your account has been updated successfully.',
        ]);
    }

    /**
     * Logout the customer.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {

        $customer = $this->resolveShopUser($request);

        !EnsureFrontendRequestsAreStateful::fromFrontend($request)
            ? $customer->tokens()->delete()
            : auth()->guard('customer')->logout();

        return response([
            'message' => 'Logged out successfully.',
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    // public function forgotPassword(Request $request)
    // {

    //     $customMessages = [
    //         'required' => 'The :attribute field is required.',
    //         'email' => 'The :attribute must be a valid email address.',
    //     ];

    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //     ], $customMessages);


    //     if ($validator->fails()) {
    //         return response([
    //             'message' => $validator->errors(),
    //         ], 400);
    //     }



    //     $request->validate([
    //         'email' => 'required|email',
    //     ]);
    //     $response = Password::broker('customers')->ApisendResetLink($request->only(['email']));
    //     return response(
    //         ['message' => __($response)],
    //         $response == Password::RESET_LINK_SENT ? 200 : 400
    //     );


    // }


    public function forgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email||regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
        ]);
        
        if($validator->fails()){
            return response([
                'error' => $validator->errors(),
            ], 422);
        }

        $response = Password::broker('customers')->sendResetLink($request->only(['email']));

        return response(
            ['message' => __($response)],
            $response == Password::RESET_LINK_SENT ? 200 : 400
        );
    }
    public function TempUser(Request $request)
    {
        $customMessages = [
            'required' => 'The :attribute field is required.',
        ];

        $validator = Validator::make($request->all(), [
            'device_name' => 'required',
        ], $customMessages);


        if ($validator->fails()) {
            return response([
                'message' => $validator->errors(),
            ], 422);
        }


        $token = Str::random(40);
        $user = $this->customerRepository->create([
            'first_name' => '',
            'last_name' => '',
            'password' => '',
            'token' => $token,
            'channel_id' => core()->getCurrentChannel()->id,
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id,
        ]);

        $token = $user->createToken($request->device_name, ['role:customer'])->plainTextToken;


        return response()->json(['token' => $token], 201);

    }


    // fbo start //


    public function get_fbo(Request $request)
    {
        // dd('fdfdbf');
        // dd($this->resolveShopUser($request));

        // sandeep || add service packaging and delievry date and time
        $data = $this->resolveShopUser($request);

        $fbo_data = DB::table('fbo_details')
            ->select('customer_id', 'packaging_section', 'tail_number', 'email_address', 'phone_number', 'full_name','service_packaging','delivery_date','delivery_time')
            ->where('customer_id', $data->id)
            ->first();
        if ($fbo_data) {
            return response(
                [
                    'data' => $fbo_data,
                ],
                200,
            );
        } else {
            return response()->json(['data' => 'Fbo not found'], 404);

        }
    }
    public function update_fbo(Request $request)
    {

        $data = $this->resolveShopUser($request);

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
        ];

        // sandeep || add service and delivery date,time validation
        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string',
                'phone_number' => 'required',
                'email' => 'required|email||regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
                'tail_number' => 'required',
                'packaging_section' => 'required',
                'service_packaging' => 'required',
                'delivery_date'  => 'required',
                'delivery_time'  => 'required',
            ],
            $customMessages,
        );

        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors(),
                ],
                422,
            ); // Use an appropriate HTTP status code, e.g., 422 Unprocessable Entity
        }
        // sandeep || add delivery date ,time and service packaging
        $update_details = DB::table('fbo_details')
            ->where('customer_id', $data->id)
            ->update([
                'delivery_date'  => $request->delivery_date,
                'delivery_time'  => $request->delivery_time,
                'service_packaging' => $request->service_packaging,
                'packaging_section' => $request->packaging_section,
                'tail_number' => $request->tail_number,
                'email_address' => $request->email,
                'phone_number' => $request->phone_number,
                'full_name' => $request->full_name,
            ]);
        return response([
            'message' => 'fbo updated successfull',
        ], 200);
    }
    public function add_fbo(Request $request)
    {
        $data = $this->resolveShopUser($request);

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
        ];

         // sandeep || add service and delivery date,time validation
        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string',
                'phone_number' => 'required',
                'email' => 'required|email|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
                'tail_number' => 'required',
                'packaging_section' => 'required',
                'service_packaging' => 'required',
                'delivery_date'  => 'required',
                'delivery_time'  => 'required',
            ],
            $customMessages,
        );

        if ($validator->fails()) {
            return response(
                [
                    'message' => $validator->errors(),
                ],
                422,
            ); // Use an appropriate HTTP status code, e.g., 422 Unprocessable Entity
        }
        $customer_existance = DB::table('fbo_details')
            ->where('customer_id', $data->id)
            ->exists();
            // sandeep || add delivery date ,time and service packaging
        if (!$customer_existance) {
            $fbo_data = DB::table('fbo_details')->insert([
                'delivery_date'  => $request->delivery_date,
                'delivery_time'  => $request->delivery_time,
                'service_packaging' => $request->service_packaging,
                'packaging_section' => $request->packaging_section,
                'tail_number' => $request->tail_number,
                'email_address' => $request->email,
                'phone_number' => $request->phone_number,
                'full_name' => $request->full_name,
                'customer_id' => $data->id,
            ]);
            if ($fbo_data) {
                return response([
                    'message' => 'fbo add successfull',
                ], 201);
            }
        } else {
            // sandeep || add delivery date ,time and service packaging
            $update_details = DB::table('fbo_details')
                ->where('customer_id', $data->id)
                ->update([
                    'delivery_date'  => $request->delivery_date,
                    'delivery_time'  => $request->delivery_time,
                    'service_packaging' => $request->service_packaging,
                    'packaging_section' => $request->packaging_section,
                    'tail_number' => $request->tail_number,
                    'email_address' => $request->email,
                    'phone_number' => $request->phone_number,
                    'full_name' => $request->full_name,
                ]);
              
            return response([
                'message' => 'fbo updated successfull',
            ], 200);
        }
    }

    //fbo end //

    public function set_password()
    {
        //dd($customer);
        // $this->validate(request(), [
        //     'token'    => 'required',
        //     'email'    => 'required|email',
        //     'password' => 'required|confirmed|min:6',
        // ]);

        $response = $this->broker()->reset(
            request(['email', 'password', 'password_confirmation', 'token']),
            function ($customer, $password) {
                dd($password, $customer);
                $this->resetPassword($customer, $password);
            }
        );
        dd($response);
    }
    /**
     * Get the broker to be used during password reset.
     *
     * @return \Illuminate\Contracts\Auth\PasswordBroker
     */
    public function broker()
    {
        return Password::broker('customers');
    }
}
