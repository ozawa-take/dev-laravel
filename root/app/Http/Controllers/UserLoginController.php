<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\UserLogs;
use Illuminate\Support\Facades\DB;

class UserLoginController extends Controller
{
    /**
     * ログイン画面
     */
    public function index(): View
    {
        return view('users.login');
    }

    /**
     * ログイン
     */
    public function login(UserLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            UserLogs::upsert(['user_id' => $user->id], 'user_id');
        }
        return redirect()->intended(route('users.index'));
    }

    /**
     * ログアウト
     */
    public function logout(): RedirectResponse
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return to_route('users.login.index');
    }
}
