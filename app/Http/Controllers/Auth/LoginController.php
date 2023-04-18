<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Service\FlashMessageService;
use App\Models\User;

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

    public function handleTwitterProviderCallback(Request $request){

         try {
             $user = Socialite::with("twitter")->user();
         }
         catch (\Exception $e) {
              FlashMessageService::error('ログインに失敗しました');
              return redirect('/');
             // エラーならログイン画面へ転送
         }
        $myinfo = User::where('twitter_id', $user->id)->where('role', config('user.role.member'))->first();
        if (!$myinfo) {
            $myinfo = User::where('twitter_nickname', $user->nickname)->where('role', config('user.role.member'))->first();
        }
        if (!$myinfo) {
               $myinfo = User::firstOrCreate(['twitter_id' => $user->id ],
                         [
                           'name' => $user->name,
                           'twitter_nickname' => $user->nickname,
                           'twitter_auth' => 1
                          ]
                       );
        } else {
          // if ($myinfo->twitter_auth == 0) {
              $myinfo->twitter_id = $user->id;
              $myinfo->twitter_nickname = $user->nickname;
              $myinfo->twitter_auth = 1;
              $myinfo->update();
          // }
        }
        Auth::login($myinfo);
        return redirect()->to('/event/index'); // homeへ転送
    }
}
