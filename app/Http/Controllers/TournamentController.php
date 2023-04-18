<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use Carbon\Carbon;
use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Models\MainGame;
use App\Models\Result;
use App\Models\MainSecond;
// use App\Models\User;

class TournamentController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $event = $request->session()->get('event');
        if (!$event) {
            return redirect()->route('event.index');
        }

        $eventDetail = Event::find($event);
        $rule[1] = $eventDetail->pre_rule1;
        $rule[2] = $eventDetail->pre_rule2;
        $rule[3] = $eventDetail->pre_rule3;

        $member = null;

        // 参加している対戦表のチェック
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event)
            ->where('user_id', Auth::id())->first();
        }
        if ($member && $request->block == '') {
            $selectSheet = $member->team->sheet;
            $selectBlock = $member->team->block;
        } else {
            $selectSheet = $request->sheet;
            $selectBlock = $request->block;
        }
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        if (!$selectSheet) {
            $selectSheet = 'all';
        }

        $blocks = Team::getBlocks($event);
        $sheets = Team::getSheets($event, $selectBlock);

        if (count($blocks) < 1) {
            $request->session()->forget('block');
            FlashMessageService::error('対戦表はまだ作成されてません');
            return redirect()->route('event.detail', ['id' => session('event')]);
        }

        $request->session()->put('block', $selectBlock);
        if ($selectSheet == 'all' || $selectSheet == '') {
            $results = Team::where('event_id', $event)
            ->where('block', $selectBlock)
            ->orderBy('sheet')
            ->orderBy('pre_rank')
            ->orderBy('number')
            ->get();

            $games = Result::where('event_id', $event)
            ->where('block', $selectBlock)
            ->get();
        } else {
            $results = Team::where('event_id', $event)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->orderBy('pre_rank')
            ->orderBy('number')
            ->get();

            $games = Result::where('event_id', $event)
            ->where('block', $selectBlock)
            ->where('sheet', $selectSheet)
            ->get();
        }
        $teams = array();
        $vs = array();
        foreach ($results as $key => $value) {
            $teams[$value->sheet][$value->number]['id'] = $value->id;
            $teams[$value->sheet][$value->number]['number'] = $value->number;
            $teams[$value->sheet][$value->number]['name'] = $value->name;
            $teams[$value->sheet][$value->number]['abstention'] = $value->abstention;
            $teams[$value->sheet][$value->number]['win_num'] = 0;
            $teams[$value->sheet][$value->number]['win_total'] = 0;
            $teams[$value->sheet][$value->number]['lose_total'] = 0;
            $teams[$value->sheet][$value->number]['created_at'] = $value->created_at;
            $teams[$value->sheet][$value->number]['rank'] = $value->pre_rank;
            $teams[$value->sheet][$value->number]['main_game'] = $value->main_game;

            $vs[$value->id] = array();
            $team_id = $value->id;
            $blockTeam = Team::where('event_id', $event)
            ->where('block', $selectBlock)
            ->where('sheet', $value->sheet)
            ->where('id', '<>', $team_id)
            ->orderBy('number')
            ->get();
            foreach ($blockTeam as $key => $v) {
                $vs[$team_id][$key]['name'] = $v->name;
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
                        if ($win->abstention == 1) {
                            $vs[$team_id][$key]['win'] = false;
                            $vs[$team_id][$key]['score'] = '△';
                        } else {
                            $vs[$team_id][$key]['win'] = true;
                            $vs[$team_id][$key]['score'] = '◯';
                        }
                        //$vs[$team_id][$key]['score'] = $win->win_score.'-'.$win->lose_score;
                        $teams[$value->sheet][$value->number]['win_num'] += 3;
                        $teams[$value->sheet][$value->number]['win_total'] += $win->win_score;
                        $teams[$value->sheet][$value->number]['lose_total'] += $win->lose_score;
                    } else if($lose) {
                        if ($lose->abstention == 1) {
                            $vs[$team_id][$key]['score'] = '△';
                        } else {
                            $vs[$team_id][$key]['score'] = '×';
                        }
                        $vs[$team_id][$key]['win'] = false;
                        //$vs[$team_id][$key]['score'] = $lose->lose_score.'-'.$lose->win_score;
                        $teams[$value->sheet][$value->number]['win_num'] += $lose->lose_score;
                        $teams[$value->sheet][$value->number]['win_total'] += $lose->lose_score;
                        $teams[$value->sheet][$value->number]['lose_total'] += $lose->win_score;
                    }
                } else {
                    $vs[$team_id][$key]['win'] = false;
                    $vs[$team_id][$key]['score'] = '';
                }
            }
            $winScore = $teams[$value->sheet][$value->number]['win_total'];
            $loseScore = $teams[$value->sheet][$value->number]['lose_total'];
            if ($winScore == 0) {
                $teams[$value->sheet][$value->number]['percent'] = 0;
            } else {
                $teams[$value->sheet][$value->number]['percent'] = round($winScore / ($winScore + $loseScore) * 100, 1);
            }
        }

        $scores = array();
        foreach (config('game.pre') as $key => $val) {
            foreach ($val as $k => $conf) {
                foreach ($games as $key => $value) {
                    if ($value->winteam->number == $conf[0] && $value->loseteam->number == $conf[1] ||
                    $value->winteam->number == $conf[1] && $value->loseteam->number == $conf[0]) {
                        if ($conf[0] == 1) {
                            $num = 0;
                        } else {
                            $num = 1;
                        }
                        $scores[$value->block][$value->sheet][$value->turn][$num]['win'] = $value->winteam->number;
                        $scores[$value->block][$value->sheet][$value->turn][$num][$value->win_team_id]['score'] = $value->win_score;
                        $scores[$value->block][$value->sheet][$value->turn][$num][$value->lose_team_id]['score'] = $value->lose_score;
                    }
                }
            }
        }
        $event = $eventDetail;
        return view('tournament.index',
        compact('selectBlock', 'selectSheet', 'sheets', 'teams', 'blocks', 'member', 'vs', 'scores', 'rule', 'event'));
    }

    public function result(Request $request) {
        $event = $request->session()->get('event');
        if (!$event) {
            return redirect()->route('event.index');
        }

        $selectBlock = 'result';
        $selectSheet = '';
        $blocks = Team::getBlocks($event);
        $sheets = Team::getSheets($event, $selectBlock);

        $eventDetail = Event::find($event);
        $first = $eventDetail->rankTeam(1);
        // print_r($first);
        // exit;
        $second = $eventDetail->rankTeam(2);
        $third = $eventDetail->rankTeam(3);
        return view('tournament.result', compact('eventDetail', 'first', 'second', 'third', 'selectBlock', 'selectSheet', 'sheets', 'blocks', ));
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
                            $return[$k] = $ary[$key];
                        } elseif ($val->id == $ary[($key+1)]['id']) {
                            $return[$k] = $ary[($key+1)];
                        } elseif ($val->id == $ary[($key+2)]['id']) {
                            $return[$k] = $ary[($key+2)];
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
                    }
                }
            }
        }
        return $return;
    }

    public function make(Request $request)
    {
        $event_id = $request->session()->get('event');
        if (!$event_id) {
            $request->session()->forget('block');
            return redirect()->route('event.index');
        }
        $event = Event::find($event_id);
        $sheetNum = 16;
        $teamBySheet = 4;

        $teams = Team::getAllTeam($event_id);
        $targetTeamCnt = count($teams);
        $makeBlockCnt = ceil(count($teams) / ($sheetNum * $teamBySheet));
        return view('tournament.make', compact('targetTeamCnt', 'makeBlockCnt'));
    }

    public function makeStore(Request $request)
    {
        try {
            $event_id = $request->session()->get('event');
            if (!$event_id) {
                return redirect()->route('event.index');
            }
            $event = Event::find($event_id);
            Team::resetAllTeam($event_id);
            $teams = Team::getAllTeam($event_id, $request->order_rule);
            $sheetNum = 16;
            $teamBySheet = 4;
            // ブロック数
            $allNum = count($teams);
            $blockNum = ceil($allNum / ($sheetNum * $teamBySheet));
            // ブロック単位のチーム数
            $teamByBlockNum =  floor($allNum / $blockNum);
            // 57

            // 奇数チームになるシート数
            $theam3 = (count($teams) % $teamBySheet);
            // ブロックごとの3チーム数
            $blockTheam3 = ceil($theam3 / $blockNum);
            $j = 0;
            $hajime = array();
            $ato = array();
            // $teamByBlock = array();

            // while ($j < $blockNum) {
            //     $teamByBlock[$j] = floor(count($teams) / $blockNum);
            //     if ($j + 1 == $blockNum) {
            //         $teamByBlock[$j] += count($teams) % $blockNum;
            //     }
            //     $hajime[$j] = floor($blockTheam3 / 2);
            //     $ato[$j] = floor($blockTheam3 / 2) + $blockTheam3 % 2;
            //     $j++;
            // }
            $block = array();
            for ( $i = 0; $i < $blockNum; $i++ ) {
                $block[] = chr(65 + $i);
            }

            $k = 0;
            $j = 0;
            $tonament = array();
            // foreach ($block as $key => $value) {
            if ($blockNum == 1) {
                $i = 0;
                while ($i < $sheetNum) {
                    if ($k < $sheetNum) {
                        $blockStr = $block[$k];
                    } else {
                        $blockStr = $block[($k % $sheetNum)];
                    }
                    $h = 0;
                    while ($h < $teamBySheet) {
                        if (empty($teams[$j])) {
                            break;
                        }

                        $team = Team::find($teams[$j]['id']);
                        $team->sheet = $i + 1;
                        $team->block = $blockStr;
                        $team->number = $h + 1;
                        $team->update();
                        $j++;
                        $h++;
                    }
                    $i++;
                }
            } else {
                while ($k < $blockNum) {
                    $i = 0;
                    $team4Cnt = 0;
                    // $blockPerTeam = ceil($teamByBlock[$i]/16);
                    // echo $teamByBlock[$i];
                    // exit;
                    while ($i < $sheetNum) {
                      if ($k < $sheetNum) {
                          $blockStr = $block[$k];
                      } else {
                          $blockStr = $block[($k % $sheetNum)];
                      }
                      $h = 0;
                      $maxPerSheetNum = 4;
                      if (24 <= $teamByBlockNum && $teamByBlockNum <= 32) {
                        // echo $team4Cnt;
                        // echo '<br>';
                        // echo $teamByBlockNum;
                        // echo '<br>';
                        // echo ($i + 1);
                        // echo '<br>aaa';
                        // exit;
                          $maxPerSheetNum = 3;
                      }
                      if (48 <= $teamByBlockNum) {
                          $team4Cnt = $teamByBlockNum - 48;
                          if ($team4Cnt < ($i + 1)) {
                            // echo $team4Cnt;
                            // echo '<br>';
                            // echo $teamByBlockNum;
                            // echo '<br>';
                            // echo ($i + 1);
                            // echo '<br>bb';
                            // exit;
                              $maxPerSheetNum = 3;
                          }
                      }
                      while ($h < $maxPerSheetNum) {
                          if (empty($teams[$j]) ||  (($k + 1) * $teamByBlockNum) < $j) {
                              break;
                          }

                          $team = Team::find($teams[$j]['id']);
                          $team->sheet = $i + 1;
                          $team->block = $blockStr;
                          $team->number = $h + 1;
                          $team->update();
                          $j++;
                          $h++;
                          $team4Cnt++;
                      }
                      $i++;
                  }
                  $k++;
              // }
                }
            }
            // 振り分けられなかった分のチェック
            // $chkTeam = Team::where('event_id', $event_id)
            // ->whereNull('block')
            // ->get();
            // foreach ($chkTeam as $val) {
            //     $sheetAndBlock = Team::select('block', 'sheet')
            //     ->where('event_id', $event_id)
            //     ->whereRaw('max(number) < 4')
            //     ->groupBy('block','sheet')
            //     ->first();
            //     print_r($sheetAndBlock);
            //     exit;
            //     $upTeam = Team::find($val->id);
            //     $upTeam->block = $sheetAndBlock->block;
            //     $upTeam->sheet = $sheetAndBlock->sheet;
            //     $upTeam->number = 4;
            //     $upTeam->update();
            // }

            FlashMessageService::success('作成が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('作成が失敗しました');
        }

        return redirect()->route('tournament.index');
    }

    public function edit(Request $request)
    {
        $event = $request->session()->get('event');
        if (!$event) {
            return redirect()->route('event.index');
        }
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        $blocks = Team::getBlocks($event);
        //$sheets = Team::getSheets($event, $selectBlock);
        $sheets = array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16);

        $teams = array();
        foreach ($sheets as $key => $value) {
            $i = 1;
            while ($i <= 4) {
                $team = Team::where('event_id', $event)
                ->where('block', $selectBlock)
                ->where('sheet', $value)
                ->where('number', $i)
                ->first();
                if ($team) {
                    $teams[] = $team;
                } else {
                    $teams[] = (object)[
                      'id' => $selectBlock.'_'.$value.'_'.$i.'_'.$event,
                      'number' => $i,
                      'name' => '',
                      'sheet' => $value,
                      'abstention' => 0,
                  ];
                }
                $i++;
            }
        }

        return view('tournament.edit',
        compact('blocks', 'selectBlock', 'teams', 'sheets'));
    }

    public function editStore(Request $request)
    {
        $event = $request->session()->get('event');
        if (!$event) {
            return redirect()->route('event.index');
        }
        try {
            \DB::transaction(function() use($request, $event) {

                $block = $request->block;
                $changeBlock = $request->changeBlock;
                $maxSheet = Team::where('event_id', $event)
                ->where('block', $changeBlock)
                ->max('sheet');
                $sheets = $request->sheet;
                foreach ($sheets as $value) {
                    $maxSheet++;
                    $teams = Team::where('event_id', $event)
                    ->where('block', $block)
                    ->where('sheet', $value)
                    ->get();
                    foreach ($teams as $v) {
                        $team = Team::find($v->id);
                        $team->block = $changeBlock;
                        $team->sheet = $maxSheet;
                        $team->save();
                    }
                }
                FlashMessageService::success('移動が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('移動が失敗しました');
        }

        return redirect()->route('tournament.edit', ['block' => $request->block]);
    }

    public function progress(Request $request)
    {
        $event = $request->session()->get('event');
        if (!$event) {
            return redirect()->route('event.index');
        }
        // 参加している対戦表のチェック
        $member = null;
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event)
            ->where('user_id', Auth::id())->first();
        }
        if (isset($member) && $member->team->block) {
            $selectBlock = $member->team->block;
        } else {
            $selectBlock = $request->block;
        }
        if (!$selectBlock) {
            $selectBlock = 1;
        }
        $selectSheet = 'progress';

        $blocks = Team::getBlocks($event);
        $sheets = Team::getSheets($event, $selectBlock);

        $progress = array();
        $results = Result::where('event_id', $event)
        ->where('block', $selectBlock)
        ->orderBy('sheet')
        ->orderBy('turn')
        ->get();
        foreach ($results as $key => $v) {
            if ($v->winteam->number == 1 || (isset($v->loseteam) && $v->loseteam->number == 1)) {
                $num = 0;
            } else {
                $num = 1;
            }
            $progress[$v->sheet][$v->turn][$num] = true;
        }
        $teams = array();
        foreach ($sheets as $value) {
            $teams[$value->sheet] = Team::where('event_id', $event)
            ->where('block', $selectBlock)
            ->where('sheet', $value->sheet)
            ->count();
        }

        return view('tournament.progress',
        compact('selectBlock', 'selectSheet', 'blocks', 'sheets', 'progress', 'teams'));
    }

    public function finalgame(Request $request)
    {
        $event_id = $request->session()->get('event');
        if (!$event_id) {
            return redirect()->route('event.index');
        }
        $event = Event::find($event_id);
        $blocks = Team::getBlocks($event->id);
        $sheets = null;
        $selectBlock = 'finalgame';
        $selectSheet = null;
        // 参加している対戦表のチェック
        $member = null;
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }
        $cnt = 0;
        $scores = [];
        $teams = [];
        foreach ($blocks as $key => $value) {
            $team = Team::where('event_id', $event->id)
            ->where('block', $value->block)
            ->where('main_rank', 1)
            ->first();
            if ($team) {
                $teams[$cnt]['id']   = $team->id;
                $teams[$cnt]['name'] = floor($cnt/2+1).'）'.$team->name;
            } else {
                $teams[$cnt]['id']   = null;
                $teams[$cnt]['name'] = floor($cnt/2+1).'）'.$value->block . 'ブロック代表';
            }
            $cnt++;
        }
        if (count($blocks)%2 == 1) {
              $tmp['id']   = null;
              $tmp['name'] = 'なし';
              $teams[] = $tmp;
        }
        $tmpNum = 0;
        $scores = array();
        foreach ($teams as $key => $value) {
            $result = null;
            $query = Result::query()->where('event_id', $event->id)
            ->where('level', 2);
            if ($value['id']) {
                $result = $query->where(function($query) use($value){
                    $query->where('win_team_id', '=', $value['id'])
                          ->orWhere('lose_team_id', '=', $value['id']);
                })->orderBy('turn', 'ASC')->get();
            }
            // $num = ($value['id']) ? $this->getTeamOrder($value['id'], $config) : 0;
            if (!$result) {
                if ($value['name'] == 'なし') {
                    if ($key%2 == 0) {
                        $scores[floor($key/2)][0] = '0';
                        $scores[floor($key/2)][1] = '3';
                    } else {
                        $scores[floor($key/2)][0] = '3';
                        $scores[floor($key/2)][1] = '0';
                    }
                }
            } else {
                foreach ($result as $k => $v) {
                  if ($v->turn == 1) {
                      $i = floor($key/2);
                  } else {
                      $h = 2;
                      $division = 4;
                      $tmp = count($teams) / 2;
                      $tmpNum = $tmp;
                      while($h < $v->turn) {
                          $tmpNum += $tmp / 2;
                          $tmp = $tmp / 2;
                          $division = $division * 2;
                          $h++;
                      }

                      $tmpNum += floor($key/$division);

                      $i = $tmpNum;
                  }
                  if ($v->win_team_id == $value['id']) {
                      $scores[$i][] = $v->win_score;
                  } elseif ($v->lose_team_id == $value['id']) {
                      $scores[$i][] = $v->lose_score;
                  }
                }
            }
        }

        ksort($scores);
        $i = 0;
        while ($i < $this->get_last_key($scores)) {
            if(empty($scores[$i])) {
                $scores[$i][0] = '';
                $scores[$i][1] = '';
            }
            $i++;
        }
        ksort($scores);
        return view('tournament.finalgame',
            compact('event',
                    'member',
                    'blocks',
                    'sheets',
                    'selectBlock',
                    'selectSheet',
                    'scores',
                    'teams')
            );
    }

    public function mainfirstgame(Request $request)
    {
        $event_id = $request->session()->get('event');
        if (!$event_id) {
            return redirect()->route('event.index');
        }
        $event = Event::find($event_id);
        // 参加している対戦表のチェック
        $member = null;
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }

        if ($member && !$request->block) {
            $selectBlock = $member->team->block;
        } else {
            $selectBlock = $request->block;
        }
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        $selectSheet = 'mainfirstgame';

        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);

        $teams = array();
        $cnt = 0;
        if ($event_id == 2) {
            $config = $this->event2gameAry();
        } else {
            $config = config('game.main'.$event->passing_order);
        }
        $tmpKey = null;
        foreach ($config as $key => $value) {
            foreach ($value as $v) {
                foreach ($v as $k => $val) {
                    $chkBlock = Team::where('event_id', $event->id)
                    ->where('block', $selectBlock)
                    ->where('sheet', $k)
                    ->count();
                    $team = Team::where('event_id', $event->id)
                    ->where('block', $selectBlock)
                    ->where('sheet', $k)
                    ->where('pre_rank', $val)
                    ->where('main_game', 1)
                    ->first();
                    if ($chkBlock == 0) {
                        if (isset($teams[$cnt-1]) && $teams[$cnt-1]['name'] == 'なし' && $tmpKey == $key) {
                            unset($teams[$cnt-1]);
                            $cnt--;
                            continue;
                        } else {
                            $teams[$cnt]['name'] = 'なし';
                            $teams[$cnt]['id'] = null;
                            $teams[$cnt]['fcode'] = null;
                            $tmpKey = $key;
                        }
                    } elseif ($team) {
                        $teams[$cnt]['name'] = $team->name;
                        if ($team->abstention == 1) {
                            $teams[$cnt]['name'] = '(棄権)'.$teams[$cnt]['name'];
                        }
                        $teams[$cnt]['id'] = $team->id;
                        $teams[$cnt]['fcode'] = $team->friend_code;
                    } else {
                        $teams[$cnt]['name'] = floor(($cnt+2)/2).'）'.$k.'-'.$val.'位通過';
                        $teams[$cnt]['id'] = null;
                        $teams[$cnt]['fcode'] = null;
                    }
                    $cnt++;
                }
            }
        }

        $tmpNum = 0;
        $scores = array();
        $teamNum = count($teams);
        if ($teamNum / 8 == 3) {
            $scores[($teamNum-2)][0] = $event->main_score;
            $scores[($teamNum-2)][1] = '0';
        }
        foreach ($teams as $key => $value) {
            $result = null;
            $query = Result::query()->where('event_id', $event->id)
            ->where('block', $selectBlock)
            ->where('level', 1);
            if ($value['id']) {
                $result = $query->where(function($query) use($value){
                    $query->where('win_team_id', '=', $value['id'])
                          ->orWhere('lose_team_id', '=', $value['id']);
                })->orderBy('turn', 'ASC')->get();
            }
            if (!$result) {
                preg_match('/[0-9+].[な][し]/u', $value['name'], $m);
                if ($m) {
                    if ($key%2 == 0) {
                        $scores[floor($key/2)][0] = '0';
                        $scores[floor($key/2)][1] = $event->main_score;
                    } else {
                        $scores[floor($key/2)][0] = $event->main_score;
                        $scores[floor($key/2)][1] = '0';
                    }
                }
            } else {
                foreach ($result as $k => $v) {
                  if ($v->turn == 1) {
                      $i = floor($key/2);
                  } else {
                      $h = 2;
                      $division = 4;
                      $tmp = count($teams) / 2;
                      $tmpNum = $tmp;
                      while($h < $v->turn) {
                          $tmpNum += $tmp / 2;
                          $tmp = $tmp / 2;
                          $division = $division * 2;
                          // $all += $tmpNum;
                          $h++;
                      }

                      $tmpNum += floor($key/$division);

                      $i = $tmpNum;
                      if ($v->turn == 5 && $event->id == 2) {
                          $i = 23;
                      }
                  }
                  if ($v->win_team_id == $value['id']) {
                      $scores[$i][] = $v->win_score;
                  } elseif ($v->lose_team_id == $value['id']) {
                      $scores[$i][] = $v->lose_score;
                  }
                }
            }
        }

        if (0 < count($scores)) {
            ksort($scores);
            $i = 0;
            $last = $this->get_last_key($scores);
            while ($i < $last) {
                if(empty($scores[$i])) {
                    $scores[$i][0] = '';
                    $scores[$i][1] = '';
                }
                $i++;
            }

            ksort($scores);
        }

        // シャッフル用dbチェック
        $this->secondDbChk($event_id, $teams, $selectBlock);
        //$num = range(1, count($teams)/2);
        //print_r($num);

        return view('tournament.mainfirstgame',
        compact('selectBlock', 'selectSheet', 'blocks', 'sheets', 'teams', 'event', 'scores', 'member'));
    }

    public static function secondDbChk ($event_id, $teams, $block) {
        $cnt = MainSecond::where('event_id', $event_id)->where('block', $block)->count();
        if ($cnt == 0) {
            //$num = range(1, count($teams)/2);
            foreach ($teams as $key => $value) {
                if (!$value['id']) {
                    $maxNum = MainSecond::where('event_id', $event_id)
                    ->where('block', $block)
                    ->orderBy('num', 'DESC')->first();
                    $second = new MainSecond();
                    $second->event_id = $event_id;
                    $second->block = $block;
                    if ($maxNum) {
                        $second->num = ($maxNum->num + 1);
                    } else {
                        $second->num = 1;
                    }
                    if ($key%2 == 0) {
                        $second->team_id = $teams[($key+1)]['id'];
                    } else {
                        $second->team_id = $teams[($key-1)]['id'];
                    }
                    $second->save();
                }
            }
        }
    }

    // public static function getRandNum () {
    //     $maxNum = MainSecond::where('event_id', $event_id)->orderBy('num', 'DESC')->first();
    //     return ($maxNum + 1);
    // }

    public function maingame(Request $request)
    {
        $event_id = $request->session()->get('event');
        if (!$event_id) {
            return redirect()->route('event.index');
        }
        $event = Event::find($event_id);
        // 参加している対戦表のチェック
        $member = null;
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }

        if ($member && !$request->block) {
            $selectBlock = $member->team->block;
        } else {
            $selectBlock = $request->block;
        }
        if (!$selectBlock) {
            $selectBlock = 'A';
        }
        $selectSheet = 'maingame';

        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);

        $teams = array();
        $cnt = 0;
        if ($event_id == 2) {
            $config = $this->event2gameAry();
        } else {
            $config = config('game.main'.$event->passing_order);
        }
        $tmpKey = null;
        if ($event->shuffle == 1) {
            $team = MainSecond::where('event_id', $event->id)
            ->where('block', $selectBlock)
            ->get();
            if (($event->passing_order == 2 && (count($team) == 16))
            or ($event->passing_order == 1 && (count($team) == 8))) {
                foreach ($team as $key => $value) {
                    $teams[$cnt]['name'] = floor(($cnt+2)/2).'）'.$value->team->name;
                    $teams[$cnt]['id'] = $value->team->id;
                    $teams[$cnt]['fcode'] = $value->team->friend_code;
                    $cnt++;
                }
            }
        } else {
            foreach ($config as $key => $value) {
                foreach ($value as $v) {
                    foreach ($v as $k => $val) {
                        $chkBlock = Team::where('event_id', $event->id)
                        ->where('block', $selectBlock)
                        ->where('sheet', $k)
                        ->count();
                        $team = Team::where('event_id', $event->id)
                        ->where('block', $selectBlock)
                        ->where('sheet', $k)
                        ->where('pre_rank', $val)
                        ->where('main_game', 1)
                        ->first();
                        if ($chkBlock == 0) {
                            if (isset($teams[$cnt-1]) && $teams[$cnt-1]['name'] == 'なし' && $tmpKey == $key) {
                                unset($teams[$cnt-1]);
                                $cnt--;
                                continue;
                            } else {
                                $teams[$cnt]['name'] = floor(($cnt+2)/2).'）なし';
                                $teams[$cnt]['id'] = null;
                                $teams[$cnt]['fcode'] = null;
                                $tmpKey = $key;
                            }
                        } elseif ($team) {
                            $teams[$cnt]['name'] = floor(($cnt+2)/2).'）'.$team->name;
                            if ($team->abstention == 1) {
                                $teams[$cnt]['name'] = '(棄権)'.$teams[$cnt]['name'];
                            }
                            $teams[$cnt]['id'] = $team->id;
                            $teams[$cnt]['fcode'] = $team->friend_code;
                        } else {
                            $teams[$cnt]['name'] = floor(($cnt+2)/2).'）'.$k.'-'.$val.'位通過';
                            $teams[$cnt]['id'] = null;
                            $teams[$cnt]['fcode'] = null;
                        }
                        $cnt++;
                    }
                }
            }
        }
        $tmpNum = 0;
        $scores = array();
        $teamNum = count($teams);
        if ($teamNum / 8 == 3) {
            $scores[($teamNum-2)][0] = $event->main_score;
            $scores[($teamNum-2)][1] = '0';
        }

        foreach ($teams as $key => $value) {
            $result = null;
            $query = Result::query()->where('event_id', $event->id)
            ->where('block', $selectBlock)
            ->where('level', 1);
            if ($value['id']) {
                $result = $query->where(function($query) use($value){
                    $query->where('win_team_id', '=', $value['id'])
                          ->orWhere('lose_team_id', '=', $value['id']);
                })->orderBy('turn', 'ASC')->get();
            }
            if (!$result) {
                preg_match('/[0-9+].[な][し]/u', $value['name'], $m);
                if ($m) {
                    if ($key%2 == 0) {
                        $scores[floor($key/2)][0] = '0';
                        $scores[floor($key/2)][1] = $event->main_score;
                    } else {
                        $scores[floor($key/2)][0] = $event->main_score;
                        $scores[floor($key/2)][1] = '0';
                    }
                }
            } else {
                foreach ($result as $k => $v) {
                  if ($event->shuffle == 1 && $v->turn == 1) {
                      continue;
                  }
                  if (($v->turn == 1 && $event->shuffle != 1) ||
                      ($v->turn == 2 && $event->shuffle == 1)) {
                      $i = floor($key/2);
                  } else {
                      if ($event->shuffle == 1) {
                          $h = 3;
                          $division = 4;
                          $tmp = count($teams) / 2;
                          $tmpNum = $tmp;
                          while($h < $v->turn) {
                              $tmpNum += $tmp / 2;
                              $tmp = $tmp / 2;
                              $division = $division * 2;
                              // $all += $tmpNum;
                              $h++;
                          }

                          $tmpNum += floor($key/$division);

                          $i = $tmpNum;
                      } else {
                          $h = 2;
                          $division = 4;
                          $tmp = count($teams) / 2;
                          $tmpNum = $tmp;
                          while($h < $v->turn) {
                              $tmpNum += $tmp / 2;
                              $tmp = $tmp / 2;
                              $division = $division * 2;
                              // $all += $tmpNum;
                              $h++;
                          }

                          $tmpNum += floor($key/$division);

                          $i = $tmpNum;
                          if ($v->turn == 5 && $event->id == 2) {
                              $i = 23;
                          }
                      }
                  }
                  if ($v->win_team_id == $value['id']) {
                      $scores[$i][] = $v->win_score;
                  } elseif ($v->lose_team_id == $value['id']) {
                      $scores[$i][] = $v->lose_score;
                  }
                }
            }
        }

        if (0 < count($scores)) {
            ksort($scores);
            $i = 0;
            $last = $this->get_last_key($scores);
            while ($i < $last) {
                if(empty($scores[$i])) {
                    $scores[$i][0] = '';
                    $scores[$i][1] = '';
                }
                $i++;
            }

            ksort($scores);
        }
