@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
              @endif
              @if (Auth::user()->role != config('user.role.member'))
                <a href="{{ route('game.resultlist', ['block' => $selectBlock]) }}" class="btn btn-info btn-sm mt-1">報告一覧</a>
              @endif
              <table class="table table-bordered table-hover mt-2" style="table-layout:fixed;">
                  <tr class="table-info">
                    <th class="text-center p-1" rowspan="2" style="width:50px;"></th>
                    <th class="text-center p-1" colspan="2" style="width:80px;">第1試合</th>
                    <th class="text-center p-1" colspan="2" style="width:80px;">第2試合</th>
                    <th class="text-center p-1" colspan="2" style="width:80px;">第3試合</th>
                  </tr>
                  <tr class="table-info">
                    @foreach (config('game.pre') as $val)
                      @foreach ($val as $conf)
                        <th class="text-center p-1">{{ $conf[0] }}vs{{ $conf[1] }}</th>
                      @endforeach
                    @endforeach
                  </tr>
                  @foreach ($sheets as $sheet)
                    <tr>
                      <th class="text-center p-1">{{ $sheet->sheet }}</th>
                      <td class="text-center p-1">@if(isset($progress[$sheet->sheet][1][0])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                      <td class="text-center p-1 @if($teams[$sheet->sheet] < 4) table-secondary @endif">@if(isset($progress[$sheet->sheet][1][1])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                      <td class="text-center p-1 @if($teams[$sheet->sheet] < 3) table-secondary @endif">@if(isset($progress[$sheet->sheet][2][0])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                      <td class="text-center p-1 @if($teams[$sheet->sheet] < 4) table-secondary @endif">@if(isset($progress[$sheet->sheet][2][1])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                      <td class="text-center p-1 @if($teams[$sheet->sheet] < 4) table-secondary @endif">@if(isset($progress[$sheet->sheet][3][0])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                      <td class="text-center p-1 @if($teams[$sheet->sheet] < 3) table-secondary @endif">@if(isset($progress[$sheet->sheet][3][1])) <i class="fas fa-check" style="color:salmon;"></i> @endif</td>
                    </tr>
                  @endforeach
              </table>
          </div>
      </div>
      <script src="{{ asset('/js/reload.js') }}"></script>
@endsection
