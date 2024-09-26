<!DOCTYPE html>
<html lang="ja">

<head>
    @include('head')
    <title>新規メッセージ</title>
</head>

<body>
    @include('admin.header')
    <div class="mt-3 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">受信一覧</a></li>
            <li class="breadcrumb-item active" aria-current="page">新規メッセージ作成</li>
            </ol>
        </nav>
        <div class="border">
            <div class="p-2 bg-secondary text-white">受講者にメッセージを作成</div>

            <form action="{{ route('admin.messages.store') }}" method="post">
                @csrf
                <div class="mx-5 px-5">

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="title">件名
                            <span class="text-danger fw-bold">＊</span>
                        </label>
                        <div class="col-sm-10">
                            <input class="form-control" type="text" name="title" id="title" required>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="user_id">宛先</label>
                        <div class="col-sm-10">
                            <select class="form-control" name="user_id" id="user_id">
                                @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->username }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row m-3">
                        <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                        <div class="col-sm-10">
                            <textarea class="form-control" name="text" id="text" rows="5"></textarea>
                            <button class="form-contorl btn btn-primary mt-3" type="submit" name="sendType" value="{{ App\Enums\ActionEnum::SEND->value }}">送信</button>
                            <button class="form-contorl btn btn-secondary mt-3" type="submit" name="sendType" value="{{ App\Enums\ActionEnum::DRAFT->value }}">下書き</button>
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
