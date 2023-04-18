@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @include('elements.flash_message')
              <ul class="nav nav-tabs">
                @foreach ($blocks as $block)
                  <li class="nav-item">
                    <a class="nav-link @if($selectBlock == $block->block) active @endif"
                      href="{{ route('tournament.edit', ['block' => $block->block]) }}">{{ $block->block }}</a>
                  </li>
                @endforeach
              </ul>
              <form method="POST">
                  @csrf
                  <h5 class="mt-2">{{ $selectBlock }}&nbsp;チーム一覧</h5>
                  @if (1 < count($blocks))
                      <div class="row">
                        <div class="col-1 ml-2 p-1">
                          <select name="changeBlock" class="form-control">
                            @foreach ($blocks as $val)
                              @if($selectBlock != $val->block)
                                <option value="{{ $val->block }}">{{ $val->block }}</option>
                              @endif
                            @endforeach
                          </select>
                        </div>
                        <div class="col-3">
                            <button type="submit" class="btn btn-primary mt-1">移動</button>
                        </div>
                      </div>
                  @endif
                  @foreach ($teams as $k => $team)
                    @if ($k % 12 == 0)
                      <div class="row">
                    @endif
                    @if ($k % 4 == 0)
                        <div class="col-md-4 col-12">
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered mt-1">
                                  <tr class="thead-light">
                                    <th class="p-1 text-center" colspan="3">
                                      @if (1 < count($blocks))<input type="checkbox" class="mr-2" name="sheet[]" value="{{ $team->sheet }}">@endif{{ $team->sheet }}</th>
                                  </tr>
                                  <tr class="thead-light">
                                    <th class="text-center">No</th>
                                    <th class="text-center">チーム名</th>
                                    <th class="text-center"></th>
                                  </tr>
                                  <tr>
                    @endif
                                    <td class="text-center">{{ $team->number }}</td>
                                    <td>
                                      <a href="{{ route('team.detail', ['id' => $team->id]) }}" target="_blank">{{ $team->name }}</a>
                                      @if ($team->abstention == 1)<span class="badge badge-warning">棄権</span>@endif
                                    </td>
                                    <td class="text-center">
                                      <button type="button" class="btn btn-sm btn-danger changeTeamBtn"
                                      data-id="{{ $team->id }}"><i class="fas fa-exchange-alt"></i></button>
                                    </td>
                                  </tr>
                    @if (($k + 1) % 4 == 0)
                            </table>
                          </div>
                        </div>
                    @endif
                    @if (($k + 1) % 12 == 0)
                      </div>
                    @endif
                  @endforeach
              </form>
          </div>
      </div>
      <input type="hidden" name="changeApi" id="changeApi" value="{{ route('tournament.changeTeam') }}">
@endsection
