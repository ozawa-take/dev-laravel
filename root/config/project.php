<?php

return [
    'database' => [

        /*
        |--------------------------------------------------------------------------
        | [project.database.system_admin] システム管理者情報
        |--------------------------------------------------------------------------
        |
        | Seeder 実行時に追加されるシステム管理者情報を設定する。
        |
        */

        'system_admin' => [
            'name' => env('SYSTEM_ADMIN_NAME', 'system_admin'),
            'password' => env('SYSTEM_ADMIN_PASSWORD', 'password'),
            'email' => env('SYSTEM_ADMIN_EMAIL', 'system_admin@admin')
        ],

        /*
        |--------------------------------------------------------------------------
        | [project.database.auto_increment] 各テーブルのAUTO_INCREMENT値
        |--------------------------------------------------------------------------
        |
        | Migration 実行時の各テーブルのid開始値を設定する。
        |
        */

        'auto_increment' => [
            'users' => 110001,
            'admins' => 120001,
            'user_logs' => 130001,
            'admin_logs' => 140001,
            'user_messages' => 150001,
            'admin_messages' => 160001,
            'information' => 170001,
            'groups' => 180001,
            'courses' => 190001,
            'contents' => 200001,
            'contents_logs' => 210001,
            'users_groups' => 220001,
            'groups_courses' => 230001,
            'groups_information' => 240001,
        ],
    ],
    'ITEMS_PER_PAGE' => env('PAGINATION_ITEMS_PER_PAGE', 50),
];
