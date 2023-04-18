@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
              @endif
              <table class="table table-bordered table-hover mt-3" style="table-layout:fixed;">
                  <tr class="table-info">
                    <th class="text-center p-1 align-middle" style="width:25px;"></th>
                    <th class="text-center p-1 align-middle" style="width:25px;">No</th>
                    <th class="text-center p-1 align-middle" style="width:25px;">本戦進出</th>
                    <th class="text-center p-1 align-middle" style="width:120px;">チーム名</th>
                    <th class="text-center p-1 align-middle" style="width:80px;">フレンドコード</th>
                    @if (!$isMobile)
                      @php
                        $i = 0;
                      @endphp
                      @while($i < $event->team_member)
                        <th class="text-center p-1 align-middle" style="width:100px;">プレイヤー{{ ($i + 1) }}</th>
                        @php
                          $i++;
                        @endphp
                      @endwhile
                    @endif
                  </tr>
                  @foreach ($teams as $k => $team)
                    <tr class="{{($team->sheet % 2 == 0) ? 'table-info':'' }}">
                        <td class="text-center p-2">{{ $team->sheet }}</td>
                        <td class="text-center p-2">{{ $team->number }}</td>
                        <td class="text-center p-2">@if ($team->main_game == 1)<span class="badge badge-info">{{ $team->pre_rank }}</span>@endif</td>
                        <td class="p-2">
                          <a href="{{ route('team.detail', ['id' => $team['id']]) }}" target="_blank">{{ $team->name }}</a>
                          @if ($team->abstention == 1)<span class="badge badge-warning">棄権</span>@endif
                        </td>
                        <td class="p-2">
                          @if($team->friend_code)
                          {{ substr($team->friend_code, 0, 4) }}-{{ substr($team->friend_code, 4, 4) }}-{{ substr($team->friend_code, 8, 4) }}
                          @endif
                        </td>
                        @if (!$isMobile)
                            @foreach ($team::members($team->id) as $key => $member)
                              @if ($key < $event->team_member)
                                <td class="p-2">
                                  @if($member->user->twitter_nickname)
                                      &nbsp;<a href="https://twitter.com/{{ $member->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                                  @endif
                                  {{ $member->name }}
                                </td>
                              @endif
                            @endforeach
                        @endif
                    </tr>
                  @endforeach
              </table>
          </div>
      </div>
@endsection
