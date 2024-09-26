<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>パスワード変更</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-3 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.edit', $admin) }}">管理者編集</a></li>
            <li class="breadcrumb-item active" aria-current="page">パスワード変更</li>
            </ol>
        </nav>
        <div class="border">
            <div class="p-2 bg-secondary text-white">パスワード変更</div>

            @if (session('error_message'))
                <div class="alert alert-danger">
                    {{ session('error_message') }}
                </div>
            @elseif ($errors->any())
                <div class="alert alert-danger">
                    @foreach($errors->all() as $error)
                        {{ $error }}<br>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('admin.admin-management.changePassword', $admin) }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="password">現在のパスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" name="password" id="password" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="new_password">新しいパスワード
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" name="new_password" id="new_password" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-3 col-form-label fw-bold" for="new_password_confirmation">パスワードを確認
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-9">
                            <input class="form-control" type="password" name="new_password_confirmation" id="new_password_confirmation" required>
                            {{-- 登録ボタン --}}
                            <input class="form-contorl btn btn-primary mt-3" type="submit" value="パスワードを変更">
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>