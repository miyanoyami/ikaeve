<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Models\MainGame;

class TestController extends Controller
{
    public function type1(Request $request)
	{
        if ($request->type='admin') {
            $myinfo = User::where('role', config('user.role.admin'))->first();
        } else {
            $myinfo = User::where('role', config('user.role.member'))->first();
        }
        Auth::login($myinfo);
        $event = 1;
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 1;
        }
        $request->session()->put('block', $selectBlock);

        $blocks = Team::getBlocks($event);
        $sheets = Team::getSheets($event, $selectBlock);

        if (count($sheets) < 1) {
            $request->session()->forget('block');
            FlashMessageService::error('対戦表はまだ作成されてません');
            return redirect()->route('event.detail', ['id' => session('event')]);
        }

        $results = Team::where('event_id', $event)
        ->where('block', $selectBlock)
        ->orderBy('sheet')
        ->orderBy('number')
        ->get();
        $teams = array();
        foreach ($results as $key => $value) {
            $teams[$value->number]['id'] = $value->id;
            $teams[$value->number]['name'] = $value->name;
            $teams[$value->number]['abstention'] = $value->abstention;
        }

        return view('test.type1',
        compact('selectBlock', 'sheets', 'teams', 'blocks'));
    }

    public function type2(Request $request)
    {
        if ($request->type='admin') {
            $myinfo = User::where('role', config('user.role.admin'))->first();
        } else {
            $myinfo = User::where('role', config('user.role.member'))->first();
        }
        Auth::login($myinfo);
        $event = 1;
        $selectBlock = $request->block;
        if (!$selectBlock) {
            $selectBlock = 1;
        }
        $selectSheet = 'A';
        $request->session()->put('block', $selectBlock);

        $blocks = Team::getBlocks($event);
        $sheets = Team::getSheets($event, $selectBlock);

        if (count($sheets) < 1) {
            $request->session()->forget('block');
            FlashMessageService::error('対戦表はまだ作成されてません');
            return redirect()->route('event.detail', ['id' => session('event')]);
        }

        $results = Team::where('event_id', $event)
        ->where('block', $selectBlock)
        ->orderBy('sheet')
        ->orderBy('number')
        ->get();
        $teams = array();
        foreach ($results as $key => $value) {
            $teams[$value->number]['id'] = $value->id;
            $teams[$value->number]['name'] = $value->name;
            $teams[$value->number]['abstention'] = $value->abstention;
        }

        return view('test.type2',
        compact('selectBlock', 'selectSheet', 'sheets', 'teams', 'blocks'));
    }

}
