<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\Event;
use App\Models\Team;
use App\Models\Point;

class RankingController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $datas = Point::selectRaw('user_id, sum(point) as point')
            ->groupBy('user_id')
            ->orderBy('point', 'DESC')
            ->get();
        return view('ranking.index', compact('datas'));
    }

    public function point(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                  $req = $request->session()->get('event');
                  $event = Event::find($req);
                  if (!$event) {
                    return redirect()->route('event.index');
                  }
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

            });

            FlashMessageService::success('ポイントを付与しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('ポイントを付与が失敗しました');
        }
        return redirect()->route('event.index');
    }

    public function history(Request $request)
    {
        $datas = Point::where('points.user_id', $request->id)
        ->join('events', 'events.id', '=', 'points.event_id')
        ->orderBy('from_date')->get();
        return view('ranking.history', compact('datas'));
    }

}
