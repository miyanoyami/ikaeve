<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Team;

class TeamController extends Controller
{
    public function check($name, $event, $team = null)
    {
        $result = array('status' => '', 'message' => '');
        try {
            $query = Team::query()
            ->where('event_id', $event)
            ->where('name', $name);
            if ($team) {
                $query->where('teams.id', '<>', $team);
            }
            $cnt = $query->count();

            if ($cnt == 0) {
                $result = ['status' => 200];
            } else {
                $result = ['status' => 400, 'message' => 'この大会に登録済みのチーム名です'];
            }
        } catch (\Exception $e) {
            report($e);
            $result = ['status' => 400, 'message' => 'システムエラーです'];
        }
        return response()->json($result);
    }

}
