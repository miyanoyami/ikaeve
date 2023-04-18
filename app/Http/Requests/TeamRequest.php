<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

class TeamRequest extends FormRequest
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
            'name' => 'required|max:50',
            'friend_code.*' => 'required|digits:4',
            'pass' => 'integer|digits:4',
            'member_name.*' => 'required|max:50',
            'twitter.*' => 'required|max:20',
            //'twitter_id.*' => 'required',
            'xp.*' => 'max:20',
        ];
        return $return;
    }

    public function attributes()
    {
        return [
            'name' => 'チーム名',
            'pass' => 'パスワード',
            'friend_code.*' => 'フレンドコード',
            'member_name.*' => 'メンバー名',
            'twitter.*' => 'twitter',
            'twitter_id.*' => 'twitterId',
            'xp.*' => 'xp',
        ];
    }

    public function messages()
    {
        return [
            'twitter_id.*.required' => '有効なTwitter名を入力してください。',
        ];
    }
}
