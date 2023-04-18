@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
                @include('elements.flash_message')
                <form method="POST">
                    @csrf
                    @if($errors->has('title.*')) <span class="text-danger">{{ $errors->first('title.*') }}</span> @endif
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="questionTable" style="max-width:700px;">
                            <thead>
                            <tr class="thead-light text-center">
                                <th></th>
                                <th>項目</th>
                                <th>必須/任意</th>
                            </tr>
                            </thead>
                            <tbody>
                              @foreach ($datas as $k => $data)
                                <tr>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger deleteTrBtn"><i class="fas fa-minus fa-lg"></i></button>
                                        <input type="hidden" name="target[]" value="{{ $data->id }}">
                                    </td>
                                    <td>
                                        <input id="title[]" type="text" class="form-control @error('title.'.$k) is-invalid @enderror" name="title[]"
                                          value="{{ old('title.'.$k, isset($data) ? $data->title : '') }}">
                                    </td>
                                    <td>
                                        <select name="required[]" class="form-control">
                                            <option value="0" {{ old('required.'.$k, isset($data->required) && $data->required == 0 ? 'selected' : '') }}>任意</option>
                                            <option value="1" {{ old('required.'.$k, isset($data->required) && $data->required == 1 ? 'selected' : '') }}>必須</option>
                                        </select>
                                    </td>
                                </tr>
                              @endforeach
                              @if (count($datas) == 0)
                                  <tr>
                                      <td class="text-center">
                                          <button type="button" class="btn btn-danger deleteTrBtn"><i class="fas fa-minus fa-lg"></i></button>
                                          <input type="hidden" name="target[]" value="0">
                                      </td>
                                      <td>
                                          @if (isset($k))
                                            <input id="title[]" type="text" class="form-control @error('title') is-invalid @enderror" name="title[]"
                                              value="{{ old('title.'.$k) }}" autofocus>
                                          @else
                                            <input id="title[]" type="text" class="form-control @error('title') is-invalid @enderror" name="title[]"
                                              value="{{ old('title.0') }}" autofocus>
                                          @endif
                                      </td>
                                      <td>
                                          <select name="required[]" class="form-control">
                                              <option value="0">任意</option>
                                              <option value="1">必須</option>
                                          </select>
                                      </td>
                                  </tr>
                                @endif
                            </tbody>
                        </table>
                        <button type="button" id="addTrBtn" class="btn btn-success"><i class="fas fa-plus fa-lg"></i></button>
                        <button type="submit" class="btn btn-info">保存</button>
                    </div>
                </form>
            </div>
        </div>
@endsection
