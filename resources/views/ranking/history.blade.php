@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                @if (0 < count($datas))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                                <th></th>
                                <th>大会</th>
                                <th>取得ポイント数</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $v => $data)
                                <tr>
                                    <td class="text-center">{{ ($v+1) }}</td>
                                    <td>{{ $data->event->name }}</td>
                                    <td>{{ $data->point }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
