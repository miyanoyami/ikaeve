@include('elements.flash_message')
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">チーム名</label>

    <div class="col-md-6">
        {{ $data->team->name }}
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right required">メンバー名</label>

    <div class="col-md-6">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required autocomplete="name" autofocus>
        @if($errors->has('name')) <span class="text-danger">{{ $errors->first('name') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right">twitter</label>

    <div class="col-md-6">
        <input id="twitter" type="text" class="form-control @error('twitter') is-invalid @enderror" name="twitter"
        value="{{ old('twitter', isset($data) ? $data->twitter : '') }}" required autocomplete="twitter" autofocus>
        @if($errors->has('twitter')) <span class="text-danger">{{ $errors->first('twitter') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="xp" class="col-md-4 col-form-label text-md-right">xp</label>

    <div class="col-md-6">
        <input id="xp" type="text" class="form-control @error('xp') is-invalid @enderror" name="xp"
        value="{{ old('xp', isset($data) ? $data->xp : '') }}" required autocomplete="xp" autofocus>
        @if($errors->has('xp')) <span class="text-danger">{{ $errors->first('xp') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="pass" class="col-md-4 col-form-label text-md-right required">修正パスワード</label>

    <div class="col-md-6">
        <input id="pass" type="password" class="form-control @error('pass') is-invalid @enderror" name="pass"
        value="" required autocomplete="pass" autofocus placeholder="半角数字4桁">
        @if($errors->has('pass')) <span class="text-danger">{{ $errors->first('pass') }}</span> @endif
    </div>
</div>
