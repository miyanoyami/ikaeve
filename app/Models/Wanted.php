<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wanted extends BaseModel
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

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function weponStr()
    {
        $config = config('wepons.all');
        $wepons = array();
        foreach ($config as $key => $value) {
            $wepons[$key] = $value;
        }

        $str = '';
        $db = $this->wepons;
        $array = explode(',', $db);
        foreach ($array as $value) {
            if (!empty($wepons[$value])) {
                $str .= '<a href="#" class="badge badge-primary">'.$wepons[$value].'</a> ';
            }
        }
        return $str;
    }

    public function selectWepon($select)
    {
        $selectFlg = false;
        $db = $this->wepons;
        $array = explode(',', $db);
        foreach ($array as $value) {
            if ($value == $select) {
                $selectFlg = true;
                break;
            }
        }
        return $selectFlg;
    }
}
