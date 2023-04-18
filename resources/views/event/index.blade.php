@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @if (Auth::user()->role == config('user.role.admin'))
                    <div class="text-right mb-2"><a href="{{ route('event.regist') }}" class="btn btn-primary">登録</a></div>
                @endif
                @include('elements.flash_message')
                @if (0 < count($datas))
                <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead>
                    <tr class="thead-light text-center">
                        @if (Auth::user()->role == config('user.role.admin')) <th></th><th>No</th> @endif
                        <th>大会名</th>
                        @if (Auth::user()->role == config('user.role.admin'))
                          <th>申請数</th>
                          <th>開催日時</th>
                         @endif
                    </tr>
                    </thead>
                    <tbody>
                      @foreach ($datas as $data)
                        <tr>
                            @if (Auth::user()->role == config('user.role.admin'))
                                <td class="text-center">
                                  <a href="{{ route('event.edit', ['id' => $data->id]) }}" class="btn btn-info btn-sm"><i class="fas fa-edit fa-lg"></i></a>
                                  &nbsp;<a href="{{ route('question.edit', ['id' => $data->id]) }}" class="btn btn-info btn-sm"><i class="fas fa-plus fa-lg"></i></a>
                                </td>
                                <td>{{ $data->id }}</td>
                            @endif
                            <td><a href="{{ route('event.detail', ['id' => $data->id]) }}">{{ $data->name }}</a></td>
                            @if (Auth::user()->role == config('user.role.admin'))
                              <td>{{ count($data->team) }}</td>
                              <td>{{ isset($data->from_date) ? $data->from_date->format('Y/m/d H:i').'〜' : '' }}</td>
                            @endif
                        </tr>
                      @endforeach
                    </tbody>
                </table>
                </div>
                @endif
            </div>
        </div>
@endsection
