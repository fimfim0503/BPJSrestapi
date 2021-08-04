<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function register(request $request)
    {
        // $this->validate($request, [
        //     'x-username'=>'required',
        //     'x-password'=>'required|min:6'
           
        // ]);
        // $username=$request->input('x-username');
        // $password=$request->input('x-password');
        $username1="rsudrslametgarut";
        $pass="12345678";
        $username=$username1;
        $password=$pass;

        $hasPassword=Hash::make($password);

        $user=User::create([
            'x-username'=>$username,
            'x-password'=>$hasPassword
        ]);

        return response()->json([
            "message"=>"succes"
        ], 201);
    }

    //proses login 
    public function login(request $request)
    {
        // $this->validate($request, [
        //     'username' => 'required',
        //     'password'=> 'required'
        // ]);

        $username=$request->header('x-username');
        $password=$request->header('x-password');

        $user=User::where('x-username', $username)->first();

        if(!$user){
            return response()->json([
                "response"=>([
                    
                ]), "metadata"=>([
                    "message"=>"Nama User Salah",
                    "code"=>401
                ])
            ], 401);
        }

        $isValidPassword = Hash::check($password, $user->password);

        if (!$isValidPassword) {
            return response()->json([
                "response"=>([
                    
                ]), "metadata"=>([
                    "message"=>"Pasword Salah",
                    "code"=>401
                ])
            ], 401);
        }

        $generateToken=bin2hex(random_bytes(40));
        $user->update([
            'x-token'=>$generateToken
        ]);

        return response()->json([
            "response"=>([
                "token"=>$generateToken
            ]), "metadata"=>([
                "message"=>"ok",
                "code"=>200
            ])
        ], 200);
    }
}
