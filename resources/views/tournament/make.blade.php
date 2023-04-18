@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @include('elements.flash_message')
              @if(Session::has('flash_message'))
                  <a href="{{ route('tournament.index', ['block' => 1, 'sheet' => 'A']) }}">ブロック1</a>
              @else
                  <form method="POST">
                    @csrf
                    <div class="form-group row">
                        <div class="col-md-6 offset-md-3 col-12">
                          * 現時点で承認済み、かつ棄権していないチームが対象です。<br>
                          * 対象チーム数 → {{ $targetTeamCnt }}チーム<br>
                          <!-- * 作成ブロック数 → {{ $makeBlockCnt }}ブロック<br> -->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-md-4 col-3 col-form-label text-md-right">割当順</label>

                        <div class="col-md-2 col-5">
                          <select name="order_rule" class="form-control">
                            <option value="0">ランダム</option>
                            <option value="1">XP</option>
                            <option value="2">申請日時</option>
                          </select>
                        </div>

                        <div class="col-md-1 col-4">
                          <button type="submit" class="btn btn-primary" @if($targetTeamCnt == 0) disabled @endif>作成</button>
                        </div>
                    </div>
                  </form>
              @endif
          </div>
      </div>
@endsection
