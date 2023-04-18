<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\Event;
use App\Models\Result;

class GameController extends Controller
{
    // public function __construct()
    // {
    //    $this->middleware('auth');
    // }
    public function result(Request $request, $win_team_name, $lose_team_name, $win_score, $lose_score)
    {
        $result = array('status' => '', 'message' => '');
        try {
                $event_id = $request->session()->get('event');
                $event = Event::find($event_id);

                $winTeam = Team::where('event_id', $event_id)
                ->where('name', $win_team_name)
                ->first();
                $loseTeam = Team::where('event_id', $event_id)
                ->where('name', $lose_team_name)
                ->first();

                $query = Result::query()->where('event_id', $event_id)
                ->where('level', 1);

                $query->where(function($query) use($winTeam){
                    $query->where('win_team_id', '=', $winTeam->id)
                          ->orWhere('lose_team_id', '=', $winTeam->id);
                });
                $query->where(function($query) use($loseTeam){
                    $query->where('win_team_id', '=', $loseTeam->id)
                          ->orWhere('lose_team_id', '=', $loseTeam->id);
                });
                $chkResult = $query->first();
                if (!$chkResult) {
                    $chkResult = new Result();
                }
                $chkResult->event_id = $event_id;
                $chkResult->level = 1;
                $chkResult->turn = 1;
                $chkResult->win_team_id = $winTeam->id;
                $chkResult->win_score = $win_score;
                $chkResult->lose_team_id = $loseTeam->id;
                $chkResult->lose_score = $lose_score;
                $chkResult->block = $winTeam->block;
                $chkResult->user_id = Auth::id();
                $chkResult->save();

                $result = ['status' => 300, 'message' => '更新しました'];

        } catch (\Exception $e) {
            report($e);
            $result = ['status' => 400, 'message' => 'システムエラーです'];
        }
        // print_r($result);
        // exit;
        return response()->json($result);
    }

}
