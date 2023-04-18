@include('elements.flash_message')
<div class="form-group row">
    <label for="name" class="col-md-4 col-form-label text-md-right required">名前</label>

    <div class="col-md-4">
        <input type="hidden" name="user_id" id="user_id" value="{{ isset($data) ? $data->id : '' }}">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required>
        @if($errors->has('name')) <span class="text-danger">{{ $errors->first('name') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="mail" class="col-md-4 col-form-label text-md-right required">mail</label>

    <div class="col-md-4">
        <input id="mail" type="text" class="form-control @error('mail') is-invalid @enderror" name="mail"
        value="{{ old('mail', isset($data) ? $data->email : '') }}" required>
        @if($errors->has('mail')) <span class="text-danger">{{ $errors->first('mail') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="role" class="col-md-4 col-form-label text-md-right required">権限</label>

    <div class="col-md-4">
      <select name="role" class="form-control col-md-10">
          <option value="2">スタッフ</option>
          <option value="1">管理者</option>
      </select>
    </div>
</div>
@if (empty($data))
    <div class="form-group row">
        <label for="password" class="col-md-4 col-form-label text-md-right required">パスワード</label>

        <div class="col-md-4">
            <input id="password" type="text" class="form-control @error('password') is-invalid @enderror" name="password"
            value="{{ old('password', isset($data) ? $data->password : '') }}" required>
            @if($errors->has('password')) <span class="text-danger">{{ $errors->first('password') }}</span> @endif
        </div>
    </div>
@endif
