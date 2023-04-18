  <div class="row">
    @if (1 < count($blocks))
      <div class="col-3 ml-2 p-1">
        <select name="block" id="selectBlock" class="form-control">
          @foreach ($blocks as $val)
            <option value="{{ $val->block }}" {{ ($selectBlock == $val->block) ? 'selected' : ''}}>{{ $val->block }}</option>
          @endforeach
          <option value="finalgame" {{ ($selectBlock == 'finalgame') ? 'selected' : ''}}>決勝戦</option>
          <!-- <option value="result" {{ ($selectBlock == 'result') ? 'selected' : ''}}>結果</option> -->
        </select>
      </div>
    @else
      <input type="hidden" id="selectBlock" value="A">
    @endif
    @if($selectBlock != 'finalgame')
      <div class="col-4 p-1 @if (1 == count($blocks)) ml-2 @endif">
        <select name="sheet" id="selectSheet" class="form-control">
          <option value="teamlist" {{ ($selectSheet == 'teamlist') ? 'selected' : ''}}>チーム一覧</option>
          <option value="progress" {{ ($selectSheet == 'progress') ? 'selected' : ''}}>進行表</option>
          <option value="all" {{ ($selectSheet == 'all') ? 'selected' : ''}}>予選</option>
          @if ($event->shuffle == 1)
            <a href="{{ route('tournament.mainfirstgame', ['block' => $selectBlock]) }}" class="btn btn-info btn-sm mt-1">本戦1回戦</a>
            <option value="mainfirstgame" {{ ($selectSheet == 'mainfirstgame') ? 'selected' : ''}}>本戦1回戦</option>
          @endif
          <option value="maingame" {{ ($selectSheet == 'maingame') ? 'selected' : ''}}>本戦</option>
          @if (1 == count($blocks))
            <!-- <option value="result" {{ ($selectBlock == 'result' || $selectSheet == 'result') ? 'selected' : ''}}>結果</option> -->
          @endif
          @foreach ($sheets as $val)
            <option value="{{ $val->sheet }}" {{ ($selectSheet == $val->sheet) ? 'selected' : ''}}>{{ $val->sheet }}</option>
          @endforeach
        </select>
      </div>
    @endif
    @if($selectSheet == 'mainfirstgame' ||($selectSheet == 'maingame' || ($selectSheet == 'finalgame' || $selectBlock == 'finalgame'))
    && (isset($member) || Auth::user()->role != config('user.role.member')))
      <div class="col-4 mt-1 p-1">
        <!-- <a href="{{ route('game.mainResultlist', ['block' => $selectBlock]) }}" class="btn btn-info btn-sm mt-1">報告一覧</a> -->
        @if ($selectSheet == 'finalgame' || $selectBlock == 'finalgame')
            <a href="{{ route('game.finalResult') }}" class="btn btn-success btn-sm">報告</a>
        @else
            <a href="{{ route('game.mainResult', ['block' => $selectBlock]) }}" class="btn btn-success btn-sm">報告</a>
        @endif
      </div>
    @endif
  </div>
