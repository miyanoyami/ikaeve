@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <form method="POST">
                    @csrf
                    <div class="form-group row mb-3">
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="team_name" value="{{ $search['team_name'] ?? '' }}" placeholder="チーム名部分一致">
                        </div>
                        <div class="col-md-3">
                            <input type="text" class="form-control" name="name" value="{{ $search['name'] ?? '' }}" placeholder="メンバー名部分一致">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">検索</button>
                        </div>
                    </div>
                </form>
                @if (0 < count($datas))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                                <th></th>
                                <th>No</th>
                                <th>チーム名</th>
                                <th>メンバー名</th>
                                <th>twiiter</th>
                                <th>xp</th>
                                <th>登録日時</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $data)
                                <tr>
                                    <td class="text-center">
                                      <a href="{{ route('member.edit', ['id' => $data->id]) }}"><i class="fas fa-edit fa-lg"></i></a>
                                    </td>
                                    <td>{{ $data->id }}</td>
                                    <td><a href="{{ route('team.detail', ['id' => $data->team_id]) }}">{{ $data->team->name }}</a></td>
                                    <td><a href="{{ route('member.detail', ['id' => $data->id]) }}">{{ $data->name }}</a></td>
                                    <td class="text-center">
                                      @if($data->twitter)
                                          <a href="https://twitter.com/{{ $data->twitter }}" target="_blank"><i class="fab fa-twitter-square fa-2x"></i></a>
                                      @endif
                                    </td>

                                    <td>{{ $data->xp }}</td>
                                    <td>{{ $data->created_at->format('Y/m/d H:i') }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
@endsection
