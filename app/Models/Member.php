<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Result;

class Member extends BaseModel
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

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function isLose($block, $sheet, $turn)
    {
        $approval = 0;
        $result = Result::where('lose_team_id', $this->team_id)
        ->where('block', $block)
        ->where('sheet', $sheet)
        ->where('turn', $turn)
        ->where('approval', 0)
        ->first();
        if ($result) {
            if ($result->approval == 0) {
                $approval = 1;
            } else {
                $approval = 2;
            }
        }

        return $approval;
    }
}
