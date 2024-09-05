<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{

    public function createUser(Request $request){

        try {

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json([
                'status' => '1',
                'message' => 'User has been created successfully.',


            ]);

        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function login(Request $request){
        $data = $request->validate([
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if(!Auth::guard('web')->attempt($data)) {
            return response()->json([
                'message' => 'Unauthorized',
                'status' => 0
            ]);
        }
        $user = $request->user();
        $tokenResult = $user->createToken($user->id);
        $token = $tokenResult->token;
        return response()->json([
            'status' => 1,
            'tokenDetails' => $token,
            'token' => $tokenResult->accessToken,
            'id' => Auth::id(),
            'user' => $user
        ])->header('Authorization', $tokenResult->accessToken);


    }
}
