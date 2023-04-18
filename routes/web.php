<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('login/twitter', 'Auth\LoginController@redirectToTwitterProvider')->name('login/twitter');
Route::get('login/twitter/callback', 'Auth\LoginController@handleTwitterProviderCallback')->name('login/twitter/callback');


// 確認ようページ
Route::prefix('test')->name('test.')
->group(function() {
    Route::get('type1', 'TestController@type1')->name('type1');
    Route::get('type2/{block?}/{sheet?}', 'TestController@type2')->name('type2');
});

Route::group(['middleware' => ['auth']], function () {

    // 大会
    Route::prefix('event')->name('event.')
    ->group(function() {
        Route::get('index', 'EventController@index')->name('index');
        Route::get('regist', 'EventController@regist')->name('regist');
        Route::post('regist', 'EventController@registStore');
        Route::get('detail/{id}', 'EventController@detail')->name('detail')->where('id', '[0-9]+');
        Route::get('edit/{id}', 'EventController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'EventController@editStore')->where('id', '[0-9]+');
    });

    // staff
    Route::prefix('staff')->name('staff.')
    ->group(function() {
        Route::get('index', 'StaffController@index')->name('index');
        Route::post('index', 'StaffController@index');
        Route::get('regist', 'StaffController@regist')->name('regist');
        Route::post('regist', 'StaffController@registStore');
        Route::get('edit/{id}', 'StaffController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'StaffController@editStore')->where('id', '[0-9]+');
    });

    // 自分のパスワード変更
    Route::prefix('user')->name('user.')
    ->group(function() {
        Route::get('password', 'UserController@password')->name('password');
        Route::post('password', 'UserController@passwordStore');
    });

    // 入力項目
    Route::prefix('question')->name('question.')
    ->group(function() {
        Route::get('edit/{id}', 'QuestionController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'QuestionController@editStore')->where('id', '[0-9]+');
    });

    // メンバー
    Route::prefix('member')->name('member.')
    ->group(function() {
        Route::get('index', 'MemberController@index')->name('index');
        Route::post('index', 'MemberController@indexStore');
        Route::get('detail/{id}', 'MemberController@detail')->name('detail')->where('id', '[0-9]+');
        Route::get('edit/{id}', 'MemberController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'MemberController@editStore')->where('id', '[0-9]+');
        Route::get('edit/{id}', 'MemberController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'MemberController@editStore')->where('id', '[0-9]+');
    });

    // チーム
    Route::prefix('team')->name('team.')
    ->group(function() {
        Route::get('index', 'TeamController@index')->name('index');
        Route::post('index', 'TeamController@indexStore');
        Route::get('detail/{id}', 'TeamController@detail')->name('detail')->where('id', '[0-9]+');
        Route::get('regist', 'TeamController@regist')->name('regist');
        Route::post('regist', 'TeamController@registStore');
        Route::get('edit/{id}', 'TeamController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'TeamController@editStore')->where('id', '[0-9]+');
        Route::get('update/{id}/{column}/{value}', 'TeamController@update')->name('update')->where('id', '[0-9]+');
        Route::get('delete/{id}', 'TeamController@delete')->name('delete')->where('id', '[0-9]+');
        Route::get('import', 'TeamController@import')->name('import');
        Route::post('import', 'TeamController@importStore');
    });

    // 募集
    Route::prefix('wanted')->name('wanted.')
    ->group(function() {
        Route::get('index', 'WantedController@index')->name('index');
        Route::post('index', 'WantedController@indexStore');
        Route::get('regist', 'WantedController@regist')->name('regist');
        Route::post('regist', 'WantedController@registStore');
        Route::get('edit/{id}', 'WantedController@edit')->name('edit')->where('id', '[0-9]+');
        Route::post('edit/{id}', 'WantedController@editStore')->where('id', '[0-9]+');
        Route::get('detail/{id}', 'WantedController@detail')->name('detail')->where('id', '[0-9]+');
        Route::post('delete/{id}', 'WantedController@deleteStore')->name('delete')->where('id', '[0-9]+');
    });

    // トーナメント表
    Route::prefix('tournament')->name('tournament.')
    ->group(function() {
        Route::get('index/{block?}/{sheet?}', 'TournamentController@index')->name('index');
        Route::get('make', 'TournamentController@make')->name('make');
        Route::post('make', 'TournamentController@makeStore');
        Route::get('edit/{block?}', 'TournamentController@edit')->name('edit');
        Route::post('edit/{block?}', 'TournamentController@editStore');
        Route::get('progress/{block?}', 'TournamentController@progress')->name('progress');
        Route::get('maingame/{block?}', 'TournamentController@maingame')->name('maingame');
        Route::get('mainfirstgame/{block?}', 'TournamentController@mainfirstgame')->name('mainfirstgame');
        Route::get('finalgame', 'TournamentController@finalgame')->name('finalgame');
        Route::get('teamlist/{block?}', 'TournamentController@teamlist')->name('teamlist');
        Route::get('result', 'TournamentController@result')->name('result');
    });

    // 対戦
    Route::prefix('game')->name('game.')
    ->group(function() {
        Route::get('result/{block?}/{sheet?}/{turn?}/{num?}', 'GameController@result')->name('result');
        Route::post('result', 'GameController@resultStore');
        Route::get('approval/{block?}/{sheet?}/{turn?}/{num?}/{mode?}', 'GameController@result')->name('approval');
        Route::get('delete/{id}/{block?}/{sheet?}', 'GameController@delete')->name('delete');
        Route::get('resultlist/{block?}', 'GameController@resultlist')->name('resultlist');
        Route::post('resultlist/{block?}', 'GameController@resultlist')->name('resultlist');
        Route::get('result_detail/{id}', 'GameController@resultdetail')->name('resultDetail');
        Route::get('main_result', 'GameController@mainResult')->name('mainResult');
        Route::post('main_result', 'GameController@mainResultStore');
        Route::get('main_resultlist/{block?}', 'GameController@mainResultlist')->name('mainResultlist');
        Route::post('main_resultlist/{block?}', 'GameController@mainResultlist')->name('mainResultlist');
        //Route::get('main_result/{win_team?}/{lose_team?}/{win_score?}/{lose_score?}', 'Api\GameController@result')->name('main_result');
        Route::get('final_result', 'GameController@finalResult')->name('finalResult');
        Route::post('final_result', 'GameController@finalResultStore');
        Route::get('final_resultlist', 'GameController@finalResultlist')->name('finalResultlist');
        Route::post('final_resultlist', 'GameController@finalResultlist')->name('finalResultlist');
        Route::get('rank_reset/{block?}/{sheet?}', 'GameController@rankReset')->name('rankReset');
        Route::get('rank_set/{block?}/{sheet?}', 'GameController@rankSet')->name('rankSet');
    });

    // ランキング
    Route::prefix('ranking')->name('ranking.')
    ->group(function() {
        Route::get('', 'RankingController@index')->name('index');
        Route::get('point', 'RankingController@point')->name('point');
        Route::get('history/{id}', 'RankingController@history')->name('history');
    });
});
