<header>
    <nav class="navbar p-0 bg-primary">
        <div class="d-flex justify-content-between align-items-center container-fluid">
            <a href="{{ route('users.index') }}" style="text-decoration: none;">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learning</h2>
            </a>
            <ul class="nav me-2 text-white">
                @isset($user)
                <li class="nav-item border-end p-1 d-flex align-items-center">ようこそ{{ $user->username }}さん</li>
                <li class="nav-item border-end p-1 d-flex align-items-center">
                    <a class="btn btn-primary" href="{{ route('users.messages.index') }}">メッセージ</a>
                </li>
                <li id="setting" class="nav-item border-end p-1 d-flex align-items-center">
                    <a class="btn btn-primary" href="{{ route('users.password.index') }}">パスワード変更</a>
                </li>
                <li class="nav-item p-1 d-flex align-items-center">
                    <form method="POST" action="{{ route('users.login.logout') }}">
                        @method('DELETE')
                        @csrf
                        <button class="btn btn-primary text-white" type="submit">ログアウト</button>
                    </form>
                </li>
                @endisset
            </ul>
        </div>
    </nav>
</header>
