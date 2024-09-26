<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <meta name="robots" content="noindex, nofollow">
</head>

<body class="text-center text-bg-light">
    <div class="p-3 mx-auto">
        <main>
            <h1>Laravel-learing</h1>
            <p class="lead">Laravel-learningのデフォルトページです。</p>
            <a href="{{ route('users.login.index') }}" class="btn btn-primary" rel="nofollow">ログイン画面へ</a>
        </main>
    </div>
    {{-- footer --}}
    @include('footer')
</body>

</html>
