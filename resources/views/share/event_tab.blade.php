        <ul class="nav nav-tabs">
          <li class="nav-item">
            <a class="nav-link @if (Route::is('team.*')) active @endif" href="{{ route('team.index') }}">チーム</a>
          </li>
          <!--
          <li class="nav-item">
            <a class="nav-link @if (Route::is('member.*')) active @endif" href="{{ route('member.index') }}">メンバー</a>
          </li>
        -->
          <li class="nav-item">
            <a class="nav-link @if (Route::is('wanted.*')) active @endif" href="{{ route('wanted.index') }}">メンバー募集</a>
          </li>
        </ul>
