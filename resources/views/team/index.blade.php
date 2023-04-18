@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <form method="POST" action="{{ route('team.index') }}">
                    @csrf
                    <div class="form-group row mb-3 ml-1">
                        @if (Auth::user()->role == config('user.role.admin'))
                          <div class="col-md-3 col-4 p-1">
                              <input type="text" class="form-control" name="name" value="{{ $search['name'] ?? '' }}" placeholder="チーム名">
                          </div>
                          <div class="col-md-3 col-4 p-1">
                              <input type="text" class="form-control" name="member_name" value="{{ $search['member_name'] ?? '' }}" placeholder="メンバー/Twitte名">
                          </div>
                        @else
                          <div class="col-md-3 col-5 p-1">
                              <input type="text" class="form-control" name="name" value="{{ $search['name'] ?? '' }}" placeholder="チーム名">
                          </div>
                          <div class="col-md-3 col-5 p-1">
                              <input type="text" class="form-control" name="member_name" value="{{ $search['member_name'] ?? '' }}" placeholder="メンバー/Twitter名">
                          </div>
                        @endif
                        <div class="col-md-2 col-1 p-1">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-lg mr-1"></i></button>
                        </div>
                    </div>
                </form>
                @if (0 < count($datas))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                                <!-- @if (Auth::user()->role != config('user.role.member'))
                                  <th></th>
                                @endif -->
                                <th>No</th>
                                <th>チーム名<br>フレンドコード</th>
                                <th>メンバー</th>
                                <th>ブロック</th>
                                @if (Auth::user()->role != config('user.role.member'))
                                  <th>申請日時</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $data)
                                <tr @if($data->abstention == 1) class="table-secondary" @endif>
                                    <!-- @if (Auth::user()->role != config('user.role.member'))
                                      <td class="text-center">
                                        <a href="{{ route('team.edit', ['id' => $data->id]) }}"><i class="fas fa-edit fa-lg"></i></a>
                                      </td>
                                    @endif -->
                                    <td>{{ $data->no }}</td>
                                    <td><a href="{{ route('team.detail', ['id' => $data->id]) }}">{{ $data->name }}</a>@if($data->abstention == 1) (棄権) @endif
                                      <br>@if($data->friend_code) {{ substr($data->friend_code, 0, 4) }}-{{ substr($data->friend_code, 4, 4) }}-{{ substr($data->friend_code, 8, 4) }} @endif</td>
                                    <td>
                                        @foreach($data::members($data->id) as $v => $member)
                                            @if($member->user->twitter_nickname)
                                                <a href="https://twitter.com/{{ $member->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-lg"></i></a>&nbsp;
                                            @endif
                                            <a href="{{ route('member.detail', ['id' => $member->id]) }}">{{ $member->name }}</a>
                                            <br>
                                        @endforeach
                                    </td>
                                    <td>
                                      @if($data->block)
                                        <a href="{{ route('tournament.index', ['block' => $data->block, 'sheet' => $data->sheet]) }}">{{ $data->block }}-{{ $data->sheet }}</a>
                                      @endif
                                    </td>
                                      @if (Auth::user()->role != config('user.role.member'))
                                      <td>{{ $data->created_at->format('Y/m/d H:i') }}</td>
                                    @endif
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $datas->links() }}
                @endif
            </div>
        </div>
@endsection
