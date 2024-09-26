<?php

use App\Http\Controllers\CourseController;
use App\Http\Controllers\AdminManagementController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\AdminMessageController;
use App\Http\Controllers\UserMessageController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\GroupController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\UserHomeController;
use App\Http\Controllers\InformationController;
use App\Http\Controllers\SelectCourseController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin/login')->name('admin.login')
->controller(AdminLoginController::class)->group(function () {
    // 管理ログイン画面
    Route::get('', 'index')->name('.index');
    // 管理ログイン
    Route::post('', 'login')->name('.login');
    // 管理ログアウト
    Route::delete('', 'logout')->name('.logout');
});

Route::prefix('users/login')->name('users.login')
->controller(UserLoginController::class)->group(function () {
    // 管理ログイン画面
    Route::get('', 'index')->name('.index');
    // 管理ログイン
    Route::post('', 'login')->name('.login');
    // 管理ログアウト
    Route::delete('', 'logout')->name('.logout');
});


// 管理ログイン後のみアクセス可
Route::middleware('auth:admin')->group(function () {
    Route::prefix('admin')->name('admin')->group(function () {
        // グループ
        Route::prefix('groups')->name('.groups')
        ->controller(GroupController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{group}', 'show')->name('.show');
            Route::get('{group}/edit', 'edit')->name('.edit');
            Route::patch('{group}', 'update')->name('.update');
            Route::delete('{group}', 'destroy')->name('.destroy');
        });

        // コース
        Route::prefix('courses')->name('.courses')
        ->controller(CourseController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::post('sort', 'sort')->name('.sort');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{course}/edit', 'edit')->name('.edit');
            Route::patch('{course}', 'update')->name('.update');
            Route::delete('{course}', 'destroy')->name('.destroy');
        });

        // コンテンツ
        Route::prefix('contents')->name('.contents')
        ->controller(ContentController::class)->group(function () {
            Route::get('{course}', 'index')->name('.index');
            Route::post('sort', 'sort')->name('.sort'); //並べ替え
            Route::get('create/{course}', 'create')->name('.create');
            Route::post('{course}', 'store')->name('.store');
            Route::get('{content}/show', 'show')->name('.show');
            Route::get('{content}/edit', 'edit')->name('.edit');
            Route::patch('{content}', 'update')->name('.update');
            Route::post('{content}/duplicate', 'duplicate')->name('.duplicate'); //複製
            Route::delete('{content}', 'destroy')->name('.destroy');
        });
        // 管理者一覧画面
        Route::prefix('admin-management')->name('.admin-management')
        ->controller(AdminManagementController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::post('search', 'search')->name('.search');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('edit', 'edit')->name('.edit');
            Route::get('{admin}/password', 'password')->name('.password');
            Route::post('{admin}/password', 'changeAdminPassword')->name('.changePassword');
            Route::patch('{admin}', 'update')->name('.update');
            Route::delete('{admin}', 'destroy')->name('.destroy');
        });

        // ユーザー　一覧画面
        Route::prefix('user-management')->name('.user-management')
        ->controller(UserManagementController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::post('search', 'search')->name('.search');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{user}/edit', 'edit')->name('.edit');
            Route::get('{user}/password', 'password')->name('.password');
            Route::post('{user}/password', 'changeUserPassword')->name('.changePassword');
            Route::patch('{user}', 'update')->name('.update');
            Route::delete('{user}', 'destroy')->name('.destroy');
        });

        //メッセージ機能
        Route::prefix('messages')->name('.messages')
        ->controller(AdminMessageController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('draft', 'draft')->name('.draft'); //下書き
            Route::get('sent', 'sent')->name('.sent'); //送信済み
            Route::get('dust', 'dust')->name('.dust'); //ゴミ箱
            Route::post('dust/{message}', 'restore')->name('.restore'); //復元
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{message}', 'show')->name('.show');
            Route::get('{message}/sent', 'sentShow')->name('.sent.show'); //送信済み
            Route::get('{message}/reply', 'reply')->name('.reply'); //返信メール
            Route::post('{message}', 'replyStore')->name('.reply.store'); //返信メール保存
            Route::get('{message}/edit', 'edit')->name('.edit');
            Route::patch('{message}', 'update')->name('.update');
            Route::delete('{message}', 'destroy')->name('.destroy');
            Route::post('{message}/hidden', 'hidden')->name('.hidden'); //非表示
        });

        //お知らせ画面一覧
        Route::prefix('informations')->name('.informations')
        ->controller(InformationController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{information}', 'adminShow')->name('.show');
            Route::get('{information}/edit', 'edit')->name('.edit');
            Route::patch('{information}', 'update')->name('.update');
            Route::delete('{information}', 'destroy')->name('.destroy');
        });
    });
});


// ユーザーログイン後のみアクセス可
Route::middleware('auth:web')->group(function () {

    Route::prefix('users')->name('users')->group(function () {

        // ユーザー側のトップページ
        Route::get('/', [UserHomeController::class, 'index'])->name('.index');

        //メッセージ機能
        Route::prefix('messages')->name('.messages')
        ->controller(UserMessageController::class)->group(function () {
            Route::get('', 'index')->name('.index');
            Route::get('draft', 'draft')->name('.draft'); //下書き
            Route::get('sent', 'sent')->name('.sent'); //送信済み
            Route::get('dust', 'dust')->name('.dust'); //ゴミ箱
            Route::post('dust/{message}', 'restore')->name('.restore'); //復元
            Route::get('create', 'create')->name('.create');
            Route::post('', 'store')->name('.store');
            Route::get('{message}', 'show')->name('.show');
            Route::get('{message}/sent', 'sentShow')->name('.sent.show'); //送信済み
            Route::get('{message}/reply', 'reply')->name('.reply'); //返信メール
            Route::post('{message}', 'replyStore')->name('.reply.store'); //返信メール保存
            Route::get('{message}/edit', 'edit')->name('.edit');
            Route::patch('{message}', 'update')->name('.update');
            Route::delete('{message}', 'destroy')->name('.destroy');
            Route::post('{message}/hidden', 'hidden')->name('.hidden'); //非表示
        });

        //コンテンツ表示画面
        Route::prefix('contents')->name('.contents')
        ->controller(ContentController::class)->group(function () {
            Route::get('/{course}', 'list')->name('.index');
            Route::get('view/{content}', 'view')->name('.show');
            Route::post('view/{content}', 'record')->name('.record'); //閲覧履歴の記録
        });

        //お知らせ閲覧機能
        Route::prefix('informations')->name('.informations')
        ->controller(InformationController::class)->group(function () {
            Route::get('', 'list')->name('.list');
            Route::get('{information}', 'show')->name('.show');
        });

         // パスワード変更機能
        Route::prefix('password')->name('.password')
        ->controller(UserManagementController::class)->group(function () {
            Route::get('/', 'userIndex')->name('.index');
            Route::post('/{user}', 'changeUserPassword')->name('.change');
        });

        // おすすめ動画診断機能
        Route::prefix('select-courses')->name('.select-courses')
        ->controller(SelectCourseController::class)->group(function () {
            Route::get('', 'index')->name('.index');
        });
    });
});
