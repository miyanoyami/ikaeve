@extends('layouts.app')

@section('content')
        <div class="card-body">
            <div class="container-fluid">
              @include('elements.flash_message')
              @if ($isMobile)
                @include('tournament/nav_sp')
              @else
                @include('tournament/nav')
              @endif
              @include('tournament/table')
            </div>
        </div>
@endsection
