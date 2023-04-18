<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Models\MainGame;
use App\Models\Result;
use App\Models\MainSecond;
use App\Models\Point;

class GameController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function mainResult(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }

        $startTurn = null;
        $member = null;
        // 参加している対戦表のチェック
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }
        if ($member) {
            $turn = Result::query()->where(function($query) use($member){
                $query->where('win_team_id', '=', $member->team->id)
                      ->orWhere('lose_team_id', '=', $member->team->id);
            })->where('level', '1')->max('turn');
            if ($turn)  {
                $startTurn = $turn + 1;
            }
        }
        if (!$startTurn) {
            $startTurn = 1;
        }

        $selectBlock = $request->block;
        // 本戦突破チーム
        $selectTeams = array();
        $cnt = 0;
        // if ($startTurn == 1) {
            $config = config('game.main'.$event->passing_order);
            foreach ($config as $key => $value) {
                foreach ($value as $v) {
                    foreach ($v as $k => $val) {
                        $team = Team::where('event_id', $event->id)
                        ->where('block', $selectBlock)
                        ->where('sheet', $k)
                        ->where('pre_rank', $val)
                        ->where('main_game', 1)
                        ->first();
                        if ($team) {
                            $chk = Result::where('event_id', $event->id)
                            ->where('block', $selectBlock)
                            ->where('level', 1)
                            ->where('lose_team_id', $team->id)
                            ->count();
                            if (0 < $chk) continue;
                            $selectTeams[$cnt]['name'] = $team->name;
                            $selectTeams[$cnt]['id'] = $team->id;
                            $cnt++;
                        }
                    }
                }
            }
            // $gameCnt = $this->getMainGameCnt($selectTeams);
            $gameCnt = 5;
        // } else {
        //     $whereTurn = $startTurn;
        //     $result = Result::where('event_id', $event->id)
        //     ->where('block', $selectBlock)
        //     ->where('level', 1)
        //     ->where('turn', ($whereTurn - 1))
        //     ->get();
        //     foreach ($result as $key => $value) {
        //         $selectTeams[$cnt]['name'] = $value->winteam->name;
        //         $selectTeams[$cnt]['id'] = $value->winteam->id;
        //         $cnt++;
        //     }
        //     $tmpAll = [];
        //     $config = config('game.main'.$event->passing_order);
        //     foreach ($config as $key => $value) {
        //         foreach ($value as $v) {
        //             foreach ($v as $k => $val) {
        //                 $team = Team::where('event_id', $event->id)
        //                 ->where('block', $selectBlock)
        //                 ->where('sheet', $k)
        //                 ->where('pre_rank', $val)
        //                 ->where('main_game', 1)
        //                 ->first();
        //                 if ($team) {
        //                     $tmpAll[] = $team->id;
        //                 }
        //             }
        //         }
        //     }
        //     $gameCnt = $this->getMainGameCnt($tmpAll);
        // }
        $data = null;
        $level = 1;

        return view('game.main_result',
            compact('selectBlock',
                    'selectTeams',
                    'event',
                    'data',
                    'gameCnt',
                    'startTurn',
                    'level'
                  ));
    }

    public function finalResult(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }

        $startTurn = null;
        $member = null;
        // 参加している対戦表のチェック
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }
        if ($member) {
            $turn = Result::query()->where(function($query) use($member){
                $query->where('win_team_id', '=', $member->team->id)
                      ->orWhere('lose_team_id', '=', $member->team->id);
            })->where('level', '2')->max('turn');
            if ($turn)  {
                $startTurn = $turn + 1;
            }
        }
        if (!$startTurn) {
            $startTurn = 1;
        }
        $blocks = Team::getBlocks($event->id);
        $selectBlock = 'finalgame';
        $selectSheet = null;
        // 本戦突破チーム
        $selectTeams = array();
        $cnt = 0;
        if ($startTurn == 1) {
            foreach ($blocks as $key => $value) {
                $team = Team::where('event_id', $event->id)
                ->where('block', $value->block)
                ->where('main_rank', 1)
                ->first();
                if ($team) {
                    $selectTeams[$cnt]['id']   = $team->id;
                    $selectTeams[$cnt]['name'] = $team->name;
                } else {
                    $selectTeams[$cnt]['id']   = null;
                    $selectTeams[$cnt]['name'] = $value->block . 'ブロック代表';
                }
                $cnt++;
            }
            $gameCnt = $this->getMainGameCnt($selectTeams);
        } else {
            $whereTurn = $startTurn;
            $result = Result::where('event_id', $event->id)
            ->where('level', 2)
            ->where('turn', ($whereTurn - 1))
            ->get();
            foreach ($result as $key => $value) {
                $selectTeams[$cnt]['name'] = $value->winteam->name;
                $selectTeams[$cnt]['id'] = $value->winteam->id;
                $cnt++;
            }
            $cnt = 0;
            $tmpAll = [];
            foreach ($blocks as $key => $value) {
                $team = Team::where('event_id', $event->id)
                ->where('block', $value->block)
                ->where('main_rank', 1)
                ->first();
                if ($team) {
                    $tmpAll[$cnt]['id']   = $team->id;
                    $tmpAll[$cnt]['name'] = $team->name;
                } else {
                    $tmpAll[$cnt]['id']   = null;
                    $tmpAll[$cnt]['name'] = $value->block . 'ブロック代表';
                }
                $cnt++;
            }
            $gameCnt = $this->getMainGameCnt($tmpAll);
        }
        $data = null;
        $level = 2;

        return view('game.final_result',
            compact('selectBlock',
                    'selectTeams',
                    'event',
                    'data',
                    'gameCnt',
                    'startTurn',
                    'level'
                  ));
    }


    private function getMainGameCnt($teams)
    {
        $gameCnt = 0;
        $cnt = count($teams);
        while(1 < $cnt) {
            $cnt = $cnt / 2;
            $gameCnt++;
        }
        return $gameCnt;
    }

    public function finalResultStore(Request $request)
    {
        if ($request->mode == 'app') {
            $str = '承認';
        } else {
            $str = '報告';
        }
        try {
            \DB::transaction(function() use($request, $str) {

                $event = $request->session()->get('event');
                $blocks = Team::getBlocks($event);
                $teams = $request->team;
                $scores = $request->score;
                $win_team = $teams[1];
                $lose_team = $teams[0];
                $win_score = $scores[1];
                $lose_score = $scores[0];
                if ($scores[1] < $scores[0]) {
                    $win_team = $teams[0];
                    $lose_team = ($teams[1] == 0) ? NULL : $teams[1];
                    $win_score = $scores[0];
                    $lose_score = $scores[1];
                }

                $id = $request->id;
                $data = Result::find($id);
                // 重複チェック
                if (!$data) {
                    $query = Result::query()->where('event_id', $event);
                    $data = $query->where(function($query) use($teams){
                        $query->where('win_team_id', '=', $teams[1])
                              ->orWhere('lose_team_id', '=', $teams[1]);
                    })
                    ->where('level', 2)
                    ->where('turn', $request->turn)
                    ->first();
                }
                if (!$data) {
                    $data = new Result();
                }
                $data->event_id = $event;
                $data->user_id = Auth::id();
                $data->win_team_id = $win_team;
                $data->lose_team_id = $lose_team;
                $data->win_score = $win_score;
                $data->lose_score = $lose_score;
                // $data->block = $request->block;
                $data->turn = $request->turn;
                $data->memo = $request->memo;
                $data->level = 2;
                    $data->unearned_win = $request->unearned_win;
                $data->save();

                $tmpAll = [];
                foreach ($blocks as $key => $value) {
                    $team = Team::where('event_id', $event)
                    ->where('block', $value->block)
                    ->where('main_rank', 1)
                    ->first();
                    if ($team) {
                        $tmpAll[]   = $team->id;
                    } else {
                        $tmpAll[]   = null;
                    }
                }
                $gameCnt = $this->getMainGameCnt($tmpAll);

                if ($request->turn == $gameCnt) {
                    $rank = 1;
                    $turn = $gameCnt;
                    for ($i = 0; $i < $gameCnt; $i++) {
                        $results = Result::where('event_id', $event)
                        // ->where('block', $block)
                        ->where('level', 2)
                        ->where('turn', $turn)
                        ->get();

                        if ($gameCnt == $turn) {
                            $result = Result::where('event_id', $event)
                            // ->where('block', $block)
                            ->where('level', 2)
                            ->where('turn', $turn)
                            ->first();
                            $first = Team::find($result->win_team_id);
                            $first->final_rank = $rank;
                            $first->update();
                            $rank++;
                            $second = Team::find($result->lose_team_id);
                            $second->final_rank = $rank;
                            $second->update();
                            $rank++;
                        } else {
                            foreach ($results as $ky => $val) {
                                $team = Team::find($val->lose_team_id);
                                $team->final_rank = $rank;
                                $team->update();
                            }
                            $rank += count($results);
                        }
                        $turn--;
                    }

                    $eventDetail = Event::find($event);
                    if ($eventDetail->point == true) {
                        $this->addPoint($eventDetail);
                    }
                }

            });

            FlashMessageService::success($str . 'が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error($str . 'が失敗しました');
        }

        return redirect()->route('tournament.finalgame');
    }

    // private function chkDistinctResult($team_id, $level, $turn) {
    //       $cnt = $query->where(function($query) use($team_id){
    //           $query->where('win_team_id', '=', $team_id)
    //                 ->orWhere('lose_team_id', '=', $team_id);
    //       })
    //       ->where('level', $level)
    //       ->where('turn', $turn)
    //       ->count();
    //       return ($cnt == 0) ? true : false;
    // }

    public function mainResultStore(Request $request)
    {
        if ($request->mode == 'app') {
            $str = '承認';
        } else {
            $str = '報告';
        }
        try {
            \DB::transaction(function() use($request, $str) {

                $event = $request->session()->get('event');
                $teams = $request->team;
                $scores = $request->score;
                $win_team = $teams[1];
                $lose_team = $teams[0];
                $win_score = $scores[1];
                $lose_score = $scores[0];
                if ($scores[1] < $scores[0]) {
                    $win_team = $teams[0];
                    $lose_team = ($teams[1] == 0) ? NULL : $teams[1];
                    $win_score = $scores[0];
                    $lose_score = $scores[1];
                }

                $id = $request->id;
                $data = Result::find($id);
                // 重複チェック
                if (!$data) {
                    $query = Result::query()->where('event_id', $event);
                    $data = $query->where(function($query) use($teams){
                        $query->where('win_team_id', '=', $teams[1])
                              ->orWhere('lose_team_id', '=', $teams[1]);
                    })
                    ->where('level', 1)
                    ->where('turn', $request->turn)
                    ->first();
                }
                if (!$data) {
                    $data = new Result();
                }
                $data->event_id = $event;
                $data->user_id = Auth::id();
                $data->win_team_id = $win_team;
                $data->lose_team_id = $lose_team;
                $data->win_score = $win_score;
                $data->lose_score = $lose_score;
                $data->block = $request->block;
                $data->turn = $request->turn;
                $data->memo = $request->memo;
                $data->level = 1;
                $data->unearned_win = $request->unearned_win;
                $data->save();

                    $tmpAll = [];
                    $eventDetail = Event::find($event);
                    $config = config('game.main'.$eventDetail->passing_order);
                    foreach ($config as $key => $value) {
                        foreach ($value as $v) {
                            foreach ($v as $k => $val) {
                                $team = Team::where('event_id', $eventDetail->id)
                                ->where('block', $request->block)
                                ->where('sheet', $k)
                                ->where('pre_rank', $val)
                                ->where('main_game', 1)
                                ->first();
                                if ($team) {
                                    $tmpAll[] = $team->id;
                                }
                            }
                        }
                    }
                    $gameCnt = $this->getMainGameCnt($tmpAll);

                    if ($eventDetail->shuffle == 1 && $request->turn == 1) {
                        $data = MainSecond::where('event_id', $event)
                        ->where('team_id', $win_team)
                        ->first();
                        if (!$data) {
                            $maxNum = MainSecond::where('event_id', $event)
                            ->where('block', $request->block)
                            ->orderBy('num', 'DESC')
                            ->first();
                            $second = new MainSecond();
                            $second->event_id = $event;
                            $second->block = $request->block;
                            if ($maxNum) {
                                $second->num = ($maxNum->num + 1);
                            } else {
                                $second->num = 1;
                            }
                            $second->team_id = $win_team;
                            $second->save();
                        }
                    }
                    $this->updateMainorFinalRank($eventDetail, $request->block, $gameCnt, $request->turn);

            });

            FlashMessageService::success($str . 'が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error($str . 'が失敗しました');
        }

        return redirect()->route('tournament.maingame', ['block' => $request->block]);
    }

    public function result(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }
        $mode = $request->mode;
        $edit_id = $request->id;
        $data = null;
        $level = 0;

        $defaultTeam = array();
        $member = null;
        // 参加している対戦表のチェック
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }
        if (isset($edit_id)) {
            $data = Result::find($edit_id);
            $selectSheet = $data->sheet;
            $selectBlock = $data->block;
            $selectTurn = $data->turn;
            $selectNum = $data->num;
            $team1 = null;
            $team2 = null;
        }
        if (!$data) {
            if ($member) {
                $selectSheet = $member->team->sheet;
                $selectBlock = $member->team->block;
                $selectTurn = $request->turn;
                $selectNum = $request->num;
                $conf = config('game.pre');
                if ($conf[$selectTurn]) {
                    $defaultTeam[] = $conf[$selectTurn][$selectNum][0];
                    $defaultTeam[] = $conf[$selectTurn][$selectNum][1];
                }
            } else {
                $selectSheet = $request->sheet;
                $selectBlock = $request->block;
                $selectTurn = $request->turn;
                $selectNum = $request->num;
                $conf = config('game.pre');
                if ($conf[$selectTurn]) {
                    $defaultTeam[] = $conf[$selectTurn][$selectNum][0];
                    $defaultTeam[] = $conf[$selectTurn][$selectNum][1];
                }
            }
            if (!$selectBlock) {
                $selectBlock = 'A';
            }
            if (!$selectSheet) {
                $selectSheet = 1;
            }
            if (!$selectTurn) {
                $selectTurn = 1;
            }

            $team1 = Team::where('event_id', $event->id)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->where('number', $defaultTeam[0])
            ->first();

            $team2 = Team::where('event_id', $event->id)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->where('number', $defaultTeam[1])
            ->first();

            $win = Result::where('win_team_id', $team1->id)
            ->where('lose_team_id', $team2->id)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->where('turn', $selectTurn)
            ->where('event_id', $event->id)
            ->first();

            $lose = Result::where('lose_team_id', $team1->id)
            ->where('win_team_id', $team2->id)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->where('turn', $selectTurn)
            ->where('event_id', $event->id)
            ->first();

            $left = null;
            $right = null;
            if ($win) {
                $data = $win;
            } elseif ($lose) {
                $data = $lose;
            }
        }
        if ($data) {
            if ($data->winteam->number < $data->loseteam->number) {
                $left['name'] = $data->winteam->name;
                $left['id'] = $data->winteam->id;
                $left['number'] = $data->winteam->number;
                $left['score'] = $data->win_score;
                $left['unearned_win'] = $data->unearned_win;
                $right['name'] = $data->loseteam->name;
                $right['id'] = $data->loseteam->id;
                $right['number'] = $data->loseteam->number;
                $right['score'] = $data->lose_score;
                $right['unearned_win'] = $data->unearned_win;
            } else {
                $right['name'] = $data->winteam->name;
                $right['id'] = $data->winteam->id;
                $right['number'] = $data->winteam->number;
                $right['score'] = $data->win_score;
                $right['unearned_win'] = $data->unearned_win;
                $left['name'] = $data->loseteam->name;
                $left['id'] = $data->loseteam->id;
                $left['number'] = $data->loseteam->number;
                $left['score'] = $data->lose_score;
                $left['unearned_win'] = $data->unearned_win;
            }
            $level = $data->level;
        }
        $maxScore = $event->pre_score;
        // if ($data->level == 1) {
        //     $maxScore = $event->main_score;
        // } elseif ($data->level == 2) {
        //     $maxScore = $event->final_score;
        // }
        return view('game.result',
            compact('selectSheet',
                    'selectBlock',
                    'selectTurn',
                    'team1',
                    'team2',
                    'defaultTeam',
                    'data',
                    'left',
                    'right',
                    'mode',
                    'event',
                    'maxScore',
                    'level',
                  ));
    }

    public function resultStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {


                $event = $request->session()->get('event');
                $teams = $request->team;
                $scores = $request->score;
                $win_team = $teams[1];
                $lose_team = $teams[0];
                $win_score = $scores[1];
                $lose_score = $scores[0];
                if ($scores[1] < $scores[0]) {
                    $win_team = $teams[0];
                    $lose_team = $teams[1];
                    $win_score = $scores[0];
                    $lose_score = $scores[1];
                }

                $id = $request->id;
                $data = Result::find($id);
                // 重複チェック
                if (!$data) {
                    $query = Result::query()->where('event_id', $event);
                    $data = $query->where(function($query) use($teams){
                        $query->where('win_team_id', '=', $teams[1])
                              ->orWhere('lose_team_id', '=', $teams[1]);
                    })
                    ->where('level', NULL)
                    ->where('turn', $request->turn)
                    ->first();
                }
                if (!$data) {
                    $data = new Result();
                }
                $data->event_id = $event;
                $data->user_id = Auth::id();
                $data->win_team_id = $win_team;
                $data->lose_team_id = $lose_team;
                $data->win_score = $win_score;
                $data->lose_score = $lose_score;
                $data->block = $request->block;
                $data->sheet = $request->sheet;
                $data->turn = $request->turn;
                $data->memo = $request->memo;
                $data->unearned_win = $request->unearned_win;
                $data->save();

                // ランク更新
                $this->updatePreRank($event, $request->block, $request->sheet);

            });

            FlashMessageService::success('報告が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('報告が失敗しました');
        }
        if ($request->level == 2) {
            return redirect()->route('game.finalResultlist');
        } elseif ($request->level == 1) {
            return redirect()->route('game.mainResultlist', ['block' => $request->block]);
        } else {
            return redirect()->route('tournament.index', ['block' => $request->block, 'sheet' => $request->sheet]);
        }
    }

    public function delete(Request $request)
    {
        $data = Result::find($request->id);
        try {
            \DB::transaction(function() use($data) {

                if ($data->level == 0) {
                    $this->updatePreRank($data->event_id, $data->block, $data->sheet);
                }
                $data->delete();
            });

            FlashMessageService::success('削除が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('削除が失敗しました');
        }
        if ($data->level == 0) {
            return redirect()->route('tournament.index', ['block' => $request->block, 'sheet' => $request->sheet]);
        } elseif ($data->level == 1) {
            return redirect()->route('game.mainResultlist', ['block' => $data->block]);
        } elseif ($data->level == 2) {
            return redirect()->route('game.finalResultlist');
        }
    }

    public function resultlist(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }
        $search = array();
        $search['searchBlock'] = $request->searchBlock;
        $search['searchSheet'] = $request->searchSheet;
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        if (!$request->has('searchBlock')) {
            $search['searchBlock'] = $selectBlock;
        }
        $selectSheet = 'progress';

        $query = Result::query();
        $query->where('event_id', $event->id)->whereNull('level');
        if (isset($search['searchBlock'])) {
            $query->where('block', $search['searchBlock']);
        }
        if (isset($search['searchSheet'])) {
            $query->where('sheet', $search['searchSheet']);
        }
        $datas = $query->orderBy('id', 'DESC')->paginate(config('common.page_num'));
        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);
        return view('game.resultlist', compact('selectBlock', 'selectSheet', 'datas', 'search', 'blocks', 'sheets'));
    }

    public function mainResultlist(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }
        $search = array();
        $search['searchBlock'] = $request->searchBlock;
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        $selectSheet = 'maingame';

        $query = Result::query();
        $query->where('event_id', $event->id)
        ->where('level', 1);
        if (isset($search['searchBlock'])) {
            $query->where('block', $search['searchBlock']);
        }
        $datas = $query->orderBy('id', 'DESC')->paginate(config('common.page_num'));
        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);
        return view('game.main_resultlist', compact('selectBlock', 'selectSheet', 'datas', 'search', 'blocks', 'sheets'));
    }

    public function finalResultlist(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }
        $search = array();
        $search['searchBlock'] = $request->searchBlock;
        $selectBlock = 'finalgame';
        $selectSheet = null;

        $query = Result::query();
        $query->where('event_id', $event->id)
        ->where('level', 2);
        if (isset($search['searchBlock'])) {
            $query->where('block', $search['searchBlock']);
        }
        $datas = $query->orderBy('id', 'DESC')->paginate(config('common.page_num'));
        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);
        return view('game.final_resultlist', compact('selectBlock', 'selectSheet', 'datas', 'search', 'blocks', 'sheets'));
    }

    public function resultdetail(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        $data = Result::find($request->id);
        if (!$event || !$data) {
          return redirect()->route('event.index');
        }
        $mode = 'view';
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        $selectSheet = $data->sheet;
        $selectTurn = $data->turn;

        $maxScore = $event->pre_score;
        if ($data->level == 1) {
            $maxScore = $event->main_score;
        } elseif ($data->level == 2) {
            $maxScore = $event->final_score;
        }

        $left[] = array();
        $left['name'] = $data->winteam->name;
        $left['id'] = $data->winteam->id;
        $left['number'] = $data->winteam->number;
        $left['score'] = $data->win_score;
        $left['unearned_win'] = $data->unearned_win;
        $right['name'] = $data->loseteam->name;
        $right['id'] = $data->loseteam->id;
        $right['number'] = $data->loseteam->number;
        $right['score'] = $data->lose_score;
        $right['unearned_win'] = $data->unearned_win;
        $level = $data->level;

        return view('game.result',
            compact('selectSheet',
                    'selectBlock',
                    'selectTurn',
                    'data',
                    'left',
                    'right',
                    'mode',
                    'event',
                    'maxScore',
                    'level',
                  ));
    }

    public function rankSet(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }

        try {
            \DB::transaction(function() use($request, $event) {
                $this->updatePreRank($event->id, $request->block, $request->sheet);
                $teams = Team::where('event_id', $event->id)
                ->where('block', $request->block)
                ->where('sheet', $request->sheet)
                //->where('abstention', 0)
                ->orderBy('pre_rank')
                ->get();
                foreach ($teams as $key => $value) {
                    $team = Team::find($value->id);
                    if ($event->passing_order < $key + 1) {
                        $team->main_game = 0;
                    } else {
                        $team->main_game = 1;
                    }
                    $team->update();
                }
            });

            FlashMessageService::success('順位確定が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('順位確定が失敗しました');
        }

        return redirect()->route('tournament.index', ['block' => $request->block, 'sheet' => $request->sheet]);
    }

    public function rankReset(Request $request)
    {
        $req = $request->session()->get('event');
        $event = Event::find($req);
        if (!$event) {
          return redirect()->route('event.index');
        }
        try {
            \DB::transaction(function() use($request, $req) {
                $teams = Team::where('event_id', $req)
                ->where('block', $request->block)
                ->where('sheet', $request->sheet)
                ->get();

                foreach ($teams as $val) {
                    $team = Team::find($val->id);
                    $team->pre_rank = null;
                    $team->main_game = 0;
                    $team->update();
                }
            });

            FlashMessageService::success('順位リセットが完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('順位リセットが失敗しました');
        }

        return redirect()->route('tournament.index', ['block' => $request->block, 'sheet' => $request->sheet]);
    }

    private function updatePreRank($event, $selectBlock, $selectSheet) {
        $results = Team::where('event_id', $event)
        ->where('block', $selectBlock)
        ->where('sheet', $selectSheet)
        ->orderBy('number')
        ->get();

        $teams = array();
        foreach ($results as $key => $value) {
            $teams[$value->number]['id'] = $value->id;
            $teams[$value->number]['number'] = $value->number;
            $teams[$value->number]['name'] = $value->name;
            $teams[$value->number]['abstention'] = $value->abstention;
            $teams[$value->number]['win_num'] = 0;
            $teams[$value->number]['win_total'] = 0;
            $teams[$value->number]['lose_total'] = 0;
            $teams[$value->number]['created_at'] = $value->created_at;

            $team_id = $value->id;
            $blockTeam = Team::where('event_id', $event)
            ->where('block', $selectBlock)
            ->where('sheet', $value->sheet)
            ->where('id', '<>', $team_id)
            ->orderBy('number')
            ->get();
            foreach ($blockTeam as $key => $v) {
                $win = Result::where('win_team_id', $team_id)
                ->where('event_id', $event)
                ->where('lose_team_id', $v->id)
                ->first();
                $lose = Result::where('lose_team_id', $team_id)
                ->where('event_id', $event)
                ->where('win_team_id', $v->id)
                ->first();
                if ($win || $lose) {
                    if ($win) {
                        $teams[$value->number]['win_num'] += 3;
                        $teams[$value->number]['win_total'] += $win->win_score;
                        $teams[$value->number]['lose_total'] += $win->lose_score;
                    } else if($lose) {
                        $teams[$value->number]['win_num'] += $lose->lose_score;
                        $teams[$value->number]['win_total'] += $lose->lose_score;
                        $teams[$value->number]['lose_total'] += $lose->win_score;
                    }
                }
            }
            $winScore = $teams[$value->number]['win_total'];
            $loseScore = $teams[$value->number]['lose_total'];
            if ($winScore == 0) {
                $teams[$value->number]['percent'] = 0;
            } else {
                $teams[$value->number]['percent'] = round($winScore / ($winScore + $loseScore) * 100, 1);
            }
        }

        $ranks = $teams;
        $abstentions = array();
        foreach ($ranks as $key => $value) {
            if ($value['abstention'] == 1) {
                $abstentions[] = $ranks[$key];
                unset($ranks[$key]);
            }
        }
        // 勝点・取得率で並び替え
        $score_keys = array_column($ranks, 'win_num');
        $percent_keys = array_column($ranks, 'percent');
        array_multisort(
            $score_keys, SORT_DESC, SORT_NUMERIC,
            $percent_keys, SORT_DESC, SORT_NUMERIC,
            $ranks
         );
        $ranks = $this->chkWinTeam($ranks);
        $resultCnt = Result::where('event_id', $event)
        ->where('block', $selectBlock)
        ->where('sheet', $selectSheet)
        ->count();
        if (2 < $resultCnt) {
            $ranks = $this->chkThreeSided($ranks);
        }

        $score_keys = array_column($abstentions, 'win_num');
        $percent_keys = array_column($abstentions, 'percent');
        array_multisort(
            $score_keys, SORT_DESC, SORT_NUMERIC,
            $percent_keys, SORT_DESC, SORT_NUMERIC,
            $abstentions
         );
        $abstentions = $this->chkWinTeam($abstentions);
        if (2 < $resultCnt) {
            $abstentions = $this->chkThreeSided($abstentions);
        }

        foreach ($abstentions as $key => $value) {
          array_push($ranks, $value);
        }
        $num = 1;
        foreach ($ranks as $value) {
            $team = Team::find($value['id']);
            $team->pre_rank = $num;
            $team->update();
            $num++;
        }

        $this->chkAllResultAndRankDecision($event, $selectBlock, $selectSheet);
    }

    private function chkThreeSided($ary)
    {
        $return = $ary;
        foreach ($ary as $key => $value) {
            if (2 < (count($return) - $key)) {
                if ($ary[$key]['percent'] == $ary[($key+1)]['percent'] &&
                    $ary[$key]['win_num'] == $ary[($key+1)]['win_num'] &&
                    $ary[$key]['percent'] == $ary[($key+2)]['percent'] &&
                    $ary[$key]['win_num'] == $ary[($key+2)]['win_num']) {
                    $result = Team::whereIn('id', [$ary[$key]['id'], $ary[($key+1)]['id'], $ary[($key+2)]['id']])
                    ->orderBy('created_at', 'ASC')
                    ->get();
                    // print_r($result);
                    foreach ($result as $k => $val) {
                        if ($val->id == $ary[$key]['id']) {
                            $return[$key+$k] = $ary[$key];
                        } elseif ($val->id == $ary[($key+1)]['id']) {
                            $return[$key+$k] = $ary[($key+1)];
                        } elseif ($val->id == $ary[($key+2)]['id']) {
                            $return[$key+$k] = $ary[($key+2)];
                        }
                    }
                }
            }
        }
        return $return;
    }

    private function chkWinTeam($ary)
    {
        $return = $ary;
        foreach ($ary as $key => $value) {
            if (isset($ary[($key+2)])) {
                if (!($ary[$key]['percent'] == $ary[($key+1)]['percent'] &&
                      $ary[$key]['win_num'] == $ary[($key+1)]['win_num'] &&
                      $ary[$key]['percent'] == $ary[($key+2)]['percent'] &&
                      $ary[$key]['win_num'] == $ary[($key+2)]['win_num'] &&
                      isset($ary[($key+2)]))) {
                    if ($ary[$key]['percent'] == $ary[($key+1)]['percent'] &&
                        $ary[$key]['win_num'] == $ary[($key+1)]['win_num']) {
                        $result =Result::where('win_team_id', $ary[($key+1)]['id'])
                        ->where('lose_team_id', $ary[$key]['id'])
                        ->first();
                        if ($result) {
                            $return[$key] = $ary[($key+1)];
                            $return[($key+1)] = $ary[$key];
                        } elseif ($ary[($key+1)]['created_at'] < $ary[$key]['created_at']) {
                            $return[$key] = $ary[($key+1)];
                            $return[($key+1)] = $ary[$key];
                        }
                    }
                }
            } else if (isset($ary[($key+1)])) {
                if ($ary[$key]['percent'] == $ary[($key+1)]['percent'] &&
                    $ary[$key]['win_num'] == $ary[($key+1)]['win_num']) {
                    $result =Result::where('win_team_id', $ary[($key+1)]['id'])
                    ->where('lose_team_id', $ary[$key]['id'])
                    ->first();
                    if ($result) {
                        $return[$key] = $ary[($key+1)];
                        $return[($key+1)] = $ary[$key];
                    } elseif ($ary[($key+1)]['created_at'] < $ary[$key]['created_at']) {
                        $return[$key] = $ary[($key+1)];
                        $return[($key+1)] = $ary[$key];
                    }
                }
            }
        }
        return $return;
    }

    private function chkAllResultAndRankDecision($event_id, $block, $sheet)
    {
        $event = Event::find($event_id);
        $cnt = Result::where('event_id', $event_id)
        ->where('block', $block)
        ->where('sheet', $sheet)
        ->count();

        $gameCnt = Team::getGameCnt($event_id, $block, $sheet);
        if ($cnt >= $gameCnt) {
            $teams = Team::where('event_id', $event_id)
            ->where('block', $block)
            ->where('sheet', $sheet)
            //->where('abstention', 0)
            ->orderBy('pre_rank')
            ->get();
            foreach ($teams as $key => $value) {
                $team = Team::find($value->id);
                if ($event->passing_order < $key + 1) {
                    $team->main_game = 0;
                } else {
                    $team->main_game = 1;
                }
                $team->update();
            }
        } else {
            // 全勝しているチームは１位確定
            $teams = Team::where('event_id', $event_id)
            ->where('block', $block)
            ->where('sheet', $sheet)
            ->orderBy('pre_rank')
            ->get();
            foreach ($teams as $key => $value) {
                $resultCnt = Result::where('event_id', $event->id)
                ->where('win_team_id', $value->id)
                ->where('level', NULL)
                ->count();
                $team = Team::find($value->id);
                if ($resultCnt == (count($teams) - 1)) {
                    $team->main_game = 1;
                }
                $team->update();
            }
        }
    }

    private function updateMainorFinalRank($event, $block, $gameCnt, $turn)
    {
      /*
        $blockNum = count(Team::getBlocks($event->id));
        $updateFlg = false;
        $selectTeams = array();
        $cnt = 0;
        // ブロックが単数ならfinal_rankを更新する
        if ($blockNum == 1) {
          $config = config('game.main'.$event->passing_order);
          foreach ($config as $key => $value) {
              foreach ($value as $v) {
                  foreach ($v as $k => $val) {
                      $team = Team::where('event_id', $event->id)
                      ->where('block', $block)
                      ->where('sheet', $k)
                      ->where('pre_rank', $val)
                      ->where('main_game', 1)
                      ->first();
                      if ($team) {
                          $chk = Result::where('event_id', $event->id)
                          ->where('block', $block)
                          ->where('level', 1)
                          ->where('lose_team_id', $team->id)
                          ->count();
                          if (0 < $chk) continue;
                          $selectTeams[$cnt]['name'] = $team->name;
                          $selectTeams[$cnt]['id'] = $team->id;
                          $cnt++;
                      }
                  }
              }
              if (count($selectTeams) == 1) {
                  $updateFlg = true;
              }
          }
        } else {
            // $cnt = 0;
            // $whereTurn = $startTurn;
            // $result = Result::where('event_id', $event->id)
            // ->where('level', 2)
            // ->where('turn', ($whereTurn - 1))
            // ->get();
            // foreach ($result as $key => $value) {
            //     $selectTeams[$cnt]['name'] = $value->winteam->name;
            //     $selectTeams[$cnt]['id'] = $value->winteam->id;
            //     $cnt++;
            // }
            // if (count($selectTeams) == 1) {
                $updateFlg = true;
            // }
        }

        if ($updateFlg == true) {
            $teams = Team::where('event_id', $event->id)
            ->where('block', $block)
            ->where('main_game', 1)
            ->get();

            $rank = 1;
            $turn = $gameCnt;
            for ($i = 0; $i < $gameCnt; $i++) {
                $results = Result::where('event_id', $event->id)
                ->where('block', $block)
                ->where('level', 1)
                ->where('turn', $turn)
                ->get();

                if ($gameCnt == $turn) {
                    $result = Result::where('event_id', $event->id)
                    ->where('block', $block)
                    ->where('level', 1)
                    ->where('turn', $turn)
                    ->first();
                    if ($result) {
                      $first = Team::find($result->win_team_id);
                      if ($blockNum == 1) {
                          $first->final_rank = $rank;
                      } else {
                          $first->main_rank = $rank;
                      }
                      $first->update();
                    }
                    $rank++;
                    if ($result) {
                        $second = Team::find($result->lose_team_id);
                        if ($blockNum == 1) {
                            $second->final_rank = $rank;
                        } else {
                            $second->main_rank = $rank;
                        }
                    }
                    $second->update();
                    $rank++;
                } else {
                    foreach ($results as $ky => $val) {
                        $team = Team::find($val->lose_team_id);
                        if ($blockNum == 1) {
                            $team->final_rank = $rank;
                        } else {
                            $team->main_rank = $rank;
                        }
                        $team->update();
                    }
                    $rank += count($results);
                }
                $turn--;
            }

            if ($event->point == true) {
                $this->addPoint($event);
            }
        }
        */
                $blockNum = count(Team::getBlocks($event->id));
        // ブロックが単数ならfinal_rankを更新する
        // if ($blockNum == 1) {
        //     $target_rank = 'final_rank';
        // } else {
        //     $target_rank = 'main_rank';
        // }

        $teams = Team::where('event_id', $event->id)
        ->where('block', $block)
        ->where('main_game', 1)
        ->get();

        $rank = 1;
        // $turn = $gameCnt;
        for ($i = 0; $i < $gameCnt; $i++) {
            $results = Result::where('event_id', $event->id)
            ->where('block', $block)
            ->where('level', 1)
            ->where('turn', $turn)
            ->get();

            if ($gameCnt == $turn) {
                $result = Result::where('event_id', $event->id)
                ->where('block', $block)
                ->where('level', 1)
                ->where('turn', $turn)
                ->first();
                $first = Team::find($result->win_team_id);
                if ($blockNum == 1) {
                    $first->final_rank = $rank;
                } else {
                    $first->main_rank = $rank;
                }
                $first->update();
                $rank++;
                $second = Team::find($result->lose_team_id);
                if ($blockNum == 1) {
                    $second->final_rank = $rank;
                } else {
                    $second->main_rank = $rank;
                }
                $second->update();
                $rank++;
            } else {
                foreach ($results as $ky => $val) {
                    $team = Team::find($val->lose_team_id);
                    if ($blockNum == 1) {
                        $team->final_rank = $rank;
                    } else {
                        $team->main_rank = $rank;
                    }
                    $team->update();
                }
                // echo $rank;
                // echo '/'.count($results);
                // echo '<br>';
                $rank += count($results);
            }
            $turn--;
        }
    }

    private function addPoint($event) {
        Point::where('event_id', $event->id)->delete();
        $teams = Team::where('event_id', $event->id)->whereBetween('final_rank', [1, 8])->get();
        foreach ($teams as $key => $value) {
            $target = Team::find($value->id);
            $point = 1;
            if ($target->final_rank == 1) {
                $point = 8;
            } elseif ($target->final_rank == 2) {
                $point = 4;
            } elseif ($target->final_rank <= 4 && 2 <= $target->final_rank) {
                $point = 2;
            }
            $members = $target->members($target->id);
            foreach ($members as $v) {
                $member = Member::find($v->id);
                $data = new Point();
                $data->user_id = $member->user_id;
                $data->member_id = $member->id;
                $data->event_id = $event->id;
                $data->point = $point;
                $data->save();
            }
        }
    }

}
