@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
            @endif
            </div>
            @include('elements.flash_message')
            @if (1 < count($blocks))
                <form method="POST">
                    @csrf
                    <div class="form-group row ml-1">
                        <div class="col-md-1 col-3 p-1">
                            <select class="form-control" name="searchBlock">
                                <option value="">A~</option>
                                @foreach ($blocks as $block)
                                  <option value="{{ $block->block }}" @if(isset($search) && $search['searchBlock'] == $block->block) selected @endif>{{ $block->block }}</option>
                                @endforeach
                            </select>
                        </div>
                      <div class="col-md-2 col-3 p-1">
                          <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-lg mr-1"></i></button>
                      </div>
                    </div>
                </form>
            @endif
            @if (0 < count($datas))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr class="thead-light text-center">
                            @if (Auth::user()->role != config('user.role.member'))
                              <th></th>
                            @endif
                            <th>No</th>
                            <th>何戦目</th>
                            <th>勝ちチーム/点</th>
                            <th>負けチーム/点</th>
                            <th>ブロック</th>
                            <th>不戦勝</th>
                            <th>報告日時</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach ($datas as $data)
                            <tr>
                                @if (Auth::user()->role != config('user.role.member'))
                                  <td class="text-center">
                                    <a href="{{ route('game.delete', ['id' => $data->id]) }}" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></a>
                                  </td>
                                @endif
                                <!-- <td><a href="{{ route('game.resultDetail', ['id' => $data->id]) }}">{{ $data->id }}</a></td> -->
                                <td><a href="{{ route('game.result', ['id' => $data->id]) }}">{{ $data->id }}</a></td>
                                <td class="text-center">{{ $data->turn }}</td>
                                <td>{{ $data->winteam->name }}
                                  <span class="badge badge-primary">{{ $data->win_score }}</span>
                                </td>
                                <td>{{ $data->loseteam->name }}
                                  <span class="badge badge-secondary">{{ $data->lose_score }}</span>
                                </td>
                                <td>{{ $data->block }}</td>
                                <td class="text-center">{{ ($data->unearned_win == 1) ? '◯' : '' }}</td>
                                <td>{{ $data->updated_at }}</td>
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
