<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Member;
use App\Models\Question;

class Answer extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected static function boot()
    {
        parent::boot();
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team');
    }

    public function question()
    {
        return $this->belongsTo('App\Models\Question');
    }

    public static function get($question_id, $team_id)
    {
        $answer =  Answer::where('team_id', $team_id)
        ->join('questions', 'questions.id', '=', 'answers.question_id')
        ->where('question_id', $question_id)
        ->first();
        if ($answer) {
            return $answer->note;
        } else {
           return '';
        }
    }

    public static function getArray($data)
    {
        $array = array();
        foreach ($data as $key => $value) {
            $chk = Question::find($value->question_id);
            if (!$chk) {
                continue;
            }
            $array[$value->question_id]['id'] = $value->id;
            $array[$value->question_id]['note'] = $value->note;
        }
        return $array;
    }
}
