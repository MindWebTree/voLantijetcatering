<?php

namespace ACME\CateringPackage\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
// use Illuminate\Foundation\Bus\DispatchesJobs;
// use Illuminate\Foundation\Validation\ValidatesRequests;
use Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Webkul\Core\Repositories\SubscribersListRepository;
use Webkul\Customer\Http\Requests\CustomerRegistrationRequest;
use Webkul\Customer\Mail\RegistrationEmail;
use Webkul\Customer\Mail\VerificationEmail;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Shop\Mail\SubscriptionEmail;
use Auth;
use Illuminate\Support\Facades\Session;
use DateTime;
use Illuminate\Support\Facades\Log;

class SignUpController extends Controller
{
    // use DispatchesJobs, ValidatesRequests;

    /**
     * Contains route related configuration
     *
     * @var array
     */
    protected $_config;

    /**
     * Create a new controller instance.
     *
     * @param  \Webkul\Customer\Repositories\CustomerRepository  $customer
     * @param  \Webkul\Customer\Repositories\CustomerGroupRepository  $customerGroupRepository
     * @param  \Webkul\Core\Repositories\SubscribersListRepository  $subscriptionRepository
     * @return void
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected CustomerGroupRepository $customerGroupRepository,
        protected SubscribersListRepository $subscriptionRepository
    ) {
        $this->_config = request('_config');
    }

    /**
     * Opens up the user's sign up form.
     *
     * @return \Illuminate\View\View
     */
    public function show()
    {
        print_r($this->_config['view']);
        return view($this->_config['view']);
    }

    /**
     * Method to store user's sign up form data to DB.
     *
     * @param  \Webkul\Customer\Http\Requests\CustomerRegistrationRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function create(CustomerRegistrationRequest $request)
    {
        $request->validated();

        $data = array_merge(request()->input(), [
            'password' => bcrypt(request()->input('password')),
            'api_token' => Str::random(80),
            'is_verified' => !core()->getConfigData('customer.settings.email.verification'),
            'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id,
            'token' => md5(uniqid(rand(), true)),
            'subscribed_to_news_letter' => isset(request()->input()['is_subscribed']),
        ]);

        Event::dispatch('customer.registration.before');
        if (!session()->has('customer_id')) {
            
            log::info('customer create succesfully',['sessuin customer id'=>session()->get('customer_id')]);
            $customer = $this->customerRepository->create($data);
            log::info('customer data',['customer data'=>$customer]);

        } else {
            log::info('create customer 23234',[session('customer_id')]);
            log::info(session('customer_id'));
            $customer = DB::table('customers')
                ->where('id', session('customer_id'))
                ->update([
                    'email' => $request->email,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'phone' => $request->phone,
                    'password' => bcrypt($request->password),
                    'is_verified' => !core()->getConfigData('customer.settings.email.verification'),
                    'token' => md5(uniqid(rand(), true)),
                    'subscribed_to_news_letter' => isset(request()->input()['is_subscribed']),
                    'customer_group_id' => $this->customerGroupRepository->findOneWhere(['code' => 'general'])->id,
                    'api_token' => Str::random(80),
                ]);
            Session::forget('customer_id');
            log::info('customer_detail',['customer_detail'=>$customer]);
        }


        Event::dispatch('customer.registration.after', $customer);

        if (!$customer) {
            session()->flash('error', trans('shop::app.customer.signup-form.failed'));

            return redirect()->back();
        }

        if (isset($data['is_subscribed'])) {
            $subscription = $this->subscriptionRepository->findOneWhere(['email' => $data['email']]);

            if ($subscription) {
                $this->subscriptionRepository->update([
                    'customer_id' => $customer->id,
                ], $subscription->id);
            } else {
                $this->subscriptionRepository->create([
                    'email' => $data['email'],
                    'customer_id' => $customer->id,
                    'channel_id' => core()->getCurrentChannel()->id,
                    'is_subscribed' => 1,
                    'token' => $token = uniqid(),
                ]);

                try {
                    Mail::queue(new SubscriptionEmail([
                        'email' => $data['email'],
                        'token' => $token,
                    ]));
                } catch (\Exception $e) {
                }
            }
        }

        if (core()->getConfigData('customer.settings.email.verification')) {
            try {
                if (core()->getConfigData('emails.general.notifications.emails.general.notifications.verification')) {
                    Mail::queue(new VerificationEmail(['email' => $data['email'], 'token' => $data['token']]));
                }

                session()->flash('success', trans('shop::app.customer.signup-form.success-verify'));

            } catch (\Exception $e) {
                report($e);

                session()->flash('info', trans('shop::app.customer.signup-form.success-verify-email-unsent'));
            }
        } else {
            try {
                if (core()->getConfigData('emails.general.notifications.emails.general.notifications.registration')) {
                    Mail::queue(new RegistrationEmail(request()->all(), 'customer'));
                }

                if (core()->getConfigData('emails.general.notifications.emails.general.notifications.customer-registration-confirmation-mail-to-admin')) {
                    Mail::queue(new RegistrationEmail(request()->all(), 'admin'));
                }

                session()->flash('success', trans('shop::app.customer.signup-form.success-verify'));
            } catch (\Exception $e) {
                report($e);

                session()->flash('info', trans('shop::app.customer.signup-form.success-verify-email-unsent'));
            }
            session()->flash('success', trans('shop::app.customer.signup-form.success'));
        }
            // return redirect()->route($this->_config['redirect']);
            auth()->guard('customer')->attempt($request->only
                (['email', 'password']));

            log::info($customer);

            Event::dispatch('customer.after.login', $request->get('email'));
            // sandeep add code for redirect to previous url
            $previous_url = $request->previous_url;
            session()->forget('previous_url');
            if(!empty($previous_url)){
                $parsedPath = parse_url($previous_url, PHP_URL_PATH);
                $redirectPaths = ['checkout/cart', 'checkout/success', 'items-count', 'customer/account/orders/view'];

                $shouldRedirect = collect($redirectPaths)->contains(function ($path) use ($parsedPath) {
                    return str_contains($parsedPath, $path);
                });

                $isStaticFile = preg_match('/\.(js|css|map|jpg|png|gif)$/', $parsedPath);
    
                if ($shouldRedirect || $isStaticFile) {
                    return redirect()->route('shop.customer.session.index');
                } else {
                    return redirect($previous_url);
                }
            }else{
                return redirect()->route('shop.customer.session.index');
            }
          
    }

    /**
     * Method to verify account.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    public function verifyAccount($token)
    {
        $customer = $this->customerRepository->findOneByField('token', $token);

        if ($customer) {
            $this->customerRepository->update(['is_verified' => 1, 'token' => 'NULL'], $customer->id);

            $this->customerRepository->syncNewRegisteredCustomerInformation($customer);

            session()->flash('success', trans('shop::app.customer.signup-form.verified'));

        } else {
            session()->flash('warning', trans('shop::app.customer.signup-form.verify-failed'));
        }
        return redirect()->route('shop.customer.session.index');
    }

    /**
     * Resend verification email.
     *
     * @param  string  $email
     * @return \Illuminate\Http\Response
     */
    public function resendVerificationEmail($email)
    {
        $verificationData = [
            'email' => $email,
            'token' => md5(uniqid(rand(), true)),
        ];

        $customer = $this->customerRepository->findOneByField('email', $email);

        $this->customerRepository->update(['token' => $verificationData['token']], $customer->id);

        try {
            Mail::queue(new VerificationEmail($verificationData));

            if (Cookie::has('enable-resend')) {
                \Cookie::queue(\Cookie::forget('enable-resend'));
            }

            if (Cookie::has('email-for-resend')) {
                \Cookie::queue(\Cookie::forget('email-for-resend'));
            }
        } catch (\Exception $e) {
            report($e);

            session()->flash('error', trans('shop::app.customer.signup-form.verification-not-sent'));

            return redirect()->back();
        }

        session()->flash('success', trans('shop::app.customer.signup-form.verification-sent'));

        return redirect()->back();
    }


