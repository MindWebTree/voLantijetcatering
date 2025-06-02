<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FboController extends CustomerController
{
    public function get(Request $request)
    {
        $data = $this->resolveShopUser($request);
        $fbo_data = DB::table('fbo_details')
            ->select('customer_id','packaging_section', 'tail_number', 'email_address', 'phone_number', 'full_name')
            ->where('customer_id', $data->id)
            ->first();
        if ($fbo_data) {
            return response(
                [
                    'data' => $fbo_data,
                ],
                422,
            );
        } else {
            return response(
                [
                    'data' => 'Fbo not found',
                ],
                422,
            );
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

        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string',
                'phone_number' => 'required',
                'email' => 'required|email',
                'tail_number' => 'required',
                'packaging_section' => 'required',
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

        $update_details = DB::table('fbo_details')
            ->where('customer_id', $data->id)
            ->update([
                'packaging_section' => $request->packaging_section,
                'tail_number' => $request->tail_number,
                'email_address' => $request->email,
                'phone_number' => $request->phone_number,
                'full_name' => $request->full_name,
            ]);
        return response([
            'message' => 'fbo updated successfull',
        ]);
    }
    public function add(Request $request)
    {
        $data = $this->resolveShopUser($request);

        $customMessages = [
            'required' => 'The :attribute field is required.',
            'string' => 'The :attribute must be a string.',
            'email' => 'The :attribute must be a valid email address.',
            'unique' => 'The :attribute has already been taken.',
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'full_name' => 'required|string',
                'phone_number' => 'required',
                'email' => 'required|email',
                'tail_number' => 'required',
                'packaging_section' => 'required',
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
        if (!$customer_existance) {
            $fbo_data = DB::table('fbo_details')->insert([
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
                ]);
            }
        } else {
            return response([
                'message' => 'fbo already exist',
            ]);
        }
    }
}