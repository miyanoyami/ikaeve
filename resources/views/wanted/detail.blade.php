@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                <table class="table table-bordered">
                    <tr>
                      <th width="80">募集者</th>
                      <td width="500">
                        {{ $data->user->name }}
                        @if($data->user->twitter_nickname)
                            &nbsp;<a href="https://twitter.com/{{ $data->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                        @endif
                      </td>
                    </tr>
                    <tr><th>xp</th><td>{{ $data->xp ?? '' }}</td></tr>
                    <tr><th>武器</th><td>{!! $data->weponStr() !!}</td></tr>
                    <tr><th>内容</th><td>{!! nl2br(e($data->note)) !!}</td></tr>
                    <tr><th>登録日時</th><td>{{ $data->created_at->format('Y/m/d H:i') }}</td></tr>
                  </tr>
                </table>
              </div>
              @if (Auth::id() == $data->user_id)
                <div class="form-group row mb-0">
                    <div class="col-md-12 text-center">
                      <a href="{{ route('wanted.edit', ['id' => $data->id]) }}" class="btn btn-primary submit">編集</a>
                    </div>
                </div>
              @endif
          </div>
@endsection
