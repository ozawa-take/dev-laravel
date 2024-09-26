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
        Schema::create('user_logs', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID')->unique();
            $table->datetimes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
        DB::statement('ALTER TABLE user_logs AUTO_INCREMENT = '.config('project.database.auto_increment.user_logs'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_logs');
    }
};
