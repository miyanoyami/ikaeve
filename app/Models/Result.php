<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Result;

class Result extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected static function boot()
    {
        parent::boot();
    }

    public function winteam()
    {
        return $this->belongsTo('App\Models\Team', 'win_team_id', 'id');
    }

    public function loseteam()
    {
        return $this->belongsTo('App\Models\Team', 'lose_team_id', 'id');
    }

    public static function chkResult($block, $sheet, $turn, $team_id=null)
    {
        if (!$team_id) {
          $team_id = $this->team_id;
        }
        $result = Result::where('lose_team_id', $team_id)
        ->where('block', $block)
        ->where('sheet', $sheet)
        ->where('turn', $turn)
        ->first();
        $result2 = Result::where('win_team_id', $team_id)
        ->where('block', $block)
        ->where('sheet', $sheet)
        ->where('turn', $turn)
        ->first();
        if ($result) {
            return $result;
        } elseif ($result2) {
            return $result2;
        } else {
            return null;
        }
    }

}
