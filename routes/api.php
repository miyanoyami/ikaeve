<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('twitter')->name('twitter.')
    ->group(function() {
        Route::get('getId/{name?}/{event?}/{team?}', 'Api\TwitterController@getId')->name('getId');
        Route::get('getBot', 'Api\TwitterController@getBot')->name('getBot');
});

Route::prefix('tournament')->name('tournament.')
    ->group(function() {
        Route::get('changeTeam/{team?}', 'Api\TournamentController@changeTeam')->name('changeTeam');
});

Route::prefix('game')->name('game.')
    ->group(function() {
        Route::get('approval/{id?}', 'Api\GameController@approval')->name('approvalapi');
});

Route::prefix('team')->name('team.')
    ->group(function() {
        Route::get('check/{name?}/{event?}/{team?}', 'Api\TeamController@check')->name('check');
});

// Route::prefix('game')->name('game.')
//     ->group(function() {
//         Route::get('result/{event_id}/{win_team}/{lose_team}/{win_score}/{lose_score}', 'Api\GameController@result')->name('result');
// });
