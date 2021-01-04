<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\TeamRequest;
use App\Models\User;
use App\Models\Event;
use App\Models\Team;
use App\Models\Member;
use App\Models\Answer;

class TeamController extends Controller
{
    // public function __construct()
    // {
    //    $this->middleware('auth');
    // }

    public function index(Request $request)
    {
        $search['event'] = $request->session()->get('event');
        $datas = Team::where('event_id', $search['event'])->orderBy('id', 'DESC')->get();
        $events = Event::orderBy('id', 'DESC')->get();
        return view('team.index', compact('datas', 'events'));
    }

    public function indexStore(Request $request)
    {
        $search['event'] = $request->session()->get('event');
        $search['name'] = $request->name;
        $search['member_name'] = $request->member_name;
        $search['approval'] = $request->approval;

        $query = Team::query();
        $query->select('teams.*');
        $query->join('members', 'members.team_id', '=', 'teams.id');
        if ($search['event']) {
            $query->where('event_id', $search['event']);
        }
        if ($search['name']) {
            $query->where('teams.name', 'LIKE', '%'.$search['name'].'%');
        }
        if ($search['member_name']) {
            $query->where('members.name', 'LIKE', '%'.$search['member_name'].'%');
        }
        if (isset($search['approval'])) {
            $query->where('approval', $search['approval']);
        }
        $datas = $query->groupBy('teams.id')->orderBy('teams.id', 'DESC')->get();
        return view('team.index', compact('datas', 'search'));
    }

    public function regist(Request $request)
    {
        $event_id = $request->session()->get('event');
        $event = Event::find($event_id);
        return view('team.regist', compact('event'));
    }

    public function registStore(TeamRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = new Team();
                $data->name = $request->name;
                $data->friend_code = join('', $request->friend_code);
                $data->note = $request->note;
                $data->event_id = $request->session()->get('event');
                $data->save();

                $names = $request->member_name;
                $twitters = $request->twitter;
                $xps = $request->xp;
                $ids = $request->member_id;
                foreach ($ids as $k => $val) {
                    $member = new Member();
                    $member->team_id = $data->id;
                    $member->name = $names[$k];
                    $member->twitter = $twitters[$k];
                    $member->xp = $xps[$k];
                    $member->save();
                    if ($k == 0) {
                        $data->member_id = $member->id;
                        $data->update();
                    }
                }
                $questions = $request->question;
                $answers = $request->answer;
                foreach ($questions as $k => $val) {
                    $answer = new Answer();
                    $answer->team_id = $data->id;
                    $answer->question_id = $val;
                    $answer->note = $answers[$k];
                    $answer->save();
                }

            });

            FlashMessageService::success('編集が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
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
        $data = Team::find($request->id);
        if ($data->pass != $request->pass && !Auth::check()) {
            FlashMessageService::error('パスワードが違います');
            return redirect()->route('team.edit', ['id' => $request->id]);
        } else {
            try {
                \DB::transaction(function() use($request, $data) {

                    $ids = $request->member_id;
                    $data->name = $request->name;
                    $data->friend_code = join('', $request->friend_code);
                    $data->note = $request->note;
                    $data->member_id = $ids[0];
                    $data->save();

                    $names = $request->member_name;
                    $twitters = $request->twitter;
                    $xps = $request->xp;
                    foreach ($ids as $k => $val) {
                        $data = Member::find($val);
                        $data->name = $names[$k];
                        $data->twitter = $twitters[$k];
                        $data->xp = $xps[$k];
                        $data->update();
                    }

                    $questions = $request->question;
                    $answers = $request->answer;
                    $aIds = $request->answer_id;
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

                });

                FlashMessageService::success('編集が完了しました');

            } catch (\Exception $e) {
                report($e);
                FlashMessageService::error('編集が失敗しました');
            }

            return redirect()->route('team.index');
        }
    }

    public function update($id, $column, $value)
    {
        $this->middleware('auth');
        try {
            \DB::transaction(function() use($id, $column, $value) {

                $data = Team::find($id);
                if ($column == 'approval') {
                    $data->approval = $value;
                } else {
                    $data->abstention = $value;
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
}