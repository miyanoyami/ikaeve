@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                    <form method="POST">
                        @csrf

                        @include('wanted/form')

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary submit" data-action="{{ route('wanted.edit', ['id' => $data->id]) }}">編集</button>
                                <button type="submit" class="btn btn-danger submit" data-action="{{ route('wanted.delete', ['id' => $data->id]) }}">削除</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection
