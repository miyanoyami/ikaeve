<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class MainGame extends BaseModel
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

}
