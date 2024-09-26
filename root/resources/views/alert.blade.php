{{-- 登録・削除　メッセージ --}}
@if (session('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
@elseif (session('danger'))
    <div class="alert alert-danger">
        {{ session('danger') }}
    </div>
@elseif (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
