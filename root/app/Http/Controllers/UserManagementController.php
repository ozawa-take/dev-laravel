<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\UserPasswordRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Admin;
use App\Models\UserLogs;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class UserManagementController extends Controller
{
    private function getLoginUser(): User|Admin
    {
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $users = User::with('groups')->get();
        $logins = UserLogs::all();
        $adminUser = $this->getLoginUser();


        return view('admin.user-management.index', compact('users', 'logins', 'adminUser'));
    }

    // ユーザ側のパスワード変更画面
    public function userIndex(): View
    {
        $user = $this->getLoginUser();
        return view('users.passwordChange.index', compact('user'));
    }

    /**
     * 検索機能
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('name');

        $results = User::leftJoin('user_logs', 'users.id', '=', 'user_logs.user_id')
            ->where('users.username', 'LIKE', "%{$search}%")
            ->select('users.*', 'user_logs.updated_at as login_at')
            ->with('groups')
            ->get();

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $adminUser = $this->getLoginUser();

        return view('admin.user-management.create', compact('adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        User::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.user-management.index')->with('message', $request->username . 'を登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $adminUser = $this->getLoginUser();
        return view('admin.user-management.edit', compact('user', 'adminUser'));
    }

    /**
     * パスワードの変更
     */
    public function password(User $user): View
    {
        $adminUser = $this->getLoginUser();
        return view('admin.user-management.password', compact('user', 'adminUser'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {

        $user->update([
            'username'     => $request->username,
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.user-management.index')->with('message', $request->username . 'の情報を更新しました');
    }

    /**
     * パスワードの更新
     */
    public function changeUserPassword(UserPasswordRequest $request, User $user): RedirectResponse
    {
        // ユーザー側のパスワード変更ボタン押下時のルート名
        $userRouteName = 'users.password.change';
        // パスワード変更成功時のリダイレクト先
        $routeName = null;

        if (!Hash::check($request->password, $user->password)) {
            return redirect()->back()->with('error_message', '現在のパスワードが正しくありません');
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        //パスワード変更がユーザー側かシステム管理側かを判断
        if ($userRouteName == Route::currentRouteName()) {
            $routeName = 'users.index';
        } else {
            $routeName = 'admin.user-management.index';
        }

        return redirect()->route($routeName)->with('message', 'パスワードが変更されました');
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $user->delete();
        return redirect()->route('admin.user-management.index')->with('danger', $user->username . 'を削除しました');
    }
}
