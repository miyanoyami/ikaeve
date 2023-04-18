@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                    <form method="POST">
                        @csrf

                        @include('staff/form')

                        <div class="form-group row mb-0">
                            <div class="col-md-4 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary w-25">登録</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection
