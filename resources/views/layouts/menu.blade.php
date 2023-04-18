<!--
    <ul class="nav nav-pills nav-stacked noto" style="display:block;">
      @auth
        <li class="nav-item">
            <a href="{{ route('event.index') }}">大会一覧</a>
        </li>
      @endauth
      @if (session('event'))
        <li><a href="{{ route('event.detail', ['id' => session('event')]) }}">大会詳細</a></li>
        <li><a href="{{ route('team.index') }}">チーム</a></li>
        <li><a href="{{ route('wanted.index') }}">メンバー募集</a></li>
      @endif
      @auth
        <li class="nav-item">
          <a href="{{ route('logout') }}" onclick="event.preventDefault();
            document.getElementById('logout-form').submit();">logout</a>
        </li>
      @endauth
    </ul>
-->
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
