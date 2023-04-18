<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\Paginator;
use Abraham\TwitterOAuth\TwitterOAuth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\TeamRequest;
use App\Models\User;
use App\Models\Event;
use App\Models\Team;
use App\Models\Member;
use App\Models\Result;
use App\Models\Answer;
use App\Models\Question;
use \SplFileObject;

class TeamController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $request->session()->forget('block');
        $search['event'] = $request->session()->get('event');
        $datas = Team::where('event_id', $search['event'])->orderBy('id', 'DESC')->paginate(config('common.page_num'));
        return view('team.index', compact('datas'));
    }

    public function indexStore(Request $request, $page = 1)
    {
        $search['event'] = $request->session()->get('event');
        $search['name'] = $request->name;
        $search['member_name'] = $request->member_name;

        $query = Team::query();
        $query->select('teams.*')
        ->join('members', 'members.team_id', '=', 'teams.id')
        ->join('users', 'users.id', '=', 'members.user_id')
        ->where('event_id', $search['event']);

        if ($search['name']) {
            $query->where('teams.name', 'LIKE', '%'.$search['name'].'%');
        }
        if ($search['member_name']) {
            $query->where(function($query) use($search){
                $query->where('members.name', 'LIKE', '%'.$search['member_name'].'%')
                      ->orWhere('users.twitter_nickname', 'LIKE', '%'.$search['member_name'].'%');
            });
        }
        $datas = $query->groupBy('teams.id')->orderBy('teams.id', 'DESC')->paginate(config('common.page_num'));
        return view('team.index', compact('datas', 'search'));
    }

    public function regist(Request $request)
    {
        if(!\old('name')) {
            $user = Auth::user();
            $request->session()->flash('_old_input', [
              'member_name.0' => $user->name,
              'twitter.0' => $user->twitter_nickname,
              'twitter_id.0' => $user->twitter_id
            ]);
        }
        $event_id = $request->session()->get('event');
        $event = Event::find($event_id);
        return view('team.regist', compact('event'));
    }

    public function registStore(TeamRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $event_id = $request->session()->get('event');
                $maxNo = Team::where('event_id', $event_id)->max('no');
                $chkTeam = Team::where('event_id', $event_id)
                ->where('name', $request->name)
                ->count();
                if ($chkTeam == 0) {
                    $data = new Team();
                    $data->no = $maxNo+1;
                    $data->name = $request->name;
                    $data->friend_code = join('', $request->friend_code);
                    $data->note = $request->note;
                    $data->event_id = $event_id;
                    $data->save();

                    $total_xp = 0;
                    $names = $request->member_name;
                    $twitters = $request->twitter;
                    $twitterIds = $request->twitter_id;
                    $xps = $request->xp;
                    $ids = $request->member_id;
                    foreach ($ids as $k => $val) {
                        $member = new Member();
                        $twitterName = str_replace('@', '', $twitters[$k]);
                        $twitterName = str_replace('＠', '', $twitterName);
                        $user = null;
                        $twId = $this->getTwitterId($twitterName);
                        if ($twId) {
                            $user = User::where('twitter_id', $twId)->first();
                        }
                        if (!$user) {
                            $user = User::where('twitter_nickname', $twitterName)->first();
                            if (!$user) {
                                $user = new User();
                                $user->twitter_id = $twId;
                                $user->twitter_nickname = $twitterName;
                                $user->save();
                            }
                        }
                        $member->user_id = $user->id;
                        $member->team_id = $data->id;
                        $member->name = $names[$k];
                        $member->xp = $xps[$k];
                        $member->save();
                        if ($k == 0) {
                            $data->member_id = $member->id;
                            $data->update();
                        }
                        if (is_numeric($xps[$k])) {
                            $total_xp += $xps[$k];
                        }
                    }
                    $data->xp_total = $total_xp;
                    $data->update();

                    $questions = $request->question;
                    $answers = $request->answer;
                    if ($request->question) {
                        foreach ($questions as $k => $val) {
                            $answer = new Answer();
                            $answer->team_id = $data->id;
                            $answer->question_id = $val;
                            $answer->note = $answers[$k];
                            $answer->save();
                        }
                    }
                }
            });

            FlashMessageService::success('登録が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('登録が失敗しました');
        }

        return redirect()->route('team.index');
    }

    public function edit(Request $request)
    {
        $event_id = $request->session()->get('event');
        $event = Event::find($event_id);
        $data = Team::find($request->id);
        $answer = Answer::getArray($data->answer);
        $members = $data::members($request->id);
        return view('team.edit', compact('data', 'members', 'event', 'answer'));
    }

    public function editStore(TeamRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = Team::find($request->id);
                $ids = $request->member_id;
                $data->name = $request->name;
                $data->friend_code = join('', $request->friend_code);
                $data->note = $request->note;
                $data->save();

                $total_xp = 0;
                $names = $request->member_name;
                $twitters = $request->twitter;
                $twitterIds = $request->twitter_id;
                $xps = $request->xp;
                foreach ($ids as $k => $val) {
                    $member = Member::find($val);
                    $twitterName = str_replace('@', '', $twitters[$k]);
                    $twitterName = str_replace('＠', '', $twitterName);
                    $twId = $this->getTwitterId($twitterName);
                    $user = User::where('twitter_id', $twId)->first();
                    if (!$user) {
                        $user = User::where('twitter_nickname', $twitterName)->first();
                        if (!$user) {
                            $user = new User();
                            $user->twitter_id = $twId;
                            $user->twitter_nickname = $twitterName;
                            $user->save();
                        }
                    }
                    $member->user_id = $user->id;
                    $member->name = $names[$k];
                    $member->xp = $xps[$k];
                    $member->update();
                    if ($k == 0) {
                        $data->member_id = $member->id;
                        $data->update();
                    }
                    if (is_numeric($xps[$k])) {
                        $total_xp += $xps[$k];
                    }
                }
                $data->xp_total = $total_xp;
                $data->update();

                $questions = $request->question;
                $answers = $request->answer;
                $aIds = $request->answer_id;
                if ($request->question) {
                    foreach ($questions as $k => $val) {
                        $answer = Answer::find($aIds[$k]);
                        if (!$answer) {
                            $answer = new Answer();
                        }
                        $answer->team_id = $request->id;
                        $answer->question_id = $val;
                        $answer->note = $answers[$k];
                        $answer->save();
                    }
                }
            });

            FlashMessageService::success('編集が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }

        return redirect()->route('team.index');
    }

    public function update($id, $column, $value)
    {
        try {
            \DB::transaction(function() use($id, $column, $value) {

                $data = Team::find($id);
                $data->abstention = $value;
                // 試合中の途中棄権の場合残りを0-2で登録
                // ランク外(5)で更新
                if ($data->block != '' && $value == 1) {
                    $this->updateResult($data);
                    //$data->pre_rank = 4;
                } elseif ($data->block != '' && $value == 0) {
                    $result = Result::where('event_id', $data->event_id)
                    ->where('lose_team_id', $data->id)
                    ->where('unearned_win', 1)
                    ->delete();
                }
                $data->save();

            });

            FlashMessageService::success('更新が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('更新が失敗しました');
        }

        return redirect()->route('team.index');
    }

    public function detail(Request $request)
    {
        $data = Team::find($request->id);
        return view('team.detail', compact('data'));
    }

    public function delete(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = Team::find($request->id);
                $test = $data->member();
                foreach ($data->members($request->id) as $value) {
                    $member = Member::find($value->id);
                    $member->delete();
                }
                $data->delete();

            });

            FlashMessageService::success('削除が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('削除が失敗しました');
        }

        return redirect()->route('team.index');
    }

    public function import(Request $request)
    {
        $event_id = $request->session()->get('event');
        $teams = Team::where('event_id', $event_id)
        ->where('abstention', 0)
        ->get();
        // チーム名重複チェック
        $nameNgAry = [];
        // ユーザー重複チェック
        $userNgAry = [];
        // フレンドコードチェック
        $fcodeNgAry = [];
        foreach ($teams as $value) {
            $chkTeamName = Team::where('event_id', $event_id)
            ->where('abstention', 0)
            ->where('id', '<>', $value->id)
            ->where('name', $value->name)
            ->count();
            if (0 < $chkTeamName) {
                $nameNgAry[] = $value;
            }
            $members = Team::members($value->id);
            foreach ($members as $member) {
                $chkUser = Member::select('members.*')
                ->join('teams', 'teams.id', 'members.team_id')
                ->where('team_id', '<>', $value->id)
                ->where('event_id', $event_id)
                ->where('user_id', $member->user_id)
                ->first();
                if (isset($chkUser)) {
                    $userNgAry[$chkUser->id] = $chkUser->name;
                }
            }
        }
        asort($userNgAry);
        $fcodeNgAry = Team::where('event_id', $event_id)
        ->where('abstention', 0)
        ->whereRaw('LENGTH(friend_code) < 12')
        ->get();

        return view('team.import', compact('nameNgAry', 'userNgAry', 'fcodeNgAry'));
    }

    public function importStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {
                // setlocaleを設定
                $event_id = $request->session()->get('event');
                $event = Event::find($event_id);

                $teams = Team::where('event_id', $event_id)->get();
                foreach ($teams as $v) {
                    $data = Team::find($v->id);
                    foreach ($data::members($data->id) as $v => $member) {
                        $target = Member::find($member->id);
                        $target->delete();
                    }
                    $data->delete();
                }
                // Member::where('event_id', $event_id)->delete();
                // Team::where('event_id', $event_id)->delete();

                setlocale(LC_ALL, 'ja_JP.UTF-8');

                // アップロードしたファイルを取得
                // 'csv_file' はCSVファイルインポート画面の inputタグのname属性
                $uploaded_file = $request->file('csv_file');

                // アップロードしたファイルの絶対パスを取得
                $file_path = $request->file('csv_file')->path($uploaded_file);

                $file = new SplFileObject($file_path);
                $file->setFlags(SplFileObject::READ_CSV);

                $row_count = 1;
                foreach ($file as $row)
                {
                    // 1行目のヘッダーは取り込まない
                    if ($row_count > 1)
                    {
                        $error = [];
                        $registDate = $row[0];
                        $teamName = $row[1];
                        $maxNo = Team::where('event_id', $event_id)->max('no');
                        $data = new Team();
                        $data->no = $maxNo+1;
                        $data->name = $teamName;
                        $data->event_id = $event_id;
                        $data->created_at = $registDate;
                        $data->save();

                        $questions = Question::where('event_id', $event_id)->get();

                        $num = 2;
                        $data->friend_code = preg_replace("/[^0-9]/","",$row[$num]);
                        $num++;
                        $xp_total = 0;
                        for ($i =0; $i < $event->team_member; $i++) {
                            if ($request->xp_import == 1) {
                                $xp = $row[$num];
                                $xp_total += $row[$num];
                                $num++;
                            }
                            $memberName = $row[$num];
                            $num++;
                            $twitterName = $row[$num];
                            $twitterName = str_replace('@', '', $twitterName);
                            $twitterName = str_replace('＠', '', $twitterName);
                            $member = new Member();
                            $user = User::where('twitter_nickname', $twitterName)->first();
                            if (!$user) {
                                $user = new User();
                                $user->twitter_nickname = $twitterName;
                                $user->save();
                            }
                            if ($request->xp_import == 1) {
                                $member->xp = $xp;
                            }
                            $member->user_id = $user->id;
                            $member->team_id = $data->id;
                            $member->name = $memberName;
                            $member->save();
                            if ($i == 0) {
                                $num++;
                                $data->member_id = $member->id;
                            } else {
                                $num++;
                            }
                        }
                        $data->note = $row[$num];
                        if ($request->xp_import == 1) {
                            $data->xp_total = $xp_total;
                        }
                        $data->update();

                        $num += 2;
                        foreach ($questions as $k => $val) {
                            $answer = new Answer();
                            $answer->team_id = $data->id;
                            $answer->question_id = $val->id;
                            $answer->note = $row[$num];
                            $answer->save();
                            $num++;
                        }

                    }
                    $row_count++;
                }
                $row_count -= 2;
                FlashMessageService::success($row_count . '件の登録が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('登録が失敗しました');
        }

        return redirect()->route('team.import');
    }

    private function getTwitterId($name)
    {
        $connection = new TwitterOAuth(
            config('twitter.consumer_key'),
            config('twitter.consumer_secret')
        );
        $userData=$connection->get("users/show", ["screen_name" => $name]);
        if (isset($userData->id)) {
            return $userData->id;
        } else {
            return null;
        }
        // return ($userData) ? $userData->id : null;
    }

    private function updateResult($team)
    {
        $event = Event::find($team->event_id);
        $teams = Team::where('event_id', $team->event_id)
        ->where('block', $team->block)
        ->where('sheet', $team->sheet)
        ->where('id', '<>', $team->id)
        ->get();
        foreach ($teams as $key => $value) {
          $query = Result::query();
          $query->where(function($query) use($team){
              $query->where('lose_team_id', '=', $team->id)
                    ->orWhere('win_team_id', '=', $team->id);
          });
          $query->where(function($query) use($value){
              $query->where('win_team_id', '=', $value->id)
                    ->orWhere('lose_team_id', '=', $value->id);
          });
          $result = $query->first();
          if (!$result) {
              foreach (config('game.pre') as $key => $val) {
                  foreach ($val as $conf) {
                      if (in_array($team->number, $conf) &&
                      in_array($value->number, $conf)) {
                          $result = new Result();
                          if ($value->abstention == 0) {
                              $result->win_score = $event->pre_score;
                          } else {
                              $result->win_score = 0;
                              $result->abstention = 1;
                          }
                          $result->win_team_id = $value->id;
                          $result->lose_team_id = $team->id;
                          $result->lose_score = 0;
                          $result->unearned_win = 1;
                          $result->block = $value->block;
                          $result->sheet = $value->sheet;
                          $result->turn = $key;
                          $result->event_id = $event->id;
                          $result->user_id = Auth::id();
                          $result->save();
                        }
                    }
                }
            // } elseif ($value->abstention == 1) {
            //     $result->win_score = 0;
            //     $result->abstention = 1;
            //     $result->user_id = Auth::id();
            //     $result->approval = 1;
            //     $result->unearned_win = 0;
            //     $result->save();
            // } elseif ($value->approval == 0) {
            //     // $result->approval = 1;
            //     $result->delete();
            }
        }
    }
    private function is_alnum($text) {
          if (preg_match("/^[a-zA-Z0-9]+$/",$text)) {
              return TRUE;
          } else {
              return FALSE;
          }
    }
}
