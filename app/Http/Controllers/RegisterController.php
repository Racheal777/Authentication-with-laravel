<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(UserRequest $request)
    {
        $user = new User();
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->save();

        $token = $user->createToken($user->name);

        $object = $token->accessToken;

        
        
        //return $object;
        return response()->json([
            'token' => $object,
            'user' => new UserResource($user)
        ]);
    }

        //function for looging
    public function login(Request $request)
    {
        $login_credentials = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if (auth()->attempt($login_credentials)) {
            $user = auth()->user();
            $token = auth()->user()->createToken($user->name);

            return response()->json([
               'token' => $token->accessToken,
               'user_details' => new UserResource($user)
            ]);
        } else {
            return response()->json([
                "message" => "invalid credentials"
            ]);
        }
    }


    //function that checks for the logged in user
    public function check( User $user)
    { 
        $loggedInUser = auth('api')->user();
        
        // if ($loggedInUser->id == $user->id) {
        //     return $user;
        // } else {
        //     return response()->json([
        //         'error' => 'User doesnt match'
        //     ]);
        // }

        if(!$loggedInUser){
            return response()->json([
                'error' => 'User not logged in'
            ]);
        }else {
            return $user;
        }

        
        
    }


    //logout
    public function logout(User $user)
    {
        $user = auth('api')->user();

        $token = $user->token();
        if($user){
            $token->revoke();
            return "successfully logged out!";
        }  else {
            return response()->json([
                'error' => 'User not found'
            ]);
        }

        // $token->revoke();

        // return 'user';
        //return $token;
    }
}