    public function fbo_details()
    {
        return view('cateringpackage::shop.customer.fbo');
    }



    public function add_fbo_details(Request $request)
    {

        $validate = $request->validate([
            'fullname' => 'required|max:30'
        ]);

        $customer_token = $request->_token;
        $dateString = $request->delivery_date;
        
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


        if (Auth::check() === true) {

            $latestRecord = DB::table('fbo_details')
                ->where('customer_id', auth()->user()->id)
                ->orderByDesc('id')
                ->first();

            if ($latestRecord) {
                // Update the latest record with the provided data
                DB::table('fbo_details')
                    ->where('id', $latestRecord->id)
                    ->update([
                        'full_name' => $validate['fullname'],
                        'phone_number' => $request->phonenumber,
                        'email_address' => $request->email,
                        'tail_number' => $request->tailnumber,
                        'packaging_section' => $request->packagingsection,
                        'service_packaging' => $request->servicepackaging,
                        'delivery_time' => $request->delivery_time,
                        'delivery_date' => $formattedDate,
                    ]);
            } else {
                // Insert a new record if no record exists
                DB::table('fbo_details')->insert([
                    'customer_id' => auth()->user()->id,
                    'full_name' => $validate['fullname'],
                    'phone_number' => $request->phonenumber,
                    'email_address' => $request->email,
                    'tail_number' => $request->tailnumber,
                    'packaging_section' => $request->packagingsection,
                    'service_packaging' => $request->servicepackaging,
                    'delivery_time' => $request->delivery_time,
                    'delivery_date' => $formattedDate,
                ]);
            }

            session(['token' => $customer_token]);
            return redirect()->route('shop.checkout.onepage.show_fbo_detail');



        } else {
            $token = DB::table('fbo_details')->pluck('customer_token')->toArray();

            // DB::table('fbo_details')
            //     ->insert([
            //         'full_name' => $request->fullname,
            //         'phone_number' => $request->phonenumber,
            //         'email_address' => $request->email,
            //         'tail_number' => $request->tailnumber,
            //         'packaging_section' => $request->packagingsection,
            //         'delivery_time' => $request->delivery_time,
            //         'delivery_date' => $formattedDate,
            //         'customer_token' => $customer_token,
            //     ]);




            $latestRecord = DB::table('fbo_details')
                ->where('customer_token', $customer_token)
                ->orderByDesc('id')
                ->first();

            if ($latestRecord) {
                // Update the latest record with the provided data
                DB::table('fbo_details')
                    ->where('id', $latestRecord->id)
                    ->update([
                        'full_name' => $request->fullname,
                        'phone_number' => $request->phonenumber,
                        'email_address' => $request->email,
                        'tail_number' => $request->tailnumber,
                        'packaging_section' => $request->packagingsection,
                        'service_packaging' => $request->servicepackaging,
                        'delivery_time' => $request->delivery_time,
                        'delivery_date' => $formattedDate,
                    ]);
            } else {
                // Insert a new record if no record exists
                DB::table('fbo_details')->insert([
                    'customer_token' => $customer_token,
                    'full_name' => $request->fullname,
                    'phone_number' => $request->phonenumber,
                    'email_address' => $request->email,
                    'tail_number' => $request->tailnumber,
                    'packaging_section' => $request->packagingsection,
                    'service_packaging' => $request->servicepackaging,
                    'delivery_time' => $request->delivery_time,
                    'delivery_date' => $formattedDate,
                ]);
            }
            session(['token' => $customer_token]);
            return redirect()->route('shop.checkout.onepage.show_fbo_detail');

            // echo 'new guest user';
            // session(['token' => $customer_token]);
            // return redirect()->route('shop.checkout.onepage.show_fbo_detail');



        }

    }

