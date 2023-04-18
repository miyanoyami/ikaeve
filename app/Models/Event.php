<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Team;

class Event extends BaseModel
{
    use SoftDeletes;
    protected $dates = [
      'from_recruit_date',
      'to_recruit_date',
      'from_date',
      'to_date',
      'created_at',
      'updated_at',
      'deleted_at'
    ];
    protected static function boot()
    {
        parent::boot();
    }

    public function team()
    {
        return $this->hasMany('App\Models\Team');
    }

    public static function approved_team($id)
    {
        return Team::where('event_id', $id)->where('approval', 1)->count();
    }

    public function question()
    {
        return $this->hasMany('App\Models\Question');
    }

    public function rankTeam($rank)
    {
        return Team::where('event_id', $this->id)
        ->where('final_rank', $rank)
        ->get();
    }
}
