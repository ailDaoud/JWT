<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Exception;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "email" => 'required|email|exists:users',
                "password" => "string|required|min:6"
            ]);
            if ($validation->fails()) {
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => $validation->errors(),
                ], 200);
            }
            $token = Auth::attempt(['email' => $request->email, 'password' => $request->password]);
            if (!$token) {
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => 'register first',
                ], 200);
            } else {
                $user = User::where('email', $request->email)->first();
                return response()->json([
                    'sucsess' => 1,
                    'user' => $user,
                    'token' => $token
                ], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'sucsess' => 0,
                'result' => null,
                'message' => $e,
            ], 200);
        }
    }

    public function register(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "email" => 'required|string|email|unique:users',
                'name' => 'required|string',
                "password" => "string|required|min:6"
            ]);

            if ($validation->fails()) {
                return response()->json([
                    'sucsess' => 0,
                    'result' => null,
                    'message' => $validation->errors(),
                ], 200);
            }
            $user = User::create([
                'email' => $request->email,
                'name' => $request->name,
                'password' => Hash::make($request->password),
                //  'password' => bcrypt($request->password)
            ]);
            return response()->json([
                'sucsess' => 1,
                'user' => $user,
                'message' => 'user created sucsessfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'sucsess' => 0,
                'result' => null,
                'message' => $e,
            ], 200);
        }
    }
}
