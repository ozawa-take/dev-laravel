<!DOCTYPE html>
<html lang="ja">
<head>
    <title>お知らせ一覧</title>
    @include('head')
</head>

<body>
    @include('users.header')
    <div class="container-md mt-4">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item active" aria-current="page">お知らせ一覧</li>
            </ol>
        </nav>
        <div class="border rounded">
            <div class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">
                <p>お知らせ一覧</p>
            </div>
            <div class="m-2">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>日付</th>
                            <th>タイトル</th>
                        </tr>
                    </thead>
                    <tbody class="fw-bold">
                        @foreach($informations as $information)
                        <tr>
                            <td>{{$information->created_at->format('Y/m/d')}}</td>
                            <td><a class="none-underline" href="{{ route('users.informations.show', $information) }}">{{$information->title}}</a></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>
                    <p class="text-center">ページ 1/1</p>
                </div>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')

</body>
</html>