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
        Schema::create('contents_logs', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->unsignedBigInteger('content_id')->comment('コンテンツID');
            $table->boolean('completed')->comment('完了・中断');
            $table->softDeletesDatetime();
            $table->datetimes();

            //ユニーク制約
            $table->unique(['user_id','content_id']);

            //外部キー制約
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('content_id')->references('id')->on('contents')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE contents_logs AUTO_INCREMENT = '.config('project.database.auto_increment.contents_logs'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contents_logs');
    }
};
