<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class ProviderController extends Controller
{
    //login process for google only
    public function redirect(){
        return Socialite::driver('google')->redirect();
    }
    //callback socialite
    public function callback(){
        $socialUser = Socialite::driver('google')->user();

        $user = User::updateOrCreate([
            'provider' =>'google',
            'provider_id' =>$socialUser->id
        ],
            [
            'name' => $socialUser->name,
            'nickname' =>$socialUser->nickname,
            'email' =>$socialUser->email,
            'provider_token'=>$socialUser->token,
            'role' => 'user'
        ]);
        Auth::login($user);
        if(Auth::user()->role=='admin'){
            return to_route('adminDashboard');
        }
        if(Auth::user()->role=='user'){
            return to_route('userDashboard');
        }
    }
}
