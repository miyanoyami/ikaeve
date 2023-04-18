<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\MemberRequest;
use App\Models\User;
use App\Models\Event;
use App\Models\Member;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $search['event'] = $request->session()->get('event');
        $query = Member::query();
        $query->select('members.*')
        ->join('teams', 'teams.id', '=', 'members.team_id')
        ->where('event_id', $search['event']);
        $datas = $query->orderBy('id', 'DESC')->get();
        $events = Event::orderBy('id', 'DESC')->get();
        return view('member.index', compact('datas', 'events'));
    }

    public function indexStore(Request $request)
    {
        $search['event'] = $request->event;
        $search['team_name'] = $request->team_name;
        $search['name'] = $request->name;

        $query = Member::query();
        $query->select('members.*');
        $query->join('teams', 'teams.id', '=', 'members.team_id');
        if ($search['event']) {
            $query->where('event_id', $search['event']);
        }
        if ($search['team_name']) {
            $query->where('teams.name', 'LIKE', '%'.$search['team_name'].'%');
        }
        if ($search['name']) {
            $query->where('members.name', 'LIKE', '%'.$search['name'].'%');
        }
        $datas = $query->orderBy('id', 'DESC')->get();
        $events = Event::orderBy('id', 'DESC')->get();
        return view('member.index', compact('datas', 'events', 'search'));
    }

    public function edit(Request $request)
    {
        $data = Member::find($request->id);
        return view('member.edit', compact('data'));
    }

    public function editStore(MemberRequest $request)
    {
        $data = Member::find($request->id);
        if ($data->team->pass != $request->pass && !Auth::check()) {
            FlashMessageService::error('パスワードが違います');
            return redirect()->route('member.edit', ['id' => $request->id]);
        } else {
            try {
                \DB::transaction(function() use($request) {

                    $data = Member::find($request->id);
                    $data->name = $request->name;
                    $data->twitter = $request->twitter;
                    $data->discord = $request->discord;
                    $data->xp = $request->xp;
                    $data->save();

                });

                FlashMessageService::success('編集が完了しました');

            } catch (\Exception $e) {
                report($e);
                FlashMessageService::error('編集が失敗しました');
            }
        }

        return redirect()->route('member.index');
    }

    public function detail(Request $request)
    {
        $data = Member::find($request->id);
        return view('member.detail', compact('data'));
    }
}
