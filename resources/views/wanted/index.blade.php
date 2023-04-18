@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <div class="text-right mb-2"><a href="{{ route('wanted.regist') }}" class="btn btn-primary">登録</a></div>
                @if (0 < count($datas))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                              <th>No</th>
                              <th>募集者/内容/持ちたい武器</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $data)
                                <tr>
                                  <td rowspan="3" class="text-center">{{ $data->id }}</td>
                                  <td>
                                    <a href="{{ route('wanted.detail', ['id' => $data->id]) }}">{{ $data->user->name }}</a>
                                    @if($data->user->twitter_nickname)
                                        &nbsp;<a href="https://twitter.com/{{ $data->user->twitter_nickname }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                                    @endif
                                  </td>
                                </tr>
                                <tr>
                                  <td>{{ $data->note }}</td>
                                </tr>
                                <tr>
                                  <td>{!! $data->weponStr() !!}</td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
