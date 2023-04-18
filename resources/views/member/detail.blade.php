@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                <table class="table table-bordered table-hover col-md-6 offset-md-4">
                    <tr><th>大会名</th><td>{{ $data->team->event->name }}</tr>
                    <tr><th>チーム名</th><td><a href="{{ route('team.detail', ['id' => $data->team_id]) }}">{{ $data->team->name }}</a></tr>
                    <tr><th>メンバー名</th><td>{{ $data->name }}</tr>
                    <tr><th>twitter</th><td>
                      @if($data->user->twitter_nickname)
                          <a href="https://twitter.com/{{ $data->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                      @endif
                    </tr>
                    <tr><th>xp</th><td>{{ $data->xp }}</tr>
                </table>
                <input type="hidden" name="system" value="{{ $data->user_id }}">
              </div>
          </div>
@endsection
