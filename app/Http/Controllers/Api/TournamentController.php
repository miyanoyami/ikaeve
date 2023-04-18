<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\TeamEdit;

class TournamentController extends Controller
{
    public function changeTeam($team_id)
    {
        $result = array('status' => '', 'message' => '');
        try {
            $target = null;
            $team = Team::find($team_id);
            if (strpos($team_id, '_') && !$team){
                $ary = explode('_', $team_id);
                $event_id = $ary[3];
            } else {
                $event_id = $team->event_id;
            }
            if ($event_id) {
                $target = Team::where('event_id', $event_id)
                ->where('change_flg', 1)
                ->first();
                if (!$target) {
                    $target = TeamEdit::where('event_id', $event_id)
                    ->where('change_flg', 1)
                    ->first();
                }
            }
            if (strpos($team_id, '_')){
                $ary = explode('_', $team_id);
                $team = TeamEdit::where('event_id', $ary[3])->first();
                if (!$team) {
                  $team = new TeamEdit();
                  $team->event_id = $ary[3];
                }
                $team->name = $ary[0].'ブロックの'.$ary[1].'のNo'.$ary[2];
                $team->block = $ary[0];
                $team->sheet = $ary[1];
                $team->number = $ary[2];
                $team->change_flg = 1;
                $team->save();
            }
            if (!$target) {
                $team->change_flg = 1;
                $team->update();
                $result = ['status' => 200, 'message' => '入れ替えるチームを選択して下さい'];
            } elseif($target->id == $team->id) {
                $team->change_flg = 0;
                $team->update();
                $result = ['status' => 200, 'message' => 'リセットしました'];
            } else {
                $block = $team->block;
                $sheet = $team->sheet;
                $number = $team->number;

                $team->block = $target->block;
                $team->sheet = $target->sheet;
                $team->number = $target->number;
                $team->update();

                $target->block = $block;
                $target->sheet = $sheet;
                $target->number = $number;
                $target->change_flg = 0;
                $target->update();
                // if (!$team->friend_code) {
                //     $team->delete();
                // }
                // if (!$target->friend_code) {
                //     $target->delete();
                // }
                $result = ['status' => 300, 'message' => $team->name.'と'.$target->name.'を入れ替えました'];
            }
        } catch (\Exception $e) {
            report($e);
            $result = ['status' => 400, 'message' => 'システムエラーです'];
        }
        return response()->json($result);
    }

}
