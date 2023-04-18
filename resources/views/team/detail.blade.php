@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                <table class="table table-bordered table-hover col-md-6 offset-md-4">
                    <tr><th>大会名</th><td>{{ $data->event->name }}</tr>
                    <tr><th>チーム名</th><td>{{ $data->name }}@if($data->abstention == 1) (棄権) @endif</tr>
                    <tr><th>フレンドコード</th><td>
                      @if($data->friend_code) {{ substr($data->friend_code, 0, 4) }}-{{ substr($data->friend_code, 4, 4) }}-{{ substr($data->friend_code, 8, 4) }} @endif</tr>
                    <tr><th>代表メンバー</th><td>@if($data->member) <a href="{{ route('member.detail', ['id' => $data->member_id]) }}">{{ $data->member->name }}</a> @endif</tr>
                    @foreach($data::members($data->id) as $v => $member)
                      <tr><th>メンバー{{($v+1)}}</th><td><a href="{{ route('member.detail', ['id' => $member->id]) }}">{{ $member->name }}</a>
                        @if($member->user->twitter_nickname)
                            &nbsp;<a href="https://twitter.com/{{ $member->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                        @endif
                      </tr>
                    @endforeach
                    @if ($data->answer)
                        @foreach($data->answer as $v => $val)
                          @if (!$val->question)
                            @continue
                          @endif
                          <tr><th>{{ $val->question->title }}</th>
                            <td>{{ $val->note }}</a></td>
                          </tr>
                        @endforeach
                    @endif
                    <tr>
                      <th>ブロック</th>
                      <td>
                      @if($data->block)
                        <a href="{{ route('tournament.index', ['block' => $data->block, 'sheet' => $data->sheet]) }}">{{ $data->block }}-{{ $data->sheet }}</a>
                      @endif
                    </td>
                    <tr><th>意気込みなど</th><td>{!! nl2br(e($data->note)) !!}</tr>
                    <tr><th>申請日時</th><td>{{ $data->created_at->format('Y/m/d H:i:s')  }}</tr>
                </table>
              </div>
              @if ($data::chkTeamMember($data->id))
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4 text-center">
                      <a href="{{ route('team.edit', ['id' => $data->id]) }}" class="btn btn-primary submit">編集</a>
                      <!-- <a href="{{ route('team.update', ['id' => $data->id, 'column' => 'abstention', 'value'=> 1]) }}" class="btn btn-danger">棄権</a> -->
                      @if($data->abstention == 0)
                          <a href="{{ route('team.update', ['id' => $data->id, 'column' => 'abstention', 'value'=> 1]) }}" class="btn btn-danger">棄権</a>
                      @else
                          <a href="{{ route('team.update', ['id' => $data->id, 'column' => 'abstention', 'value'=> 0]) }}" class="btn btn-success">取消</a>
                      @endif
                      @if (empty($data->block))
                        <a href="{{ route('team.delete', ['id' => $data->id]) }}" class="btn btn-warning submit">削除</a>
                      @endif
                    </div>
                </div>
              @endif
              @if (Auth::user()->role != config('user.role.member'))
                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4 text-center">
                      <a href="{{ route('team.edit', ['id' => $data->id]) }}" class="btn btn-primary submit">編集</a>
                      @if($data->abstention == 0)
                          <a href="{{ route('team.update', ['id' => $data->id, 'column' => 'abstention', 'value'=> 1]) }}" class="btn btn-danger">棄権</a>
                      @else
                          <a href="{{ route('team.update', ['id' => $data->id, 'column' => 'abstention', 'value'=> 0]) }}" class="btn btn-success">取消</a>
                      @endif
                      @if (empty($data->block))
                        <a href="{{ route('team.delete', ['id' => $data->id]) }}" class="btn btn-warning submit">削除</a>
                      @endif
                    </div>
                </div>
              @endif
          </div>
@endsection
