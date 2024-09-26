<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>返信</title>
</head>
@include('admin.header')
<div class="mt-3 container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">受信一覧</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.messages.show', $message) }}">メッセージ詳細</a></li>
        <li class="breadcrumb-item active" aria-current="page">メッセージ返信</li>
        </ol>
    </nav>
    <div class="border">
        <div class="p-2 bg-secondary text-white">返信</div>

        <form action="{{ route('admin.messages.reply.store', $message) }}" method="post">
            @csrf
            <div class="mx-5 px-5">

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="title">件名
                        <span class="text-danger fw-bold">＊</span>
                    </label>
                    <div class="col-sm-10">
                        <input class="form-control" type="text" name="title" id="title" required value="Re:{{ $message->title }}">
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="user_id">宛先</label>
                    <div class="col-sm-10">
                        <select class="form-control" disabled>
                            <option>{{ $user->username }}</option>
                        </select>
                        <input type="text" name="user_id" id="user_id" value="{{ $user->id }}" hidden>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold" for="text">本文</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" name="text" id="text" rows="10"></textarea>
                    </div>
                </div>

                <div class="row m-3">
                    <label class="col-sm-2 col-form-label fw-bold">メッセージ内容</label>
                    <div class="col-sm-10">
                        <textarea class="form-control" rows="10" disabled>{{ $message->text }}</textarea>
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
