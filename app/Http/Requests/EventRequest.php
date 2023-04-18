<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(Request $request)
    {
        $return = [
            'name' => 'required|max:255',
            'from_recruit_date' => 'date_format:Y/m/d H:i',
            'to_recruit_date' => 'date_format:Y/m/d H:i',
            'from_date' => 'date_format:Y/m/d H:i',
            'to_date' => 'date_format:Y/m/d H:i',
        ];
        return $return;
    }

    public function attributes()
    {
        return [
            'name' => '大会名',
            'from_recruit_date' => '募集日時',
            'to_recruit_date' => '募集日時',
            'from_date' => '開催日時',
            'to_date' => '開催日時',
        ];
    }
}
