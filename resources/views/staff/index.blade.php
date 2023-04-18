@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <form method="POST">
                    @csrf
                    <div class="form-group row mb-3 ml-1">
                        <div class="col-md-3 col-4 p-1">
                            <input type="text" class="form-control" name="name" value="{{ $search['name'] ?? '' }}" placeholder="名前">
                        </div>
                        <div class="col-md-3 col-4 p-1">
                            <input type="text" class="form-control" name="mail" value="{{ $search['mail'] ?? '' }}" placeholder="mail">
                        </div>
                        <div class="col-md-2 col-1 p-1">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-search fa-lg mr-1"></i></button>
                        </div>
                        <div class="col-md-4 col-1 p-1 text-right">
                            <a href="{{ route('staff.regist') }}" class="btn btn-primary">登録</a>
                        </div>
                    </div>
                </form>
                @if (0 < count($datas))
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                            <tr class="thead-light text-center">
                                <th></th>
                                <th>名前</th>
                                <th>mail</th>
                                <th>権限</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $data)
                                <tr>
                                    <td class="text-center">
                                      <a href="{{ route('staff.edit', ['id' => $data->id]) }}"><i class="fas fa-edit fa-lg"></i></a>
                                    </td>
                                    <td>{{ $data->name }}</td>
                                    <td>{{ $data->email }}</td>
                                    <td>@if ($data->role == config('user.role.staff')) スタッフ @else 管理者 @endif</td>
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
