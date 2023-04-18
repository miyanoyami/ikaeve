@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @include('elements.flash_message')
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
              @endif

                <div style="padding: 1rem; margin-top: 3px;">
                <div class="row">
                  <div class="col-md-8 col-12 mt-4">
                    <h4 class="blue_title mt-3">本戦1回戦対戦表</h4>
                        <table class="table table-bordered">
                          <colgroup>
                            <col style="width: 36%">
                            <col style="width: 5%">
                            <col style="width: 36%">
                            <col style="width: 23%">
                          </colgroup>
                          <thead>
                          </thead>
                          <tbody>
                            @php
                              $cnt = 0;
                            @endphp
                            @foreach ($teams as $k => $val)
                                @if ($k%2 == 0)
                                  <tr>
                                    <td class="align-middle text-center">{{ $val['name'] ?? '' }}
                                      @if ($teams[$k+1]['name'] == 'なし')
                                        <p class="text-center win_block align-bottom">{{ $event->main_score }}</p>
                                      @elseif ($val['name'] == 'なし')
                                        <p class="text-center lose_block align-bottom">0</p>
                                      @elseif (isset($scores[$cnt][0]))
                                        @if ($scores[$cnt][0] && ($scores[$cnt][0] == $event->main_score))
                                          <p class="text-center win_block align-bottom">{{ $scores[$cnt][0] }}</p>
                                        @elseif (isset($scores[$cnt][0]))
                                          <p class="text-center lose_block align-bottom">{{ $scores[$cnt][0] }}</p>
                                        @endif
                                      @endif
                                    </td>
                                    <td class="align-middle text-center p-1">VS</td>
                                @else
                                    <td class="align-middle text-center">{{ $val['name'] ?? '' }}
                                      @if ($teams[$k-1]['name'] == 'なし')
                                        <p class="text-center win_block align-bottom">{{ $event->main_score }}</p>
                                      @elseif ($val['name'] == 'なし')
                                        <p class="text-center lose_block align-bottom">0</p>
                                      @elseif (isset($scores[$cnt][1]))
                                        @if ($scores[$cnt][1] && ($scores[$cnt][1] == $event->main_score))
                                          <p class="text-center win_block align-bottom">{{ $scores[$cnt][1] }}</p>
                                        @elseif (isset($scores[$cnt][1]))
                                          <p class="text-center lose_block align-bottom">{{ $scores[$cnt][1] }}</p>
                                        @endif
                                      @endif
                                    <td class="align-middle text-center">
                                      @if (Auth::user()->role != config('user.role.member'))
                                        <a href="{{ route('game.mainResult', ['block' => $selectBlock]) }}" class="btn btn-success btn-sm mt-1">報告</a>
                                      @endif
                                    </td>
                                  </tr>
                                  @php
                                    $cnt++;
                                  @endphp
                                @endif
                            @endforeach
                          </tbody>
                        </table>
                    </div>
                  </div>
                </div>
            </div>
        </div>
@endsection
