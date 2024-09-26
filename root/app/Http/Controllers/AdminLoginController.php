<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\AdminLogs;

class AdminLoginController extends Controller
{
    /**
     * ログイン画面
     */
    public function index(): View
    {
        return view('admin.login');
    }

    /**
     * ログイン
     */
    public function login(AdminLoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            AdminLogs::upsert(['admin_id' => $user->id], 'admin_id');
        }
        return redirect()->intended(route('admin.admin-management.index'));
    }

    /**
     * ログアウト
     */
    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        session()->invalidate();
        session()->regenerateToken();
        return to_route('admin.login.index');
    }
}
