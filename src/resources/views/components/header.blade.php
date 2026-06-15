<header class="header">  
    <div class="header-logo-group">
        <a href="/" class="header-logo-link">
            <img src="{{ asset('images/COACHTECHヘッダーロゴ.png') }}" alt="COACHTECH" class="logo-image">
        </a>
    </div>
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )
    <nav class="header__navi">
        <ul class="header__navi-ul">
            @auth
                @if(Auth::user()->admin_status)
                <li><a class="header__navi-item" href="/admin/attendance/list">勤怠一覧</a></li>
                <li><a class="header__navi-item" href="/admin/staff/list">スタッフ一覧</a></li>
                <li><a class="header__navi-item" href="/stamp_correction_request/list">申請一覧</a></li>
                @else
                <li><a class="header__navi-item" href="/attendance">勤怠</a></li>
                <li><a class="header__navi-item" href="/attendance/list">勤怠一覧</a></li>
                <li><a class="header__navi-item" href="/stamp_correction_request/list">申請</a></li>
                <li><a class="header__navi-item" href="">レポート未</a></li>
                @endif
                <li>
                    <form action="/logout" method="post">
                        @csrf
                        <button class="header__logout">ログアウト</button>
                    </form>
                </li>
            @endauth
        </ul>
    </nav>
    @endif
</header>