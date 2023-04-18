@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                    <form method="POST">
                        @csrf

                        @include('member/form')

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">編集</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection
