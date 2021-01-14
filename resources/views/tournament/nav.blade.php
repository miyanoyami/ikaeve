@if (Auth::user()->role != config('user.role.member'))
  <ul class="nav nav-tabs mb-1">
    @foreach ($blocks as $block)
      <li class="nav-item">
        <a class="nav-link @if($selectBlock == $block->block) active @endif"
          href="{{ route('tournament.index', ['block' => $block->block, 'sheet' => 'A']) }}">{{ $block->block }}</a>
      </li>
    @endforeach
  </ul>

  <ul class="nav nav-tabs">
    <li class="nav-item">
      <a class="nav-link @if($selectSheet == 'teamlist') active @endif"
        href="{{ route('tournament.teamlist', ['block' => $selectBlock]) }}">チーム一覧</a>
    </li>
    <li class="nav-item">
      <a class="nav-link @if($selectSheet == 'maingame') active @endif"
        href="{{ route('tournament.maingame', ['block' => $selectBlock]) }}">本戦</a>
    </li>
    <li class="nav-item">
      <a class="nav-link @if($selectSheet == 'sheet') active @endif"
        href="{{ route('tournament.index', ['block' => $selectBlock, 'sheet' => 'A']) }}">対戦表</a>
    </li>
    <li class="nav-item">
      <a class="nav-link @if($selectSheet == 'progress') active @endif"
        href="{{ route('tournament.progress', ['block' => $selectBlock]) }}">進捗</a>
    </li>
  </ul>
  @endif