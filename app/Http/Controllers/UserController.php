<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(request $request)
    {
        $this->validate($request, [
            'username'=>'required',
            'password'=>'required|min:6'
           
        ]);
        $username=$request->input('username');
        $password=$request->input('password');

        $hasPassword=Hash::make($password);

        $user=User::create([
            'username'=>$username,
            'password'=>$hasPassword
        ]);

        return response()->json([
            "message"=>"succes"
        ], 201);
    }

    //proses login 
    public function login(request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'password'=> 'required'
        ]);

        $username=$request->header('username');
        $password=$request->header('password');

        $user=User::where('username', $username)->first();

        if(!$user){
            return response()->json([
                "message"=>"login gagal"
            ], 401);
        }

        $isValidPassword = Hash::check($password, $user->password);

        if (!$isValidPassword) {
            return response()->json([
                'message'=>'loging gagal'
            ], 401);
        }

        $generateToken=bin2hex(random_bytes(40));
        $user->update([
            'token'=>$generateToken
        ]);

        return response()->json([
            'message'=>$user->token
        ]);
    }
}
