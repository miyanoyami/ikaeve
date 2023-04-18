<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
	{
            /*kokkara*/
            //セッションからアクセストークン取得
            // $accessToken = session()->get('accessToken');
            //
            // //インスタンス生成
            // $twitter = new TwitterOAuth(
            //     //API Key
            //     $this->consumerKey,
            //     //API Secret
            //     $this->consumerSecret,
            //     //アクセストークン
            //     $accessToken['oauth_token'],
            //     $accessToken['oauth_token_secret']
            // );
            //
            // //ユーザ情報を取得
            // //'account/verify_credentials'はユーザ情報を取得するためのAPIのリソース
            // // get_object_vars()でオブジェクトの中身をjsonで返す
            // $userInfo = get_object_vars($twitter->get('account/verify_credentials'));
            // if ($userInfo) {
            //   print_r($userInfo);
            // }
        return view('home');
        // return redirect()->route('event.index');
    }
}
