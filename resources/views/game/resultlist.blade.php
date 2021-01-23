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
            <form method="POST">
                @csrf
                <div class="form-group row mb-3 mt-1">
                    <div class="col-md-1 col-2">
                        <select class="form-control" name="searchBlock">
                            <option value="">A~</option>
                            @foreach ($blocks as $block)
                              <option value="{{ $block->block }}" @if(isset($search) && $search['searchBlock'] == $block->block) selected @endif>{{ $block->block }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 col-2">
                        <select class="form-control" name="searchSheet">
                            <option value="">1~</option>
                            @foreach ($sheets as $sheet)
                              <option value="{{ $sheet->sheet }}" @if(isset($search) && $search['searchSheet'] == $sheet->sheet) selected @endif>{{ $sheet->sheet }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1 col-2">
                        <select class="form-control" name="approval">
                            <option value="">承認</option>
                            <option value="0" @if(isset($search) && $search['approval'] == '0') selected @endif>未</option>
                            <option value="1" @if(isset($search) && $search['approval'] == 1) selected @endif>済</option>
                        </select>
                    </div>
                  <div class="col-md-2 col-1">
                      <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-lg mr-1"></i></button>
                  </div>
                </div>
            </form>
            @if (0 < count($datas))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead>
                        <tr class="thead-light text-center">
                            <th>No</th>
                            <th>何戦目</th>
                            <th>勝ちチーム/点</th>
                            <th>負けチーム/点</th>
                            <th>ブロック</th>
                            <th>承認</th>
                            <th>報告日時</th>
                        </tr>
                        </thead>
                        <tbody>
                          @foreach ($datas as $data)
                            <tr>
                                <td>{{ $data->id }}</td>
                                <td>{{ $data->turn }}</td>
                                <td>{{ $data->winteam->number }}.{{ $data->winteam->name }}
                                  <span class="badge badge-primary">{{ $data->win_score }}</span>
                                </td>
                                <td>{{ $data->loseteam->number }}.{{ $data->loseteam->name }}
                                  <span class="badge badge-secondary">{{ $data->lose_score }}</span>
                                </td>
                                <td>{{ $data->block }}-{{ $data->sheet }}</td>
                                <td class="text-center">{{ ($data->approval == 1) ? '済' : '未' }}</td>
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