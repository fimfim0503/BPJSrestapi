<?php

namespace App\Providers;

use App\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {   
        // $this->app['auth']->viaRequest('api', function ($request) {
        //     if ($request->input('api_token')) {
        //         return User::where('api_token', $request->input('api_token'))->first();
        //     }
        // });

        $this->app['auth']->viaRequest('api', function ($request) {
            $username= $request->header('x-username');
            $token= $request->header('x-token');
           
            if ($request->header('x-token') )  {
                return User::where('x-token', $token)
                ->where('x-username', $username)
                ->first();
            }

            // if ($request->header('token')) {
            //     return User::where('token', $token)->first();
            // }
        });


    }
}
