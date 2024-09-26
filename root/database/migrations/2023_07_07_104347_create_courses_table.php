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
        Schema::create('courses', function (Blueprint $table) {
            $table->id()->comment('ID');
            $table->string('title')->comment('タイトル');
            $table->unsignedInteger('position')->default(0)->comment('ソート番号');
            $table->softDeletesDatetime();
            $table->datetimes();
        });
        DB::statement('ALTER TABLE courses AUTO_INCREMENT = '.config('project.database.auto_increment.courses'));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
