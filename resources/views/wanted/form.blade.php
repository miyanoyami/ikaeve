@include('elements.flash_message')
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right required">募集者</label>

    <div class="col-md-6">
        @if (isset($data))
            {{ $data->user->name }}
            @if($data->user->twitter_nickname)
                &nbsp;<a href="https://twitter.com/{{ $data->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
            @endif
        @else
            {{ $user->name }}
            @if($user->twitter_nickname)
                &nbsp;<a href="https://twitter.com/{{ $user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
            @endif
        @endif
    </div>
</div>
<div class="form-group row">
    <label for="xp" class="col-md-4 col-form-label text-md-right">最高xp</label>

    <div class="col-md-3 col-6">
        <input id="xp" type="text" class="form-control @error('xp') is-invalid @enderror" name="xp"
        value="{{ old('xp', isset($data) ? $data->xp : '') }}" autocomplete="xp">
    </div>
</div>
<div class="form-group row">
    <label for="wepons" class="col-md-4 col-form-label text-md-right"></label>
    
    <div class="col-md-3">
      <select multiple="multiple" id="selecter" name="wepons[]" class="form-control">
        @foreach (config('wepons.all') as $key => $val)
         <option value='{{ $key }}' {{ (isset($data) && $data->selectWepon($key) == true) ? 'selected' : ''}}>{{ $val }}</option>
        @endforeach
      </select>
    </div>
</div>
<div class="form-group row mt-4">
    <label for="note" class="col-md-4 col-form-label text-md-right"></label>

    <div class="col-md-6">
        <textarea class="form-control" name="note" placeholder="自己PRやどんな相方さんがいいかなど">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
    </div>
</div>
