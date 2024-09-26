<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>メッセージ内容</title>
</head>
<body>
@include('users.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('users.index') }}">ユーザー画面トップ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('users.messages.index') }}">受信一覧</a></li>
            @if ( $source )
            <li class="breadcrumb-item"><a href="{{ $backRoute }}?page={{$currentPage}}">送信済み一覧</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">メッセージ内容</li>
            </ol>
        </nav>
        <div class="mb-2">
            @if ( $source == false )
                <a class="btn btn-success" href="{{ route('users.messages.reply', $message) }}">返信</a>
            @endif
        </div>

        <div>
            <p>件名：{{ $message->title }}</p>
        </div>
        <div>
            <p>@if ( $source == true ) 宛先 @else 差出人 @endif：
                @foreach ($admins as $admin)
                    {{ $message->admin_id == $admin->id ? $admin->username : '' }}
                @endforeach
            </p>
        </div>
        <div>
            <p>本文</p>
            {!! nl2br(htmlspecialchars($message->text)) !!}
        </div>
    </div>
    {{-- footer --}}
    @include('footer')
</body>
</html>
