@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-4">
                            <input id="file" type="file" name="csv_file" @change="onFileChange">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-2">
                            <select name="xp_import" id="xp_import" class="form-control">
                                <option value="0">XP列なし</option>
                                <option value="1">XP列あり</option>
                            </select>
                        </div>
                    </div>
                    <!--
                    <div class="form-group row">
                        <div class="col-md-2">
                            <select name="block_cnt" id="block_cnt" class="form-control">
                              @for($cnt = 1; $cnt <= 16; $cnt++)
                                <option value="{{ $cnt }}">{{ $cnt }}ブロック</option>
                              @endfor
                            </select>
                        </div>
                    </div>
                  -->
                    <div class="form-group row">
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">登録</button>
                            <a href="javascript:history.back()" class="btn btn-outline-secondary">戻る</a>
                        </div>
                    </div>
              </form>
              <div class="form-group row">
                  <div class="col-md-6">
                    @if (0 < count($nameNgAry))
                      <div class="card" style="width: 30rem;">
                        <div class="card-header">
                          チーム名重複件数　{{ count($nameNgAry) }}件
                        </div>
                        <div class="card-body">
                          <p class="card-text">
                            @foreach ($nameNgAry as $val)
                                No{{ $val->no }} . <a href="{{ route('team.detail', ['id' => $val->id]) }}" target="_blank">{{ $val->name }}</a><br>
                            @endforeach
                          </p>
                        </div>
                      </div>
                    @endif
                    <br>
                    @if (0 < count($fcodeNgAry))
                      <div class="card" style="width: 30rem;">
                        <div class="card-header">
                          フレンドコード修正対象件数　{{ count($fcodeNgAry) }}件
                        </div>
                        <div class="card-body">
                          <p class="card-text">
                            @foreach ($fcodeNgAry as $val)
                                No{{ $val->no }} . <a href="{{ route('team.detail', ['id' => $val->id]) }}" target="_blank">{{ $val->name }}</a> {{ $val->friend_code }}<br>
                            @endforeach
                          </p>
                        </div>
                      </div>
                    @endif
                  </div>
                  <div class="col-md-6">
                    @if (0 < count($userNgAry))
                      <div class="card" style="width: 30rem;">
                        <div class="card-header">
                          メンバー重複件数　{{ count($userNgAry) }}件
                        </div>
                        <div class="card-body">
                          <p class="card-text">
                            @foreach ($userNgAry as $id => $val)
                                <a href="{{ route('member.detail', ['id' => $id]) }}" target="_blank">{{ $val }}</a><br>
                            @endforeach
                          </p>
                        </div>
                      </div>
                    @endif
                  </div>
              </div>
            </div>
        </div>
@endsection
