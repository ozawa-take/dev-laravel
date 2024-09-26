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
        Schema::create('groups_information', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->bigInteger('group_id')->comment('対象グループ');
            $table->bigInteger('information_id')->comment('お知らせID');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE groups_information AUTO_INCREMENT = '.config('project.database.auto_increment.groups_information'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups_information');
    }
};
