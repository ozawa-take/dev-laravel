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
        Schema::create('admin_messages', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('admin_id')->comment('管理者ID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->string('title')->comment('件名');
            $table->text('text')->nullable()->comment('本文');
            $table->integer('action')->default(0)->comment('状態:0:下書き 1:送信 2:未返信');
            $table->boolean('is_hidden')->default(0)->comment('表示・非表示');
            $table->boolean('is_replied')->default(0)->comment('返信の有・無');
            $table->unsignedBigInteger('reply_message_id')->nullable()->comment('返信するメッセージのID');
            $table->softDeletesDatetime();
            $table->datetimes();

            $table->foreign('admin_id')->references('id')->on('admins');
            $table->foreign('user_id')->references('id')->on('users');

        });
        DB::statement('ALTER TABLE admin_messages AUTO_INCREMENT = '.config('project.database.auto_increment.admin_messages'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_messages');
    }
};
