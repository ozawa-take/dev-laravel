<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>新規コース登録</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-3 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.courses.index') }}">コース一覧</a></li>
                <li class="breadcrumb-item active" aria-current="page">新規コース登録</li>
            </ol>
        </nav>
        <div class="border">
            <div class="p-2 bg-secondary text-white">新規コース登録</div>

            <form action="{{ route('admin.courses.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">コース名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" required>
                        </div>
                    </div>

                    <div class="m-3">
                        <input class="form-contorl btn btn-primary mt-3" type="submit" value="登録">
                    </div>

                </div>
            </form>
        </div>
    </div>
</body>
{{-- footer --}}
@include('footer')

</html>