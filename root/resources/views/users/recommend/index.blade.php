<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <link rel="stylesheet" href="/css/recommend.css">
    <link rel="stylesheet" href="/css/user_index.css">
    <title>動画相性診断</title>
</head>

<body>
@include('users.header')
<div class="container my-4">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item active" aria-current="page">おすすめ動画診断</li>
        </ol>
    </nav>
    <div class="panel panel-primary form-signin">
        <div class="panel-heading text-bg-primary text-center p-3">
            <h3>{{ $q['q_order']}}問目</h3>
        </div>
        <div class="panel-body text-bg-light p-3">
            <p>{{ $q['text'] }}</p>

            <form method="get" action="{{ route('users.select-courses.index') }}" class="answer">
                <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
                <input type="hidden" value="yes" name="answer">
                <input type="submit" value="はい" class="btn btn-primary">
            </form>

            <form method="get" action="{{ route('users.select-courses.index') }}" class="answer">
                <input type="hidden" value="{{ $q['q_id'] }}" name="q_id">
                <input type="hidden" value="no" name="answer">
                <input type="submit" value="いいえ" class="btn btn-danger">
            </form>
        </div>
    </div>
</div>
{{-- footer --}}
@include('footer')
</body>
</html>
