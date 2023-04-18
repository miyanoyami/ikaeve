<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use Carbon\Carbon;
use App\Models\User;

class StaffController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search['name'] = $request->name;
        $search['mail'] = $request->mail;
        $query = User::query();
        $query->where(function($query) use($search){
            $query->where('role', config('user.role.staff'))
                  ->orWhere('role', config('user.role.admin'));
        });
        if (isset($search['name'])) {
            $query->where('name', 'LIKE', '%'.$search['name'].'%');
        }
        if (isset($search['mail'])) {
            $query->where('email', 'LIKE', '%'.$search['mail'].'%');
        }
        $datas = $query->paginate(config('common.page_num'));
        return view('staff.index', compact('datas', 'search'));
    }

    public function regist()
    {
        return view('staff.regist');
    }

    public function registStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = new User();
                $data->name = $request->name;
                $data->email = $request->mail;
                $data->role = $request->role;
                $data->password = Hash::make($request->password);
                //$data->role = config('user.role.staff');
                $data->save();
                FlashMessageService::success('登録が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('登録が失敗しました');
        }

        return redirect()->route('staff.index');
    }

    public function edit($id)
    {
        $data = User::find($id);
        return view('staff.regist', compact('data'));
    }

    public function editStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = User::find($request->user_id);
                $data->name = $request->name;
                $data->email = $request->mail;
                $data->role = $request->role;
                $data->save();
                FlashMessageService::success('編集が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }

        return redirect()->route('staff.index');
    }

}
