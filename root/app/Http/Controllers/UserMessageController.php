<?php

namespace App\Http\Controllers;

use App\Enums\ActionEnum;
use App\Http\Requests\UserMessageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\AdminMessage;
use App\Models\UserMessage;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\RedirectResponse;

class UserMessageController extends Controller
{
    private const DEFAULT_PAGE_NUMBER = 1;

    /**
     * ログインユーザーのIDを取得
     */
    private function getUserId(): int
    {
        return Auth::guard('web')->user()->id;
    }

    /**
     * 管理者情報取得
     */
    private function getAdminAll()
    {
        return Admin::all();
    }

    private function getCurrentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): view
    {
        $userId = $this->getUserId();
        $messages = AdminMessage::withTrashed()
            ->where('user_id', $userId)
            ->where('action', '=', ActionEnum::SEND)
            ->where('is_hidden', '=', false)
            ->orderByDesc('id')
            ->paginate(config('project.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        return view('users.messages.index', compact('messages', 'admins', 'user'));
    }

    /**
     * 下書き一覧
     */
    public function draft(Request $request): view
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
            ->where('action', '!=', ActionEnum::SEND)
            ->orderByDesc('updated_at')
            ->paginate(config('project.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        return view('users.messages.draftIndex', compact('messages', 'admins', 'user'));
    }

    /**
     * 送信済み一覧
     */
    public function sent(Request $request): view
    {
        $userId = $this->getUserId();
        $messages = UserMessage::where('user_id', $userId)
            ->where('action', '=', ActionEnum::SEND)
            ->orderByDesc('updated_at')
            ->paginate(config('project.ITEMS_PER_PAGE'));
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        return view('users.messages.sentIndex', compact('messages', 'admins', 'user'));
    }

    /**
     * ゴミ箱
     */
    public function dust(Request $request): view
    {
        $userId = $this->getUserId();
        $userMessages = UserMessage::onlyTrashed()->where('user_id', $userId)->get();
        $messages = $userMessages->map(function ($item) {
            $item->is_hidden = false;
            return $item;
        });
        $adminMessages  = AdminMessage::where('is_hidden', true)->where('user_id', $userId)->get();
        $combinedMessages = $messages->concat($adminMessages)->sortByDesc('updated_at');
        $user = $this->getCurrentUser();
        Session::put('pageNumber', $request->get('page', self::DEFAULT_PAGE_NUMBER));
        $action = ActionEnum::cases();

        // カスタムページネーション
        $ItemsPerPage = config('project.ITEMS_PER_PAGE');
        $page = $request->get('page', self::DEFAULT_PAGE_NUMBER);
        $paginator = new LengthAwarePaginator(
            $combinedMessages->forPage($page, $ItemsPerPage),
            $combinedMessages->count(),
            $ItemsPerPage,
            $page,
            ['path' => route('users.messages.dust')]
        );

        return view('users.messages.dust', compact('paginator', 'action', 'user'));
    }

    // 復元
    public function restore(int $message): RedirectResponse
    {
        $record = UserMessage::withTrashed()->find($message);
        $record->restore();
        return redirect()->back()->with('success', $record->title . 'を復元しました。');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): view
    {
        $source = $request->input('source');
        if ($source === 'draft') {
            $backRoute = route('users.messages.draft');
        } elseif ($source === 'send') {
            $backRoute = route('users.messages.sent');
        } elseif ($source === 'dust') {
            $backRoute = route('users.messages.dust');
        } else {
            $backRoute = route('users.messages.index');
        }
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
        return view('users.messages.create', compact('admins', 'currentPage', 'backRoute', 'user'));
    }

    /**
     * Store a newly created resource in storage.f
     */
    public function store(UserMessageRequest $request): RedirectResponse
    {
        $userId = $this->getUserId();
        $sendType = (int) $request->input('sendType');
        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
            'action'   => $sendType
        ];

        switch ($sendType) {
            case ActionEnum::SEND->value:
                UserMessage::create($data);
                return redirect()->route('users.messages.index')->with('message', 'メッセージを送信しました');

            case ActionEnum::DRAFT->value:
                UserMessage::create($data);
                return redirect()->route('users.messages.draft')->with('message', '下書きを保存しました');

            default:
                abort(400, '不正なリクエストです。');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(AdminMessage $message, Request $request): view
    {
        $source = $request->input('source');
        if ($source === 'dust') {
            $source = true;
            $backRoute = route('users.messages.dust');
        } else {
            $source = false;
            $backRoute = route('users.messages.index');
        }
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
        return view('users.messages.show', compact('message', 'admins', 'source', 'currentPage', 'backRoute', 'user'));
    }

    public function sentShow(UserMessage $message, Request $request): view
    {
        $source = true;
        $backRoute = route('users.messages.sent');
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);
        return view('users.messages.show', compact('message', 'source', 'admins', 'currentPage', 'backRoute', 'user'));
    }

    /**
     * 返信画面
     */
    public function reply(AdminMessage $message): view
    {
        $admin = $message->admin;
        $user = $this->getCurrentUser();
        return view('users.messages.reply', compact('message', 'admin', 'user'));
    }

    /**
     * 返信登録
     */
    public function replyStore(UserMessageRequest $request, $message): RedirectResponse
    {
        $userId = $this->getUserId();
        $sendType = (int) $request->input('sendType');
        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
            'reply_message_id' => $message,
            'action'   => $sendType
        ];
        switch ($sendType) {
            case ActionEnum::SEND->value:
                UserMessage::create($data);
                // 返信フラッグ
                    $adminMessage = AdminMessage::find($message);
                    $adminMessage->is_replied = true;
                    $adminMessage->save();
                return redirect()->route('users.messages.index', compact('message'))->with('message', 'メッセージを返信しました');

            case ActionEnum::DRAFT->value:
                UserMessage::create($data);
                return redirect()->route('users.messages.index', compact('message'))->with('message', '下書きを保存しました');

            default:
                abort(400, '不正なリクエストです。');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UserMessage $message): view
    {
        $admins = $this->getAdminAll();
        $user = $this->getCurrentUser();
        $currentPage = Session::get('pageNumber', self::DEFAULT_PAGE_NUMBER);

        if ($message->action === ActionEnum::NO_REPLY) {
            $reply = AdminMessage::find($message->reply_message_id);
        } else {
            $reply = null;
        }

        return view('users.messages.edit', compact('message', 'admins', 'reply', 'currentPage', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserMessageRequest $request, UserMessage $message): RedirectResponse
    {
        $userId = $this->getUserId();
        $sendType = (int) $request->input('sendType');

        $data = [
            'admin_id' => $request->admin_id,
            'user_id'  => $userId,
            'title'    => $request->title,
            'text'     => $request->text,
            'action'   => $sendType,
        ];

        if ($sendType === ActionEnum::DRAFT->value) {
            if ($message->action === ActionEnum::NO_REPLY) {
                $data['action'] = ActionEnum::NO_REPLY;
            }
            $message->update($data);
            return redirect()->route('users.messages.draft')->with('message', '下書きを保存しました');
        }

        if ($message->action === ActionEnum::NO_REPLY) {
            // 返信フラッグ
            $adminMessage = AdminMessage::find($message->reply_message_id);
            $adminMessage->is_replied = true;
            $adminMessage->save();
        }
        $message->update($data);
        return redirect()->route('users.messages.index')->with('message', 'メッセージを送信しました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(UserMessage $message): RedirectResponse
    {
        $message->delete();
        return Redirect::back()->with('danger', $message->title . 'を削除しました');
    }

    /**
     * 非表示
     */
    public function hidden(AdminMessage $message): RedirectResponse
    {
        $hidden = AdminMessage::find($message->id);

        if ($message->is_hidden) {
            $hidden->update(['is_hidden' => false]);
            return redirect()->route('users.messages.dust')->with('success', $message->title . 'を復元しました');
        } else {
            $hidden->update(['is_hidden' => true]);
            return redirect()->route('users.messages.index')->with('danger', $message->title . 'を削除しました');
        }
    }
}
