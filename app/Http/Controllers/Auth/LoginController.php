<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\Member;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToTwitterProvider()
    {
       return Socialite::driver('twitter')->redirect();
    }

    public function handleTwitterProviderCallback(){

       try {
           $user = Socialite::with("twitter")->user();
       }
       catch (\Exception $e) {
           return redirect('/login')->with('oauth_error', 'ログインに失敗しました');
           // エラーならログイン画面へ転送
       }
       print_r($user);
       exit;

       $myinfo = Member::firstOrCreate(['twitter_id' => $user->token ],
                 ['name' => $user->nickname,'twitter_id_str' => $user->nickname]);
                 Auth::login($myinfo);
                 return redirect()->to('/'); // homeへ転送

    }
}