    public function add_profile_fbo(Request $request)
    {
        DB::table('fbo_details')
            ->insert([
                'full_name' => $request->fullname,
                'phone_number' => $request->phonenumber,
                'email_address' => $request->email,
                'tail_number' => $request->tailnumber,
                'packaging_section' => $request->packagingsection,
                'service_packaging' => $request->servicepackaging,
                'customer_id' => auth()->user()->id,
            ]);
        return redirect()->route('shop.customer.addresses.index');
    }

    public function update_fbo_detail(Request $request)
    {


        $dateString = $request->delivery_date;
        // dd($dateString);
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

        // dd($request);
        $customerId = Auth::guard('customer')->id();
        $customer_token = $request->_token;

        // sandeep add check
        if($customerId){
            DB::table('fbo_details')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->update([
                'full_name' => $request->fullname,
                'phone_number' => $request->phonenumber,
                'email_address' => $request->email,
                'tail_number' => $request->tailnumber,
                'packaging_section' => $request->packagingsection,
                'service_packaging' => $request->servicepackaging,
                'delivery_date' => $formattedDate,
                'delivery_time' => $request->delivery_time,
            ]);
        }else{
        DB::table('fbo_details')
            ->where('customer_token', $customer_token)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->update([
                'full_name' => $request->fullname,
                'phone_number' => $request->phonenumber,
                'email_address' => $request->email,
                'tail_number' => $request->tailnumber,
                'packaging_section' => $request->packagingsection,
                'service_packaging' => $request->servicepackaging,
                'delivery_date' => $formattedDate,
                'delivery_time' => $request->delivery_time,
            ]);
        }
        return redirect()->route('shop.checkout.onepage.show_fbo_detail');
    }
    public function update_fbo_profile(Request $request)
    {
        $customerId = Auth::guard('customer')->id();
        $customer_token = $request->_token;

        // sandeep add check 
        if($customerId){
            DB::table('fbo_details')
            ->where('customer_id', $customerId)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->update([
                'full_name' => $request->fullname,
                'phone_number' => $request->phonenumber,
                'email_address' => $request->email,
                'tail_number' => $request->tailnumber,
                'packaging_section' => $request->packagingsection,
                'service_packaging' => $request->servicepackaging,
            ]);
        }else{
            DB::table('fbo_details')
            ->where('customer_token', $customer_token)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->update([
                'full_name' => $request->fullname,
                'phone_number' => $request->phonenumber,
                'email_address' => $request->email,
                'tail_number' => $request->tailnumber,
                'packaging_section' => $request->packagingsection,
                'service_packaging' => $request->servicepackaging,
            ]);
        }
       
        return redirect()->route('shop.customer.addresses.index');
    }
    public function category()
    {
        return view('shop::products.parentcat');
    }
}
