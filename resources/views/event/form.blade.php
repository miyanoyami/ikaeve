<div class="form-group row">
    <label for="name" class="col-md-3 col-6 col-form-label text-md-right required">大会名</label>

    <div class="col-md-6 col-12">
        <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name"
        value="{{ old('name', isset($data) ? $data->name : '') }}" required autocomplete="name" autofocus>
        @if($errors->has('name')) <span class="text-danger">{{ $errors->first('name') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="recruit_date" class="col-md-3 col-6 col-form-label text-md-right required">募集日時</label>

    <div class="col-md-3 col-10">
      <input id="from_recruit_date" type="text" class="form-control datepicker1 @error('from_recruit_date') is-invalid @enderror" required name="from_recruit_date"
      value="{{ old('from_recruit_date', isset($data->from_recruit_date) ? $data->from_recruit_date->format('Y/m/d H:i') : '') }}" autocomplete="from_recruit_date">
      @if($errors->has('from_recruit_date')) <span class="text-danger">{{ $errors->first('from_recruit_date') }}</span> @endif
    </div>〜
    <div class="col-md-3 col-10">
      <input id="to_recruit_date" type="text" class="form-control datepicker1 @error('to_recruit_date') is-invalid @enderror" required name="to_recruit_date"
      value="{{ old('to_recruit_date', isset($data->to_recruit_date) ? $data->to_recruit_date->format('Y/m/d H:i') : '') }}" autocomplete="to_recruit_date">
      @if($errors->has('to_recruit_date')) <span class="text-danger">{{ $errors->first('to_recruit_date') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="date" class="col-md-3 col-form-label text-md-right required">開催日時</label>

    <div class="col-md-3 col-10">
      <input id="from_date" type="text" class="form-control datepicker1 @error('from_date') is-invalid @enderror" required name="from_date"
      value="{{ old('from_date', isset($data->from_date) ? $data->from_date->format('Y/m/d H:i') : '') }}" autocomplete="from_date">
      @if($errors->has('from_date')) <span class="text-danger">{{ $errors->first('from_date') }}</span> @endif
    </div>〜
    <div class="col-md-3 col-10">
      <input id="to_date" type="text" class="form-control datepicker1 @error('to_date') is-invalid @enderror" required name="to_date"
      value="{{ old('to_date', isset($data->to_date) ? $data->to_date->format('Y/m/d H:i') : '') }}" autocomplete="to_date">
      @if($errors->has('to_date')) <span class="text-danger">{{ $errors->first('to_date') }}</span> @endif
    </div>
</div>
<div class="form-group row">
    <label for="team_member" class="col-md-3 col-5 col-form-label text-md-right">チーム人数</label>

    <div class="col-md-2 col-6">
      <select name="team_member" class="form-control">
        <option value="2" {{ old('team_member', isset($data->team_member) && $data->team_member == 2 ? 'selected' : '') }}>2</option>
        <option value="4" {{ old('team_member', isset($data->team_member) && $data->team_member == 4 ? 'selected' : '') }}>4</option>
      </select>
    </div>
</div>
<div class="form-group row p-1">
    <label for="passing_order" class="col-md-3 col-5 col-form-label text-md-right">予選通過順位</label>

    <div class="col-md-2 col-6">
      <select name="passing_order" class="form-control">
        <option value="1" {{ old('passing_order', isset($data->passing_order) && $data->passing_order == 1 ? 'selected' : '') }}>1</option>
        <option value="2" {{ old('passing_order', isset($data->passing_order) && $data->passing_order == 2 ? 'selected' : '') }}>2</option>
      </select>
    </div>
    <label for="pre_score" class="col-md-2 col-5 col-form-label text-md-right">予選先取点</label>

    <div class="col-md-2 col-6">
      <select name="pre_score" class="form-control">
        <option value="1" {{ old('pre_score', isset($data->pre_score) && $data->pre_score == 1 ? 'selected' : '') }}>1</option>
        <option value="2" {{ old('pre_score', isset($data->pre_score) && $data->pre_score == 2 ? 'selected' : '') }}>2</option>
        <option value="3" {{ old('pre_score', isset($data->pre_score) && $data->pre_score == 3 ? 'selected' : '') }}>3</option>
      </select>
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-3 col-6 col-form-label text-md-right">予選1回戦</label>

    <div class="col-md-6 col-12">
        <input id="name" type="text" class="form-control @error('pre_rule1') is-invalid @enderror" name="pre_rule1"
        value="{{ old('pre_rule1', isset($data) ? $data->pre_rule1 : '') }}" autocomplete="pre_rule1">
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-3 col-6 col-form-label text-md-right">予選2回戦</label>

    <div class="col-md-6 col-12">
        <input id="name" type="text" class="form-control @error('pre_rule2') is-invalid @enderror" name="pre_rule2"
        value="{{ old('pre_rule2', isset($data) ? $data->pre_rule2 : '') }}" autocomplete="pre_rule2">
    </div>
</div>
<div class="form-group row">
    <label for="name" class="col-md-3 col-6 col-form-label text-md-right">予選3回戦</label>

    <div class="col-md-6 col-12">
        <input id="name" type="text" class="form-control @error('pre_rule3') is-invalid @enderror" name="pre_rule3"
        value="{{ old('pre_rule3', isset($data) ? $data->pre_rule3 : '') }}" autocomplete="pre_rule3">
    </div>
</div>
<div class="form-group row p-1">
    <label for="main_score" class="col-md-3 col-5 col-form-label text-md-right">本戦先取点</label>

    <div class="col-md-2 col-6">
      <select name="main_score" class="form-control">
        <option value="1" {{ old('main_score', isset($data->main_score) && $data->main_score == 1 ? 'selected' : '') }}>1</option>
        <option value="2" {{ old('main_score', isset($data->main_score) && $data->main_score == 2 ? 'selected' : '') }}>2</option>
        <option value="3" {{ old('main_score', isset($data->main_score) && $data->main_score == 3 ? 'selected' : '') }}>3</option>
      </select>
    </div>
    <label for="final_score" class="col-md-2 col-5 col-form-label text-md-right">決勝戦先取点</label>

    <div class="col-md-2 col-6">
      <select name="final_score" class="form-control">
        <option value="1" {{ old('final_score', isset($data->final_score) && $data->final_score == 1 ? 'selected' : '') }}>1</option>
        <option value="2" {{ old('final_score', isset($data->final_score) && $data->final_score == 2 ? 'selected' : '') }}>2</option>
        <option value="3" {{ old('final_score', isset($data->final_score) && $data->final_score == 3 ? 'selected' : '') }}>3</option>
      </select>
    </div>
</div>
<div class="form-group row">
    <label for="note" class="col-md-3 col-5 col-form-label text-md-right">概要</label>

    <div class="col-md-6 col-12">
        <textarea class="form-control" name="note" id="editor">{{ old('note', isset($data) ? $data->note : '') }}</textarea>
    </div>
</div>
<div class="form-group row">
    <label for="view" class="col-md-3 col-5 col-form-label text-md-right">一覧表示</label>

    <div class="col-md-2 col-6">
      <select name="view" class="form-control">
        <option value="0" {{ old('view', isset($data->view) && $data->view == 0 ? 'selected' : '') }}>表示</option>
        <option value="1" {{ old('view', isset($data->view) && $data->view == 1 ? 'selected' : '') }}>非表示</option>
      </select>
    </div>
    <label for="grade" class="col-md-2 col-5 col-form-label text-md-right">グレート</label>

    <div class="col-md-2 col-6">
      <select name="grade" class="form-control">
        <option value="1" {{ old('final_score', isset($data->final_score) && $data->final_score == 1 ? 'selected' : '') }}>1</option>
        <option value="2" {{ old('final_score', isset($data->final_score) && $data->final_score == 2 ? 'selected' : '') }}>2</option>
        <option value="3" {{ old('final_score', isset($data->final_score) && $data->final_score == 3 ? 'selected' : '') }}>3</option>
      </select>
    </div>
</div>
<div class="form-group row">
    <label for="view" class="col-md-3 col-5 col-form-label text-md-right">ポイント追加</label>

    <div class="col-md-2 col-6">
      <select name="point" class="form-control">
        <option value="0" {{ old('point', isset($data->point) && $data->point == 0 ? 'selected' : '') }}>非対象</option>
        <option value="1" {{ old('point', isset($data->point) && $data->point == 1 ? 'selected' : '') }}>対象</option>
      </select>
    </div>
    <label for="view" class="col-md-2 col-5 col-form-label text-md-right">本戦シャッフル</label>

    <div class="col-md-2 col-6">
      <select name="shuffle" class="form-control">
        <option value="0" {{ old('point', isset($data->shuffle) && $data->shuffle == 0 ? 'selected' : '') }}>非対象</option>
        <option value="1" {{ old('point', isset($data->shuffle) && $data->shuffle == 1 ? 'selected' : '') }}>対象</option>
      </select>
    </div>
</div>
