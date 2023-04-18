@include('elements.flash_message')
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right required">チーム名</label>

    <div class="col-md-6">
        <input type="hidden" name="team_id" id="team_id" value="{{ isset($data) ? $data->id : '' }}">
        <input type="hidden" name="event_id" id="event_id" value="{{ session('event') }}">
        <input id="teamname" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required autocomplete="name">
        @if($errors->has('name')) <span class="text-danger">{{ $errors->first('name') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="friend_code" class="col-md-4 col-form-label text-md-right required">フレンドコード</label>

    <div class="col-md-2 col-4">
        <input id="friend_code1" type="text" class="form-control friend_code @error('friend_code') is-invalid @enderror" name="friend_code[]"
        value="{{ old('friend_code.0', isset($data) ? substr($data->friend_code, 0, 4) : '') }}" maxlength="4" required>
    </div>
    <div class="col-md-2 col-4">
        <input id="friend_code2" type="text" class="form-control friend_code @error('friend_code') is-invalid @enderror" name="friend_code[]"
        value="{{ old('friend_code.1', isset($data) ? substr($data->friend_code, 4, 4) : '') }}" maxlength="4" required>
    </div>
    <div class="col-md-2 col-4">
        <input id="friend_code3" type="text" class="form-control @error('friend_code') is-invalid @enderror" name="friend_code[]"
        value="{{ old('friend_code.2', isset($data) ? substr($data->friend_code, 8, 4) : '') }}" maxlength="4" required>
    </div>
</div>
@if($errors->has('friend_code.*'))
  <div class="form-group row">
      <label for="error" class="col-md-4 col-form-label text-md-right"></label>
      <div class="col-md-6">
          <span class="text-danger">{{ $errors->first('friend_code.*') }}</span>
      </div>
  </div>
@endif
@for ($i = 0; $i < $event->team_member; $i++)
    <input type="hidden" name="member_id[]" value="{{ isset($members[$i]) ? $members[$i]->id : '0' }}">
    <div class="form-group row">
        <label for="member_name[]" class="col-md-4 col-form-label text-md-right required">メンバー{{ ($i+1) }}</label>
        <div class="col-md-2">
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="member_name[]"
            value="{{ old('member_name.'.$i, isset($members[$i]) ? $members[$i]->name : '') }}" required placeholder="メンバー名">
        </div>
        <div class="input-group col-md-2">
              <input type="text" class="form-control @error('twitter') is-invalid @enderror" name="twitter[]"
              value="{{ old('twitter.'.$i, isset($members[$i]) ? $members[$i]->user->twitter_nickname : '') }}" required placeholder="twitter(@以降)">
              <input type="hidden" name="twitter_id[]" value="{{ old('twitter_id.'.$i, isset($members[$i]) ? $members[$i]->user->twitter_id : '') }}">
              <div class="input-group-append">
                <span class="input-group-text">
                  <a name="twitterlink[]" href="#"><i class="fab fa-twitter fa-lg"></i></a>
                </span>
              </div>
        </div>
        <div class="col-md-2">
            <input type="text" class="form-control @error('xp') is-invalid @enderror" name="xp[]"
            value="{{ old('xp.'.$i, isset($members[$i]) ? $members[$i]->xp : '') }}" placeholder="xp">
        </div>
    </div>
@endfor
@if($errors->has('twitter_id.*'))
  <div class="form-group row">
      <label for="error" class="col-md-4 col-form-label text-md-right"></label>
      <div class="col-md-6">
          <span class="text-danger">{{ $errors->first('twitter_id.*') }}</span>
      </div>
  </div>
@endif
@foreach ($event->question as $k => $question)
    <div class="form-group row">
        <label for="error" class="col-md-4 col-form-label text-md-right @if ($question->required == 1) required @endif">{{ $question->title }}</label>
        <div class="col-md-6">
            <input type="hidden" name="question[]" value="{{ $question->id }}">
            <input type="hidden" name="answer_id[]" value="{{ isset($answer[$question->id]) ? $answer[$question->id]['id'] : '0' }}">
            <input id="answer[]" type="text" class="form-control @error('answer') is-invalid @enderror" name="answer[]"
            value="{{ old('answer.'.$k, isset($answer[$question->id]) ? $answer[$question->id]['note'] : '') }}" @if ($question->required == 1) required @endif>
        </div>
    </div>
@endforeach
<div class="form-group row">
    <label for="note" class="col-md-4 col-form-label text-md-right">意気込みなど</label>

    <div class="col-md-6">
        <textarea class="form-control" name="note">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
    </div>
</div>
<input type="hidden" name="twitterApi" id="twitterApi" value="{{ route('twitter.getId') }}">
<input type="hidden" name="teamApi" id="teamApi" value="{{ route('team.check') }}">
