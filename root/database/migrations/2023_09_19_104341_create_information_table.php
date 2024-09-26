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
        Schema::create('information', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('title')->comment('タイトル');
            $table->text('text')->nullable()->comment('本文');
            $table->bigInteger('admin_id')->comment('管理者ID');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE information AUTO_INCREMENT = '.config('project.database.auto_increment.information'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('information');
    }
};
