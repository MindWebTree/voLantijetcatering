<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Webkul\Shop\Http\Controllers\Controller; // Base Controller import
use Webkul\Product\Models\Product; // Product Model import (Adjust namespace if needed)
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class AgeVerificationController extends Controller
{
    /**
     * Checks if age verification is required for a specific product.
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

public function save_user_dob(Request $request){
   
        $request->validate([
        'dob' => 'required|date'
    ]);

    $dob = $request->dob;

        try {
            if (Auth::check()) {
                $user = Auth::user();
                $updated = DB::table('customers')
                    ->where('id', $user->id)
                    ->update([
                        'date_of_birth' => $dob,
                        'updated_at' => now()
                    ]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'DOB saved to database',
                    'is_logged_in' => true,
                    'dob' => $dob
                ]);
            } else {
                $guestToken = Session::token();
                DB::table('customers')->updateOrInsert(
                    ['token' => $guestToken], // Condition
                    [
                        'date_of_birth' => $dob,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
                
                return response()->json([
                    'success' => true,
                    'message' => 'DOB saved to session',
                    'is_logged_in' => false,
                    'dob' => $dob
                ]);
            }
        } catch (\Exception $e) {
            Log::error('DOB Save Error:', ['error' => $e->getMessage()]);
            
            return response()->json([
                'success' => false,
                'message' => 'Error saving DOB: ' . $e->getMessage()
            ], 500);
        }
}




}