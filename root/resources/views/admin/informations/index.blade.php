<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    @include('admin.sort')
    <link rel="stylesheet" href="/css/information.css">
    <title>お知らせ一覧</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-current="page">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
                <li class="breadcrumb-item active" aria-current="page">お知らせ一覧</li>
            </ol>
        </nav>
        @include('admin.menu')
        <div class="d-flex justify-content-between">
            <h2 class="col">お知らせ一覧</h2>
            <div class="col-auto">
                <a class="btn btn-primary" href="{{ route('admin.informations.create')}}">&plus;追加</a>
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

        <table class="table table-striped" id="sortable">
            <thead>
                <tr>
                    <th class="col-3 sort" data-sort="asc">タイトル</th>
                    <th class="col-3">対象グループ</th>
                    <th class="col-2 text-center sort" data-sort="asc">作成日時</th>
                    <th class="col-2 text-center sort" data-sort="asc">更新日時</th>
                    <th class="col-2 text-center">Actions</th>
                </tr>
            </thead>

            <tbody>

                @foreach ($informations as $information)
                <tr data-id="{{ $information->id }}">
                    <td class="align-middle"><a href="{{ route('admin.informations.show', $information) }}">{{ $information->title }}</td>
                    <td class="align-middle text-start">
                        @foreach($information->groups as $group)
                        {{ $group->group_name }}
                        @endforeach
                    </td>
                    <td class="align-middle text-center">{{ $information->created_at }}</td>
                    <td class="align-middle text-center">{{ $information->updated_at }}</td>

                    <td class="text-center">
                        <a class="btn btn-success" href="{{ route('admin.informations.edit', $information) }}">編集</a>

                        <form action="{{ route('admin.informations.destroy', $information) }}" method="post" class="d-inline">
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
    {{-- footer --}}
    @include('footer')
</body>

</html>
