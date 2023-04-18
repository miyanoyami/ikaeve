<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Models\Team;
use App\Models\Member;

class EventController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $request->session()->forget('event');
        $request->session()->forget('eventName');
        $request->session()->forget('block');
        if (Auth::user()->role == config('user.role.admin')) {
            $datas = Event::orderBy('id', 'DESC')->get();
        } else {
            $datas = Event::where('view', 0)->orderBy('id', 'DESC')->get();
        }
        return view('event.index', compact('datas'));
    }

    public function regist()
    {
        if (!$this->checkAdmin()) {
            return redirect()->route('event.index');
        }
        return view('event.regist');
    }

    public function registStore(EventRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = new Event();
                $data->name = $request->name;
                $data->from_recruit_date = $request->from_recruit_date;
                $data->to_recruit_date = $request->to_recruit_date;
                $data->from_date = $request->from_date;
                $data->to_date = $request->to_date;
                $data->team_member = $request->team_member;
                $data->passing_order = $request->passing_order;
                $data->pre_score = $request->pre_score;
                $data->pre_rule1 = $request->pre_rule1;
                $data->pre_rule2 = $request->pre_rule2;
                $data->pre_rule3 = $request->pre_rule3;
                $data->main_score = $request->main_score;
                $data->final_score = $request->final_score;
                $data->note = $request->note;
                $data->view = $request->view;
                $data->point = $request->point;
                $data->grade = $request->grade;
                $data->shuffle = $request->shuffle;
                $data->save();

            });

            FlashMessageService::success('登録が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('登録が失敗しました');
        }

        return redirect()->route('event.index');
    }

    public function edit(Request $request)
    {
        if (!$this->checkAdmin()) {
            return redirect()->route('event.index');
        }
        $data = Event::find($request->id);
        return view('event.edit', compact('data'));
    }

    public function editStore(EventRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = Event::find($request->id);
                $data->name = $request->name;
                $data->from_recruit_date = $request->from_recruit_date;
                $data->to_recruit_date = $request->to_recruit_date;
                $data->from_date = $request->from_date;
                $data->to_date = $request->to_date;
                $data->team_member = $request->team_member;
                $data->passing_order = $request->passing_order;
                $data->pre_score = $request->pre_score;
                $data->pre_rule1 = $request->pre_rule1;
                $data->pre_rule2 = $request->pre_rule2;
                $data->pre_rule3 = $request->pre_rule3;
                $data->main_score = $request->main_score;
                $data->final_score = $request->final_score;
                $data->note = $request->note;
                $data->view = $request->view;
                $data->grade = $request->grade;
                $data->point = $request->point;
                $data->shuffle = $request->shuffle;
                $data->update();

            });

            FlashMessageService::success('編集が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }

        return redirect()->route('event.index');
    }

    public function detail(Request $request)
    {
        $request->session()->put('event', $request->id);
        $data = Event::find($request->id);
        if (!$data) {
          return redirect()->route('event.index');
        }
        $request->session()->put('eventName', $data->name);

        $member = array();
        // 参加している対戦表のチェック
        if (Auth::user()->role == config('user.role.member')) {
            $member = Member::join('teams', 'teams.id', 'members.team_id')
            ->where('event_id', $data->id)
            ->where('user_id', Auth::id())->first();
        }

        $dt = new Carbon();
        $recruitBtnView = Carbon::parse($dt)->between($data->to_recruit_date, $data->from_recruit_date);
        $makeBtnView = Carbon::parse($dt)->between($data->from_recruit_date, $data->from_date);
        return view('event.detail', compact('data', 'recruitBtnView', 'makeBtnView', 'member'));
    }
}
