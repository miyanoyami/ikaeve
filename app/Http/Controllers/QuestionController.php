<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Service\FlashMessageService;
use App\Http\Requests\QuestionRequest;
use App\Models\User;
use App\Models\Event;
use App\Models\Question;

class QuestionController extends Controller
{
    public function __construct()
    {
       $this->middleware('auth');
    }

    public function edit($id)
    {
        $datas = Question::where('event_id', $id)->get();
        return view('question.edit', compact('datas'));
    }

    public function editStore(QuestionRequest $request)
    {
        try {
            \DB::transaction(function() use($request) {
                $ids = $request->target;
                $titles = $request->title;
                $requireds = $request->required;
                Question::whereNotIn('id', $ids)->delete();
                foreach ($ids as $k => $val) {
                    $data = Question::find($val);
                    if (!$data) {
                        $data = new Question();
                        $data->event_id = $request->id;
                    }
                    $data->title = $titles[$k];
                    $data->required = $requireds[$k];
                    $data->save();
                }
            });

            FlashMessageService::success('編集が完了しました');

        } catch (\Exception $e) {
            report($e);
            FlashMessageService::error('編集が失敗しました');
        }

        return redirect()->route('question.edit', ['id' => $request->id]);
    }

}
