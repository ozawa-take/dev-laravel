<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>新規お知らせ登録</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-3 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.informations.index') }}">お知らせ一覧</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a>新規お知らせ登録</a></li>
            </ol>
        </nav>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規お知らせ登録</div>

            @if($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('admin.informations.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">タイトル
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="text" id="text" rows="5"></textarea>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold">対象グループ
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <select class="form-select" name="group[]" id="group" multiple>
                                @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->group_name }}</option>
                                @endforeach
                            </select>
                            <input class="form-control btn btn-primary mt-3" type="submit" value="登録">
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>

</html>