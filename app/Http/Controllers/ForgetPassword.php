<?php

namespace App\Http\Controllers;

use App\Mail\ForgetPasswordMail;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ForgetPassword extends Controller
{
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'email' => 'required|email',
        ]);
        
        if ($validator->fails()) {
            return response()->json(
               [
                'error' => $validator->errors(),

               ],401
            );
        }else{
            $email = $request->email;
            if(User::where('email',$email)->doesntExist()){
                return response()->json([
                    'message' => 'Email Invalid'
                ]);
            }

            // generate random token 
            $token = rand(10,100000);

            try{
                $data = [
                    'email' => $email,
                    'token' => $token
                ];
                DB::table('password_resets')->insert($data);

                // kirim email ke user 
                Mail::to($email)->send(new ForgetPasswordMail($token));

                return response()->json([
                    'message' => 'Reset password send on your email'
                ], 200);
            }catch (Exception $exception) {
                return response()->json([
                    'message' => $exception->getMessage()
                ], 400);
            }
        }
    }
}

