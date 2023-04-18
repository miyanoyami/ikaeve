@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                @if (0 < count($datas))
                    （ポイント数クリックで履歴がみれます）
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                                <th></th>
                                <th>twitter</th>
                                <th>ポイント数</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $v => $data)
                                <tr>
                                    <td class="text-center">{{ ($v+1) }}</td>
                                    <td>
                                      @if($data->user->name)
                                        {{ $data->user->name }} /
                                      @endif
                                      {{ $data->user->twitter_nickname }}
                                      @if($data->user->twitter_nickname)
                                      &nbsp;&nbsp;<a href="https://twitter.com/{{ $data->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                                      @endif
                                    </td>
                                    <td><a href="{{ route('ranking.history', ['id' => $data->user->id]) }}">{{ $data->point }}</a></td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
