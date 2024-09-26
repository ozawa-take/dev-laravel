<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>動画相性診断</title>
</head>

<body>
@include('users.header')
<div class="container my-4 center-block text-center">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item active" aria-current="page">おすすめ動画コース診断</li>
        </ol>
    </nav>

    <div class="panel panel-primary form-signin">
        <div class="panel-body text-bg-light p-3">
            <p>回答ありがとうございます。診断結果が出ました。</p>
            <p>あなたへのおすすめ動画コースは{{ $course->title }}です。</p>
            <button class="btn btn-lg btn-primary btn-block" type="submit">
                <a href="{{ route('users.contents.index', $course) }}" class="text-white">おすすめ動画コースに進む</a>
            </button>
        </div>
    </div>
</div>
{{-- footer --}}
@include('footer')
</body>
</html>
