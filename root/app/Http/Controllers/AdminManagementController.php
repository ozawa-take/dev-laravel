<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AdminPasswordRequest;
use App\Http\Requests\StoreAdminRequest;
use App\Http\Requests\UpdateAdminRequest;
use App\Models\Admin;
use App\Models\AdminLogs;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AdminManagementController extends Controller
{
    /**
     * Get the authenticated admin user.
     */
    private function getAdminUser(): Admin
    {
        return Auth::user();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $adminUser = $this->getAdminUser();
        $admins = Admin::all();
        $logins = AdminLogs::all();

        return view('admin.admin-management.index', compact('adminUser', 'admins', 'logins'));
    }

    /**
     * 検索機能
     */
    public function search(Request $request): JsonResponse
    {
        $search = $request->input('name');

        $results = Admin::leftJoin('admin_logs', 'admins.id', '=', 'admin_logs.admin_id')
            ->where('admins.username', 'LIKE', "%{$search}%")
            ->select('admins.*', 'admin_logs.updated_at as login_at')
            ->get();

        return response()->json($results);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): view
    {
        $adminUser = $this->getAdminUser();
        return view('admin.admin-management.create', compact('adminUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdminRequest $request): RedirectResponse
    {

        Admin::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', $request->username . 'を登録しました');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(): View
    {
        $adminUser = $this->getAdminUser();
        return view('admin.admin-management.edit', compact('adminUser'));
    }

    /**
     * パスワードの変更
     */
    public function password(Admin $admin): View
    {
        $adminUser = $this->getAdminUser();
        return view('admin.admin-management.password', compact('admin', 'adminUser'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function update(UpdateAdminRequest $request, Admin $admin): RedirectResponse
    {
        $admin->update([
            'username'     => $request->username,
            'mail_address' => $request->mail_address,
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', $request->username . 'の情報を更新しました');
    }

    /**
     * パスワードの更新
     */
    public function changeAdminPassword(AdminPasswordRequest $request, Admin $admin): RedirectResponse
    {

        if (!Hash::check($request->password, $admin->password)) {
            return redirect()->back()->with('error_message', '現在のパスワードが正しくありません');
        }

        $admin->update([
            'password' => Hash::make($request->new_password),
        ]);

        return redirect()->route('admin.admin-management.index')->with('message', 'パスワードが変更されました');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Admin $admin): RedirectResponse
    {
        $admin->delete();
        return redirect()->route('admin.admin-management.index')->with('danger', $admin->username . 'を削除しました');
    }
}
