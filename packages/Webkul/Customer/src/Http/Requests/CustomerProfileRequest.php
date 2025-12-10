<?php

namespace Webkul\Customer\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Core\Contracts\Validations\AlphaNumericSpace;

class CustomerProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = auth()->guard('customer')->user()->id;

        return [
            'fullname'            => ['required', new AlphaNumericSpace],
//            'last_name'             => ['required', new AlphaNumericSpace],
            'gender'                => 'required|in:Other,Male,Female',
//            'date_of_birth'         => 'date|before:today',
'date_of_birth' => 'date|before_or_equal:today',  
          'email'                 => 'email|unique:customers,email,' . $id,
            'password'              => 'confirmed|min:6|required_with:oldpassword',
            'oldpassword'           => 'required_with:password',
            'password_confirmation' => 'required_with:password',
            'image.*'               => 'mimes:bmp,jpeg,jpg,png,webp',
            'phone' => 'required|regex:/^\(\d{3}\) \d{3}-\d{3,4}( \d{1,4})?$/',
        ];
    }
}
