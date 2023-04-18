@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                    <form method="POST">
                        @csrf

                        @include('elements.flash_message')
                        <div class="form-group row mt-5">
                            <label for="name" class="col-md-4 col-form-label text-md-right required">パスワード</label>

                            <div class="col-md-6">
                                <input type="password" class="form-control @error('password') is-invalid @enderror col-md-10" name="password"
                                value="" required>
                                @if($errors->has('password')) <span class="text-danger">{{ $errors->first('password') }}</span> @endif
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-4 offset-md-4 text-center">
                                <button type="submit" class="btn btn-primary">更新</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
@endsection
