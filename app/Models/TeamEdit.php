<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamEdit extends BaseModel
{
    use SoftDeletes;
    protected $dates = ['created_at','updated_at','deleted_at'];
    protected static function boot()
    {
        parent::boot();
    }

    public function event()
    {
        return $this->belongsTo('App\Models\Event');
    }
}
