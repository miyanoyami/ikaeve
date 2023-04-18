@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                    <form method="POST">
                        @csrf

                        @include('event/form')

                        <div class="form-group row mb-0">
                            <div class="col-md-6 offset-md-3 text-center">
                                <button type="submit" class="btn btn-primary w-25">編集</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection
