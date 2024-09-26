<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>{{ $course_title }}</title>
</head>

<body>
    @include('users.header')
    <main class="container-md mt-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item active" aria-current="page">コンテンツ一覧</li>
            </ol>
        </nav>
        <div class="border rounded">
            <div class="rounded-top p-2 card-header text-primary shadow-sm" style="background-color: #d3e6fd">
                <p>{{ $course_title }}</p>
            </div>
            <div class="m-2">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>タイトル</th>
                            <th>学習開始日</th>
                            <th>前回学習日</th>
                            <th>完了</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold">
                        @foreach($contents as $content)
                        <tr>
                            <td><a class="none-underline" href="{{ route('users.contents.show', $content) }}">{{$content->title}}</a></td>
                            @if($content->getLog($user))
                            <td><p>{{ $content->getLog($user)->created_at }}</p></td>
                            <td><p>{{ $content->getLog($user)->updated_at }}</p></td>
                            <td><p>@if($content->getLog($user)->completed)☑@endif</p></td>
                            @else
                            <td><p>-</p></td>
                            <td><p>-</p></td>
                            <td><p></p></td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
    {{-- footer --}}
    @include('footer')
</body>

</html>