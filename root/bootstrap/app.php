<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->redirectGuestsTo(function(Request $request) {
            // ゲストが auth ミドルウェアによってどこにリダイレクトされるかを設定

            // [Laravel10] App/Http/Middleware/Authenticate.php => [Laravel11] bootstrap/app.php
            // 参考: Laravel 11.x 認証 認証されていないユーザーのリダイレクト
            //   https://readouble.com/laravel/11.x/ja/authentication.html#redirecting-unauthenticated-users
            // 参考: Laravel 10.x 認証 認証されていないユーザーのリダイレクト
            //   https://readouble.com/laravel/10.x/ja/authentication.html#redirecting-unauthenticated-users

            if (request()->routeIs('admin.*')) {
                return $request->expectsJson() ? null : route('admin.login.index');
            }

            return $request->expectsJson() ? null : route('users.login.index');
        });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
