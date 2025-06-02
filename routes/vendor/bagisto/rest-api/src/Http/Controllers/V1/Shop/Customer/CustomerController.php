<?php

namespace Webkul\RestApi\Http\Controllers\V1\Shop\Customer;

use Webkul\RestApi\Http\Controllers\V1\Shop\ShopController;
use ACME\paymentProfile\Models\CustomerInquery;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CustomerController extends ShopController
{
    // sandeep || save inquiry
    public function saveInquiry(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'fname' => 'required|string|max:25',
            'lname' => 'required|string|max:25',
            'email' => 'required|email|regex:/^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/',
            'mobile_number' => 'required|min:10|max:12',
            'message' => 'required',
            'uploadfile.*' => 'required|mimes:doc,docx,xls,xlsx,pdf|max:2048',
        ], [
            'uploadfile.*.mimes' => 'File must be a type: doc, docx, xls, xlsx, pdf.',
            'uploadfile.*.max' => 'File may not be greater than 2048 kilobytes.',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }


        $inquiry = new CustomerInquery();
        $inquiry->fname = $request->fname;
        $inquiry->lname = $request->lname;
        $inquiry->email = $request->email;
        $inquiry->mobile_number = $request->mobile_number;
        $inquiry->message = $request->message;
        $inquiry->save();

    
        $id = $inquiry->id;

        $uploadedFileUrls = [];
        if ($request->hasFile('uploadfile')) {
            foreach ($request->file('uploadfile') as $key => $file) {
           if ($file->isValid()) {
             $fileName = $id . '_' . ($key + 1) . '.' . $file->getClientOriginalExtension();
             $filePath = $file->storeAs('customerinquery', $fileName);
             
            //  $fileUrl = url('storage/' . $filePath);
             $fileUrl = url('storage/' . $filePath);
            // download file url
            //  $fileUrl = url('api/v1/download/' . $filePath);
             $uploadedFileUrls[] = [
                'file_name' => $fileName,
                'file_url' => $fileUrl,
            ];
    
           } else {
            return response()->json([ 
                'error', 'One or more files could not be uploaded.',
            ], 422);
           }
       }
   } else {
        return response()->json([ 
            'error' => 'No file found',
        ]);
   }

          return response()->json([
                'data' => $inquiry,
                'file' => $uploadedFileUrls,
                'message' => 'Inquiry created successfully',
            ], 200);


    }

// download api file
    public function downloadInquiry($file){

        $filePath = storage_path('app/public/customerinquery/' . $file);

        return response()->download($filePath);
    }
}
