<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>ユーザーログイン画面</title>
    <link rel="stylesheet" href="/css/login.css">
</head>

<body>
    {{-- header --}}
    @include('users.header')

    {{-- body --}}

    <body>
        <div id="container">
            <div id="content" class="row">
                {{-- ログインフォーム --}}
                <div class="users-login">
                    <div class="panel panel-info form-signin">
                        <div class="panel-heading text-bg-info p-3">ユーザーログイン画面</div>
                        <div class="panel-body text-bg-light p-3">
                            <form action="{{ route('users.login.login') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <div class="required">
                                        <label for="username">
                                            ユーザー名
                                        </label>
                                        <div class="input text required">
                                            <input type="text" name="username" id="username" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="required">
                                        <label for="password">
                                            パスワード
                                        </label>
                                        <div class="input password required">
                                            <input type="password" name="password" id="password" class="form-control" required />
                                        </div>
                                    </div>
                                </div>
                                <div class="submit">
                                    @error('failed')
                                    <p style="color:red">{{ $message }}</p>
                                    @enderror
                                    <button class="btn btn-lg btn-primary btn-block" type="submit">ログイン</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
{{-- footer --}}
@include('footer')

</html>
