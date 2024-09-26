<!DOCTYPE html>
<html lang="ja">
<head>
    {{-- CSRFトークン --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- CSS --}}
    <link rel="stylesheet" href="/css/course.css">
    @include('head')
    <title>コンテンツ</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
              <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">コース一覧</a></li>
              <li class="breadcrumb-item active" aria-current="page">コース名『{{ $course->title }}』のコンテンツ一覧</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between">
            <h2 class="col">『{{ $course->title }}』のコンテンツ</h2>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('admin.contents.create', $course)}}">&plus;追加</a>
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
                    <th class="col-2">コンテンツ名</th>
                    <th class="col-2 text-center">作成日時</th>
                    <th class="col-2 text-center">更新日時</th>
                    <th class="col-2 text-center">Actions</th>
                    </tr>
            </thead>

            <tbody id="tableBody">
                @foreach ($contents as $content)
                    <tr data-id="{{ $content->id }}">
                        <td class="align-middle">
                            <a href="{{ route('admin.contents.show', $content->id) }}">{{ $content->title }}</a>
                        </td>
                        <td class="align-middle text-center">{{ $content->created_at }}</td>
                        <td class="align-middle text-center">{{ $content->updated_at }}</td>

                        <td class="text-center">
                            <a class="btn btn-success" href="{{ route('admin.contents.edit', $content->id) }}">編集</a>

                            <form action="{{ route('admin.contents.duplicate', $content) }}" method="post" class="action">
                                @csrf
                                <input class="btn btn-info text-white action" type="submit" value="複製">
                            </form>

                            <form action="{{ route('admin.contents.destroy', $content) }}" method="post" class="action">
                                @csrf
                                @method('delete')
                                <input class="btn btn-danger" type="submit" value="削除"
                                onClick="return confirm('本当に削除しますか？');">
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @include('admin.contents.sort')

    {{-- footer --}}
    @include('footer')
</body>
</html>
