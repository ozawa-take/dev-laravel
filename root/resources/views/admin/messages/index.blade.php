<!DOCTYPE html>
<html lang="ja">
<head>
    @include('head')
    <title>メッセージ一覧</title>
</head>

<body>
@include('admin.header')
<div class="mt-5 container">
    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ route('admin.admin-management.index') }}">管理画面トップ</a></li>
          <li class="breadcrumb-item active" aria-current="page">受信一覧</li>
        </ol>
      </nav>
    <div class="d-flex justify-content-between">
        <h2 class="col">受信一覧</h2>
        <div class="col-auto">
            <a class="btn btn-primary" href="{{ route('admin.messages.create')}}">&plus;追加</a>
            <a class="btn btn-success" href="{{ route('admin.messages.draft')}}">下書き</a>
            <a class="btn btn-secondary" href="{{ route('admin.messages.sent')}}">送信済み</a>
            <a class="btn btn-danger" href="{{ route('admin.messages.dust')}}">ゴミ箱</a>
        </div>
    </div>

    @include('alert')

    <table class="table table-striped">
        <thead>
            <tr>
                <th class="col-3">件名</th>
                <th class="col-2">差出人</th>
                <th class="col-5">本文</th>
                <th class="col-2 text-center">受信日時</th>
                <th class="col-2 text-center">Actions</th>
                </tr>
        </thead>

        <tbody>

            @foreach ($messages as $message)
                <tr data-id="{{ $message->id }}">
                    <td class="align-middle">
                        <a href="{{ route('admin.messages.show', $message) }}">
                            {{ Str::limit($message->title, $limit = 28, $end = '...') }}
                        </a>
                        @if ($message->is_replied == 1) &#9166; @endif
                    </td>
                    <td class="align-middle">
                        @foreach ($users as $user)
                            {{ $message->user_id == $user->id ? $user->username : ''}}
                        @endforeach
                    </td>
                    <td class="align-middle">{{ Str::limit($message->text, $limit = 50, $end = '...') }}</td>
                    <td class="align-middle text-center">{{ $message->created_at }}</td>

                    <td class="text-center">
                        <form action="{{ route('admin.messages.hidden', $message) }}" method="post" class="d-inline">
                            @csrf
                            <input class="btn btn-danger" name="action" type="submit" value="削除"
                            onClick="return confirm('本当に削除しますか？');">
                        </form>
                    </td>

                </tr>
            @endforeach
        </tbody>
    </table>
    {{ $messages->links('pagination::bootstrap-5') }}
</div>
{{-- footer --}}
@include('footer')
</body>
</html>
