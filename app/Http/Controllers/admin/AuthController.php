<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

use App\Admin;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function loginForm()
    {
        return view('admin.loginForm');
    }

    public function handleLogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|max:191',
            'password' => 'required|string',
        ]);


        if(Auth::guard('admin')->attempt(['email' => $data['email'], 'password' => $data['password']]))
        {
            return redirect('admin');
        }
        return back();
        
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/loginform');

    }

    public function redirectToProvider()
    {
        return Socialite::driver('github')->redirect();
    }

    public function handleProviderCallback()
    {
        $user = Socialite::driver('github')->user();

        $email = $user->email;

        $db_user=Admin::where('email' , '=' , $email)->first();

        if($db_user == null)
        {
            $register=Admin::create([
                'name' => $user->name,
                'email' => $user->email,
                'password' => Hash::make('1234'),
                'oauth_token' => $user->token,
            ]);

            Auth::login($register);
        }
        else
        {
            Auth::login($db_user);
        }

        return redirect(url('/admin'));
        
    }

} 
