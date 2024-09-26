<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // NOTE: usersテーブルを新規作成  オリジナル: https://github.com/laravel/laravel/blob/v11.0.3/database/migrations/0001_01_01_000000_create_users_table.php#L14-L22
        Schema::create('users', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('username')->unique('users_username_unique')->comment('ユーザー名');
            $table->string('password')->comment('パスワード');
            $table->string('mail_address')->comment('メールアドレス');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE users AUTO_INCREMENT = '.config('project.database.auto_increment.users'));

        // NOTE: created_at の型を変更
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->datetime('created_at')->nullable(); // timestamp => datetime
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
