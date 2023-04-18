@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                <form method="POST" name="resultFrm" action="{{ route('game.result') }}">
                    @csrf
                    <h3 class="mb-3 blue_title offset-md-4 col-md-5">
                    @if ($level == 0)
                        {{ $selectBlock }}ブロック {{ $selectSheet }}第{{ $selectTurn }}試合
                    @elseif ($level == 1)
                        本戦 第{{ $selectTurn }}試合
                    @else
                        決勝戦 第{{ $selectTurn }}試合
                    @endif
                    </h3>
                    <div class="form-group row offset-md-4 mt-5">
                        <div class="col-md-3 col-5 text-center">
                          @if ($data)
                            <h6 style="color:{{ ($data->win_team_id == $left['id'] && $data->abstention == 0) ? 'salmon' : 'gray' }}">
                              @if(!$data || (isset($data) && $data->level > 2)){{ $left['number'] }}.@endif{{ $left['name'] }}</h6>
                            <input type="hidden" name="team[]" value="{{ $left['id'] }}">
                          @else
                            <h6 id="left_team">@if(!$data || (isset($data) && $data->level > 2)){{ $team1->number }}.@endif{{ $team1->name }}</h6>
                            <input type="hidden" name="team[]" value="{{ $team1->id }}">
                          @endif
                        </div>
                        <div class="col-md-1 col-1 text-center"></div>
                        <div class="col-md-3 col-5 text-center">
                          @if ($data)
                            <h6 style="color:{{ ($data->win_team_id == $right['id'] && $data->abstention == 0) ? 'salmon' : 'gray' }}">
                              @if(!$data || (isset($data) && $data->level > 2)){{ $right['number'] }}.@endif{{ $right['name'] }}</h6>
                            <input type="hidden" name="team[]" value="{{ $right['id'] }}">
                          @else
                            <h6 id="right_team">@if(!$data || (isset($data) && $data->level > 2)){{ $team2->number }}.@endif{{ $team2->name }}</h6>
                            <input type="hidden" name="team[]" value="{{ $team2->id }}">
                          @endif
                        </div>
                    </div>
                    <div class="form-group offset-md-4 row mt-4">
                      <div class="col-md-3 col-4 ml-3">
                        <select name="score[]" id="score" class="form-control col-md-10" @if ($mode != '') disabled @endif>
                          @for($cnt = 0; $cnt <= $maxScore; $cnt++)
                            <option value="{{ $cnt }}" {{ ($data && $left['score'] == $cnt) ? 'selected' : '' }}>{{ $cnt }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-md-1 col-2"><h3>VS</h3></div>
                      <div class="col-md-3 col-4 ml-3">
                        <select name="score[]" class="form-control col-md-10" @if ($mode != '') disabled @endif>
                          @for($cnt = 0; $cnt <= $maxScore; $cnt++)
                            <option value="{{ $cnt }}" {{ ($data && $right['score'] == $cnt) ? 'selected' : '' }}>{{ $cnt }}</option>
                          @endfor
                        </select>
                      </div>
                    </div>
                    <div class="form-group row offset-md-4">
                      <div class="col-md-3 col-5 text-center">
                        @if ($data && $data->unearned_win == 1 && ($data->win_team_id == $left['id']))
                          <button type="button" name="unearned[]" style="background:#38c172; color:#fff" class="btn btn-outline-success">不戦勝</button>
                        @else
                          <button type="button" name="unearned[]" class="btn btn-outline-success" @if ($mode != '') disabled @endif>不戦勝</button>
                        @endif
                      </div>
                      <div class="col-md-1 col-2 text-center"></div>
                      <div class="col-md-3 col-5 text-center">
                        @if ($data && $data->unearned_win == 1 && ($data->win_team_id == $right['id']))
                          <button type="button" name="unearned[]" style="background:#38c172; color:#fff" class="btn btn-outline-success">不戦勝</button>
                        @else
                          <button type="button" name="unearned[]" class="btn btn-outline-success" @if ($mode != '') disabled @endif>不戦勝</button>
                        @endif
                      </div>
                    </div>
                    <div class="form-group row offset-md-4">
                        <div class="col-md-7 col-12">
                          @if ($mode != '')
                            {!! nl2br(e($data->memo)) ?? '' !!}
                          @else
                            <textarea class="form-control" name="memo" rows="7" placeholder="メモ" >{{ $data->memo ?? '' }}</textarea>
                          @endif
                        </div>
                    </div>
                    <div class="form-group row offset-md-4">
                        <div class="col-md-7 text-center">
                            @if ($mode == 'view')
                              <a href="javascript:history.back()" class="btn btn-outline-secondary">戻る</a>
                            @else
                              <button type="button" id="submitBtn" class="btn btn-primary submit" @if(!$data && Auth::user()->role == config('user.role.member')) disabled @endif>登録</button>
                              <button type="button" id="resetBtn" class="btn btn-secondary">リセット</button>
                              @if ($data)
                                <a href="{{ route('game.delete', ['block' => $selectBlock, 'sheet' => $selectSheet, 'id' => $data->id]) }}" class="btn btn-danger">削除</a>
                              @endif
                              <a href="javascript:history.back()" class="btn btn-outline-secondary">戻る</a>
                              <!--
                              <button type="button" class="btn btn-success submit">削除</button>
                            -->
                              <input type="hidden" name="mode" value="result">
                            @endif
                            <input type="hidden" name="unearned_win" id="unearned_win" value="{{ ($data->unearned_win) ?? 0}}">
                            <input type="hidden" name="block" value="{{ $selectBlock }}">
                            <input type="hidden" name="sheet" value="{{ $selectSheet }}">
                            <input type="hidden" name="turn" value="{{ $selectTurn }}">
                            <input type="hidden" name="id" id="result_id" value="{{ ($data->id) ?? ''}}">
                            <input type="hidden" name="role" id="role" value="{{ Auth::user()->role }}">
                            <input type="hidden" name="level" value="{{ ($data->level) ?? ''}}">
                        </div>
                    </div>
                    @if ($mode == 'app')
                    <div class="form-group row offset-md-4">
                        <div class="col-md-7 col-11 m-3">
                          ※　内容に間違えがあった場合はDiscordより勝利チームへ修正のお願いをしてください。
                        </div>
                    </div>
                    @endif
                </form>
            </div>
        </div>
@endsection
