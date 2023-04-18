@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              <ul class="nav nav-tabs mb-1">
                @foreach ($blocks as $block)
                  <li class="nav-item">
                    <a class="nav-link @if($selectBlock == $block->block) active @endif"
                      href="{{ route('test.type1', ['block' => $block->block, 'sheet' => 'A']) }}">{{ $block->block }}</a>
                  </li>
                @endforeach
              </ul>
              @foreach ($sheets as $select)
              <h5 class="mt-2">{{ $select->sheet }}ブロック総当たり表</h5>
              <table class="table table-bordered table-hover mt-3" style="table-layout:fixed;">
                  <tr class="table-info">
                    <th class="text-center p-1 align-middle" style="width:25px;">No</th>
                    <th class="text-center p-1 align-middle" style="width:80px;">チーム名</th>
                    @foreach ($teams as $k => $team)
                        <th colspan="2" class="p-2 align-middle" style="width:80px;"><a href="{{ route('team.detail', ['id' => $team['id']]) }}" target="_blank">{{ $team['name'] }}</a>
                          @if ($team['abstention'] == 1)<span class="badge badge-warning">棄権</span>@endif</th>
                    @endforeach
                    <th class="text-center p-1 align-middle" style="width:25px;">勝点</th>
                    <th class="text-center p-1 align-middle" style="width:25px;">取得率</th>
                    <th class="text-center p-1 align-middle" style="width:25px;">順位</th>
                  </tr>
                  @foreach ($teams as $k => $team)
                    <tr>
                        <th class="text-center p-1">{{ $k }}</th>
                        <th class="p-2"><a href="{{ route('team.detail', ['id' => $team['id']]) }}" target="_blank">{{ $team['name'] }}</a>
                          @if ($team['abstention'] == 1)<span class="badge badge-warning">棄権</span>@endif
                        </th>
                        @foreach ($teams as $key => $team)
                            @if ($k == $key)
                                <td class="table-secondary"></td>
                                <td class="table-secondary"></td>
                            @else
                                <td class="text-center p-1">0-0</td>
                                <td class="text-center p-1">0</td>
                            @endif
                        @endforeach
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                        <td class="text-center"></td>
                    </tr>
                  @endforeach
              </table>
              <div class="row">
                <div class="col-md-6 col-12">
                  <h5 class="mt-2">対戦表</h5>

                  @foreach (config('game.pre') as $key => $val)
                    <table class="table table-bordered mt-3" style="width:500px;">
                        <colgroup>
                          <col style="width: 200px;">
                          <col style="width: 20px;">
                          <col style="width: 200px;">
                          <col style="width: 70px;">
                        </colgroup>
                        <tr>
                          <th class="text-center table-info" colspan="4">第{{ $key }}試合</th>
                        </tr>
                        @foreach ($val as $k => $conf)
                          @if (empty($teams[$conf[0]]['name']) || empty($teams[$conf[1]]['name']))
                            @continue
                          @endif
                          <tr>
                            <td>{{ $conf[0] }}.{{ $teams[$conf[0]]['name'] ?? '' }}
                              @if ($teams[$conf[0]]['abstention'] == 1)<span class="badge badge-warning">棄権</span>@endif</td>
                            <td class="text-center" style="width:10px;">VS</td>
                            <td>{{ $conf[1] }}.{{ $teams[$conf[1]]['name'] ?? '' }}
                            @if ($teams[$conf[1]]['abstention'] == 1)<span class="badge badge-warning">棄権</span>@endif</td>
                            <td>
                              <a href="{{ route('game.result') }}" class="btn btn-primary btn-sm">報告</a>
                            </td>
                          </tr>
                        @endforeach
                    </table>
                  @endforeach
                </div>
            </div>
          @endforeach
          </div>
      </div>
@endsection
