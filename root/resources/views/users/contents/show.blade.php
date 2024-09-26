<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>{{ $title }}</title>
</head>

<body>
    @include('users.header')
    <main class="container-md mt-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.contents.index', ['course' => $content->course_id]) }}">コンテンツ一覧</a></li>
            <li class="breadcrumb-item active" aria-current="page">コンテンツ</li>
            </ol>
        </nav>
        <div class="container border py-2  my-2 rounded">
            <div class="ratio ratio-16x9">
                <iframe src="https://www.youtube.com/embed/{{ $content->youtube_video_id }}"></iframe>
            </div>
        </div>
    </main>
    <footer>
        <div class="container d-flex justify-content-end align-items-center p-4">
            <div class="row">
                <form action="{{route('users.contents.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="1">
                    <button type="submit" class="btn btn-primary">完了</button>
                </form>
                <form action="{{route('users.contents.record',$content)}}" method="post" class="col">
                    @csrf
                    <input type="hidden" id="log" name="log" value="0">
                    <button type="submit" class="btn btn-danger">中断</button>
                </form>
            </div>
        </div>
    </footer>
    {{-- footer --}}
    @include('footer')
</body>

</html>