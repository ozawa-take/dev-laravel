<html lang="ja">

<head>
    @include('head')
    <title>お知らせ</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.informations.index') }}">お知らせ一覧</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a>お知らせ詳細</a></li>
            </ol>
        </nav>
        <div class="mt-3">
            <h3 class="rounded-top p-2 card-header text-success shadow-sm" style="background-color: #cdeee0">{{ $information->title }}</h3>
            <div class="border">
                <div class="d-flex flex-column">
                    <div class="mt-2 p-3">
                        <h4>本文</h4>
                        <div class="border">
                            <div class="p-2">
                                <ul style="padding-left: 0px !important;">
                                    <li class="list-unstyled">{{ $information->text }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2 p-3">
                        <div class="fw-bold fs-5">作成日時</div>
                        <div>{{ $information->created_at }}</div>
                    </div>
                    <div class="mt-2 p-3">
                        <div class="fw-bold fs-5">更新日時</div>
                        <div>{{ $information->updated_at }}</div>
                    </div>
                    <div class="mt-2 p-3">
                        <div class="fw-bold fs-5">Action</div>
                        <div class="action-btn">
                            <a class="btn btn-success" href="{{ route('admin.informations.edit', ['information' => $information->id]) }}">編集</a>
                            <form action="{{ route('admin.informations.destroy', $information) }}" method="post" class="d-inline">
                                @csrf
                                @method('delete')
                                <input class="btn btn-danger" type="submit" value="削除" onClick="return confirm('本当に削除しますか？');">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>

</html>