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
        try{
            $validation=Validator::make($request->all(),[
                "email" => 'required|email|exists:users',
                "password" => "string|required|min:6"
            ]);
            if($validation->fails()){
                return response()->json([
                  'sucsess'=>0,
                  'result'=>null,
                  'message'=>$validation->errors(),
                ],200);
            }
            $token=Auth::attempt(['email'=>$request->email,'password'=>$request->password]);
           /* if(!Auth::attempt($request->only(['email','password']))){
                return response()->json([
                    'sucsess'=>0,
                    'result'=>null,
                    'message'=>'register first',
                  ],200);

            }*/
            if(!$token){
                return response()->json([
                    'sucsess'=>0,
                    'result'=>null,
                    'message'=>'register first',
                  ],200);

            }
            $user=User::where('email',$request->email)->first();
            return response()->json([
                'sucsess'=>1,
                'result'=>$user,
                'message'=>'register first',
                'token'=>$token
              ],200);


        }
        catch(Exception $e){
            return response()->json([
                'sucsess'=>0,
                'result'=>null,
                'message'=>$e,
              ],200);
        }

    }

    public function register(Request $request)
    {
        try {
            $validation = Validator::make($request->all(), [
                "email" => 'required|email|unique:users,email',
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
              //  'password' => Hash::make($request->password),
                'password' => bcrypt($request->password)
            ]);
           /* $user->email = $request->email;
            $user->name = $request->name;
            $user->password = Hash::make($request->password);
            $user->save();*/
            return response()->json([
                'sucsess' => 1,
                'result' => $user,
                'message' => 'user created sucsessfully',
                'token' => $user->createToken("API-TOKEN")->plainTextToken
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