//         print_r($scores);
// exit;
        return view('tournament.maingame',
        compact('selectBlock', 'selectSheet', 'blocks', 'sheets', 'teams', 'event', 'scores', 'member'));
    }

    private function get_last_key($array)
    {
        $keys = array_keys($array);
        return ($keys[count($array)-1]) ?? $keys;
    }

    public static function getTeamOrder($team_id, $config)
    {
        $team = Team::find($team_id);
        $num = 1;
        foreach ($config as $key => $tmp) {
            $cnt = 0;
            foreach ($tmp as $v) {
                foreach ($v as $k => $a) {
                    if ($team->sheet == $k && $team->pre_rank == $a) {
                        $num += $cnt;
                        break;
                    }
                }
                $cnt++;
            }
        }
        return $num;
    }

    public function teamlist(Request $request)
    {
        $event_id = $request->session()->get('event');
        if (!$event_id) {
            return redirect()->route('event.index');
        }
        $event = Event::find($event_id);
        // 参加している対戦表のチェック
        $member = null;
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $event->id)
            ->where('user_id', Auth::id())->first();
        }
        // if ($member) {
        //     $selectBlock = $member->team->block;
        // } else {
            $selectBlock = $request->block;
        // }
        if (!$selectBlock) {
            $selectBlock = 1;
        }
        $selectSheet = 'teamlist';

        $blocks = Team::getBlocks($event->id);
        $sheets = Team::getSheets($event->id, $selectBlock);

        $teams = Team::where('block', $selectBlock)
        ->where('event_id', $event_id)
        ->orderBy('sheet')
        ->orderBy('number')
        ->get();

        return view('tournament.teamlist',
        compact('selectBlock', 'selectSheet', 'blocks', 'sheets', 'teams', 'event'));
    }

    private function event2gameAry() {

        $array = [
                0 => [
                    ['11' => '1'],
                    ['4' => '2'],
                ],
                1 => [
                    ['2' => '1'],
                    ['3' => '2'],
                ],
                2 => [
                    ['11' => '2'],
                    ['1' => '1'],
                ],
                3 => [
                    ['2' => '2'],
                    ['4' => '1'],
                ],
                4 => [
                    ['1' => '2'],
                    ['3' => '1'],
                ],
                5 => [
                    ['12' => '1'],
                    ['5' => '1'],
                ],
                6 => [
                    ['8' => '2'],
                    ['6' => '1'],
                ],
                7 => [
                    ['12' => '2'],
                    ['7' => '1'],
                ],
                8 => [
                    ['9' => '1'],
                    ['6' => '2'],
                ],
                9 => [
                    ['10' => '1'],
                    ['8' => '1'],
                ],
                10 => [
                    ['5' => '2'],
                    ['10' => '2'],
                ],
                11 => [
                    ['7' => '2'],
                    ['9' => '2'],
                ]
        ];
        return $array;
    }
}
