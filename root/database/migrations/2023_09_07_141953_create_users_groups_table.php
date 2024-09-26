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
        Schema::create('users_groups', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->unsignedBigInteger('user_id')->comment('ユーザーID');
            $table->unsignedBigInteger('group_id')->comment('グループID');
            $table->softDeletesDatetime();
            $table->datetimes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');

            $table->unique(['user_id', 'group_id']);
        });
        DB::statement('ALTER TABLE users_groups AUTO_INCREMENT = '.config('project.database.auto_increment.users_groups'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_groups');
    }
};
