<?php
namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;

class MemberLoginController extends Controller
{

    public function twitter()
    {
        $twitter = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret')
        );
        # 認証用のrequest_tokenを取得
        # このとき認証後、遷移する画面のURLを渡す
        $token = $twitter->oauth('oauth/request_token', array(
            'oauth_callback' => config('twitter.callback_url')
        ));

        # 認証画面で認証を行うためSessionに入れる
        session(array(
            'oauth_token' => $token['oauth_token'],
            'oauth_token_secret' => $token['oauth_token_secret'],
        ));

        # 認証画面へ移動させる
        ## 毎回認証をさせたい場合： 'oauth/authorize'
        ## 再認証が不要な場合： 'oauth/authenticate'
        $url = $twitter->url('oauth/authenticate', array(
            'oauth_token' => $token['oauth_token']
        ));

        return redirect($url);
    }

    public function callBack(Request $request){
        //GETパラメータから認証トークン取得
        $oauth_token = $request->oauth_token;
        //GETパラメータから認証キー取得
        $oauth_verifier = $request->oauth_verifier;

        //インスタンス生成
        $twitter = new TwitterOAuth(
            //API Key
            config('twitter.consumer_key'),
            //API Secret
            config('twitter.consumer_secret'),
            //認証トークン
            $oauth_token,
            //認証キー
            $oauth_verifier
        );

        //アクセストークン取得
        //'oauth/access_token'はアクセストークンを取得するためのAPIのリソース
        $accessToken = $twitter->oauth('oauth/access_token', array('oauth_token' => $oauth_token, 'oauth_verifier' => $oauth_verifier));

        //セッションにアクセストークンを登録
        session()->put('accessToken', $accessToken);

        //indexというビューにユーザ情報が入った$userInfoを受け渡す
        // return view('index', ['userInfo' => $userInfo]);

        //indexページにリダイレクト
        return redirect('home');
    }
}
