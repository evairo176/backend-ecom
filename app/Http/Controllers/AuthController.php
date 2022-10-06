<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user =  Auth::user();
                $token = $user->createToken('app')->accessToken;

                return response()->json([
                    'message' => 'Login Success',
                    'token' => $token,
                    'user' => $user
                ], 200);
            }
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
        }

        return response()->json([
            'message' => 'Invalid email or password',
        ], 401);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),
        [
            'name' => 'required',
            'email' => 'required|email|unique:users|min:5',
            'password' => 'required|min:6|confirmed'
        ]);
        
        if ($validator->fails()) {
            return response()->json(
               [
                'message' => $validator->errors(),

               ],401
            );
        }else{
        try{
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ];
            $user = User::create($data);
            $token = $user->createToken('app')->accessToken;

            return response()->json([
                'message' => 'Register Success',
                'token' => $token,
                'user' => $user
            ],200);
        }catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage()
            ], 400);
            }
         }
    }
}
