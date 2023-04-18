<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Models\Team;

class TwitterController extends Controller
{
    // screennameからID取得
    public function getId($name, $event, $team = null)
    {
        $result = array('status' => '', 'message' => '');
        try {
            $connection = new TwitterOAuth(
                config('twitter.consumer_key'),
                config('twitter.consumer_secret')
            );
            $userData = $connection->get("users/show", ["screen_name" => $name]);
            $cnt = 0;
            if (!empty($userData->id)) {
                $query = Team::query()
                ->join('members', 'members.team_id', '=', 'teams.id')
                ->join('users', 'users.id', '=', 'members.user_id')
                ->where('event_id', $event)
                ->where(function($query) use($userData){
                    $query->where('twitter_id', $userData->id)
                          ->orWhere('twitter_nickname', $userData->screen_name);
                });
                if ($team) {
                    $query->where('teams.id', '<>', $team);
                }
                $cnt = $query->count();
            }

            if ($cnt == 0 && empty($userData->id)) {
                $result = ['status' => 400, 'message' => 'twitter名が無効です'];
            } elseif ($cnt == 0) {
                $result = ['status' => 200, 'result' => $userData->id];
            } else {
                $result = ['status' => 400, 'message' => 'この大会に登録済みのtwitter名です'];
            }
        } catch (\Exception $e) {
            report($e);
            $result = ['status' => 400, 'message' => 'twitter名が無効です'];
        }
        return response()->json($result);
    }
    //
    // public function getBot() {
    //     $connection = new TwitterOAuth(
    //         config('twitter.consumer_key'),
    //         config('twitter.consumer_secret')
    //     );
    //     $timeline = $connection->get("statuses/user_timeline", [
    //         "count" => 1,
    //         "tweet_mode" => "extended",
    //         'screen_name' => 'splatoon2_mini'
    //       ]);
    //
    //     if(isset($timeline->errors)) {
    //
    //     } else {
    //       $webhookurl = "https://discord.com/api/webhooks/809019197027123230/Y15agTnlUtRae7CypX_qQwukpUgKhf5r9J6IsDIbtv4V1vwr3RT5Zj5_LRtmSUKGKtUa";
    //       //取得成功
    //       foreach ($timeline as $key => $value) {
    //           // echo $value->full_text;
    //           $json_data = array ('content'=>"$value->full_text");
    //           $make_json = json_encode($json_data);
    //
    //           $ch = curl_init( $webhookurl );
    //           curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
    //           curl_setopt( $ch, CURLOPT_POST, 1);
    //           curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
    //           curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
    //           curl_setopt( $ch, CURLOPT_HEADER, 0);
    //           curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
    //
    //           $response = curl_exec( $ch );
    //       }
    //     }
    // }

}
