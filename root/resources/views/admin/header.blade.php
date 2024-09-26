<header>
    <nav class="navbar p-0 bg-primary">
        <div class="d-flex justify-content-between align-items-center container-fluid">
            <a href="{{ route('admin.admin-management.index') }}" style="text-decoration: none;">
                <h2 class="navbar-brand ms-1 fs-3 text-white">laravel-learning</h2>
            </a>
            <ul class="nav me-2 text-white">
                @isset($adminUser)
                <li class="nav-item border-end p-1 d-flex align-items-center">ようこそ{{ $adminUser->username }}さん</li>
                <li class="nav-item border-end p-1 d-flex align-items-center">
                    <a class="btn btn-primary" href="{{ route('admin.messages.index') }}">メッセージ</a>
                </li>
                <li id="setting" class="nav-item border-end p-1 d-flex align-items-center">
                    <a class="btn btn-primary" href="{{ route('admin.admin-management.edit') }}">アカウント設定</a>
                </li>
                <li class="nav-item p-1 d-flex align-items-center">
                    <form method="POST" action="{{ route('admin.login.logout') }}">
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
