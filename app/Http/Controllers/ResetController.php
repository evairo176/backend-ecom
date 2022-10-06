<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ResetController extends Controller
{
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'token' => 'required',
            'email' => 'required|email|min:5',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if ($validator->fails()) {
            return response()->json(
               [
                'message' => $validator->errors(),
               ],401
            );
        }else{

            $email = $request->email;
            $token = $request->token;
            $password = Hash::make($request->password);

            $emailCheck = DB::table('password_resets')->where('email',$email)
            ->first();
            $pinCheck = DB::table('password_resets')->where('token',$token)
            ->first();

            if(!$emailCheck){
                return response()->json([
                    'message' => 'Email not found'
                ],401);
            }
            if(!$pinCheck){
                return response()->json([
                    'message' => 'Pin code invalid'
                ],401);
            }
            
            DB::table('users')->where('email',$email)->update(['password' => $password]);
            DB::table('password_resets')->where('email',$email)->delete();

            return response()->json([
                'message' => 'Password change success'
            ],200);
        }
    }
}
