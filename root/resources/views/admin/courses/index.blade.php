<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    {{-- CSRFトークン --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- CSS --}}
    <link rel="stylesheet" href="/css/course.css">
    <title>コース一覧</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
                <li class="breadcrumb-item active" aria-current="page">コース一覧</li>
            </ol>
        </nav>
        @include('admin.menu')
        <div class="d-flex justify-content-between">
            <h2 class="col">コース一覧</h2>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('admin.courses.create')}}">&plus;追加</a>
            </div>
        </div>
        {{-- 登録・削除　メッセージ --}}
        <div id="message">
            @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
            @elseif (session('danger'))
            <div class="alert alert-danger">
                {{ session('danger') }}
            </div>
            @endif
        </div>

        <div class="alert alert-warning">
            ドラッグ＆ドロップでコースの並び順が変更できます。
            <button class="btn btn-primary me-md-2" id="saveBtn" disabled>変更確定</button>
            <button class="btn btn-secondary me-md-2" id="backBtn" disabled>元に戻す</button>
        </div>

        <table class="table table-striped" id="sortable">
            <thead>
                <tr>
                    <th class="col-6">コース名</th>
                    <th class="col-2 text-center">作成日時</th>
                    <th class="col-2 text-center">更新日時</th>
                    <th class="col-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody id="tableBody">

                @foreach ($courses as $course)
                <tr data-id="{{ $course->id }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.contents.index', $course) }}">{{ $course->title }}</a>
                    </td>
                    <td class="align-middle text-center">{{ $course->created_at }}</td>
                    <td class="align-middle text-center">{{ $course->updated_at }}</td>

                    <td class="text-center">
                        <a class="btn btn-success" href="{{ route('admin.courses.edit', $course->id) }}">編集</a>

                        <form action="{{ route('admin.courses.destroy', $course) }}" method="post" class="d-inline">
                            @csrf
                            @method('delete')
                            <input class="btn btn-danger" type="submit" value="削除" onClick="return confirm('本当に削除しますか？');">
                        </form>

                    </td>
                </tr>
                @endforeach

            </tbody>

        </table>
    </div>
    @include('admin.courses.sort')

    {{-- footer --}}
    @include('footer')
</body>

</html>