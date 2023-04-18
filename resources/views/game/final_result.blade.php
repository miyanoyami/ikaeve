@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                <form method="POST" name="resultFrm" action="{{ route('game.finalResult') }}">
                    @csrf
                    <h3 class="mb-3 blue_title offset-md-4 col-md-5">決勝戦</h3>
                    <div class="form-group row offset-md-4 mt-5">
                        <div class="col-md-3 col-5 text-center">
                          <select name="turn" class="form-control col-md-10">
                            @for($i = $startTurn;$i <= $gameCnt;$i++)
                              <option value="{{ $i }}">{{ $i }}戦目</option>
                            @endfor
                          </select>
                        </div>
                    </div>
                    <div class="form-group row offset-md-4">
                        <div class="col-md-3 col-5 text-center">
                          <select name="team[]" class="form-control col-md-10">
                            @foreach($selectTeams as $val)
                              <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                          </select>
                        </div>
                        <div class="col-md-1 col-1 text-center"></div>
                        <div class="col-md-3 col-5 text-center">
                          <select name="team[]" class="form-control col-md-10">
                            @foreach($selectTeams as $val)
                              <option value="{{ $val['id'] }}">{{ $val['name'] }}</option>
                            @endforeach
                          </select>
                        </div>
                    </div>
                    <div class="form-group offset-md-4 row mt-4">
                      <div class="col-md-3 col-4 ml-3">
                        <select name="score[]" id="score" class="form-control col-md-10">
                          @for($cnt = 0; $cnt <= $event->main_score; $cnt++)
                            <option value="{{ $cnt }}">{{ $cnt }}</option>
                          @endfor
                        </select>
                      </div>
                      <div class="col-md-1 col-2"><h3>VS</h3></div>
                      <div class="col-md-3 col-4 ml-3">
                        <select name="score[]" class="form-control col-md-10">
                          @for($cnt = 0; $cnt <= $event->main_score; $cnt++)
                            <option value="{{ $cnt }}">{{ $cnt }}</option>
                          @endfor
                        </select>
                      </div>
                    </div>
                    <div class="form-group row offset-md-4">
                      <div class="col-md-3 col-5 text-center">
                        <button type="button" name="unearned[]" class="btn btn-outline-success">不戦勝</button>
                      </div>
                      <div class="col-md-1 col-2 text-center"></div>
                      <div class="col-md-3 col-5 text-center">
                        <button type="button" name="unearned[]" class="btn btn-outline-success">不戦勝</button>
                      </div>
                    </div>
                    <div class="form-group row offset-md-4">
                        <div class="col-md-7 col-12">
                          <textarea class="form-control" name="memo" rows="7" placeholder="メモ" >{{ $data->memo ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="form-group row offset-md-4">
                        <div class="col-md-7 text-center">
                            <button type="button" id="submitBtn" class="btn btn-primary submit" @if(!$data && Auth::user()->role == config('user.role.member')) disabled @endif>登録</button>
                            <button type="button" id="resetBtn" class="btn btn-secondary">リセット</button>
                            @if ($data)
                              <a href="{{ route('game.delete', ['block' => $selectBlock, 'id' => $data->id]) }}" class="btn btn-danger">削除</a>
                            @endif
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">戻る</a>
                            <input type="hidden" name="mode" value="result">
                            <input type="hidden" name="unearned_win" id="unearned_win" value="{{ ($data->unearned_win) ?? 0}}">
                            <input type="hidden" name="block" value="{{ $selectBlock }}">
                            <input type="hidden" name="id" id="result_id" value="{{ ($data->id) ?? ''}}">
                            <input type="hidden" name="role" id="role" value="{{ Auth::user()->role }}">
                        </div>
                    </div>
                </form>
            </div>
        </div>
@endsection
