<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Models\Wanted;

class WantedController extends Controller
{
    public function index(Request $request)
    {
        $event = $request->session()->get('event');
        $datas = Wanted::where('event_id', $event)->orderBy('id', 'DESC')->get();
        return view('wanted.index', compact('datas'));
    }

    public function regist()
    {
        $user = Auth::user();
        return view('wanted.regist', compact('user'));
    }

    public function registStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $event = $request->session()->get('event');
                $data = new Wanted();
                $data->user_id = Auth::id();
                $data->xp = $request->xp;
                if ($request->wepons) {
                    $data->wepons = implode(',', $request->wepons);
                }
                $data->note = $request->note;
                $data->event_id = $event;
                $data->save();

            });

            FlashMessageService::success('登録が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('登録が失敗しました');
        }

        return redirect()->route('wanted.index');
    }

    public function edit(Request $request)
    {
        $data = Wanted::find($request->id);
        $user = $data->user;
        return view('wanted.edit', compact('data', 'user'));
    }

    public function editStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = Wanted::find($request->id);
                $data->xp = $request->xp;
                if ($request->wepons) {
                    $data->wepons = implode(',', $request->wepons);
                }
                $data->note = $request->note;
                $data->save();

            });

            FlashMessageService::success('編集が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }
        return redirect()->route('wanted.index');
    }

    public function detail(Request $request)
    {
        $data = Wanted::find($request->id);
        return view('wanted.detail', compact('data'));
    }

    public function deleteStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = Wanted::find($request->id);
                $data->delete();

            });

            FlashMessageService::success('削除が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('削除が失敗しました');
        }

        return redirect()->route('wanted.index');
    }
}
