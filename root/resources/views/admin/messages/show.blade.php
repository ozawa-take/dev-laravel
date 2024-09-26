<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>メッセージ内容</title>
</head>
<body>
    @include('admin.header')
    <div class="mt-5 container">
        <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">受信一覧</a></li>
            @if ( $backRoute === route('admin.messages.sent'))
            <li class="breadcrumb-item"><a href="{{ $backRoute }}?page={{$currentPage}}">送信済み一覧</a></li>
            @elseif($backRoute == route('admin.messages.dust'))
            <li class="breadcrumb-item"><a href="{{ $backRoute }}?page={{$currentPage}}">ゴミ箱一覧</a></li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">メッセージ詳細</li>
            </ol>
        </nav>
        <div class="mb-2">
            @if ( $source == false )
                <a class="btn btn-success" href="{{ route('admin.messages.reply', $message) }}">返信</a>
            @endif
        </div>

        <div>
            <p>件名：{{ $message->title }}</p>
        </div>
        <div>
            <p>@if ( $source == true ) 宛先 @else 差出人 @endif：
                @foreach ($users as $user)
                    {{ $message->user_id == $user->id ? $user->username : '' }}
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
