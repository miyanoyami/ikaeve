@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                @if ($recruitBtnView)
                    <a href="{{ route('team.regist') }}" class="btn btn-primary">参加申請</a>
                @endif
                @if (Auth::user()->role == config('user.role.admin') && $makeBtnView)
                    <a href="{{ route('team.import') }}" class="btn btn-info">CSV</a>
                    <a href="{{ route('tournament.make') }}" class="btn btn-success">対戦表作成</a>
                    <a href="{{ route('tournament.edit') }}" class="btn btn-success">対戦表編集</a>
                @endif
                @if (Auth::user()->role == config('user.role.admin'))
                    <a href="{{ route('ranking.point') }}" class="btn btn-info">ポイント付与</a>
                @endif
                @if (Auth::user()->role == config('user.role.member'))
                    @if ($member)
                        <div class="card mt-1 mx-auto text-center text-white bg-info" style="width: 100%;">
                          <div class="card-header">
                            <b>チーム名：{{ $member->team->name }}</b>
                          </div>
                          <div class="card-body">
                            <p class="card-text">
                              <b>ブロック：{{ $member->team->block }} - {{ $member->team->sheet }}</b>
                            </p>
                          </div>
                        </div>
                    @else
                        <div class="alert alert-danger" role="alert">
                          <b>該当するチームはありません。</b><br>
                          【ログイン中のtwitterアカウント：{{Auth::user()->twitter_nickname}}】
                          <br>
                          参加申請されている方は、申請時のtwitterアカウント名の入力を間違えているか
                          申請以外のtwitterアカウントでログインしている可能性があります。メニューのチーム一覧より
                          自チームを検索しログインしているtwitterアカウントが正しいかご確認をお願いいたします。
                          （twitterアカウントを間違えていた場合はチームの他のメンバーから修正をお願いいたします）
                        </div>
                    @endif
                @endif
                <table class="table table-bordered mt-3">
                    <tr>
                      <th>大会名</th>
                      <td>{{ $data->name }}</td>
                    </tr>
                    <tr>
                      <th>申請日時</th>
                      <td>
                        {{ isset($data->from_recruit_date) ? $data->from_recruit_date->format('Y/m/d H:i') : '' }}〜
                        {{ isset($data->to_recruit_date) ? $data->to_recruit_date->format('Y/m/d H:i') : '' }}
                      </td>
                    </tr>
                    <tr>
                      <th>開催日時</th>
                      <td>
                        {{ isset($data->from_date) ? $data->from_date->format('Y/m/d H:i') : '' }}〜
                        {{ isset($data->to_date) ? $data->to_date->format('Y/m/d H:i') : '' }}
                      </td>
                    </tr>
                    <tr>
                      <th>予選<br>通過順位</th>
                      <td>
                        {{ $data->passing_order ?? '' }}
                      </td>
                    </tr>　
                    <tr>
                      <th>予選<br>先取点</th>
                      <td>
                        {{ $data->pre_score ?? '' }}
                      </td>
                    </tr>
                    <tr>
                      <th>予選<br>1回戦</th>
                      <td>
                        {{ $data->pre_rule1 ?? '' }}
                      </td>
                    </tr>　
                    <tr>
                      <th>予選<br>2回戦</th>
                      <td>
                        {{ $data->pre_rule2 ?? '' }}
                      </td>
                    </tr>　
                    <tr>
                      <th>予選<br>3回戦</th>
                      <td>
                        {{ $data->pre_rule3 ?? '' }}
                      </td>
                    </tr>　
                    <tr>
                      <th>本戦<br>先取点</th>
                      <td>
                        {{ $data->main_score ?? '' }}
                      </td>
                    </tr>
                    <tr>
                      <th>決勝戦<br>先取点</th>
                      <td>
                        {{ $data->final_score ?? '' }}
                      </td>
                    </tr>
                    <tr><th>チーム申請数</th><td>{{ count($data->team) }}</tr>
                    <tr><th>チーム人数</th><td>{{ $data->team_member }}</tr>
                    <tr><th>概要</th><td>{!! nl2br(e($data->note)) !!}</tr>
                </table>
              </div>
          </div>
@endsection
