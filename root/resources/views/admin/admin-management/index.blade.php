<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <link rel="stylesheet" href="/css/mgmt.css">
    <title>管理者画面</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
            <li class="breadcrumb-item active" aria-current="page">管理者一覧</li>
            </ol>
        </nav>
        <div class="mb-5">
            @include('admin.menu')
            <div class="d-flex justify-content-between">
                <h2 class="me-4">管理者一覧</h2>
                <div class="me-auto">
                    <a class="btn btn-info" href="{{ route('admin.user-management.index') }}">⇆ ユーザー 一覧</a>
                </div>

                @can('is_system_admin',$adminUser)
                <div class="col-auto  ms-2">
                    <a class="btn btn-primary" href="{{ route('admin.admin-management.create')}}">&plus;追加</a>
                </div>
                @endcan
            </div>

            {{-- 検索 --}}
            <form id="searchForm" method="GET">
                @csrf
                <div class="d-flex justify-content-start">
                    <div class="p-2">
                        <label class="col-form-label" for="name">管理者ID</label>
                    </div>
                    <div class="p-2">
                        <input class="form-control" type="text" name="name" id="name">
                    </div>
                    <div class="p-2">
                        <input class="form-control btn btn-info" type="submit" value="検索">
                    </div>
                </div>
            </form>

            <div id="searchResults"></div>

            {{-- 登録・削除　メッセージ --}}
            @if (session('message'))
            <div class="alert alert-success">
                {{ session('message') }}
            </div>
            @elseif (session('danger'))
            <div class="alert alert-danger">
                {{ session('danger') }}
            </div>
            @endif

            <table class="table table-striped" id="sortable">
                <thead>
                    <tr>
                        <th class="col-2 pe-auto sort" data-sort="asc">管理者ID</th>
                        <th class="col-2 text-center sort" data-sort="asc">mail</th>
                        <th class="col text-center sort" data-sort="asc">最終ログイン日時</th>
                        <th class="col text-center sort" data-sort="asc">作成日時</th>
                        @can('is_system_admin',$adminUser)
                        <th class="col text-center sort" data-sort="asc"></th>
                        @endcan
                    </tr>
                </thead>

                <tbody>
                    @foreach ($admins as $admin)
                    <tr data-id="{{ $admin->id }}">
                        <td class="align-middle">{{ $admin->username }}</td>
                        <td class="align-middle text-center">{{ $admin->mail_address }}</td>

                        <td class="align-middle text-center">
                            @foreach ($logins as $login)
                            @if ($admin->id == $login->admin_id)
                            {{ $login->updated_at }}
                            @break
                            @endif
                            @endforeach
                        </td>

                        <td class="align-middle text-center">{{ $admin->created_at }}</td>
                        @can('is_system_admin',$adminUser)
                        <td class="text-center">
                            @if(!($admin->is_system_admin))
                            <form action="{{ route('admin.admin-management.destroy', $admin) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <input class="btn btn-danger" type="submit" value="削除"
                                onClick="return confirm('本当に削除しますか？');">
                            </form>
                            @endif
                        </td>
                        @endcan
                    </tr>
                    @endforeach
                </tbody>

            </table>
        </div>
    </div>
        @include('admin.sort')
        @include('admin.admin-management.search')

        {{-- footer --}}
        @include('footer')
    </body>

</html>
