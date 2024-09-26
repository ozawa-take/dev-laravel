<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('username')->unique('admins_username_unique')->comment('ユーザー名');
            $table->string('password')->comment('パスワード');
            $table->string('mail_address')->comment('メールアドレス');
            $table->boolean('is_system_admin')->default(False)->comment('システム管理者権限');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE admins AUTO_INCREMENT = '.config('project.database.auto_increment.admins'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
