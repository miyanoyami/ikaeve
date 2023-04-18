@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
              @endif
              <br>
              <br>
              @if ($first)
                  <h5><img src="{{ asset('img/1.png') }}" alt="1" class="rounded-circle" style="background-color:#e6b422;">&nbsp;&nbsp;{{ $first[0]->name }}</h5>
              @endif
              @if ($second)
                  <h5><img src="{{ asset('img/2.png') }}" alt="2" class="rounded-circle" style="background-color:#c0c0c0;">&nbsp;&nbsp;{{ $second[0]->name }}</h5>
              @endif
              @if ($third)
                @foreach ($third as $val)
                  <h5><img src="{{ asset('img/3.png') }}" alt="3" class="rounded-circle" style="background-color:#976B2F;">&nbsp;&nbsp;{{ $val->name }}</h5>
                @endforeach
              @endif
            </div>
        </div>
@endsection
