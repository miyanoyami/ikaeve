<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\EventRequest;
use App\Http\Requests\PasswordRequest;
use Carbon\Carbon;
use App\Models\User;

class UserController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function registStore(Request $request)
    {
        try {
            \DB::transaction(function() use($request) {

                $data = new User();
                $data->name = $request->name;
                $data->email = $request->mail;
                $data->password = Hash::make($request->password);
                $data->role = config('user.role.staff');
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
                $data->save();
                FlashMessageService::success('編集が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }

        return redirect()->route('staff.index');
    }

    public function password()
    {
        return view('user.password');
    }

    public function passwordStore(PasswordRequest $request){
        try {
            \DB::transaction(function() use($request) {

                $user = \Auth::user();
                $user->password = Hash::make($request->password);
                $user->save();
                FlashMessageService::success('パスワード変更が完了しました');
            });

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('パスワード変更が失敗しました');
        }

        return redirect()->route('user.password');
    }

}
